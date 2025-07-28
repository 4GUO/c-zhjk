<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_vr_buy
{
	public function check_buy($uid = 0, $buy_num = 0, $goods_info = array()){
		if($buy_num){
			if(!empty($goods_info['virtual_limit']) && $goods_info['virtual_limit'] > 0){
				if($buy_num > $goods_info['virtual_limit']){
					return callback(false, '[' . $goods_info['goods_name'] . ']购买数量上限' . $goods_info['virtual_limit']);
				}
			}
			if (!empty($goods_info['buy_xiangou']) && $goods_info['buy_xiangou'] > 0) {
				if ($buy_num != $goods_info['buy_xiangou']){
					return callback(false, '[' . $goods_info['goods_name'] . ']必须一次性购买' . $goods_info['buy_xiangou']);
				}
			}
			if($buy_num > $goods_info['goods_storage']){
				return callback(false, '[' . $goods_info['goods_name'] . ']库存不足');
			}
		}
		return callback(true);
	}
	/**
     * 得到虚拟商品购买数据(商品、店铺、会员)
     * 
     * @param int $goods_id
     *        	商品ID
     * @param int $quantity
     *        	购买数量
     * @param int $member_info
     *        	会员信息
     * @return array
     */
    public function getBuyStepData($goods_id, $quantity, $member_info)
    {
        $result = logic('shop_goods')->get_shop_goods_info($goods_id, $member_info['level_id']);
		$goods_info = $result['goods_info'];
        if (empty($goods_info)) {
            return callback(false, '该商品不符合购买条件，可能的原因有：下架、不存在、过期等');
        }
        if ($goods_info['virtual_limit'] > $goods_info['goods_storage']) {
            $goods_info['virtual_limit'] = $goods_info['goods_storage'];
        }
        $quantity = abs(intval($quantity));
        $quantity = $quantity <= 0 ? 1 : $quantity;
        $quantity = $quantity > $goods_info['virtual_limit'] ? $goods_info['virtual_limit'] : $quantity;
        if ($quantity > $goods_info['goods_storage'] || $goods_info['goods_storage'] <= 0) {
            return callback(false, '该商品库存不足');
        }
        $goods_info['quantity'] = $quantity;
        $goods_info['goods_total'] = priceFormat($goods_info['goods_price'] * $goods_info['quantity']);
        $store_info = model('seller')->getInfo(array('id' => $goods_info['store_id']), 'id,name,member_id');
		$goods_info['store_name'] = $store_info['name'];
		$ckeck_result = $this->check_buy(0, $quantity, $goods_info);
		if (!$ckeck_result['state']) {
			return callback(false, $ckeck_result['msg']);
		}
		$return = array();
		$return['goods_info'] = $goods_info;
		$return['store_info'] = $store_info;
        $return['member_info'] = $member_info;
        return callback(true, '', $return);
    }
	/**
     * 虚拟商品购买第二步
     * 
     * @param array $post 接收POST数据，必须传入goods_id:商品ID，quantity:购买数量,buyer_phone:接收手机,buyer_msg:买家留言
     * @param int $member_info        	
     * @return array
     */
    public function buyStep2($post, $member_info)
    {
        $result = $this->getBuyStepData($post['goods_id'], $post['quantity'], $member_info);
        if (!$result['state']) {
            return $result;
        }
        $goods_info = $result['data']['goods_info'];
        $member_info = $result['data']['member_info'];
        // 应付总金额计算
        $pay_total = priceFormat($goods_info['goods_price'] * $goods_info['quantity']);
        $store_id = $goods_info['store_id'];
        $store_goods_total_list = array($store_id => $pay_total);
        $pay_total = $store_goods_total_list[$store_id];
        // 整理数据
        $input = array();
        $input['quantity'] = $goods_info['quantity'];
        $input['buyer_phone'] = $post['buyer_phone'];
        $input['buyer_msg'] = $post['message'];
        $input['pay_total'] = $pay_total;
        $input['order_from'] = $post['order_from'];
        try {
            $model = model();
            // 开始事务
            $model->beginTransaction();
            // 生成订单
            $order_info = $this->_createOrder($input, $goods_info, $member_info);
            // 提交事务
            $model->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $model->rollback();
            return callback(false, $e->getMessage());
        }
        // 变更库存和销量
		//logic('shop_queue')->createOrderUpdateStorage(array($goods_info['goods_id'] => $goods_info['quantity']));
        return callback(true, '', array('order_id' => $order_info['order_id'], 'order_sn' => $order_info['order_sn']));
    }
	/**
     * 生成订单
     * 
     * @param array $input
     *        	表单数据
     * @param unknown $goods_info
     *        	商品数据
     * @param unknown $member_info
     *        	会员数据
     * @throws Exception
     * @return array
     */
    private function _createOrder($input, $goods_info, $member_info)
    {
        $model_vr_order = model('shop_vr_order');
        // 存储生成的订单,函数会返回该数组
        $order_list = array();
        $order = array();
        $order_code = array();
        $order['order_sn'] = $order_sn = $this->_makeOrderSn($member_info['uid']);
        $order['store_id'] = $goods_info['store_id'];
        $order['store_name'] = $goods_info['store_name'];
        $order['buyer_id'] = $member_info['uid'];
		$order['owner_id'] = $member_info['is_distributor'] == 1 ? $member_info['uid'] : $member_info['inviter_id'];
        $order['buyer_name'] = $member_info['nickname'];
        $order['buyer_phone'] = $input['buyer_phone'];
        $order['buyer_msg'] = $input['buyer_msg'];
        $order['add_time'] = TIMESTAMP;
        $order['order_state'] = ORDER_STATE_NEW;
        $order['order_amount'] = $input['pay_total'];
        $order['goods_id'] = $goods_info['goods_id'];
        $order['goods_name'] = $goods_info['goods_name'];
        $order['goods_price'] = $goods_info['goods_price'];
        $order['goods_num'] = $input['quantity'];
        $order['goods_image'] = $goods_info['goods_image'];
        $order['gc_id'] = $goods_info['gc_id'];
        $order['vr_indate'] = $goods_info['virtual_indate'];
        $order['vr_invalid_refund'] = $goods_info['virtual_invalid_refund'];
        $order['order_from'] = $input['order_from'];
		$order['goods_costprice'] = $goods_info['goods_costprice'];
		$order['promotions_amount'] = 0;
        $order_id = $model_vr_order->add($order);
        if (!$order_id) {
            throw new \Exception('订单保存失败');
        }
        $order['order_id'] = $order_id;
        return $order;
    }
    /**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位 = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * 
     * @return string
     */
    private function _makeOrderSn($member_id)
    {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (double) microtime() * 1000) . sprintf('%03d', (int) $member_id % 1000);
    }
	
	/**
     * 取得实物订单所需支付金额等信息
     * @param int $order_sn
     * @param int $member_id
     * @return array
     */
    public function getOrderInfo($order_sn, $member_id = null)
    {
        //验证订单信息
        $model_order = model('shop_vr_order');
        $order_pay_info = array();
		$order_pay_info['pay_sn'] = $order_sn;
        $order_pay_info['subject'] = '订单_' . $order_sn;
        $order_pay_info['order_type'] = 'vr_order';
		$condition = array();
        $condition['order_sn'] = $order_sn;
		if (!empty($member_id)) {
            $condition['buyer_id'] = $member_id;
        }
		$condition['order_state'] = ORDER_STATE_NEW;
        $result = $model_order->getList($condition);
		$order_list = $result['list'];
		if (empty($order_list)) {
			return callback(false, '订单信息不存在');
		}
        $order_pay_info['order_list'] = $order_list;
        return callback(true, '', $order_pay_info);
    }
	public function updateOrderAll($order_list, $payment_code, $trade_no = '', $paytime = '') {
	    $order_list = array_values($order_list);
		$model_order = model('shop_vr_order');
		$model_goods = model('shop_goods');
		$kucun_data = array();
		$kucun_data_where = array();
        try {
			$model = model();
            $model->beginTransaction();
			$order_sn = $order_list[0]['order_sn'];
			$buyer_id = $order_list[0]['buyer_id'];
            $logic_pd = logic('predeposit');
			$goods_ids = array();
            foreach ($order_list as $key => $order_info) {
                $order_id = $order_info['order_id'];
                if ($order_info['order_state'] != ORDER_STATE_NEW) {
                    continue;
                }
                //下单，支付被冻结的预存款
                $pd_amount = floatval($order_info['pd_amount']);
                if ($pd_amount > 0) {
                    $data_pd = array();
                    $data_pd['uid'] = $order_info['buyer_id'];
                    $data_pd['member_name'] = $order_info['buyer_name'];
                    $data_pd['amount'] = $pd_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $logic_pd->changePd('order_comb_pay', $data_pd);
                }
				//库存销量处理
				$kucun_data[] = array(
					'goods_id' => $order_info['goods_id'],
					'goods_salenum' => '(goods_salenum+' . $order_info['goods_num'] . ')',
					'goods_storage' => '(goods_storage-' . $order_info['goods_num'] . ')',
				);
				$kucun_data_where[] = '`goods_storage` >= ' . $order_info['goods_num'];
				$goods_ids[$order_info['goods_id']] = $order_info['goods_id'];
				//发放兑换码
				$insert = $model_order->addOrderCode($order_info);
				if (!$insert) {
					throw new \Exception('兑换码发送失败');
				}
            }
			if ($kucun_data) {
				$sql = batchUpdate('ims_fxy_shop_goods', $kucun_data, 'goods_id', $kucun_data_where, true);
				$result = $model->query($sql, 'update');
				if (!$result) {
					throw new \Exception('库存更新失败');
					lib\logging::write(var_export($sql, true));
				}
			}
            //更新订单状态
            $update_order = array();
            $update_order['order_state'] = ORDER_STATE_PAY;
            $update_order['payment_time'] = !empty($paytime) ? $paytime : TIMESTAMP;
            $update_order['payment_code'] = $payment_code;
			$update_order['trade_no'] = $trade_no;
            $update = $model_order->edit(array('order_sn' => $order_sn, 'order_state' => ORDER_STATE_NEW), $update_order);
            if (!$update) {
                throw new \Exception('操作失败');
            }
            $model->commit();
			//更新商品总销量
			$goods_commonids = array();
			if (!empty($goods_ids)) {
				$rs = model('shop_goods')->getList(array('goods_id' => array_keys($goods_ids)), 'goods_commonid,goods_salenum');
				$goods_common_data = array();
				foreach ($rs['list'] as $k => $v) {
					$goods_commonids[] = $v['goods_commonid'];
					if (!isset($goods_salenum[$v['goods_commonid']])) {
						$goods_salenum[$v['goods_commonid']] = $v['goods_salenum'];
					} else {
						$goods_salenum[$v['goods_commonid']] += $v['goods_salenum'];
					}
					$goods_common_data[] = array(
						'goods_commonid' => $v['goods_commonid'],
						'goods_salenum' => $goods_salenum[$v['goods_commonid']],
					);
				}
				$sql = batchUpdate('ims_fxy_shop_goods_common', $goods_common_data, 'goods_commonid');
				$result = $model->query($sql, 'update');
			}
        } catch (\Exception $e) {
            $model->rollback();
            return callback(false, $e->getMessage());
        }
		$buyer_info = model('member')->getInfo(array('uid' => $buyer_id));
        $pay_amount = 0;
		foreach ($order_list as $order_info) {
            //防止重复发送消息
            if ($order_info['order_state'] != ORDER_STATE_NEW) {
                continue;
            }
			$order_info['uid'] = $order_info['buyer_id'];
			$order_sn = $order_info['order_sn'];
			$goods_num = $order_info['goods_num'];
			//发送兑换码到手机
			$param = array('order_id' => $order_info['order_id'], 'buyer_id' => $order_info['buyer_id'], 'buyer_phone' => $order_info['buyer_phone'], 'name' => config('name'));
			logic('shop_queue')->sendVrCode($param);
			logic('store_bill')->ins_supply_commiss_vr_order($order_info);
            logic('yewu')->self_performance_add($order_info, $goods_num);
			logic('yewu')->team_performance_add($order_info, $goods_num);
			$pay_amount += $order_info['order_amount'];
			//diy yewu
			$order_goods = array();
			$order_goods[] = $order_info;
			logic('yewu')->add_distributor_good_commission($order_goods, $buyer_info['uid'], $buyer_info['nickname'], 0);
        }
		logic('yewu')->add_distributer($buyer_id, $goods_commonids, $pay_amount);//购买成为分销商
		logic('public_yewu')->deal_public($buyer_id, $goods_commonids, $pay_amount);
        return callback(true, '操作成功');
	}
}