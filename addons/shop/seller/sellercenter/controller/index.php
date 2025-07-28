<?php
namespace sellercenter\controller;
class index extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
	public function analysOp() {
		$agent_order = model('shop_order');
		//今日
		$today = \lib\timer::today();
		$begin_time = $today[0];
		$end_time = $today[1];
		$where_order = array(
			'store_id' => $this->store_id, 
			'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS), 
			'lock_state' => 0,
			'add_time >=' => $begin_time,
			'add_time <=' => $end_time
		);
		//今日付款订单
		$today_pay_order_count = $agent_order->where($where_order)->total();
		$this->assign('today_pay_order_count', $today_pay_order_count);
		//今日付款
		$today_pay_order_money = $agent_order->field('SUM(order_amount) as total')->where($where_order)->find();
		$this->assign('today_pay_order_money', $today_pay_order_money['total'] ?: 0);
		//总金额
		$where_order = array(
			'store_id' => $this->store_id, 
			'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS), 
			'lock_state' => 0,
		);
		$pay_order_money = $agent_order->field('SUM(order_amount) as total')->where($where_order)->find();
		$this->assign('pay_order_money', $pay_order_money['total'] ?: 0);
		$this->display();
	}
	public function statisticsOp() {
		if(IS_API){
			$agent_order = model('shop_order');
			$ORDER_STATE_NEW = $agent_order->where(array('store_id' => $this->store_id, 'order_state' => ORDER_STATE_NEW, 'lock_state' => 0))->total();
			$ORDER_STATE_PAY = $agent_order->where(array('store_id' => $this->store_id, 'order_state' => ORDER_STATE_PAY, 'lock_state' => 0))->total();
			$ORDER_STATE_SEND = $agent_order->where(array('store_id' => $this->store_id, 'order_state' => ORDER_STATE_SEND, 'lock_state' => 0))->total();
			$ORDER_STATE_SUCCESS = $agent_order->where(array('store_id' => $this->store_id, 'order_state' => ORDER_STATE_SUCCESS, 'lock_state' => 0))->total();
			$ALL = $agent_order->where(array('store_id' => $this->store_id))->total();
			$state_refund = model('shop_refund_return')->where(array('store_id' => $this->store_id, 'refund_state' => 1))->total();
			$output_data = array(
				'state_new' => $ORDER_STATE_NEW, 
				'state_pay' => $ORDER_STATE_PAY, 
				'state_send' => $ORDER_STATE_SEND, 
				'state_success' => $ORDER_STATE_SUCCESS, 
				'all' => $ALL,
				'state_refund' => $state_refund,
			);
			output_data($output_data);
		}
	}

	public function load_order_chartOp() {
		$model = model('shop_order');
		//交易统计图
        $type = 'normal';
        $begin_time = strtotime(date('Y-m-d') . ' 00:00:00') - 7 * 86400;
        $end_time = time();
		$result = array();
		$count_value = array();
		$price_key = array();
		$price_value = array();
		$price_value_2 = array();
		for ($i = 7; $i >= 1; $i--) {
			$begin_time = strtotime(date('Y-m-d') . ' 00:00:00') - $i * 86400;
			$end_time = $begin_time + 86400;
			$where = array(
				'store_id' => $this->store_id,
				'add_time >=' => $begin_time,
				'add_time <=' => $end_time,
				'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),
				'lock_state' => 0,
			);
			$where_refund = array(
				'store_id' => $this->store_id,
				'add_time >=' => $begin_time,
				'add_time <=' => $end_time,
				'lock_state' => 1,
			);
			$count = $model->where($where)->total();
			$sum_info = $model->field('SUM(order_amount) as total')->where($where)->find();
			$refund_sum_info = $model->field('SUM(order_amount) as total')->where($where_refund)->find();
			if (empty($sum_info) || empty($sum_info['total']) || $sum_info['total'] < 0) {
				$total = 0;
			} else {
				$total = $sum_info['total'];
			}
			if (empty($refund_sum_info) || empty($refund_sum_info['total']) || $refund_sum_info['total'] < 0) {
				$refund_total = 0;
			} else {
				$refund_total = $refund_sum_info['total'];
			}
			$price_key[] = date('m-d', $begin_time);
			$count_value[] = $count;
			$price_value[] = sprintf('%.2f', $total);
			$price_value_2[] = sprintf('%.2f', $refund_total);
		}
		$result['lines'] = array();
		$result['lines']['payAmountLine']['xAxisData'] = $price_key;
		$result['lines']['payAmountLine']['seriesData'][0]['data'] = $price_value;
		$result['lines']['payAmountLine']['seriesData'][1]['data'] = $price_value_2;
		$result['price_key'] = $price_key;
		$result['count_value'] = $count_value;
		$result['price_value'] = $price_value;
		echo json_encode(array(
			'code' => 0,
			'data' => $result
		));
		die();
	}
	//商品月销售排行
	public function load_echat_month_goods_salesOp() {
		$model_order = model('shop_order');
        $type = input('type', 1, 'intval');
        if ($type == 1) {
			$month = \lib\timer::month();
            $begin_time = $month[0];
			$end_time = $month[1];
            $date_month = date('Y-m', $begin_time);
        } else {
			$lastMonth = \lib\timer::lastMonth();
            $begin_time = $lastMonth[0];
			$end_time = $lastMonth[1];
            $date_month = date('Y-m', $begin_time);
        }
		$where = array(
			'store_id' => $this->store_id,
			'add_time >=' => $begin_time,
			'add_time <=' => $end_time,
			'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),
			'lock_state' => 0,
		);
        $result = $model_order->getList($where, 'order_sn', 'order_id desc', null, null);
        $order_sns = array(0);
		foreach ($result['list'] as $k => $v) {
			$order_sns[] = $v['order_sn'];
		}
		unset($result);
		$result = model('shop_order_goods')->field('goods_id,goods_num,goods_price')->where(array('order_sn' => $order_sns))->select();
        $result = $result ?: array();
		$goods_list = array();
		foreach ($result as $k => $v) {
			if (!isset($goods_list[$v['goods_id']]['total_quantity'])) {
				$goods_list[$v['goods_id']]['total_quantity'] = $v['goods_num'];
			} else {
				$goods_list[$v['goods_id']]['total_quantity'] += $v['goods_num'];
			}
			if (!isset($goods_list[$v['goods_id']]['total'])) {
				$goods_list[$v['goods_id']]['total'] = $v['goods_num'] * $v['goods_price'];
			} else {
				$goods_list[$v['goods_id']]['total'] += $v['goods_num'] * $v['goods_price'];
			}
			$goods_list[$v['goods_id']]['goods_id'] = $v['goods_id'];
		}
        unset($result);
		$goods_list = fxy_array_sort($goods_list, 'total', 'DESC');
		$goods_list = array_slice($goods_list, 0, 10);
		$goods_ids = $top10 = array();
		foreach ($goods_list as $k => $v) {
			$goods_ids[] = $v['goods_id'];
			$top10[$v['goods_id']] = $v;
		}
		unset($result);
		$list = array();
		$total = 0;
        $total_quantity = 0;
		if ($goods_ids) {
			$result = model('shop_goods')->field('goods_id,goods_name')->where(array('goods_id' => $goods_ids))->select();
			foreach ($result as $key => $val) {
				$val['name'] = mb_substr($val['goods_name'], 0, 8, 'utf-8') . '  ' . '销量:' . $top10[$val['goods_id']]['total_quantity'];
				$val['total'] = sprintf('%.2f', $top10[$val['goods_id']]['total']);
				$total += $val['total'];
				$total_quantity += $top10[$val['goods_id']]['total_quantity'];
				$list[] = $val;
			}
			//如果goods表顺序查询不对，请在此处对$list排序
			unset($result);
		}
        $total = sprintf('%.2f', $total);
        echo json_encode(array(
            'code' => 0,
            'list' => $list,
            'total_quantity' => $total_quantity,
            'total' => $total,
            'month' => $date_month
        ));
        die();
    }
	/**
		* @author injection(injection.mail@gmail.com)
		* @var date1日期1
		* @var date2 日期2
		* @var tags 年月日之间的分隔符标记,默认为'-' 
		* @return 相差的月份数量
		* @example:
		$date1 = "2003-08-11";
		$date2 = "2008-11-06";
		$monthNum = getMonthNum( $date1 , $date2 );
		echo $monthNum;
	*/
	private function getMonthNum( $date1, $date2, $tags = '-' ){
		$date1 = explode($tags, $date1);
		$date2 = explode($tags, $date2);
		return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
	}
	
	public function json_areaOp(){
        echo $_GET['callback'] . '('.json_encode(model('area')->getAreaArrayForJson()).')';
    }
	/**
     * json输出地址数组
     */
    public function json_area_showOp(){
        $area_info['text'] = model('area')->getTopAreaName(intval($_GET['area_id']));
        echo $_GET['callback'] . '(' . json_encode($area_info) . ')';
    }
	//密码设置
	public function passwordOp() {
		$is_admin = input('session.is_admin', false);
		if ($is_admin) {
			$seller = model('seller')->getInfo(array('id' => $this->store_id));
		} else {
			$account = model('account_store')->getInfo(array('account_id' => input('session.account_id', 0, 'intval')));
		}
		
		if (chksubmit()) {
			$password = input('password', '', 'trim');
			$repassword = input('repassword', '', 'trim');
			if ($password != $repassword) {
				output_error('密码不一致');
			}
			if ($password) {
				if ($is_admin) {
					$password = md5($password . $seller['login_slat']);
					$data = array(
						'login_password' => $password,
					);
					model('seller')->edit(array('id' => $this->store_id), $data);
				} else {
					$password = md5($password . $account['salt']);
					$data = array(
						'password' => $password,
					);
					model('account_store')->edit(array('account_id' => input('session.account_id', 0, 'intval')), $data);
				}
				$this->log('修改密码成功');
				output_data(array('msg' => '操作成功', 'url' => users_url('index/password')));
			} else {
				output_error('密码不能为空！');
			}
		}
		if ($is_admin) {
			$this->assign('info', $seller);
		} else {
			$this->assign('info', $account);
		}
		$this->display();
	}
}