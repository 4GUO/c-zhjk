<?php
namespace userscenter\controller;
class shop_goods_class extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
	    $where['is_spike'] = 0;
		$where['is_points'] = 0;
		$list_temp = model('shop_goods_class')->getList($where, '*', 'gc_parent_id asc');
		$gc_list = array();
		foreach($list_temp['list'] as $gc_info) {
			if ($gc_info['gc_parent_id'] == 0) {
				if (empty($gc_list[$gc_info['gc_id']]['child'])) {
					$gc_list[$gc_info['gc_id']] = $gc_info;
				} else {
					$child = $gc_list[$gc_info['gc_id']]['child'];
					$gc_list[$gc_info['gc_id']] = $gc_info;
					$gc_list[$gc_info['gc_id']]['child'] = $child;
				}
			} else {
				$gc_list[$gc_info['gc_parent_id']]['child'][] = $gc_info;
			}
		}
		$this->assign('list', $gc_list);
		$this->display();
	}
	public function addOp(){
		$model_class = model('shop_goods_class');
		if (chksubmit()) {			
			$class_array = array();
            $class_array['gc_name'] = input('gc_name', '');
            $class_array['gc_state'] = input('gc_state', 0, 'intval');
            $class_array['gc_sort'] = input('gc_sort', 9999, 'intval');
			$class_array['index_show'] = input('index_show', 0, 'intval');
			$class_array['gc_parent_id'] = input('gc_parent_id', 0, 'intval');
			$class_array['gc_image'] = input('image_path', '');
			$class_array['gc_virtual'] = input('gc_virtual', 0, 'intval');
            $state = $model_class->add($class_array);
            if ($state) {
				if($class_array['gc_parent_id'] > 0){
					$model_class->edit(array('gc_id' => $class_array['gc_parent_id']), 'has_child = has_child + 1');
				}				
				output_data(array('msg' => '添加成功', 'url' => users_url('shop_goods_class/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$list_temp = model('shop_goods_class')->getList(array('gc_parent_id' => 0));
			$this->assign('first_gc', $list_temp['list']);
			$this->display();
		}
	}
	public function editOp(){
		$model_class = model('shop_goods_class');
		if (chksubmit()) {
			$gc_id = input('id', 0, 'intval');
			$class_info = model('shop_goods_class')->getInfo(array('gc_id' => $gc_id));
			if(empty($class_info['gc_id'])){
				output_error('分类不存在！');
			}
			$class_array = array();
            $class_array['gc_name'] = input('gc_name', '');
            $class_array['gc_state'] = input('gc_state', 0, 'intval');
            $class_array['gc_sort'] = input('gc_sort', 9999, 'intval');
			$class_array['index_show'] = input('index_show', 0, 'intval');
			$class_array['gc_image'] = input('image_path', '');
			$class_array['gc_parent_id'] = input('gc_parent_id', 0, 'intval');
			$class_array['gc_virtual'] = input('gc_virtual', 0, 'intval');
            $state = $model_class->edit(array('gc_id' => input('id', 0, 'intval')), $class_array);
            if ($state) {
				if($class_array['gc_parent_id'] != $class_info['gc_parent_id']){
					$model_class->edit(array('gc_id' => $class_array['gc_parent_id']), 'has_child = has_child + 1');
					$model_class->edit(array('gc_id' => $class_info['gc_parent_id']), 'has_child = has_child - 1');
				}
                output_data(array('msg' => '编辑成功', 'url' => users_url('shop_goods_class/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$gc_id = input('get.id', 0, 'intval');
			$class_info = model('shop_goods_class')->getInfo(array('gc_id' => $gc_id));			
			$this->assign('class_info', $class_info);
			$list_temp = model('shop_goods_class')->getList(array('gc_parent_id' => 0));
			$this->assign('first_gc', $list_temp['list']);
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('shop_goods_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['gc_parent_id'] = $id_array;
		
		$child_info = $model_class->getInfo($where, 'gc_id');
		if(!empty($child_info['gc_id'])){
			output_error('该分类下有子分类，不能删除');
		}
		unset($where);
		unset($child_info);
		
		$where = array();
		$where['gc_id'] = $id_array;
		$goods_info = model('shop_goods_common')->getInfo($where, 'goods_commonid');
		if(!empty($goods_info['goods_commonid'])){
			output_error('该分类下有商品，不能删除');
		}
		
		$parent_ids = array();
		$list_temp = model('shop_goods_class')->getList($where);
		foreach($list_temp['list'] as $gc_info){
			if($gc_info['gc_parent_id'] == 0){
				continue;
			}
			$parent_ids[] = $gc_info['gc_parent_id'];
		}
		$state = $model_class->del($where);
        if ($state) {
			if(!empty($parent_ids)){
				$model_class->edit(array('gc_id' => $parent_ids), 'has_child = has_child - 1');
			}
            output_data(array('url' => users_url('shop_goods_class/index')));
        } else {
			output_error('删除失败！');
        }
    }
}