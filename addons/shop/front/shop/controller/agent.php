<?php
namespace shop\controller;
use lib;
class agent extends member {
	public function __construct() {
		parent::_initialize();
		if (IS_API) {
			if (!$this->member_info['is_distributor']) {
				//output_error('您无推广权限，请购买商品', array('redirect' => '/pages/user/index'));
			}
		}
	}
	public function indexOp() {
		if (IS_API) {
			$account_info = model('distribute_account')->getInfo(array('uid' => $this->member_info['uid']));
			$member_info = array_merge($account_info, $this->member_info);
			$level_info = model('vip_level')->getInfo(array('id' => $this->member_info['level_id']), 'id,level_name,level_sort');
			if (!$level_info) {
				$level_info['level_name'] = '不存在';
				$level_info['level_sort'] = 0;
			}
			//分销总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$get_all_rewards = $this->get_rewards($where);
			
			//公排总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$get_all_public_rewards = $this->get_public_rewards($where);
			
			//分销+公排可提现
			$f_p_all_rewards = $get_all_rewards['rewards'] + $get_all_public_rewards['rewards'];
			$member_info['f_p_all_rewards'] = priceFormat($f_p_all_rewards);
			$get_used_rewards = $this->get_user_use_money($this->member_info['uid']);
			$f_p_can_rewards = priceFormat($f_p_all_rewards - $get_used_rewards);
			$member_info['f_p_can_rewards'] = $f_p_can_rewards;
			//可用提货券数量
			$tihuoquan_num = model('shop_goods_tihuoquan')->where(array('uid' => $this->member_info['uid'], 'state' => array(0)))->total();
			$member_info['tihuoquan_num'] = $tihuoquan_num ?: 0;
			//累计提货
			$use_tihuoquan_num = model('shop_goods_tihuoquan')->where(array('uid' => $this->member_info['uid'], 'state' => array(1,2)))->total();
			$member_info['use_tihuoquan_num'] = $use_tihuoquan_num ?: 0;
			//累计移库
			$outcome_tihuoquan_num = model('shop_goods_tihuoquan_log')->where(array('lg_member_id' => $this->member_info['uid'], 'lg_type' => 'outcome'))->group('tihuoquan_id')->total();
			$member_info['outcome_tihuoquan_num'] = $outcome_tihuoquan_num ?: 0;
			$return = array(
				'title' => $this->config['name'],
				'member_info' => $member_info,
				'level_info' => $level_info,
			);
			output_data($return);
		}
	}
	public function agent_up_indexOp() {
		$levels = model('vip_level')->where(array('level_default' => 0))->order('level_sort DESC')->select();
		$level_info = model('vip_level')->where(array('id' => $this->member_info['level_id']))->find();
		//可用提货券数量
		$tihuoquan_num = model('shop_goods_tihuoquan')->where(array('uid' => $this->member_info['uid'], 'state' => array(0)))->total();
		foreach ($levels as $k => $v) {
			if ($level_info['level_sort'] >= $v['level_sort']) {
				$v['status'] = 0;
			} else {
				$v['status'] = 1;
			}
			$v['desc'] = '需要' . $v['tihuoquan_num'] . '个未激活提货券可升级，您的当前未激活提货券数量：' . $tihuoquan_num;
			$levels[$k] = $v;
		}
		$return = array(
			'levels' => $levels,
		);
		output_data($return);
	}
	public function apply_level_upgradeOp() {
		$level_id = input('level_id', 0, 'intval');
		$level_info = model('vip_level')->where(array('id' => $level_id))->find();
		//可用提货券数量
		$tihuoquan_num = model('shop_goods_tihuoquan')->where(array('uid' => $this->member_info['uid'], 'state' => array(0)))->total();
		if ($level_info['tihuoquan_num'] > $tihuoquan_num) {
			output_error('您的提货券数量不够，不能申请升级，请向上级购买提货券！');
		}
		//不能退级
		$buyer_level_info = model('vip_level')->where(array('id' => $this->member_info['level_id']))->find();
		if ($buyer_level_info['level_sort'] >= $level_info['level_sort']) {
		    output_error('非法操作，不能退级');
		}
		$check = model('vip_level_up_log')->where(array('uid' => $this->member_info['uid'], 'status' => 0))->find();
		if ($check) {
			output_error('您还有未审核的申请记录，请勿多次提交！');
		}
		if ($level_info['is_shenhe'] == 1) {
		    $status = 0;
		} else {
		    $status = 1;
		}
		$log_data = array(
			'uid' => $this->member_info['uid'],
			'level_id' => $level_id,
			'level_name' => $level_info['level_name'],
			'status' => $status,
			'add_time' => time(),
		);
		$flag = model('vip_level_up_log')->insert($log_data);
		if ($flag) {
		    if ($status) {
		        $upgrade_account['level_id'] = $level_id;
    			$upgrade_member['level_id'] = $level_id;
    			model('member')->where(array('uid' => $this->member_info['uid']))->update($upgrade_member);
    			model('distribute_account')->where(array('uid' => $this->member_info['uid']))->update($upgrade_account);
		    }
		}
		output_data('1');
	}
	public function apply_up_logOp() {
		if (IS_API) {
			$model_log = model('vip_level_up_log');
			$this->title = '升级申请记录';
			$where = array(
				'uid' => $this->member_info['uid'],
			);
			$detail_list_tmp = $model_log->getList($where, '*', 'id desc', 20, input('page', 1, 'intval'));
			$detail_list = $detail_list_tmp['list'];
			foreach($detail_list as $k => $r){
			    if ($r['status'] == 0) {
			        $status = '未审核';
			    }
			    if ($r['status'] == 1) {
			        $status = '已审核';
			    }
			    if ($r['status'] == 2) {
			        $status = '已驳回';
			    }
				$detail_list[$k]['status'] = $status;
				$detail_list[$k]['add_time'] = date('Y/m/d H:i:s', $r['add_time']);
			}
			$return = array(
				'title' => $this->title,
				'list' => $detail_list,
				'totalpage' => $detail_list_tmp['totalpage'],
				'hasmore' => $detail_list_tmp['hasmore'],
			);
			output_data($return);
		}
	}
}