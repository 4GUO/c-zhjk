<?php
namespace userscenter\controller;
class shop_spec extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
	    $where = array();
		$where['gc_parent_id'] = 0;
		$where['gc_state'] = 1;
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
		$list_goods_class = array();
		$result = model('shop_goods_class')->getList($where);
		
		if(!empty($result['list']) && is_array($result['list'])){
			foreach($result['list'] as $r){
				$list_goods_class[$r['gc_id']] = $r['gc_name'];
			}
		}
		$this->assign('gc_list', $list_goods_class);
		$this->display();
	}
	public function addOp(){
		$model_spec = model('shop_spec');
		if(chksubmit()){
			$gc_id = input('gc_id', 0, 'intval');
			if(empty($gc_id)){
				output_error('请选择分类');
				exit;
			}
			
			$spec_info = $model_spec->getInfo(array('spec_name' => input('spec_name', '')));
			if(!empty($spec_info['spec_name'])){
				output_error('规格名称不得重复！');
				exit;
			}
			unset($spec_info);
			$is_image = input('is_image', 0, 'intval');
			if($is_image == 1){
				$spec_info = $model_spec->getInfo(array('is_image' => 1));
				if(!empty($spec_info['spec_id'])){
					output_error('同分类下只能有一个规格设置成区分图片！');
					exit;
				}
				unset($spec_info);
			}
			$spec_array = array();          
            $spec_array['gc_id'] = $gc_id;
			$spec_array['spec_name'] = input('spec_name', '');
            $spec_array['spec_state'] = input('spec_state', 0, 'intval');
			$spec_array['is_image'] = $is_image;
            $spec_array['spec_sort'] = input('spec_sort', 9999, 'intval');
            $state = $model_spec->add($spec_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'spec_info' => array('spec_id'=>$state,'spec_name'=>$spec_array['spec_name'])));
            } else {
				output_error('添加失败！');
            }
		} else {
			$gc_id = input('gc_id', 0, 'intval');
			if (empty($gc_id)) {
				output_error('请选择分类');
				exit;
			}
			$this->assign('gc_id', $gc_id);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp() {
		$model_spec = model('shop_spec');
		if (chksubmit()) {
			$spec_info = $model_spec->getInfo(array('spec_name' => input('spec_name', '')));
			if (!empty($spec_info['spec_name']) && $spec_info['spec_id'] != input('id', 0, 'intval')) {
				output_error('规格名称不得重复！');
				exit;
			}
			unset($spec_info);
			
			$is_image = input('is_image', 0, 'intval');
			if($is_image == 1){
				$spec_info = $model_spec->getInfo(array('is_image' => 1));
				if(!empty($spec_info['spec_id']) && $spec_info['spec_id'] != input('id', 0, 'intval')){
					output_error('同分类下只能有一个规格设置成区分图片！');
				}
				unset($spec_info);
			}
			
			$spec_array = array();
            $spec_array['gc_id'] = input('gc_id', 0, 'intval');
			$spec_array['spec_name'] = input('spec_name', '');
            $spec_array['spec_state'] = input('spec_state', 0, 'intval');
			$spec_array['is_image'] = $is_image;		
            $spec_array['spec_sort'] = input('spec_sort', 9999, 'intval');
            $state = $model_spec->edit(array('spec_id' => input('id', 0, 'intval')), $spec_array);
            output_data(array('msg' => '编辑成功', 'spec_info' => array('spec_id' => input('id', 0, 'intval'), 'spec_name' => $spec_array['spec_name'])));
		} else {
			$spec_id = input('id', 0, 'intval');
			$spec_info = $model_spec->getInfo(array('spec_id' => $spec_id));
			$this->assign('spec_info', $spec_info);
			$where = array();
			$where['gc_parent_id'] = 0;
			$where['gc_state'] = 1;
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$list_goods_class = array();
			$result = model('shop_goods_class')->getList($where);
			
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $r){
					$list_goods_class[$r['gc_id']] = $r['gc_name'];
				}
			}
			$this->assign('gc_list', $list_goods_class);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp() {
		$model_spec = model('shop_spec');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['spec_id'] = $id_array;
		$state = $model_spec->del($where);
		
		//删除对应的规格值
		$where = array();
		$where['sp_id'] = $id_array;
		$state1 = model('shop_spec_value')->del($where);
        if ($state) {
            output_data('');
        } else {
			output_error('删除失败！');
        }
    }
	
	public function ajaxOp() {
		$type = input('type', '');
		if (empty($type)) {
			echo json_encode(array('state'=>400));
			exit;
		}
		$where = array();
		$gc_id = input('gc_id', 0, 'intval');
		if(empty($gc_id)){
			echo json_encode(array('state'=>400));
			exit;
		}
		if($type == 'spec') {
			$where['gc_id'] = $gc_id;
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$list_temp = model('shop_spec')->getList($where);
			
			if(empty($list_temp['list'])){
				echo json_encode(array('state'=>400));
				exit;
			}
			echo json_encode(array('state'=>200, 'list'=>$list_temp['list']));
			exit;
		} else if($type == 'specvalue') {
			$where['sp_id'] = $gc_id;
			$list_temp = model('shop_spec_value')->getList($where);		
			if(empty($list_temp['list'])){
				echo json_encode(array('state'=>400));
				exit;
			}
			echo json_encode(array('state'=>200, 'list'=>$list_temp['list']));
			exit;
		}		
	}
	
	public function goods_specOp(){
		$gc_id = input('gc_id', 0, 'intval');
		if(empty($gc_id)){
			echo '';
			exit;
		}
		$where['gc_state'] = 1;
		$where['gc_id'] = $gc_id;
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
		$class_info = model('shop_goods_class')->getInfo($where);
		if(empty($class_info['gc_id'])){
			echo '';
			exit;
		}
		unset($where);
		$where = array();
		$where['gc_id'] = $class_info['gc_parent_id'] == 0 ? $gc_id : $class_info['gc_parent_id'];
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
		$list_temp = model('shop_spec')->getList($where);
			
		$spec_list = array();
		if(empty($list_temp['list'])){
			echo '';
			exit;
		}
			
		foreach($list_temp['list'] as $r){
			$spec_list[$r['spec_id']] = array(
				'sp_name' => $r['spec_name'],
				'value' => array()
			);
		}			
			
		unset($list_temp);
		unset($where);
		$where = array(
			'sp_id' => array_keys($spec_list)
		);
			
		$list_temp = model('shop_spec_value')->getList($where);		
		if(empty($list_temp['list'])){
			echo '';
			exit;
		}
			
		foreach($list_temp['list'] as $r){
			$spec_list[$r['sp_id']]['value'][] = array(
				'sp_value_id' => $r['sp_value_id'],
				'sp_value_name' => $r['sp_value_name']
			);
		}
		
		$this->assign('spec_list', $spec_list);	
		$this->assign('sign_i', count($spec_list));
		
		$level_temp = model('vip_level')->getList(array(), '*', 'level_sort DESC');
		$this->assign('member_levels', $level_temp['list']);
		$this->assign('member_levels_num', count($level_temp['list']));
		
		// 取得商品规格的输入值
		$common_id = input('commonid', 0, 'intval');		
        $goods_array = model('shop_goods')->getList(array('goods_commonid' => $common_id), 'goods_id,goods_marketprice,goods_costprice,goods_price,goods_price_vip,goods_storage,goods_serial,goods_spec,goods_salenum');
        $sp_value = array();
        if (!empty($goods_array['list']) && is_array($goods_array['list'])) {
            $spec_checked = array();
            foreach ($goods_array['list'] as $k => $v) {
                $a = fxy_unserialize($v['goods_spec']);
                if (!empty($a)) {
                    foreach ($a as $key => $val) {
                        $spec_checked[$key]['id'] = $key;
                        $spec_checked[$key]['name'] = $val;
                    }
                    $matchs = array_keys($a);
                    sort($matchs);
                    $id = str_replace(',', '', implode(',', $matchs));
                    $sp_value['i_' . $id . '|marketprice'] = $v['goods_marketprice'];
					$sp_value['i_' . $id . '|costprice'] = $v['goods_costprice'];
                    //$sp_value['i_' . $id . '|price'] = $v['goods_price'];
					$price_vip = fxy_unserialize($v['goods_price_vip']);
					foreach($price_vip as $k_p => $v_p){
						$sp_value['i_' . $id . '|price_vip_' . $k_p] = $v_p;
					}					
                    $sp_value['i_' . $id . '|id'] = $v['goods_id'];
                    $sp_value['i_' . $id . '|stock'] = $v['goods_storage'];
					$sp_value['i_' . $id . '|salenum'] = $v['goods_salenum'];
                    $sp_value['i_' . $id . '|alarm'] = 0;
                    $sp_value['i_' . $id . '|sku'] = $v['goods_serial'];
                }
            }
            $this->assign('spec_checked', $spec_checked);
        }
        $this->assign('sp_value', $sp_value);
		$this->assign('config', $this->config);
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
}