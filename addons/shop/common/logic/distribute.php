<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class distribute
{
	//$type 为1是直推  2为间接
    public function _get_next_distribute($agent_id, $gc_id = 0, $type, $status = 1){
		if($type == 1){
			$sql = 'SELECT m.wechat,m.truename,m.mobile,a.agent_no,a.gc_id,a.id,a.dis_path,a.payimg,a.agent_starttime,a.agent_endtime,a.add_time,a.jujue_text,a.level_id,a.uid FROM ims_fxy_agent AS a left join ims_fxy_member as m on a.uid=m.uid WHERE a.inviter_id=' . $agent_id . ' AND a.agent_status=' . $status . ' AND a.gc_id=' . $gc_id . ' ORDER BY a.level_id ASC';
		}else{
			$sql = 'SELECT m.wechat,m.truename,m.mobile,a.agent_no,a.gc_id,a.id,a.dis_path,a.payimg,a.agent_starttime,a.agent_endtime,a.add_time,a.jujue_text,a.level_id,a.uid FROM ims_fxy_agent AS a left join ims_fxy_member as m on a.uid=m.uid WHERE a.dis_path LIKE \'%,' . $agent_id . ',%\' AND a.inviter_id!=' . $agent_id . '  AND a.agent_status=' . $status . ' AND a.gc_id=' . $gc_id . ' ORDER BY a.level_id ASC';
		}
		$agent_list = model()->query($sql, 'select');
		$jianjie_list = array();
		if($type == 2){
			if($agent_list){
				foreach($agent_list as $key => $val){
					$dis_path_arr = explode(',', trim($val['dis_path'], ','));
					$pos = array_keys($dis_path_arr, $agent_id);
					$ok_count = array_slice($dis_path_arr, ($pos[0] + 1));
					if(count($ok_count) == 1){
						$jianjie_list[$key] = $val;
					}
				}
			}
			$agent_list = $jianjie_list;
		}
		return $agent_list;
	}
	public function deal_pool($detail_data) {
		$award_arr = $award_arr_data = array();
		foreach($detail_data as $k => $v) {
			if (!isset($award_arr[$v['uid']])) {
				$award_arr[$v['uid']] = $v['detail_bonus'];
			} else {
				$award_arr[$v['uid']] += $v['detail_bonus'];
			}
		}
		foreach($award_arr as $uid => $detail_bonus) {
			$award_arr_data[] = array(
				'uid' => $uid,
				'dis_total_award' => '(dis_total_award+' . $detail_bonus . ')',
			);
		}
		if ($award_arr_data) {
			$sql = batchUpdate('ims_fxy_member', $award_arr_data, 'uid', array(), true);
			model()->query($sql, 'update');
		}
		return true;
	}
	public function deal_public_pool($detail_data) {
		$award_arr = $award_arr_data = array();
		foreach($detail_data as $k => $v) {
			if (!isset($award_arr[$v['member_id']])) {
				$award_arr[$v['member_id']] = $v['detail_bonus'];
			} else {
				$award_arr[$v['member_id']] += $v['detail_bonus'];
			}
		}
		foreach($award_arr as $member_id => $detail_bonus) {
			$award_arr_data[] = array(
				'uid' => $member_id,
				'dis_total_award' => '(dis_total_award+' . $detail_bonus . ')',
			);
		}
		if ($award_arr_data) {
			$sql = batchUpdate('ims_fxy_member', $award_arr_data, 'uid', array(), true);
			model()->query($sql, 'update');
		}
		return true;
	}
}