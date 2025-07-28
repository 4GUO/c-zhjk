<?php
namespace userscenter\controller;
use lib;
class shop_vr_order extends control {
	public function __construct() {
		parent::_initialize();
	}
	
	public function indexOp(){
		$model_order = model('shop_vr_order');
		$where = array();
		$where['is_del'] = 0;
		$buyer_name = input('buyer_name', '');
		if($buyer_name){
			$where['buyer_name'] = $buyer_name;
		}
		$order_sn = input('order_sn', '');
		if($order_sn){
			$where['order_sn'] = $order_sn;
		}
		$allow_state_array = array('state_new', 'state_pay', 'state_success', 'state_cancel');
		$state_type = input('state_type', '');
        if (in_array($state_type, $allow_state_array)) {
            $where['order_state'] = str_replace($allow_state_array, array(ORDER_STATE_NEW, ORDER_STATE_PAY, ORDER_STATE_SUCCESS, ORDER_STATE_CANCEL), $state_type);
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
		$where['refund_state'] = 0;
		$list = $model_order->getList($where, '*', 'order_id desc', 20, input('page', 1, 'intval'));
		$order_list = array();
		if(!empty($list['list'])){
			foreach ($list['list'] as $value) {
				//显示取消订单
				$value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $value);					
				$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
				$value['payment_name'] = orderPaymentName($value['payment_code']);
				$order_list[] = $value;
			}
		}
		$this->assign('list', $order_list);
		$this->assign('page', page(isset($list['totalpage']) ? $list['totalpage'] : 0, array('page' => input('get.page', 1, 'intval'), 'state_type' => $state_type, 'buyer_name' => $buyer_name, 'order_sn' => $order_sn, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_vr_order/index')));
		$this->display();
	}
	
	/**
     * 订单详情
     *
     */
    public function show_orderOp(){
        $order_id = input('order_id', 0, 'intval');		
		if ($order_id <= 0) {
            web_error('参数有误！', users_url('shop_vr_order/index'));
        }
        $model_order = model('shop_vr_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition);
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！', users_url('shop_vr_order/index'));
        }
        $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        //显示取消订单
        $order_info['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        //显示系统自动取消订单日期
        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY * 24 * 3600;
        }
		//取兑换码列表
        $vr_code_list = $model_order->getOrderCodeList(array('order_id' => $order_info['order_id']));
        $order_info['extend_vr_order_code'] = $vr_code_list;
        $this->assign('order_info', $order_info);
        $this->display();
    }
	
	/**
     * 取消订单
     *
     */
    public function order_cancelOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_vr_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition);
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        if (!$if_allow) {
            output_error('无权操作！');
        }
		if (chksubmit()) {
			$msg = input('state_info1', '') != '' ? input('state_info1', '') : input('state_info', '');
            $result = $model_order->changeOrderStateCancel($order_info, 'admin', input('session.username', ''), $msg);			
			output_data(array('msg' => '取消成功', 'url' => users_url('shop_vr_order/index')));
		} else {
			$this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
		
    }
	/**
     * 兑换码消费
     */
    public function exchangeOp() {
        if (chksubmit()) {
			$vr_code = input('vr_code', '');
            if (!preg_match('/^[a-zA-Z0-9]{15,18}$/', $vr_code)) {
				output_error('兑换码格式错误，请重新输入');
            }
            $model_vr_order = model('shop_vr_order');
            $vr_code_info = $model_vr_order->getOrderCodeInfo(array('vr_code' => $vr_code));
            if (empty($vr_code_info)) {
				output_error('该兑换码不存在');
            }
            if ($vr_code_info['vr_state'] == 1) {
				output_error('该兑换码已被使用');
            }
            if ($vr_code_info['vr_indate'] < TIMESTAMP) {
				output_error('该兑换码已过期，使用截止日期为： ' . date('Y-m-d H:i:s', $vr_code_info['vr_indate']));
            }
            if ($vr_code_info['refund_lock'] > 0) {
                //退款锁定状态:0为正常,1为锁定(待审核),2为同意
				output_error('该兑换码已申请退款，不能使用');
            }
            //更新兑换码状态
            $update = array();
            $update['vr_state'] = 1;
            $update['vr_usetime'] = TIMESTAMP;
            $update = $model_vr_order->editOrderCode($update, array('vr_code' => $vr_code));
            //如果全部兑换完成，更新订单状态
            $model_vr_order->changeOrderStateSuccess($vr_code_info['order_id']);
            if ($update) {
                //取得返回信息
                $order_info = $model_vr_order->getInfo(array('order_id' => $vr_code_info['order_id']));
                if ($order_info['use_state'] == 0) {
                    $model_vr_order->edit(array('order_id' => $vr_code_info['order_id']), array('use_state' => 1));
                }
				output_data(array('data' => $order_info));
            }
        } else {
            $this->display();
        }
    }
	/**
     * 系统收到货款
     * @throws Exception
     */
    public function receive_payOp() {
		$model_order = model('shop_vr_order');
        $logic_order = logic('shop_vr_buy');
		if (chksubmit()) {
			$order_id = input('order_id', 0, 'intval');
			$order_info = $model_order->getInfo(array('order_id' => $order_id));
			$if_allow = $model_order->getOrderOperateState('system_receive_pay', $order_info);
			if (!$if_allow) {
				output_error('无权操作');
			}
			$order_list[] = $order_info;
			$payment_code = input('payment_code', '');
			if (!$payment_code) {
				output_error('请选择付款方式');
			}
			$trade_no = input('trade_no', '');
			if (!$trade_no) {
				output_error('请输入第三方支付流水单号');
			}
			$paytime = input('paytime', '');
			if (!$paytime) {
				output_error('请选择付款时间');
			}
			$paytime = strtotime($paytime);
			$result = $logic_order->updateOrderAll($order_list, $payment_code, $trade_no, $paytime);
			if (!$result['state']) {
				output_error($result['msg']);
			}
			output_data(array('msg' => '操作成功', 'url' => users_url('shop_vr_order/index')));
		} else {
			$order_id = input('order_id', 0, 'intval');
			$order_info = $model_order->getInfo(array('order_id' => $order_id));
			$this->assign('order_info', $order_info);
			$payment_list = model('mb_payment')->where(array('payment_state' => 1))->select();
			if (!empty($payment_list)) {
				foreach ($payment_list as $k => $value) {
					$payment_list[$k]['payment_config'] = fxy_unserialize($value['payment_config']);
					unset($payment_list[$k]['payment_id']);
					unset($payment_list[$k]['payment_config']);
				}
			}
			$this->assign('payment_list', $payment_list);
			$this->display();
		}
    }
}