<?php
namespace sellercenter\controller;
use lib;
class shop_daddress extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list_temp = model('shop_daddress')->getList(array('store_id' => $this->store_id), '*', 'address_id desc');
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp(){
		$model_daddress = model('shop_daddress');
		if(chksubmit()){			
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('seller_name', ''), 'require' => 'true', 'message' => '请填写联系人'), array('input' => input('region', ''), 'require' => 'true', 'message' => '请选择地区'), array('input' => input('address', ''), 'require' => 'true', 'message' => '请填写地址'), array('input' => input('telphone', ''), 'require' => 'true', 'message' => '请填写电话'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }			
			$area_data = array(
				'store_id' => $this->store_id,
				'seller_name' => input('seller_name', ''),
				'area_id' => input('area_id', 0, 'intval'),
				'city_id' => input('city_id', 0, 'intval'),
				'area_info' => input('region', ''),
				'address' => input('address', ''),
				'telphone' => input('telphone', ''),
				'company' => input('company', '')
			);            		
            $state = $model_daddress->add($area_data);
            if ($state) {			
				output_data(array('msg' => '添加成功', 'url' => users_url('shop_daddress/index')));
            } else {
				output_error('添加失败！');
            }
		}else{			
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_daddress = model('shop_daddress');
		if(chksubmit()){
			$address_id = input('address_id', 0, 'intval');
			$address_info = $model_daddress->getInfo(array('store_id' => $this->store_id, 'address_id' => $address_id));
			if(empty($address_info['address_id'])){
				output_error('地址不存在！');
			}
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('seller_name', ''), 'require' => 'true', 'message' => '请填写联系人'), array('input' => input('region', ''), 'require' => 'true', 'message' => '请选择地区'), array('input' => input('address', ''), 'require' => 'true', 'message' => '请填写地址'), array('input' => input('telphone', ''), 'require' => 'true', 'message' => '请填写电话'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }			
			$area_data = array(
				'seller_name' => input('seller_name', ''),
				'area_id' => input('area_id', 0, 'intval'),
				'city_id' => input('city_id', 0, 'intval'),
				'area_info' => input('region', ''),
				'address' => input('address', ''),
				'telphone' => input('telphone', ''),
				'company' => input('company', '')
			);
            $state = $model_daddress->edit(array('address_id' => input('address_id', 0, 'intval')), $area_data);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('shop_daddress/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$address_id = input('get.address_id', 0, 'intval');
			$address_info = $model_daddress->getInfo(array('store_id' => $this->store_id, 'address_id' => $address_id));		
			$this->assign('address_info', $address_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_daddress = model('shop_daddress');
		$address_id = input('get.address_id', 0, 'intval');
		$where = array();
		$where['address_id'] = $address_id;
		$where['store_id'] = $this->store_id;
		$state = $model_daddress->del($where);
        output_data(array('url' => users_url('shop_daddress/index')));
    }
	
	/**
     * 设置默认发货地址
     */
    public function default_setOp()
    {
		$address_id = input('get.address_id', 0, 'intval');
        if ($address_id <= 0) {
            return false;
        }
        $condition = array();
        $condition['store_id'] = $this->store_id;
        $update = model('shop_daddress')->edit($condition, array('is_default' => 0));
        $condition['address_id'] = $address_id;
        $update = model('shop_daddress')->edit($condition, array('is_default' => 1));
    }
}
?>