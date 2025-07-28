<?php
namespace sellercenter\controller;
use lib;
class shop_order extends control {
	public function __construct() {
		parent::_initialize();
	}
	
	public function indexOp(){
		$model_order = model('shop_order');
		$where = array();
		$where['store_id'] = $this->store_id;
		$where['is_del'] = 0;
		$buyer_name = input('buyer_name', '');
		if($buyer_name){
			$where['member_name'] = '%' . $buyer_name . '%';
		}
		$order_sn = input('order_sn', '');
		if($order_sn){
			$where['order_sn'] = $order_sn;
		}
		$allow_state_array = array('state_new', 'state_pay', 'state_send', 'state_success', 'state_cancel');
		$state_type = input('state_type', '');
        if (in_array($state_type, $allow_state_array)) {
            $where['order_state'] = str_replace($allow_state_array, array(ORDER_STATE_NEW, ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS, ORDER_STATE_CANCEL), $state_type);
        }
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date) : null;
        $end_unixtime = $if_end_date ? strtotime($query_end_date) : null;
        if ($start_unixtime || $end_unixtime) {
            $where['add_time >='] = $start_unixtime;
			$where['add_time <='] = $end_unixtime;
        }
		$where['lock_state'] = 0;
		$list = $model_order->getList($where, '*', 'order_id desc', 20, input('page', 1, 'intval'), array('order_common', 'order_goods', 'member'));
		$order_list = array();
		if(!empty($list['list'])){
			foreach ($list['list'] as $value) {
				//显示取消订单
				$value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $value);
				//显示发货
				$value['if_send'] = $model_order->getOrderOperateState('send', $value);				
				//显示调整费用
				$value['if_modify_price'] = $model_order->getOrderOperateState('modify_price', $value);
				//显示调整订单费用
				$value['if_spay_price'] = $model_order->getOrderOperateState('spay_price', $value);				
				//显示锁定中
				$value['if_lock'] = $model_order->getOrderOperateState('lock', $value);            
				//显示物流跟踪
				$value['if_deliver'] = $model_order->getOrderOperateState('deliver', $value);
				$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
				$value['goods_count'] = count($value['extend_order_goods']);
				$value['payment_name'] = orderPaymentName($value['payment_code']);
				$order_list[] = $value;
			}
		}
		$this->assign('list', $order_list);
		$this->assign('page', page(isset($list['totalpage']) ? $list['totalpage'] : 0, array('page' => input('get.page', 1, 'intval'), 'state_type' => $state_type, 'buyer_name' => $buyer_name, 'order_sn' => $order_sn, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_order/index')));
		$this->display();
	}
	
	/**
     * 订单详情
     *
     */
    public function show_orderOp(){
        $order_id = input('order_id', 0, 'intval');		
		if ($order_id <= 0) {
            web_error('参数有误！', users_url('shop_order/index'));
        }
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！', users_url('shop_order/index'));
        }
        $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        //$model_refund_return = model('shop_refund_return');
        //$order_list = array();
        //$order_list[$order_id] = $order_info;
        //$order_list = $model_refund_return->getGoodsRefundList($order_list, 1);
        //订单商品的退款退货显示
        //$order_info = $order_list[$order_id];
        //$refund_all = isset($order_info['refund_list'][0]) ? $order_info['refund_list'][0] : array();
        //if (!empty($refund_all) && $refund_all['seller_state'] < 3) {
            //订单全部退款商家审核状态:1为待审核,2为同意,3为不同意
            //core\tpl::output('refund_all', $refund_all);
        //}
        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock', $order_info);
        //显示调整运费
        $order_info['if_modify_price'] = $model_order->getOrderOperateState('modify_price', $order_info);
        //显示调整价格
        $order_info['if_spay_price'] = $model_order->getOrderOperateState('spay_price', $order_info);
        //显示取消订单
        $order_info['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        //显示发货
        $order_info['if_send'] = $model_order->getOrderOperateState('send', $order_info);
        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver', $order_info);
        //显示系统自动取消订单日期
		
		$refund_state = '';
		if($order_info['if_lock']){
			$model_return = model('shop_refund_return');
			$refund_info = $model_return->getInfo(array('store_id' => $this->store_id, 'order_id' => $order_id));
			if(!empty($refund_info['order_id'])){
				$refund_state = $model_return->_orderState($refund_info);				
			}
		}
		$this->assign('refund_state', $refund_state);
		
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY * 24 * 3600;
        }
		
        //显示快递信息
        if ($order_info['shipping_code'] != '') {
			if(!empty($order_info['extend_order_common']['shipping_express_id'])){
				$express = model('express')->getInfo(array('id' => $order_info['extend_order_common']['shipping_express_id']));
				$order_info['express_info']['e_code'] = $express['e_code'];
				$order_info['express_info']['e_name'] = $express['e_name'];
				$order_info['express_info']['e_url'] = $express['e_url'];
			} else {
				$order_info['express_info']['e_code'] = '';
				$order_info['express_info']['e_name'] = '不需要物流';
				$order_info['express_info']['e_url'] = '';
			}            
        }
        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
        }
        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id' => $order_info['order_id']), 'log_id desc');
        }
        if (empty($order_info['zengpin_list'])) {
            $order_info['goods_count'] = count($order_info['extend_order_goods']);
        } else {
            $order_info['goods_count'] = count($order_info['extend_order_goods']) + 1;
        }
        $this->assign('order_info', $order_info);
		
        //发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = model('shop_daddress')->getInfo(array('address_id' => $order_info['extend_order_common']['daddress_id']));
            $this->assign('daddress_info', $daddress_info);
        }
        $this->display();
    }
	
	/**
     * 取消订单
     *
     */
    public function order_cancelOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        if (!$if_allow) {
            output_error('无权操作！');
        }
		if (chksubmit()) {
			$msg = input('state_info1', '') != '' ? input('state_info1', '') : input('state_info', '');
            $result = $model_order->changeOrderStateCancel($order_info, 'seller', input('session.sellername', ''), $msg);			
			output_data(array('msg' => '取消成功', 'url' => users_url('shop_order/index')));
		} else {
			$this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
		
    }
	
    /**
     * 修改运费
     * @param unknown $order_info
     */
    public function shipping_priceOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('modify_price', $order_info);
        if (!$if_allow) {
            return callback(false, '无权操作');
        }
        if (chksubmit()) {
			$shipping_fee = input('shipping_fee', '') == '' ? 0 : priceFormat(input('shipping_fee', ''));			
            $result = $model_order->changeOrderShipPrice($order_info, 'seller', input('session.sellername', ''), $shipping_fee);			
			output_data(array('msg' => '修改成功', 'url' => users_url('shop_order/index')));
        } else {
            $this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }
    
	/**
     * 修改商品价格
     * @param unknown $order_info
     */
    public function order_priceOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('spay_price', $order_info);
        if (!$if_allow) {
            return callback(false, '无权操作');
        }
        if (chksubmit()) {
			$order_price = input('order_price', '') == '' ? 0 : priceFormat(input('order_price', ''));			
            $result = $model_order->changeOrderSpayPrice($order_info, 'seller', input('session.sellername', ''), $order_price);			
			output_data(array('msg' => '修改成功', 'url' => users_url('shop_order/index')));
        } else {
            $this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }
	
	/**
     * 打印订单
     * @param unknown $order_info
     */
    public function print_orderOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		
		$goods_new_list = array();
        $goods_all_num = 0;
        $goods_total_price = 0;
		
        if (!empty($order_info['extend_order_goods'])) {
            $i = 1;
            foreach ($order_info['extend_order_goods'] as $k => $v) {
                $v['goods_name'] = str_cut($v['goods_name'], 100);
                $goods_all_num += $v['goods_num'];
                $v['goods_all_price'] = $v['goods_num'] * $v['goods_price'];
                $goods_total_price += $v['goods_all_price'];
                $goods_new_list[ceil($i / 15)][$i] = $v;
                $i++;
            }
        }
		
		$config = model('config')->getInfo();
		
        //优惠金额
        $promotion_amount = $goods_total_price - $order_info['goods_amount'];
		$this->assign('promotion_amount', $promotion_amount);
        $this->assign('goods_all_num', $goods_all_num);
        $this->assign('goods_total_price', $goods_total_price);
        $this->assign('goods_list', $goods_new_list);
        $this->assign('order_info', $order_info);
		$this->assign('config', $config);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
}
?>