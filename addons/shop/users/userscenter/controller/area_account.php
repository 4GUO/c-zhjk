<?php
namespace userscenter\controller;
use lib;
class area_account extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_account = model('area_account');
		$model_member = model('member');
		$where = array();
        $where['uniacid'] = $this->uniacid;
		
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			$where2[$search_type] = '%' . trim($keyword) . '%';
			$result = $model_member->getList($where2);
			$uids = array();
			foreach ($result['list'] as $v) {
				$uids[] = $v['uid'];
			}
			$where['uid'] = $uids;
        }
		$level_id = input('get.level_id', 0, 'intval');
		if($level_id){
			$where['level_id'] = $level_id;
		}
		$status = input('get.status', 0, 'intval');
		if($status){
			$where['status'] = $status - 1;
		}
        $list = $model_account->getList($where, '*', 'id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'level_id' => $level_id, 'status' => $status), users_url('area_account/index')));
        $this->assign('list', $list['list']);
		
		$mapping_fans = $account_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				$uids[] = $r['uid'];
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
		$result = model('area_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
		foreach($result['list'] as $level_info){
			$level_list[$level_info['id']] = $level_info['level_name'];
		}
		$this->assign('level_list', $level_list);
		$this->display();
	}
	public function publishOp(){
		$model_account = model('area_account');
		$model_member = model('member');
		if (chksubmit()) {
			$id = input('post.id', 0, 'intval');
			$account_array = array();
			$account_array['uniacid'] = $this->uniacid;
			$account_array['uid'] = input('post.uid', 0, 'intval');
			$region = input('post.region', '', 'trim');
			if (!$region) {
				output_error('请选择地区');
			}
			$account_array['area_info'] = $region;
			$region_arr = explode(' ', $region);
			
			$check_where['province_name'] = $account_array['province_name'] = isset($region_arr[0]) ? $region_arr[0] : '';
			$check_where['city_name'] = $account_array['city_name'] = isset($region_arr[1]) ? $region_arr[1] : '';
			$check_where['county_name'] = $account_array['county_name'] = isset($region_arr[2]) ? $region_arr[2] : '';
			$check_where['level_id'] = $account_array['level_id'] = input('post.level_id', 0, 'intval');
			$check_where['status'] = $account_array['status'] = input('post.status', 0, 'intval');
			$check = $model_account->where($check_where)->total();
			if ($check) {
			    output_error('该地区已经有相同级别的代理了');
			}
			if ($id) {
				$state = $model_account->where(array('id' => $id))->update($account_array); 
			} else {
				$account_array['add_time'] = time();
				$state = $model_account->add($account_array); 
			}
            output_data(array('msg' => '操作成功', 'url' => users_url('area_account/index')));
		} else {
			$id = input('id', 0, 'intval');
			if ($id) {
				$account_info = $model_account->where(array('id' => $id, 'uniacid' => $this->uniacid))->find();
				$member_info = $model_member->where(array('uid' => $account_info['uid'], 'uniacid' => $this->uniacid))->find();
				$member_info['nickname'] = isset($member_info['nickname']) ? $member_info['nickname'] : '';
				$member_info['headimg'] = isset($member_info['headimg']) ? $member_info['headimg'] : STATIC_URL . '/shop/img/default_user.png';
				$this->assign('member_info', $member_info);
				$this->assign('account_info', $account_info);
			}
			$level_list = array();
			$result = model('area_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
			foreach($result['list'] as $level_info){
				$level_list[$level_info['id']] = $level_info['level_name'];
			}
			$this->assign('level_list', $level_list);
			$this->display();
		}
	}
	public function delOp() {
		$model_account = model('area_account');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$state = $model_account->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('area_account/index')));
        } else {
			output_error('删除失败！');
        }
    }
}