<?php
namespace userscenter\controller;
use lib;
class tihuoquan extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function sendOp() {
		if (chksubmit()) {
			$uids = input('uids/a', array());
			$uids = $uids ? array_values($uids) : array();
			if (!$uids) {
				output_error('请选择会员');
			}
			$member_list = model('member')->getList(array('uid' => $uids));
			$num = input('num', 0, 'intval');
			if ($num <= 0) {
				output_error('请填写正确的数量');
			}
			$level_list = logic('yewu')->get_level_list('*', 'level_sort DESC');
			$lianchuang_level = model('vip_level')->where(array('level_default' => 0))->order('level_sort DESC')->find();
			//添加提货券券信息
			$tihuoquan_data = array();
			$log_data = array();
			foreach ($member_list['list'] as $member_info) {
			    if ($member_info['level_id'] != $lianchuang_level['id']) {
			        continue;
			    }
				$level = $level_list[$member_info['level_id']];
				for($i = 0; $i < $num; $i++) {
					$tihuoquan_data[] = array(
						'uid' => $member_info['uid'],
						'state' => 0,
						'amount' => $level['tihuoquan_price'],//计入业绩
						'add_time' => time(),
						'edit_time' => time(),
					);
				}
				$log_data[] = array(
    				'uniacid' => $this->uniacid,
    				'lg_member_id' => $member_info['uid'],
    				'lg_member_name' => $member_info['nickname'],
    				'lg_type' => 'sys_add',
    				'tihuoquan_id' => 0,
    				'lg_add_time' => time(),
    				'lg_desc' => '系统发送' . $num . '个',
    				'relation_uid' => 0,
    			);
    			
			}
			if ($log_data) {
			    model('shop_goods_tihuoquan_log')->insertAll($log_data);
			}
			if (!$tihuoquan_data) {
			    output_error('请选择有效会员');
			}
			if ($tihuoquan_data) {
				$flag = model('shop_goods_tihuoquan')->insertAll($tihuoquan_data);
				if ($flag) {
					foreach($uids as $uid) {
						logic('yewu')->tongji_reward($uid, $num);
					}
				}
			}
			output_data(array('msg' => '提货券发送成功', 'url' => _url('tihuoquan/send')));
		} else {
			$this->display();
		}
	}
	public function send_logOp() {
	    $model = model('shop_goods_tihuoquan_log');
		$where = array();
		$where['lg_type'] = 'sys_add';
        $query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['lg_add_time >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['lg_add_time <='] = $end_unixtime;
        }
        $list = $model->getList($where, '*', 'lg_id DESC', 10, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('tihuoquan/send_log')));
		$this->assign('list', $list['list']);
		
		$member_list = array();
		$uids = array();
		if(!empty($list['list'])){
			foreach($list['list'] as $r){
				if(!in_array($r['lg_member_id'], $uids)){
					$uids[] = $r['lg_member_id'];
				}
			}
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$member_list[$rr['uid']] = array('nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png', 'mobile' => $rr['mobile']);
				}
			}
			unset($result);
		}
		$this->assign('member_list', $member_list);
		$this->display();
	}
}