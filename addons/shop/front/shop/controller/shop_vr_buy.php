<?php
namespace shop\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_vr_buy extends member
{
    public function __construct()
    {
        parent::_initialize();
    }
	/**
     * 虚拟商品购买第一步，设置购买数量
     * POST
     * 传入：cart_id:商品ID，quantity:购买数量
     */
    public function buy_step1Op()
    {
		if (IS_API) {
			$cart_info = explode('|', input('cart_id', ''));
			$goods_id = $cart_info[0];
			$quantity = $cart_info[1];
			$logic_buy_virtual = logic('shop_vr_buy');
			$result = $logic_buy_virtual->getBuyStepData($goods_id, $quantity, $this->member_info);
			if (!$result['state']) {
				output_error($result['msg']);
			} else {
				$result = $result['data'];
			}
			unset($result['member_info']);
			$result['available_predeposit'] = $this->member_info['available_predeposit'];
			output_data($result);
		}
    }
    /**
     * 直接购买第二步:保存订单入库，产生订单号，开始选择支付方式
     */
    public function buy_step2Op()
    {
		if (IS_API) {
			if (!input('mobile', '')) {
				output_error('请填写手机号');
			}
			$param = array();
			$param['ifcart'] = input('ifcart', 0, 'intval');
			$cart_info = explode('|', input('cart_id', ''));
			$param['goods_id'] = $cart_info[0];
			$param['quantity'] = $cart_info[1];
			$param['paycode'] = input('paycode', '');
			$param['buyer_phone'] = input('mobile', '');
			$param['message'] = input('message', '');
			$param['order_from'] = 1;
			$logic_buy = logic('shop_vr_buy');
			$result = $logic_buy->buyStep2($param, $this->member_info);
			if (!$result['state']) {
				output_error($result['msg']);
			}
			$only_payment_list = $this->get_payment_list();
			output_data(array('pay_sn' => $result['data']['order_sn'], 'available_predeposit' => $this->member_info['available_predeposit'], 'payment_list' => $only_payment_list));
		}
    }
	/**
	 * 流程第三步  跳转支付
	 */
	public function payOp() {
		header('Content-type: text/html; charset=UTF-8');
		$gpc = array_merge(input('get.'), input('post.'));
        $pay_sn = isset($gpc['pay_sn']) ? $gpc['pay_sn'] : '';
        if (!preg_match('/^\\d{18}$/', $pay_sn)) {
            output_error('支付单号错误');
        }
		$paycode = isset($gpc['payment_code']) ? $gpc['payment_code'] : '';
		if (!empty($paycode) && !in_array($paycode, array('wxpay_jsapi', 'wxapp', 'alipay', 'wxpay_h5'))) {
            output_error('付款方式错误，请重新选择');
        }
		$pay_info = $this->_get_pay_info($pay_sn, $gpc);
        if (isset($pay_info['error'])) {
            output_error($pay_info['error']);
        }
        // 第三方API支付
		$pay_info['pay_check'] = input('pay_check', 0);
        $this->_pay_api($pay_info);
	}
	/**
     * 获取订单支付信息
     */
    private function _get_pay_info($pay_sn, $gpc = array())
    {
		// 取订单信息
        $result = logic('shop_vr_buy')->getOrderInfo($pay_sn, $this->member_info['uid']);
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
			if ($this->client_type == 'wxapp') {
				output_data(array('msg' => '支付成功', 'url' => '/pages/vr_order/index'));
			} else if ($this->client_type == 'wap' || $this->client_type == 'wxweb' || $this->client_type == 'pc') {
				output_data(array('msg' => '支付成功', 'url' => '/pages/vr_order/index'));
			} else {
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
		$model_order = model('shop_vr_order');
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
						$update_order_list[$key]['order_id'] = $order_info['order_id'];
						$update_order_list[$key]['pd_amount'] = $available_pd_amount;
						$available_pd_amount = 0;
						$order_list[$key]['pd_amount'] = $order_amount;
					}
				}
			}
			if ($update_pay_order_list) {
				$result = logic('shop_vr_buy')->updateOrderAll($update_pay_order_list, 'predeposit');
				if (!$result['state']) {
					throw new \Exception($result['msg']);
				}
			}
			if ($update_order_list) {
				$sql = batchUpdate('ims_fxy_shop_vr_order', $update_order_list, 'order_id');
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
	public function check_pdOp() {
		$pay_sn = input('pay_sn', 0);
		$model_order = model('shop_vr_order');
        $condition = array();
		$condition['order_state'] = ORDER_STATE_NEW;
        $condition['order_sn'] = $pay_sn;
		$condition['buyer_id'] = $this->member_info['uid'];
        $result = $model_order->getList($condition);
		$order_list = $result['list'];
		if (empty($order_list)) {
			output_error('订单数据不存在');
        }
		$pay_amount = 0;
		foreach ($order_list as $order_info) {
			$pay_amount += floatval($order_info['order_amount']) - floatval($order_info['pd_amount']);
		}
		$available_predeposit = $this->member_info['available_predeposit'];
		$only_payment_list = $this->get_payment_list();
		output_data(array('pay_sn' => $pay_sn, 'order_amount' => $pay_amount, 'available_predeposit' => $available_predeposit, 'payment_list' => $only_payment_list));
	}
}