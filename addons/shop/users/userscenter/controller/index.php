<?php
namespace userscenter\controller;
class index extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
	public function analysOp() {
		//今日
		$agent_order = model('shop_order');
		$today = \lib\timer::today();
		$begin_time = $today[0];
		$end_time = $today[1];
		//今日会员数量
		$today_member_count = model('member')->where(array('add_time >' => $begin_time))->total();
		$this->assign('today_member_count', $today_member_count);
		$where_order = array(
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
			'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS), 
			'lock_state' => 0,
		);
		//今日提货订单
		$today_tihuo_order_count = $agent_order->where(array('add_time >=' => $begin_time, 'tihuoquan_id >' => 0))->total();
		$this->assign('today_tihuo_order_count', $today_tihuo_order_count);
		$pay_order_money = $agent_order->field('SUM(order_amount) as total')->where($where_order)->find();
		$this->assign('pay_order_money', $pay_order_money['total'] ?: 0);
		//今日代理区营业额
		$where_order = array(
			'state' => array(0,1,2), 
			'add_time >=' => $begin_time,
			'add_time <=' => $end_time
		);
		$today_yeji = $total_yeji = 0;
		$shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where($where_order)->select();
		foreach ($shop_goods_tihuoquan as $v) {
			$today_yeji += $v['amount'];
		}
		$this->assign('today_yeji', $today_yeji);
		//代理区累计营业额
		$where_order = array(
			'state' => array(0,1,2), 
		);
		$shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where($where_order)->select();
		foreach ($shop_goods_tihuoquan as $v) {
			$total_yeji += $v['amount'];
		}
		$this->assign('total_yeji', $total_yeji);
		//已提货提货券数量
		$use_tihuoquan_num = model('shop_goods_tihuoquan')->where(array('state' => array(1,2)))->total();
		$this->assign('use_tihuoquan_num', $use_tihuoquan_num ?: 0);
		//未提货提货券数量
		$tihuoquan_num = model('shop_goods_tihuoquan')->where(array('state' => 0))->total();
		$this->assign('tihuoquan_num', $tihuoquan_num ?: 0);
		$total_fenhong_quan = model('member')->field('SUM(total_fenhong_quan) as all_fenhong_quan')->where(array('total_fenhong_quan >' => 0))->find();
		
		//剩余可用分红券
		$fenhong_quan = model('member')->field('SUM(fenhong_quan) as can_fenhong_quan')->where(array('fenhong_quan >' => 0))->find();
		$this->assign('fenhong_quan', $fenhong_quan['can_fenhong_quan'] ?: 0);
		//已消耗分红券数量
		$this->assign('used_fenhong_quan', ($total_fenhong_quan['all_fenhong_quan'] ?: 0) - ($fenhong_quan['can_fenhong_quan'] ?: 0));
		$this->display();
	}
	public function statisticsOp() {
		if(IS_API){
			$agent_order = model('shop_order');
			$ORDER_STATE_NEW = $agent_order->where(array('order_state' => ORDER_STATE_NEW, 'lock_state' => 0))->total();
			$ORDER_STATE_PAY = $agent_order->where(array('order_state' => ORDER_STATE_PAY, 'lock_state' => 0))->total();
			$ORDER_STATE_SEND = $agent_order->where(array('order_state' => ORDER_STATE_SEND, 'lock_state' => 0))->total();
			$ORDER_STATE_SUCCESS = $agent_order->where(array('order_state' => ORDER_STATE_SUCCESS, 'lock_state' => 0))->total();
			$state_refund = model('shop_refund_return')->where(array('refund_state' => 1))->total();
			$output_data = array(
				'state_new' => $ORDER_STATE_NEW, 
				'state_pay' => $ORDER_STATE_PAY, 
				'state_send' => $ORDER_STATE_SEND, 
				'state_success' => $ORDER_STATE_SUCCESS, 
				'state_refund' => $state_refund,
			);
			//未审核评论
			$wait_shen_order_comment_count = model('shop_evaluate_goods')->where(array('geval_state' => 0))->total();
			$output_data['wait_shen_order_comment_count'] = $wait_shen_order_comment_count;
			//下架商品
			$wait_shen_goods_count = model('shop_goods_common')->where(array('goods_state' => 0))->total();
			$output_data['wait_shen_goods_count'] = $wait_shen_goods_count;
			//商家提现
			$store_tixian_count = model('store_tixian_order')->where(array('state' => 0))->total();
			$output_data['store_tixian_count'] = $store_tixian_count;
			output_data($output_data);
		}
	}
	
	public function withdraw_recordOp() {
		if (IS_API) {
			$data = array(
				'w_all' => 0,
				'w_new' => 0,
				'w_deal' => 0,
				'w_reject' => 0
			);
			$result = model('withdraw_record')->getList(array(), 'record_status');
			$data['w_all'] = count($result['list']);
			foreach ($result['list'] as $k => $v) {
				if($v['record_status'] == 0) {
					$data['w_new'] = $data['w_new'] + 1;
				} elseif($v['record_status'] == 1) {
					$data['w_deal'] = $data['w_deal'] + 1;
				} elseif($v['record_status'] == 2) {
					$data['w_reject'] = $data['w_reject'] + 1;
				}
			}
			output_data($data);
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
				'add_time >=' => $begin_time,
				'add_time <=' => $end_time,
				'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),
				'lock_state' => 0,
			);
			$where_refund = array(
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
	//7天会员增长情况
	public function load_echat_member_incrOp() {
		$model = model('member');
        $begin_time = strtotime(date('Y-m-d') . ' 00:00:00') - 7 * 86400;
        $end_time = time();
        $date_arr = array();
        $member_count_arr = array();
        for ($i = 7; $i >= 1; $i--) {
            $begin_time = strtotime(date('Y-m-d') . ' 00:00:00') - $i * 86400;
            $end_time = $begin_time + 86400;
            $member_count = $model->where(array('add_time >=' => $begin_time, 'add_time <' => $end_time))->total();
            $date_arr[] = date('m-d', $begin_time);
            $member_count_arr[] = $member_count;
        }
        echo json_encode(array(
            'code' => 0,
            'date_arr' => $date_arr,
            'member_count' => $member_count_arr
        ));
        die();
    }
	//商家月销售排行
	public function load_echat_month_store_salesOp() {
		$model_order = model('shop_order');
        $type = input('type', 1);
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
			'add_time >=' => $begin_time,
			'add_time <=' => $end_time,
			'order_state' => array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS),
			'lock_state' => 0,
		);
        $result = $model_order->getList($where, 'SUM(order_amount) as total,store_id,order_id,order_sn', 'total desc', null, null);
        $list = array();
		foreach ($result['list'] as $k => $v) {
			$list[$v['store_id']] = $v;
		}
        $total = 0;
		$store_ids = array();
		foreach ($list as $k => $v) {
			$store_ids[] = $v['store_id'];
		}
		$hd_list = array();
		if ($store_ids) {
			$result = model('seller')->field('id,name')->where(array('id' => $store_ids))->select();
			foreach ($result as $k => $v) {
				$hd_list[$v['id']] = $v;
			}
			unset($result, $store_ids);
		}
        foreach ($list as $key => $val) {
            $hd_info = isset($hd_list[$val['store_id']]) ? $hd_list[$val['store_id']] : array();
			if (!$hd_info) {
				continue;
			}
            $val['storename'] = $hd_info['name'];
            $val['total'] = sprintf('%.2f', $val['total']);
            $total += $val['total'];
            $list[$key] = $val;
        }
		unset($hd_list);
        $total = sprintf('%.2f', $total);
        echo json_encode(array(
            'code' => 0,
            'list' => $list,
            'total' => $total,
            'month' => $date_month
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
	private function getMonthNum( $date1, $date2, $tags = '-' ) {
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
			$users = model('users')->getInfo(array('uniacid' => $this->uniacid));
		} else {
			$account = model('account')->getInfo(array('account_id' => input('session.account_id', 0, 'intval')));
		}
		
		if (chksubmit()) {
			$password = input('password', '', 'trim');
			$repassword = input('repassword', '', 'trim');
			if ($password != $repassword) {
				output_error('密码不一致');
			}
			if ($password) {
				if ($is_admin) {
					$password = md5($password . $users['salt']);
					$data = array(
						'password' => $password,
					);
					model('users')->edit(array('uniacid' => $this->uniacid), $data);
				} else {
					$password = md5($password . $account['salt']);
					$data = array(
						'password' => $password,
					);
					model('account')->edit(array('account_id' => input('session.account_id', 0, 'intval')), $data);
				}
				$this->log('修改密码成功');
				output_data(array('msg' => '操作成功', 'url' => users_url('index/password')));
			} else {
				output_error('密码不能为空！');
			}
		}
		if ($is_admin) {
			$this->assign('info', $users);
		} else {
			$this->assign('info', $account);
		}
		$this->display();
	}
	
	public function untihuoquan_listOp() {
		$model = model('shop_goods_tihuoquan');
		$where = array();
		$where['state'] = 0; // 未提货状态
		
		// 搜索条件
		$keyword = input('get.keyword', '');
		if ($keyword) {
			$search_type = input('get.search_type', 'nickname');
			if ($search_type == 'nickname') {
				$member_uids = model('member')->where(array('uniacid' => $this->uniacid, 'nickname' => '%' . trim($keyword) . '%'))->field('uid')->select();
			} elseif ($search_type == 'mobile') {
				$member_uids = model('member')->where(array('uniacid' => $this->uniacid, 'mobile' => '%' . trim($keyword) . '%'))->field('uid')->select();
			} elseif ($search_type == 'truename') {
				$member_uids = model('member')->where(array('uniacid' => $this->uniacid, 'truename' => '%' . trim($keyword) . '%'))->field('uid')->select();
			}
			
			if (!empty($member_uids)) {
				$uids = array_column($member_uids, 'uid');
				$where['uid'] = $uids;
			} else {
				$where['uid'] = 0; // 没有匹配的用户
			}
		}
		
		// 时间范围搜索
		$query_start_date = input('get.query_start_date', '');
		$query_end_date = input('get.query_end_date', '');
		$if_start_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_start_date);
		$if_end_date = preg_match('/^20\d{2}-\d{2}-\d{2}$/', $query_end_date);
		$start_unixtime = $if_start_date ? strtotime($query_start_date . ' 00:00:00') : 0;
		$end_unixtime = $if_end_date ? strtotime($query_end_date . ' 23:59:59') : 0;
		
		if ($start_unixtime > 0) {
			$where['add_time >='] = $start_unixtime;
		}
		if ($end_unixtime > 0) {
			$where['add_time <='] = $end_unixtime;
		}
		
		$list = $model->getList($where, '*', 'id DESC', 20, input('get.page', 1, 'intval'));
		$this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'keyword' => $keyword, 'search_type' => input('get.search_type', 'nickname'), 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('index/untihuoquan_list')));
		$this->assign('list', $list['list']);
		
		// 获取会员信息
		$member_list = array();
		$uids = array();
		if (!empty($list['list'])) {
			foreach ($list['list'] as $r) {
				if (!in_array($r['uid'], $uids)) {
					$uids[] = $r['uid'];
				}
			}
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile,truename,level_id');
			if (!empty($result['list']) && is_array($result['list'])) {
				foreach ($result['list'] as $rr) {
					$member_list[$rr['uid']] = array(
						'nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'],
						'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png',
						'mobile' => $rr['mobile'],
						'truename' => $rr['truename'],
						'level_id' => $rr['level_id']
					);
				}
			}
			unset($result);
		}
		$this->assign('member_list', $member_list);
		
		// 获取级别信息
		$level_list = logic('yewu')->get_level_list('*', 'level_sort DESC');
		$this->assign('level_list', $level_list);
		
		$this->display();
	}
}