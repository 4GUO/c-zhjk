<?php
namespace userscenter\controller;
use lib;
class shop_waybill extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list_temp = model('shop_waybill')->getList(array('store_id' => 0), '*', 'waybill_id desc');
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp(){
		$model_waybill = model('shop_waybill');
		if(chksubmit()){			
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('waybill_image', ''), 'require' => 'true', 'message' => '请上传模板图片'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$waybill_express = input('waybill_express', '');
			if(empty($waybill_express)){
				 output_error('请选择物流公司');
			}
			$express_arr = explode('|', $waybill_express);
			if(empty($express_arr[0]) || empty($express_arr[1])){
				output_error('请选择物流公司');
			}
			$waybill_data = array(
				'store_id' => 0,
				'waybill_name' => input('waybill_name', ''),
				'express_id' => $express_arr[0],
				'express_name' => $express_arr[1],
				'waybill_image' => input('waybill_image', ''),
				'waybill_width' => input('waybill_width', 0, 'intval'),
				'waybill_height' => input('waybill_height', 0, 'intval'),				
				'waybill_top' => input('waybill_top', 0, 'intval'),
				'waybill_left' => input('waybill_left', 0, 'intval'),
				'waybill_usable' => input('waybill_usable', 0, 'intval')
			);
            $state = $model_waybill->add($waybill_data);
            if ($state) {			
				output_data(array('msg' => '添加成功', 'url' => _url('shop_waybill/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$result = model('express')->getList(array('e_state' => 1), '*', 'e_letter asc');
			$this->assign('express_list', $result['list']);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_waybill = model('shop_waybill');
		if(chksubmit()){
			$waybill_id = input('waybill_id', 0, 'intval');
			$waybill_info = $model_waybill->getInfo(array('store_id' => 0, 'waybill_id' => $waybill_id));
			if(empty($waybill_info['waybill_id'])){
				output_error('模板不存在！');
			}
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('waybill_image', ''), 'require' => 'true', 'message' => '请上传模板图片'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$waybill_express = input('waybill_express', '');
			if(empty($waybill_express)){
				 output_error('请选择物流公司');
			}
			$express_arr = explode('|', $waybill_express);
			if(empty($express_arr[0]) || empty($express_arr[1])){
				output_error('请选择物流公司');
			}
			$waybill_data = array(
				'waybill_name' => input('waybill_name', ''),
				'express_id' => $express_arr[0],
				'express_name' => $express_arr[1],
				'waybill_image' => input('waybill_image', ''),
				'waybill_width' => input('waybill_width', 0, 'intval'),
				'waybill_height' => input('waybill_height', 0, 'intval'),				
				'waybill_top' => input('waybill_top', 0, 'intval'),
				'waybill_left' => input('waybill_left', 0, 'intval'),
				'waybill_usable' => input('waybill_usable', 0, 'intval')
			);
            $state = $model_waybill->edit(array('waybill_id' => $waybill_id), $waybill_data);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => _url('shop_waybill/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$waybill_id = input('get.waybill_id', 0, 'intval');
			$waybill_info = $model_waybill->getInfo(array('store_id' => 0, 'waybill_id' => $waybill_id));
			if(empty($waybill_info['waybill_id'])){
				output_error('模板不存在！');
			}
			$this->assign('waybill_info', $waybill_info);
			
			$result = model('express')->getList(array('e_state' => 1), '*', 'e_letter asc');
			$express_list = array();
			foreach($result['list'] as $express_info){
				if($express_info['id'] == $waybill_info['express_id']){
					$express_info['selected'] = 1;
				} else {
					$express_info['selected'] = 0;
				}
				$express_list[] = $express_info;
			}
			$this->assign('express_list', $express_list);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_waybill = model('shop_waybill');
		$waybill_id = input('get.waybill_id', 0, 'intval');
		$where = array();
		$where['waybill_id'] = $waybill_id;
		$where['store_id'] = 0;
		$state = $model_waybill->del($where);
        output_data(array('url' => _url('shop_waybill/index')));
    }
	
	public function designOp(){
		$model_waybill = model('shop_waybill');
		if(chksubmit()){
			$waybill_id = input('waybill_id', 0, 'intval');
			
			$waybill_info = $model_waybill->getInfo(array('store_id' => 0, 'waybill_id' => $waybill_id));
			if(empty($waybill_info['waybill_id'])){
				output_error('模板不存在！');
			}
			$waybill_data = input('waybill_data/a', array());
			
			$waybill_data = array(
				'waybill_data' => empty($waybill_data) ? '' : serialize($waybill_data)
			);
            $state = $model_waybill->edit(array('waybill_id' => $waybill_id), $waybill_data);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => _url('shop_waybill/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$waybill_id = input('get.waybill_id', 0, 'intval');
			$waybill_info = $model_waybill->getInfo(array('store_id' => 0, 'waybill_id' => $waybill_id));
			if(empty($waybill_info['waybill_id'])){
				output_error('模板不存在！');
			}
			
			$waybill_pixel = 3.8;
			$waybill_info['waybill_pixel_width'] = $waybill_info['waybill_width'] * $waybill_pixel;
            $waybill_info['waybill_pixel_height'] = $waybill_info['waybill_height'] * $waybill_pixel;
            $waybill_info['waybill_pixel_top'] = $waybill_info['waybill_top'] * $waybill_pixel;
            $waybill_info['waybill_pixel_left'] = $waybill_info['waybill_left'] * $waybill_pixel;
			
            if (!empty($waybill_info['waybill_data'])) {
                $waybill_info['waybill_data'] = fxy_unserialize($waybill_info['waybill_data']);
                //整理打印模板
                $waybill_item = $this->getWaybillItemList();
                foreach ($waybill_info['waybill_data'] as $key => $value) {
                    $waybill_info['waybill_data'][$key]['content'] = $waybill_item[$key]['item_text'];
                }
            }
			
			$this->assign('waybill_info', $waybill_info);
			
			$waybill_info_data = $waybill_info['waybill_data'];
			$waybill_item_list = $this->getWaybillItemList();
			if (!empty($waybill_info_data)) {
				foreach ($waybill_info_data as $key => $value) {
					$waybill_info_data[$key]['item_text'] = $waybill_item_list[$key]['item_text'];
				}
			}
			foreach ($waybill_item_list as $key => $value) {
				$waybill_item_list[$key]['check'] = !empty($waybill_info_data[$key]['check']) ? 'checked' : '';
				$waybill_item_list[$key]['width'] = !empty($waybill_info_data[$key]['width']) ? $waybill_info_data[$key]['width'] : '0';
				$waybill_item_list[$key]['height'] = !empty($waybill_info_data[$key]['height']) ? $waybill_info_data[$key]['height'] : '0';
				$waybill_item_list[$key]['top'] = !empty($waybill_info_data[$key]['top']) ? $waybill_info_data[$key]['top'] : '0';
				$waybill_item_list[$key]['left'] = !empty($waybill_info_data[$key]['left']) ? $waybill_info_data[$key]['left'] : '0';
			}
			
			$this->assign('waybill_info_data', $waybill_info_data);
			$this->assign('waybill_item_list', $waybill_item_list);
			$this->display();
		}
	}
	
	/**
     * 获取运单项目列表
     */
    private function getWaybillItemList()
    {
        $item = array('buyer_name' => array('item_text' => '收货人'), 'buyer_area' => array('item_text' => '收货人地区'), 'buyer_address' => array('item_text' => '收货人地址'), 'buyer_mobile' => array('item_text' => '收货人手机'), 'buyer_phone' => array('item_text' => '收货人电话'), 'seller_name' => array('item_text' => '发货人'), 'seller_area' => array('item_text' => '发货人地区'), 'seller_address' => array('item_text' => '发货人地址'), 'seller_phone' => array('item_text' => '发货人电话'), 'seller_company' => array('item_text' => '发货人公司'));
        return $item;
    }
}