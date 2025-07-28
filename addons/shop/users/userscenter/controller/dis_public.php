<?php
namespace userscenter\controller;
use lib;
class dis_public extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$pub_lists = array();
		$condition = array();
		$miny = input('miny', 0, 'intval');
		$maxy = input('maxy', 0, 'intval');
		if (!empty($miny) && !empty($maxy)) {
			$condition['distributor_y >='] = $miny;
			$condition['distributor_y <='] = $maxy;
		} else if (!empty($miny)) {
			$condition['distributor_y >='] = $miny;
		} else if (!empty($maxy)) {
			$condition['distributor_y <='] = $maxy;
		}
		$minx = input('minx', 0, 'intval');
		$maxx = input('maxx', 0, 'intval');
		if (!empty($minx) && !empty($maxx)) {
			$condition['distributor_x >='] = $minx;
			$condition['distributor_x <='] = $maxx;
		} else if (!empty($minx)) {
			$condition['distributor_x >='] = $minx;
		} else if (!empty($maxx)) {
			$condition['distributor_x <='] = $maxx;
		}
		$status = input('status', 0, 'intval');
		if (!empty($status)) {
			$condition['status'] = $status - 1;
		}
		$membername = input('membername', '');
		if (!empty($membername)) {
			$search_memberids = array();
			$where_member['nickname'] = '%' . trim($membername) . '%';
			$result = model('member')->getList($where_member, 'uid');
			foreach($result['list'] as $r){
				$search_memberids[] = $r['uid'];
			}
			unset($result);
			unset($r);
			if (!empty($search_memberids)) {
				$condition['member_id'] = $search_memberids;
			}
		}
		$list = model('distributor_gp')->getList($condition, '*', 'ralate_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'miny' => $miny, 'maxy' => $maxy, 'minx' => $minx, 'maxx' => $maxx, 'status' => $status, 'membername' => $membername), users_url('dis_public/index')));
        
		
		//获得分销商及上级会员信息
		$member_ids = $member_info = array();
		foreach ($list['list'] as $account) {
			if(!in_array($account['member_id'], $member_ids)){
				$member_ids[] = $account['member_id'];
			}
			if (!in_array($account['parentid'], $member_ids) && $account['parentid'] > 0) {
				$member_ids[] = $account['parentid'];
			}
		}
		
		if (!empty($member_ids)) {
			$where['uid'] = $member_ids;
			$result = model('member')->getList($where, 'uid,nickname,headimg');
			foreach ($result['list'] as $r) {
				$member_info[$r['uid']] = $r;
			}
		}
		
		foreach($list['list'] as $value){
			$value['member_name'] = empty($member_info[$value['member_id']]['nickname']) ? '暂无' : $member_info[$value['member_id']]['nickname'];
			$value['member_avatar'] = !empty($member_info[$value['member_id']]['headimg']) ? $member_info[$value['member_id']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			$value['parent_name'] = empty($member_info[$value['parentid']]['nickname']) ? '暂无' : $member_info[$value['parentid']]['nickname'];
			$value['parent_avatar'] = !empty($member_info[$value['parentid']]['headimg']) ? $member_info[$value['parentid']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			$pub_lists[$value['ralate_id']] = $value;
		}
		$this->assign('list', $pub_lists);
		$this->display();
	}
	public function pubchildsOp() {
		$aid = input('aid', 0, 'intval');
		if (empty($aid)) {
			web_error('参数错误');
		}
		$my_info = model('distributor_gp')->getInfo(array('ralate_id' => $aid));
		if (empty($my_info)) {
			web_error('信息不存在');
		}
		$pub_lists = array();
		$condition = array();
		
		$condition['parentpath'] = '%,' . $my_info['member_id'] . ',%';
		$result = model('distributor_gp')->getInfo($condition, 'MAX(distributor_y) as y');
		if(is_array($result) && !empty($result['y'])){
			$this->assign('my_child_level', intval($result['y']) - $my_info['distributor_y']);
		}else{
			$this->assign('my_child_level', 0);
		}
		$level = input('level', 0);
		if (!empty($level)) {
			$condition['distributor_y'] = $my_info['distributor_y'] + intval($level);
			$minx = pow(config('public_times'), intval($level)) * ($my_info['distributor_x'] - 1) + 1;
			$maxx = pow(config('public_times'), intval($level)) * $my_info['distributor_x'];
			$condition['distributor_x >='] = $minx;
			$condition['distributor_x <='] = $maxx;
		}
		$status = input('status', 0);
		if (!empty($status)) {
			$condition['status'] = intval($status)-1;
		}
		$membername = input('membername', '');
		if (!empty($membername)) {
			$search_memberids = array();
			$where_member['nickname'] = '%' . trim($membername) . '%';
			$result = model('member')->getList($where_member, 'uid');
			foreach ($result['list'] as $r) {
				$search_memberids[] = $r['uid'];
			}
			unset($result);
			unset($r);
			if (!empty($search_memberids)) {
				$condition['member_id'] = $search_memberids;
			}
		}
		
		$list = model('distributor_gp')->getList($condition, '*', 'ralate_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'aid' => $aid, 'level' => $level, 'status' => $status, 'membername' => $membername), users_url('dis_public/pubchilds')));
        
		
		//获得分销商及上级会员信息
		$member_ids = $member_info = array();
		foreach ($list['list'] as $account) {
			if(!in_array($account['member_id'], $member_ids)){
				$member_ids[] = $account['member_id'];
			}
			if (!in_array($account['parentid'], $member_ids) && $account['parentid'] > 0) {
				$member_ids[] = $account['parentid'];
			}
		}
		
		if (!empty($member_ids)) {
			$where['uid'] = $member_ids;
			$result = model('member')->getList($where, 'uid,nickname,headimg');
			foreach ($result['list'] as $r) {
				$member_info[$r['uid']] = $r;
			}
		}
		
		foreach($list['list'] as $value){
			$value['member_name'] = empty($member_info[$value['member_id']]['nickname']) ? '暂无' : $member_info[$value['member_id']]['nickname'];
			$value['member_avatar'] = !empty($member_info[$value['member_id']]['headimg']) ? $member_info[$value['member_id']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			$value['parent_name'] = empty($member_info[$value['parentid']]['nickname']) ? '暂无' : $member_info[$value['parentid']]['nickname'];
			$value['parent_avatar'] = !empty($member_info[$value['parentid']]['headimg']) ? $member_info[$value['parentid']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			$pub_lists[$value['ralate_id']] = $value;
		}
		$this->assign('list', $pub_lists);
		$this->display();
	}
	public function award_listOp() {
		$record_lists = array();
		$condition = array();	
		$membername = input('membername', '');
		if (!empty($membername)) {
			$search_memberids = array();
			$where_member['nickname'] = '%' . trim($membername) . '%';
			$result = model('member')->getList($where_member, 'uid');
			foreach ($result['list'] as $r) {
				$search_memberids[] = $r['uid'];
			}
			unset($result);
			$search_memberids[] = 0;
			$condition['member_id'] = $search_memberids;
		}
		$type = input('type', '');
		if (!empty($type)) {
			$condition['detail_type'] = $type;
		}
		
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : null;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : null;
        if ($start_unixtime || $end_unixtime) {
            $condition['detail_addtime >='] = $start_unixtime;
			$condition['detail_addtime <='] = $end_unixtime;
        }
		
		$list = model('distributor_gp_detail')->getList($condition, '*', 'item_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'membername' => $membername, 'type' => $type, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('dis_public/award_list')));

		
		//获得分销商及上级会员信息
		$member_ids = $member_info = array();
		foreach ($list['list'] as $account) {
			if (!in_array($account['member_id'], $member_ids)) {
				$member_ids[] = $account['member_id'];
			}
		}
		
		if (!empty($member_ids)) {
			$where['uid'] = $member_ids;
			$result = model('member')->getList($where, 'uid,nickname,headimg');
			foreach ($result['list'] as $r) {
				$member_info[$r['uid']] = $r;
			}
		}
		
		foreach ($list['list'] as $value) {
			$value['member_name'] = empty($member_info[$value['member_id']]['nickname']) ? '暂无' : $member_info[$value['member_id']]['nickname'];
			$value['member_avatar'] = !empty($member_info[$value['member_id']]['headimg']) ? $member_info[$value['member_id']]['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			$record_lists[$value['item_id']] = $value;
		}
		
		$public_award_title = array(
			'level' => '见点奖',
			'invite' => '直推奖',
			'parent' => '懒人奖',
			'thinkful' => '感恩奖'
		);
        $this->assign('list', $record_lists);
		$this->assign('public_award_title', $public_award_title);
		$this->display();
	}
}