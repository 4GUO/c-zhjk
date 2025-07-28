<?php
namespace userscenter\controller;
class area_level extends control {
	public function __construct() {
		parent::_initialize();
	}
	
	public function indexOp(){
		$list_temp = model('area_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	
	public function addOp() {
		$model_level = model('area_level');
		if(chksubmit()){
			$level_array = array();
			$level_array['uniacid'] = $this->uniacid;
            $level_array['level_name'] = input('level_name', '');
			$level_array['level_sort'] = input('level_sort', 0, 'intval');
			if (empty($level_array['level_sort'])) {
				output_error('级别序号不能为空，且为数字');
			}
			$level_array['bonus_bili'] = input('bonus_bili', 0);
            $state = $model_level->insert($level_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('area_level/index')));
            } else {
				output_error('添加失败！');
            }
		} else {
			$this->display();
		}
	}
	
	public function editOp(){
		$model_level = model('area_level');
		if (chksubmit()) {
			$level_array = array();
            $level_array['level_name'] = input('level_name', '');
			$level_array['level_sort'] = input('level_sort', 0, 'intval');
			if (empty($level_array['level_sort'])) {
				output_error('级别序号不能为空，且为数字');
			}
			$level_array['bonus_bili'] = input('bonus_bili', 0);
            $state = $model_level->where(array('id' => input('id', 0, 'intval')))->update($level_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('area_level/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$level_id = input('id', 0, 'intval');
			$level_info = $model_level->where(array('id' => $level_id))->find();
			$this->assign('level_info', $level_info);
			$this->display();
		}
	}
	
	public function delOp(){
		$model_level = model('area_level');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$agent_info = model('member')->where($where)->find();
		if(!empty($agent_info['id'])){
			output_error('该级别下有代理，不能删除');
		}
		$where['level_default'] = 0;
		$state = $model_level->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('area_level/index')));
        } else {
			output_error('删除失败！');
        }
    }
}