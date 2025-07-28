<?php
namespace userscenter\controller;
class store_goods_class extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$store_id = input('store_id', 0, 'intval');
		$list_temp = model('store_goods_class')->getList(array('store_id' => $store_id), '*', 'stc_parent_id asc');
		$gc_list = array();
		foreach($list_temp['list'] as $gc_info) {
			if ($gc_info['stc_parent_id'] == 0) {
				if (empty($gc_list[$gc_info['stc_id']]['child'])) {
					$gc_list[$gc_info['stc_id']] = $gc_info;
				} else {
					$child = $gc_list[$gc_info['stc_id']]['child'];
					$gc_list[$gc_info['stc_id']] = $gc_info;
					$gc_list[$gc_info['stc_id']]['child'] = $child;
				}
			} else {
				$gc_list[$gc_info['stc_parent_id']]['child'][] = $gc_info;
			}
		}
		$this->assign('list', $gc_list);
		$this->display();
	}
	public function addOp(){
		$model_class = model('store_goods_class');
		if(chksubmit()){			
			$class_array = array();
            $class_array['stc_name'] = input('stc_name', '');
            $class_array['stc_state'] = input('stc_state', 0, 'intval');
            $class_array['store_id'] = input('store_id', 0, 'intval');
            $class_array['stc_sort'] = input('stc_sort', 9999, 'intval');
			$class_array['stc_parent_id'] = input('stc_parent_id', 0, 'intval');		
            $state = $model_class->add($class_array);
            if ($state) {
				if($class_array['stc_parent_id'] > 0){
					$model_class->edit(array('stc_id' => $class_array['stc_parent_id']), 'has_child = has_child + 1');
				}				
				output_data(array('msg' => '添加成功', 'url' => users_url('store_goods_class/index', array('store_id' => input('store_id', 0, 'intval')))));
            } else {
				output_error('添加失败！');
            }
		}else{
			$list_temp = model('store_goods_class')->getList(array('store_id' => input('store_id', 0, 'intval'), 'stc_parent_id' => 0));
			$this->assign('first_gc', $list_temp['list']);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_class = model('store_goods_class');
		if(chksubmit()){
			$stc_id = input('id', 0, 'intval');
			$class_info = model('store_goods_class')->getInfo(array('stc_id' => $stc_id));
			if(empty($class_info['stc_id'])){
				output_error('分类不存在！');
			}
			$class_array = array();
            $class_array['stc_name'] = input('stc_name', '');
            $class_array['stc_state'] = input('stc_state', 0, 'intval');
            $class_array['stc_sort'] = input('stc_sort', 9999, 'intval');
			$class_array['stc_parent_id'] = input('stc_parent_id', 0, 'intval');
            $state = $model_class->edit(array('stc_id' => input('id', 0, 'intval')), $class_array);
            if ($state) {
				if($class_array['stc_parent_id'] != $class_info['stc_parent_id']){
					$model_class->edit(array('stc_id' => $class_array['stc_parent_id']), 'has_child = has_child + 1');
					$model_class->edit(array('stc_id' => $class_info['stc_parent_id']), 'has_child = has_child - 1');
				}
                output_data(array('msg' => '编辑成功', 'url' => users_url('store_goods_class/index', array('store_id' => $class_info['store_id']))));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$stc_id = input('get.id', 0, 'intval');
			$class_info = model('store_goods_class')->getInfo(array('stc_id' => $stc_id));			
			$this->assign('class_info', $class_info);
			$list_temp = model('store_goods_class')->getList(array('store_id' => input('store_id', 0, 'intval'), 'stc_parent_id' => 0));
			$this->assign('first_gc', $list_temp['list']);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('store_goods_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['stc_parent_id'] = $id_array;	
		
		$child_info = $model_class->getInfo($where, 'stc_id,store_id');
		if(!empty($child_info['stc_id'])){
			output_error('该分类下有子分类，不能删除');
		}
		unset($where);
		unset($child_info);
		
		$where = array();
		$where['stc_id'] = $id_array;
		$goods_info = model('shop_goods_common')->getInfo($where, 'goods_commonid');
		if(!empty($goods_info['goods_commonid'])){
			output_error('该分类下有商品，不能删除');
		}
		
		$parent_ids = array();
		$list_temp = model('store_goods_class')->getList($where);
		foreach($list_temp['list'] as $gc_info){
			if($gc_info['stc_parent_id'] == 0){
				continue;
			}
			$parent_ids[] = $gc_info['stc_parent_id'];
		}
		$state = $model_class->del($where);
        if ($state) {
			if(!empty($parent_ids)){
				$model_class->edit(array('stc_id' => $parent_ids), 'has_child = has_child - 1');
			}
            output_data(array('url' => users_url('store_goods_class/index', array('store_id' => $child_info['store_id']))));
        } else {
			output_error('删除失败！');
        }
    }
	public function ajax_listOp() {
		$store_id = input('store_id', 0, 'intval');
		$stc_id = input('stc_id', 0, 'intval');
		$list_temp = model('store_goods_class')->getList(array('store_id' => $store_id), '*', 'stc_parent_id asc');
		$gc_list = array();
		
		foreach($list_temp['list'] as $gc_info) {
			if ($gc_info['stc_parent_id'] == 0) {
				$gc_list[$gc_info['stc_id']] = $gc_info;
			} else {
				$gc_list[$gc_info['stc_parent_id']]['child'][] = $gc_info;
			}
		}
		if (!empty($gc_list)) {
			$html = '<select name=\'stc_id\'>';
			foreach($gc_list as $k => $v) {
				if (!empty($v['child'])) {
					$html .= '<option value=\'' . $v['stc_id'] . '\' disabled=\'disabled\'>' . $v['stc_name'] . '</option>';
					foreach($v['child'] as $c_k => $c_v) {
						if ($stc_id == $c_v['stc_id']) {
							$html .= '<option selected=\'selected\' value=\'' . $c_v['stc_id'] . '\'>&nbsp;&nbsp;└└ ' . $c_v['stc_name'] . '</option>';
						} else {
							$html .= '<option value=\'' . $c_v['stc_id'] . '\'>&nbsp;&nbsp;└└ ' . $c_v['stc_name'] . '</option>';
						}
					}
				} else {
					if ($stc_id == $v['stc_id']) {
						$html .= '<option selected=\'selected\' value=\'' . $v['stc_id'] . '\'>' . $v['stc_name'] . '</option>';
					} else {
						$html .= '<option value=\'' . $v['stc_id'] . '\'>' . $v['stc_name'] . '</option>';
					}
				}
			}
			$html .= '</select>';
		} else {
			$html = '<a href=\'' . users_url('store_goods_class/index', array('store_id' => $store_id)) . '\'>添加分类</a>';
		}
		echo $html;
	}
}