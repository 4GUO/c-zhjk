<?php
namespace sellercenter\controller;
class attachment extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$list_temp = model('album_class')->getList(array('store_id' => $this->store_id));
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp() {
		$model_class = model('album_class');
		if (chksubmit()) {
			$class_array = array();
            $class_array['aclass_name'] = input('post.aclass_name', '');
            $class_array['store_id'] = $this->store_id;
            $class_array['aclass_sort'] = input('post.aclass_sort', 9999, 'intval');
            $state = $model_class->insert($class_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('attachment/index')));
            } else {
				output_error('添加失败！');
            }
		} else {
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp() {
		$model_class = model('album_class');
		if (chksubmit()) {
			$class_array = array();
            $class_array['aclass_name'] = input('post.aclass_name', '');
            $class_array['aclass_sort'] = input('post.aclass_sort', 9999, 'intval');
			
            $state = $model_class->where(array('aclass_id' => input('post.id', 0, 'intval')))->update($class_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('attachment/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$aclass_id = input('get.id', 0, 'intval');
			$class_info = model('album_class')->where(array('aclass_id' => $aclass_id))->find();
			$this->assign('class_info', $class_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp() {
		$model_class = model('album_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['aclass_id'] = $id_array;
		$where['store_id'] = $this->store_id;
		
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('attachment/index')));
        } else {
			output_error('删除失败！');
        }
    }
}