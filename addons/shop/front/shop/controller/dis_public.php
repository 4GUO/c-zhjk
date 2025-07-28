<?php
namespace shop\controller;
use lib;
class dis_public extends member {
	public function __construct() {
		parent::_initialize();
		if (IS_API) {
			if (!$this->member_info['is_distributor']) {
				output_error('您无推广权限，请购买商品', array('redirect' => '/pages/user/index'));
			}
		}
	}
	public function indexOp() {
		if (IS_API) {
		    //公排今日收入
			$today = lib\timer::today();
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$where['detail_addtime >='] = $today[0];
			$where['detail_addtime <='] = $today[1];
			$get_today_public_rewards = $this->get_public_rewards($where);
			//公排总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$get_all_public_rewards = $this->get_public_rewards($where);
			
			$array = array(
				'title' => '官方奖励',
				'get_today_rewards' => $get_today_public_rewards['rewards'],
				'get_all_rewards' => $get_all_public_rewards['rewards'],
			);
			output_data($array);
		}
	}
	public function positionOp() {
		if (IS_API) {
			//状态
			$_STATUS = array('已出局', '正常');
			$where['member_id'] = $this->member_info['uid'];
			$result = model('distributor_gp')->getList($where, '*', 'ralate_id asc', 20, input('page', 1, 'intval'));
			$record_list = $result['list'];
			$lists = array();
			foreach($record_list as $key => $value) {
				$lists[] = array(
					'y' => $value['distributor_y'],
					'x' => $value['distributor_x'],
					'state' => $_STATUS[$value['status']],
					'status' => $value['status'],
					'addtime' => date('m-d H:i', $value['addtime'])
				);
			}
			$return = array(
				'title' => '我的卡位',
				'list' => $lists,
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
			);
			output_data($return);
		}
	}
	public function teamOp() {
		if (IS_API) {
			$my_term_list = $level_list = array();
			$level = input('level', 1, 'intval');
			
			//获取我的坐标
			$my_position = model('distributor_gp')->getInfo(array('member_id' => $this->member_info['uid']), '*', 'ralate_id ASC');
			if (empty($my_position)) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'totalpage' => 0, 'total' => 0, 'cur_num' => 0, 'level_list' => array()));
			}
			
			//获取我的全部下级数量
			$str = '%,' . $this->member_info['uid'] . ',%';
			$condition['parentpath'] = $str;
			$total = model('distributor_gp')->where($condition)->total();
			if ($total == 0) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'totalpage' => 0, 'total' => 0, 'cur_num' => 0, 'level_list' => array()));
			}
			
			//获取最深层次的y坐标
			$result = model('distributor_gp')->getInfo($condition, 'MAX(distributor_y) as y');
			$max_level = $result['y'] - $my_position['distributor_y'];
			for ($i = 0; $i < $max_level; $i++) {
				$level_list[$i] = $i;
			}
			if ($max_level < $level) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'totalpage' => 0, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			}
			
			//计算x区间
			$min_x = pow(config('public_times'), $level) * ($my_position['distributor_x'] - 1) + 1;
			$max_x = pow(config('public_times'), $level) * $my_position['distributor_x'];
			$where['parentpath'] = $str;
			$where['distributor_x >='] = $min_x;
			$where['distributor_x <='] = $max_x;
			$where['distributor_y'] = $my_position['distributor_y'] + $level;
			$count = model('distributor_gp')->where($where)->total();
			if ($count == 0) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'totalpage' => 0, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			}
			$list = model('distributor_gp')->getList($where, '*', 'distributor_y asc,distributor_x asc,ralate_id asc', 20, input('get.page', 1, 'intval'));
			$my_term_list = $list['list'];
			$cur_num = $count;
			
			$memberids = array();
			foreach ($my_term_list as $k_t => $v_t) {
				$memberids[] = $v_t['member_id'];
			}
			
			//获取会员信息
			$members = array();
			$result = model('member')->getList(array('uid' => $memberids), 'nickname,uid,headimg');
			if (empty($result['list'])) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'totalpage' => 0, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			} else {
				foreach ($result['list'] as $kk => $vv) {
					$members[$vv['uid']] = $vv;
				}
			}
			
			$_STATUS = array('已出局', '正常');
			$lists = array();
			//组合数组
			foreach ($my_term_list as $mid => $meminfo) {
				$lists[$mid]['member_id'] = $meminfo['member_id'];
				$lists[$mid]['addtime'] = date('Y-m-d H:i:s', $meminfo['addtime']);
				$lists[$mid]['y'] = $level;
				$lists[$mid]['x'] = $meminfo['distributor_x'];
				$lists[$mid]['state'] = $_STATUS[$meminfo['status']];
				$lists[$mid]['status'] = $meminfo['status'];
				$lists[$mid]['nick_name']  = empty($members[$meminfo['member_id']]['nickname']) ? '暂无！' : $members[$meminfo['member_id']]['nickname'];
				$lists[$mid]['avatar']  = !empty($members[$meminfo['member_id']]['headimg']) ? $members[$meminfo['member_id']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			}
			output_data(array('term_list' => $lists, 'hasmore' => $list['hasmore'], 'totalpage' => $list['totalpage'], 'total' => $total, 'cur_num' => $cur_num, 'level_list' => $level_list));
		}
	}
	public function commissionOp() {
		if (IS_API) {
			$level = input('level', 'all');
			$lists = array();
			$TYPE = array(
				'all' => '全部',
				'level' => '级别奖',
				'parent' => '见点奖',
				'invite' => '直接推荐奖',
				'thankful' => '感恩奖'
			);
			
			//获得总红包
			$where['member_id'] = $this->member_info['uid'];
			if ($level != 'all') {
				$where['detail_type'] = $level;
			}
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if ($start_time) {
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if ($end_time) {
				$where['detail_addtime <='] = strtotime($end_time);
			}
			$result = model('distributor_gp_detail')->getInfo($where, 'SUM(detail_bonus) as money');
			$total = empty($result['money']) ? 0 : $result['money'];
			$record_list_tmp = model('distributor_gp_detail')->getList($where, '*', 'item_id desc', 20, input('page', 1, 'intval'));
			$record_list = $record_list_tmp['list'];
			foreach($record_list as $k => $value) {
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime']),
					'status' => $value['detail_status'],
					'id' => $value['item_id']
				);
			}
			$curinfo = empty($TYPE[$level]) ? '' : $TYPE[$level] . '(￥' . $total . ')';
			$return = array(
				'title' => '公排奖励',
				'list' => $lists,
				'totalpage' => $record_list_tmp['totalpage'],
				'hasmore' => $record_list_tmp['hasmore'],
				'curinfo' => $curinfo,
				'total_money' => $total,
			);
			output_data($return);
		}
	}
}