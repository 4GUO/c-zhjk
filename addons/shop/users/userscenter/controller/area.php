<?php
namespace userscenter\controller;
class area extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
	    $area_parent_id = input('area_parent_id', 0, 'intval');
	    $where['area_parent_id'] = $area_parent_id;
		$list_temp = model('area')->getList($where, '*', 'area_id asc');
		$this->assign('list', $list_temp['list']);
		if ($area_parent_id) {
		    $parent_area_info = model('area')->getInfo(array('area_id' => $area_parent_id));
		    $this->assign('parent_area_info', $parent_area_info);
		}
		$this->display();
	}
	public function publishOp() {
	    $area_parent_id = input('area_parent_id', 0, 'intval');
	    $parent_area_info = array();
	    if ($area_parent_id) {
		    $parent_area_info = model('area')->getInfo(array('area_id' => $area_parent_id));
		    $this->assign('parent_area_info', $parent_area_info);
		}
		$model_class = model('area');
		if (chksubmit()) {
		    $area_deep = 0;
		    if ($parent_area_info) {
		        $area_deep = $parent_area_info['area_deep'] + 1;
		    }
		    if ($area_deep > 3) {
		        output_error('只允许最深三级');
		    }
			$class_array = array();
            $class_array['area_name'] = input('area_name', '');
            $class_array['area_parent_id'] = $area_parent_id;
            $class_array['area_sort'] = 0;
            $get_id = input('id', 0, 'intval');
            if ($get_id) {
				$id = $get_id;
				$model_class->where(array('area_id' => $id))->update($class_array);
			} else {
				$id = $model_class->insert($class_array);
			}
            if ($id) {
				output_data(array('msg' => '保存成功', 'url' => users_url('area/index', array('area_parent_id' => $area_parent_id))));
            } else {
				output_error('添加失败！');
            }
		} else {
			$this->view->_layout_file = 'null_layout';
			$id = input('id', 0, 'intval');
			if ($id) {
    		    $info = model('area')->getInfo(array('area_id' => $id));
    		    $this->assign('info', $info);
    		}
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('area');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['area_parent_id'] = $id_array;
		
		$child_info = $model_class->getInfo($where);
		if(!empty($child_info['area_id'])){
			output_error('该区域下有子地区，不能删除');
		}
		unset($where);
		unset($child_info);
		
		$where = array();
		$where['area_id'] = $id_array;
		$state = $model_class->delete($where);
        if ($state) {
            output_data(array('url' => users_url('area/index')));
        } else {
			output_error('删除失败！');
        }
    }
}