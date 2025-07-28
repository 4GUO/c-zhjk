<?php
namespace userscenter\controller;
class vip_level extends control {
	public function __construct() {
		parent::_initialize();
	}

	public function indexOp(){
		$list_temp = model('vip_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
		$this->assign('list', $list_temp['list']);
		$this->display();
	}

	public function addOp() {
		$model_level = model('vip_level');
		if(chksubmit()){
			$level_array = array();
			$level_array['uniacid'] = $this->uniacid;
            $level_array['level_name'] = input('level_name', '');
			$level_array['level_sort'] = input('level_sort', 0, 'intval');
			if (empty($level_array['level_sort'])) {
				output_error('级别序号不能为空，且为数字');
			}
			$level_array['level_desc'] = input('level_desc', '');
            $state = $model_level->insert($level_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('vip_level/index')));
            } else {
				output_error('添加失败！');
            }
		} else {
			$this->display();
		}
	}

	public function editOp(){
		$model_level = model('vip_level');
		if (chksubmit()) {
			$level_array = array();
            $level_array['level_name'] = input('level_name', '');
			$level_array['level_sort'] = input('level_sort', 0, 'intval');
			if (empty($level_array['level_sort'])) {
				output_error('级别序号不能为空，且为数字');
			}
			$level_array['level_desc'] = input('level_desc', '');
			$level_array['tihuoquan_num'] = input('tihuoquan_num', 0, 'intval');
			$level_array['tihuoquan_price'] = input('tihuoquan_price', 0, 'floatval');
			$level_array['jiaquan_fenhong_yue_yeji'] = input('jiaquan_fenhong_yue_yeji', 0, 'floatval');
			$level_array['jiaquan_fenhong_total'] = input('jiaquan_fenhong_total', 0, 'floatval');
			$level_array['jiaquan_fenhong_bili'] = input('jiaquan_fenhong_bili', 0, 'floatval');
            $level_array['tongji_bonus'] = input('tongji_bonus', 0, 'floatval');
            $level_array['tongji_bonus2'] = input('tongji_bonus2', 0, 'floatval');
            $level_array['is_shenhe'] = input('is_shenhe', 0, 'intval');
            $level_array['need_buy_experience_goods'] = input('need_buy_experience_goods', 0, 'intval');
            $level_array['fgjdjl'] = input('fgjdjl', 0, 'intval');
			$state = $model_level->where(array('id' => input('id', 0, 'intval')))->update($level_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('vip_level/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$level_id = input('id', 0, 'intval');
			$level_info = $model_level->where(array('id' => $level_id))->find();
			$this->assign('level_info', $level_info);
			//获取下级级别
			$list_temp = model('vip_level')->getList(array('level_sort <' => $level_info['level_sort']), '*', 'level_sort DESC');
			$this->assign('next_levels', $list_temp['list']);
			$this->display();
		}
	}

	public function delOp(){
		$model_level = model('vip_level');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['level_id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$agent_info = model('member')->where($where)->find();
		if(!empty($agent_info['id'])){
			output_error('该级别下有代理，不能删除');
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$where['level_default'] = 0;
		$state = $model_level->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('vip_level/index')));
        } else {
			output_error('删除失败！');
        }
    }
	//升级攻略
	public function configOp() {
		if (chksubmit()) {
			$data = array(
				'level_rule' => input('level_rule', ''),
			);
            model('renwu_config')->save($data);
			output_data(array('msg' => '操作成功', 'url' => users_url('vip_level/config')));
		}
		$data =  model('renwu_config')->get_all_config();
        $this->assign('config', $data);
		$this->display();
	}
	public function up_logOp() {
	    $model_log = model('vip_level_up_log');
		$this->title = '升级申请记录';
		$where = array(
			'status' => 0,
		);
		$detail_list_tmp = $model_log->getList($where, '*', 'id desc', 20, input('page', 1, 'intval'));
		$this->assign('page', page($detail_list_tmp['totalpage'], array('page' => input('get.page', 1, 'intval')), users_url('vip_level/up_log')));
		$this->assign('list', $detail_list_tmp['list']);
		$uids = array();
		foreach($detail_list_tmp['list'] as $v) {
		    $uids[] = $v['uid'];
		}
		$member_list = array();
		$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,truename,headimg,mobile');
		if(!empty($result['list']) && is_array($result['list'])){
			foreach($result['list'] as $rr){
				$member_list[$rr['uid']] = $rr;
			}
		}
		unset($result);
		$this->assign('member_list', $member_list);
		$level_list = logic('yewu')->get_level_list();
		$this->assign('level_list', $level_list);
		$this->display();
	}
	public function shenheOp() {
	    $id = input('id', 0, 'intval');
	    $info = model('vip_level_up_log')->where(array('id' => $id))->find();
	    if ($info['status'] == 0) {
	        $upgrade_account['level_id'] = $info['level_id'];
    		$upgrade_member['level_id'] = $info['level_id'];
    		model('member')->where(array('uid' => $info['uid']))->update($upgrade_member);
    		model('distribute_account')->where(array('uid' => $info['uid']))->update($upgrade_account);
    		model('vip_level_up_log')->where(array('id' => $id))->update(array('status' => 1, 'edit_time' => time()));
    		output_data(array('url' => users_url('vip_level/up_log')));
	    }
	    output_error('审核失败！');
	}
	public function bohuiOp() {
	    $id = input('id', 0, 'intval');
	    $info = model('vip_level_up_log')->where(array('id' => $id))->find();
	    if ($info['status'] == 0) {
	        model('vip_level_up_log')->where(array('id' => $id))->update(array('status' => 2, 'edit_time' => time()));
    		output_data(array('url' => users_url('vip_level/up_log')));
	    }
	    output_error('审核失败！');
	}
}
