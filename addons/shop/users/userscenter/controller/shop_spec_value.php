<?php
namespace userscenter\controller;
class shop_spec_value extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function addOp() {		
		$model_spec_value = model('shop_spec_value');
		if (chksubmit()) {
			$sp_id = input('sp_id', 0, 'intval');
			if(empty($sp_id)){
				output_error('请选择规格');
				exit;
			}
			
			$sp_value_info = $model_spec_value->getInfo(array('sp_value_name' => input('sp_value_name', '')));
			if(!empty($sp_value_info['sp_value_name'])){
				output_error('规格值不能重复！');
				exit;
			}
			
			$spec_array = array();          
            $spec_array['sp_id'] = $sp_id;
			$spec_array['sp_value_name'] = input('sp_value_name', '');         
            $spec_array['sp_value_sort'] = input('sp_value_sort', 9999, 'intval');
            $state = $model_spec_value->add($spec_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'sp_value_info' => array('sp_value_id' => $state, 'sp_value_name' => $spec_array['sp_value_name'])));
            } else {
				output_error('添加失败！');
            }
		} else {
			$sp_id = input('sp_id', 0, 'intval');
			if (empty($sp_id)) {
				output_error('请选择规格');
				exit;
			}
			$this->assign('sp_id', $sp_id);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp() {
		$model_spec_value = model('shop_spec_value');
		if(chksubmit()) {
			$sp_value_info = $model_spec_value->getInfo(array('sp_value_name' => input('sp_value_name', '')));
			if(!empty($sp_value_info['sp_value_name']) && $sp_value_info['sp_value_id'] != input('id', 0, 'intval')){
				output_error('规格值不能重复！');
				exit;
			}
			$spec_array = array();
			$spec_array['sp_value_name'] = input('sp_value_name', '');         
            $spec_array['sp_value_sort'] = input('sp_value_sort', 9999, 'intval');
            $state = $model_spec_value->edit(array('sp_value_id' => input('id', 0, 'intval')), $spec_array);            
            output_data(array('msg' => '编辑成功', 'sp_value_info' => array('sp_value_id' => input('id', 0, 'intval'), 'sp_value_name'  => $spec_array['sp_value_name'])));            
		} else {
			$sp_value_id = input('id', 0, 'intval');
			$sp_value_info = $model_spec_value->getInfo(array('sp_value_id' => $sp_value_id));
			$this->assign('sp_value_info', $sp_value_info);			
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp() {
		$model_spec_value = model('shop_spec_value');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['sp_value_id'] = $id_array;
		$state = $model_spec_value->del($where);
		
        if ($state) {
            output_data('');
        } else {
			output_error('删除失败！');
        }
    }
}