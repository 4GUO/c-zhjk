<?php
namespace shop\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class tihuo_buy extends member
{
    public function __construct()
    {
        parent::_initialize();
    }
    /**
     * 购物车、直接购买第一步:选择收获地址和配置方式
     */
    public function buy_step1Op()
    {
		if(IS_API){
			$tc_id = input('tc_id', 0, 'intval');
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			$cart_id = explode(',', input('cart_id', ''));
			$city_id = input('city_id', 0, 'intval');
			if (!$city_id) {
				$address_info = model('address')->getInfo(array('uid' => $this->member_info['uid'], 'is_default' => 1));
				if (!$address_info) {
					$city_id = 0;
					$address_info = array();
				} else {
					$city_id = $address_info['city_id'];
				}
			} else {
				$address_info = array();
			}
			$logic_buy = logic('tihuo_buy');
			// 得到购买数据
			$result = $logic_buy->buyStep1($cart_id, $this->member_info, $city_id, $tc_id, $tihuoquan_id);
			if (!$result['state']) {
				output_error($result['msg']);
			} else {
				$result = $result['data'];
			}
			$buy_list = array();
			$buy_list['title'] = $this->config['name'];
			$buy_list['goods_list'] = $result['goods_list'];
			$buy_list['address_info'] = $address_info;
			$buy_list['available_predeposit'] = $this->member_info['available_predeposit'];
			$buy_list['order_amount'] = $result['final_order_total'];
			output_data($buy_list);
		}
    }
    /**
     * 购物车、直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
     */
    public function buy_step2Op()
    {
		if (IS_API) {
			$param = array();
			$param['tc_id'] = input('tc_id', 0, 'intval');
			$param['tihuoquan_id'] = input('tihuoquan_id', 0, 'intval');
			$param['ifcart'] = input('ifcart', 0, 'intval');
			$param['cart_id'] = explode(',', input('cart_id', ''));
			$param['address_info'] = json_decode(htmlspecialchars_decode(input('address_info', '')), true);
			if (!$param['address_info']) { //兼容网页版
				$address_id = input('address_id', 0, 'intval');
				if($address_id){
					$param['address_info'] = model('address')->getInfo(array('address_id' => $address_id));
				}
			}
			$param['paycode'] = input('paycode', '');
			
			$post_voucher = input('voucher_info', '') ? explode(',', input('voucher_info', '')) : array();
			$voucher_info = array();
			foreach ($post_voucher as $v) {
				$item = explode('|', $v);
				$voucher_info[$item[0]] = $item[1];
			}
			$param['voucher_info'] = $voucher_info;
			
			$post_message = explode(',', input('message', ''));
			$message = array();
			foreach ($post_message as $v) {
				$item = explode('|', $v);
				$message[$item[0]] = $item[1];
			}
			$param['message'] = $message;
			//lib\logging::write(var_export($param, true));
			$logic_buy = logic('tihuo_buy');
			$result = $logic_buy->buyStep2($param, $this->member_info);
			//lib\logging::write(var_export($result, true));
			if (!$result['state']) {
				output_error($result['msg']);
			}
			$only_payment_list = $this->get_payment_list();
			$return = array(
				'pay_sn' => $result['data']['pay_sn'],
				'available_predeposit' => $this->member_info['available_predeposit'],
				'payment_list' => $only_payment_list
			);
			output_data($return);
		}
    }
    /**
	 * 流程第四步  跳转支付
	 */
	public function payOp() {
		//header('Content-type: text/html; charset=UTF-8');
		$gpc = array_merge(input('get.'), input('post.'));
        $pay_sn = isset($gpc['pay_sn']) ? $gpc['pay_sn'] : '';
        if (!preg_match('/^\\d{18}$/', $pay_sn)) {
            output_error('支付单号错误');
        }
		$pay_info = $this->_get_pay_info($pay_sn, $gpc);
        if (isset($pay_info['error'])) {
            output_error($pay_info['error']);
        }
        $paycode = isset($gpc['payment_code']) ? $gpc['payment_code'] : '';
		if (!empty($paycode) && !in_array($paycode, array('wxpay_jsapi', 'wxapp', 'alipay', 'wxpay_h5'))) {
            output_error('付款方式错误，请重新选择');
        }
        // 第三方API支付
		$pay_info['pay_check'] = input('pay_check', 0);
        $this->_new_pay_api($pay_info);
	}
	/**
     * 获取订单支付信息
     */
    private function _get_pay_info($pay_sn, $gpc = array())
    {
		// 取订单信息
        $result = logic('tihuo_buy')->getRealOrderInfo($pay_sn, $this->member_info['uid']);
        if (!$result['state']) {
            return array('error' => $result['msg']);
        }
		$order_pay_info = $result['data'];
		$order_list = $order_pay_info['order_list'];
		// 站内余额支付
        if (!empty($gpc['pd_pay'])) {
			$result = $this->_pd_pay($order_list, $gpc);
			if (isset($result['error'])) {
				return array('error' => $result['error']);
			}else{
				$order_list = $result['order_list'];
			}
		}
		// 计算本次需要在线支付的订单总金额
        $pay_amount = 0;
        if (!empty($order_list)) {
            foreach ($order_list as $order_info) {
                if ($order_info['order_state'] == ORDER_STATE_NEW) {
					$pay_amount += floatval($order_info['order_amount']) - floatval($order_info['pd_amount']);
                }
            }
        }
        if ($pay_amount <= 0) {
			if($this->client_type == 'wxapp') {
				output_data(array('msg' => '支付成功', 'url' => '/pages/agent/index'));
			}else if($this->client_type == 'wap' || $this->client_type == 'wxweb' || $this->client_type == 'pc') {
				output_data(array('msg' => '支付成功', 'url' => '/pages/agent/index'));
			}else{
				output_data(array('msg' => '支付成功'));
			}
        }
		$order_pay_info['payment_code'] = $gpc['payment_code'];
		$order_pay_info['api_pay_amount'] = priceFormat($pay_amount);
        return $order_pay_info;
    }
	/**
     * 站内余额支付实物订单
     */
    private function _pd_pay($order_list, $post)
    {
		$model_order = model('shop_order');
        try {
			$model = model();
            $model->beginTransaction();
			if (empty($post['password'])) {
				throw new \Exception('请输入支付密码');
			}
			if (empty($this->member_info['paypwd']) || $this->member_info['paypwd'] != f_hash($post['password'])) {
				throw new \Exception('支付密码不正确');
			}
			if ($this->member_info['available_predeposit'] <= 0) {
				throw new \Exception('余额不足');
			}
            $available_pd_amount = floatval($this->member_info['available_predeposit']);
			$logic_pd = logic('predeposit');
			$update_pay_order_list = array();
			$update_order_list = array();
			foreach ($order_list as $key => $order_info) {
				// 货到付款的订单、已经充值卡支付的订单跳过
				if ($order_info['payment_code'] == 'offline') {
					continue;
				}
				if ($order_info['order_state'] != ORDER_STATE_NEW) {
					continue;
				}
				$order_amount = floatval($order_info['order_amount']);
				$data_pd = array();
				
				$data_pd['uid'] = $this->member_info['uid'];
				$data_pd['member_name'] = $this->member_info['nickname'];
				$data_pd['amount'] = $order_amount;
				$data_pd['order_sn'] = $order_info['order_sn'];
				if ($available_pd_amount >= $order_amount) {
					// 预存款立即支付，订单支付完成
					$logic_pd->changePd('order_pay', $data_pd);
					$available_pd_amount -= $order_amount;
					// 订单状态 置为已支付
					$order_list[$key]['order_state'] = ORDER_STATE_PAY;
					$order_list[$key]['pd_amount'] = $order_amount;
					$update_pay_order_list[] = $order_info;
				} else {
					// 暂冻结预存款,后面还需要 API彻底完成支付
					if ($available_pd_amount > 0) {
						$data_pd['amount'] = $available_pd_amount;
						$logic_pd->changePd('order_freeze', $data_pd);
						// 预存款支付金额保存到订单
						$order_list[$key]['pd_amount'] = $available_pd_amount;
						$update_order_list[$key]['order_id'] = $order_info['order_id'];
						$update_order_list[$key]['pd_amount'] = $available_pd_amount;
						$available_pd_amount = 0;
					}
				}
			}
			if ($update_pay_order_list) {
				$result = logic('tihuo_buy')->updateOrderAll($update_pay_order_list, 'predeposit');
				if (!$result['state']) {
					throw new \Exception($result['msg']);
				}
			}
			if ($update_order_list) {
				$sql = batchUpdate('ims_fxy_shop_order', $update_order_list, 'order_id');
				$result = $model->query($sql, 'update');
				if (!$result) {
					throw new \Exception('订单更新失败');
				}
			}
            $model->commit();
        } catch (\Exception $e) {
            $model->rollback();
			return array('error' => $e->getMessage());
        }
        return array('order_list' => $order_list);
    }
}