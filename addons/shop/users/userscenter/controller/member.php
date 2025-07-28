<?php
namespace userscenter\controller;
use lib;
class member extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_member = model('member');
		$where = array();
        $where['uniacid'] = $this->uniacid;
		
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			if($search_type == 't_nickname' || $search_type == 't_truename' || $search_type == 't_mobile'){
				$search_uids = array();
				$search_uids[] = -1;
				$key_s = str_replace('t_', '', $search_type);
				$result = $model_member->getList(array('uniacid' => $this->uniacid, $key_s => '%' . trim($keyword) . '%'), 'uid');
				foreach($result['list'] as $r){
					$search_uids[] = $r['uid'];
				}
				$where['inviter_id'] = $search_uids;
			} else {
				$where[$search_type] = '%' . trim($keyword) . '%';
			}            
        }
		$state = input('get.state', 0, 'intval');
		if($state){
			$where['status'] = $state - 1;
		}
		$level_id = input('get.level_id', 0, 'intval');
		if($level_id){
			$where['level_id'] = $level_id;
		}
        $list = $model_member->getList($where, '*', 'add_time DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'state' => $state, 'level_id' => $level_id), users_url('member/index')));
        $this->assign('list', $list['list']);
		
		$mapping_fans = $account_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				$uids[] = $r['uid'];
				if($r['inviter_id'] > 0 && !in_array($r['inviter_id'], $uids)){
					$uids[] = $r['inviter_id'];
				}
			}
			
			$list = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
			if(!empty($list['list']) && is_array($list['list'])){
				foreach($list['list'] as $rr){
					$mapping_fans[$rr['uid']] = array('nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png');
				}
			}
			$result = model('distribute_account')->getList(array('uid' => $uids));
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$account_list[$rr['uid']] = $rr;
				}
			}
		}
		$this->assign('mapping_fans', $mapping_fans);
		$this->assign('account_list', $account_list);
		$level_list = array();
		$result = model('vip_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
		foreach($result['list'] as $level_info){
			$level_list[$level_info['id']] = $level_info['level_name'];
		}
		$this->assign('level_list', $level_list);
		$this->display();
	}
	public function editOp() {
		$model_member = model('member');
		if (chksubmit()) {
			$uid = input('post.uid', 0, 'intval');
			$member_array = $account_array = array();
			$member_array['truename'] = input('post.truename', '');
			$member_array['mobile'] = input('post.mobile', '');
			if (!empty($member_array['mobile'])) {
				$result = $model_member->where(array('mobile' => input('request.mobile', ''), 'uniacid' => $this->uniacid))->find();
				if(!empty($result['uid']) && $result['uid'] != input('post.uid', 0, 'intval')){
					output_error('手机号已存在');
				}
				unset($result);
			}
			$password = input('post.password', '', 'trim');
			if (!empty($password)) {
				$member_array['password'] = f_hash($password);
			}
			$member_array['is_admin'] = input('post.is_admin', 0, 'intval');
			if (!empty($member_array['is_admin'])) {
				$result = $model_member->where(array('uid' => $uid, 'uniacid' => $this->uniacid))->find();
				if (empty($result['weixin_unionid'])) {
					output_error('会员微信openid缺失，不能成为管理员');
				}
				unset($result);
			}
			//更新上级start
			$inviter_id = input('request.inviter_id', 0, 'intval');
			if ($inviter_id) {
			    if ($inviter_id == $uid) {
			        output_error('推荐人不能是自己');
			    }
				$account_info = model('distribute_account')->where(array('uid' => $uid, 'uniacid' => $this->uniacid))->find();
				$inviter_info = model('distribute_account')->where(array('uid' => $inviter_id, 'uniacid' => $this->uniacid))->find();
				if(empty($inviter_info['uid'])){
					output_error('选择的上级不存在');
				}
				if ($account_info['inviter_id'] != $inviter_id) {
				    //一条线下级逆袭成上级
				    if (in_array($uid, explode(',', trim($inviter_info['dis_path'], ',')))) {
				        model('distribute_account')->where(array('uid' => $inviter_id))->update(array('dis_path' => $account_info['dis_path'], 'inviter_id' => $account_info['inviter_id']));
				        
				        model('distribute_account')->where(array('inviter_id' => $inviter_id))->update(array('inviter_id' => $uid));

				        $dis_path = $account_info['dis_path'] ? $account_info['dis_path'] . $inviter_id . ',' : ',' . $inviter_id . ',';
				        model('distribute_account')->where(array('uid' => $uid))->update(array('dis_path' => $dis_path, 'inviter_id' => $inviter_id));
				        $old_path = $inviter_info['dis_path'] ? $inviter_info['dis_path'] . $inviter_id . ',': ',' . $inviter_id . ',';
					    $new_path = $dis_path ? $dis_path . $uid . ',' : ',' . $uid . ',';
					    model('distribute_account')->where(array('dis_path' => '%,' . $inviter_id . ',%'))->update('`dis_path` = replace(`dis_path`,\'' . $old_path . '\',\'' . $new_path . '\')');
					    model('distribute_account')->edit(array('uid' => $inviter_id), 'team_num=team_num+1');
					    model('distribute_account')->edit(array('uid' => $uid), 'team_num=team_num-1');
					    if ($inviter_info['inviter_num'] == 0) {
					        model('distribute_account')->edit(array('uid' => $inviter_id), 'inviter_num=inviter_num+1');
					        if ($inviter_info['inviter_id'] == $uid) {
					            model('distribute_account')->edit(array('uid' => $uid), 'inviter_num=inviter_num-1');
					        }
					    }
				    } else {
				        //换线
				        $dis_path = $inviter_info['dis_path'] ? $inviter_info['dis_path'] . $inviter_id . ',' : ',' . $inviter_id . ',';
				        $old_path = $account_info['dis_path'] ? $account_info['dis_path'] . $uid . ',': ',' . $uid . ',';
					    $new_path = $dis_path ? $dis_path . $uid . ',' : ',' . $uid . ',';
					    model('distribute_account')->where(array('uid' => $uid))->update(array('dis_path' => $dis_path, 'inviter_id' => $inviter_id));
					    model('distribute_account')->where(array('dis_path' => '%,' . $uid . ',%'))->update('`dis_path` = replace(`dis_path`,\'' . $old_path . '\',\'' . $new_path . '\')');
					    model('distribute_account')->edit(array('uid' => explode(',', trim($account_info['dis_path'], ','))), 'team_num=team_num-1');
					    model('distribute_account')->edit(array('uid' => $account_info['inviter_id']), 'inviter_num=inviter_num-1');
					    model('distribute_account')->edit(array('uid' => $inviter_id), 'inviter_num=inviter_num+1');
					    model('distribute_account')->edit(array('uid' => explode(',', trim($dis_path, ','))), 'team_num=team_num+1');
				    }
					$member_array['inviter_id'] = $inviter_id;
				}
			}
			//更新上级end
			$account_array['level_id'] = $member_array['level_id'] = input('post.level_id', 0, 'intval');
			$default_level_info = model('vip_level')->getInfo(array('uniacid' => $this->uniacid, 'level_default' => 1), '*', 'level_sort ASC');
			if ($default_level_info && $default_level_info['id'] == $member_array['level_id']) {
				$member_array['is_distributor'] = 1;
			} else {
				$member_array['is_distributor'] = 1;
			}
			$member_array['is_author'] = input('post.is_author', 0, 'intval');
			$member_array['status'] = input('post.status', 0, 'intval');
			$member_array['can_baodan'] = input('post.can_baodan', 0, 'intval');
            $state = $model_member->where(array('uid' => input('post.uid', 0, 'intval')))->update($member_array);  
			$state = model('distribute_account')->where(array('uid' => input('post.uid', 0, 'intval')))->update($account_array); 
            output_data(array('msg' => '编辑成功', 'url' => users_url('member/index')));
		} else {
			$uid = input('get.uid', 0, 'intval');
			$member_info = $model_member->where(array('uid' => $uid, 'uniacid' => $this->uniacid))->find();
			$fans_info = model('fans')->where(array('uid' => $uid, 'uniacid' => $this->uniacid))->find();
			
			if(empty($member_info['uid'])){
				web_error('用户不存在', users_url('member/index'));
			}
			
			$arr = $fans_info ? fxy_unserialize(base64_decode($fans_info['tag'])) : array();
			$member_info['nickname'] = isset($arr['nickname']) ? $arr['nickname'] : '';
			$member_info['headimg'] = isset($arr['headimgurl']) ? $arr['headimgurl'] : STATIC_URL . '/shop/img/default_user.png';
			
			$this->assign('member_info', $member_info);
			
			$level_list = array();
			$result = model('vip_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
			foreach($result['list'] as $level_info){
				$level_list[$level_info['id']] = $level_info['level_name'];
			}
			$this->assign('level_list', $level_list);
			
			$inviter_info = $model_member->where(array('uid' => $member_info['inviter_id'], 'uniacid' => $this->uniacid))->find();
			if ($inviter_info) {
			    $inviter_info['nickname'] = !empty($inviter_info['nickname']) ? $inviter_info['nickname'] : $inviter_info['mobile'];
			    $inviter_info['headimg'] = !empty($inviter_info['headimg']) ? $inviter_info['headimg'] : STATIC_URL . '/shop/img/default_user.png';
			}
			$this->assign('inviter_info', $inviter_info);
			$this->display();
		}
	}
	public function selectViewOp() {
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
	public function selectUserOp() {
		if(IS_API){
			$model_member = model('member');
			$where['uniacid'] = $this->uniacid;
			$mobile = input('mobile', '');
			if ($mobile) {
				$where['mobile'] = '%' . $mobile . '%';
			}
			$ext_uid = input('uid', 0, 'intval');
			$result = $model_member->getList($where, '*', 'uid DESC', 18, input('page', 1, 'intval'));
			$list = array();
			foreach($result['list'] as $k => $v) {
				if ($v['uid'] == $ext_uid) {
					continue;
				}
				$v['headimg'] = !empty($v['headimg']) ? $v['headimg'] : STATIC_URL . '/shop/img/default_user.png';
				$list[] = $v;
			}
			output_data(array('list' => $list, 'totalpage' => $result['totalpage'], 'page_html' => page($result['totalpage'], array('page' => input('get.page', 1, 'intval')), users_url('member/selectUser'), true)));
		}
	}
}