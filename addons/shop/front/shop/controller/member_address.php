<?php
/**
 * 我的地址
 *
 */
namespace shop\controller;

use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class member_address extends member {
    public function __construct() {
		parent::_initialize();
    }
    /**
     * 地址列表
     */
    public function address_listOp() {
		if (IS_API) {
			$model_address = model('address');
			$where = array(
				'uid' => $this->member_info['uid']
			);
			$result = $model_address->getList($where, '*', 'sort_time DESC', 20, input('page', 1, 'intval'));
			$return = array(
				'title' => '地址管理',
				'list' => $result['list'],
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
			);
			output_data($return);
		}		
    }
    /**
     * 地址详细信息
     */
    public function address_infoOp() {
		if (IS_API) {
			$result = model('area')->getList();
			$list = $result['list'];
			unset($result);
			$area_json = $this->_get_tree_list($list);
			$address_id = input('address_id', 0, 'intval');
			if (!$address_id) {
				output_data(array('title' => '地址详情', 'address_info' => array(), 'area_json' => $area_json));
			}
			$model_address = model('address');
			$condition = array();
			$condition['address_id'] = $address_id;
			$address_info = $model_address->getInfo($condition);
			if (!empty($address_id) && $address_info['uid'] == $this->member_info['uid']) {
				
				output_data(array('title' => '地址详情', 'address_info' => $address_info, 'area_json' => $area_json));
			} else {
				output_error('地址不存在');
			}
		}
    }
    /**
     * 删除地址
     */
    public function address_delOp() {
		if (IS_API) {
			$address_id = input('address_id', 0, 'intval');
			$model_address = model('address');
			$condition = array();
			$condition['address_id'] = $address_id;
			$condition['uid'] = $this->member_info['uid'];
			$model_address->del($condition);
			output_data('1');
		}
    }
	public function set_defaultOp() {
		if (IS_API) {
			$address_id = input('address_id', 0, 'intval');
			$model_address = model('address');
			//验证地址是否为本人
			$address_info = $model_address->getInfo(array('address_id' => $address_id));
			if ($address_info['uid'] != $this->member_info['uid']) {
				output_error('参数错误');
			}
			$model_address->edit(array('uid' => $this->member_info['uid'], 'is_default' => 1), array('is_default' => 0));
			$model_address->edit(array('address_id' => $address_id, 'is_default' => 0), array('is_default' => 1));
			output_data('1');
		}
	}
    /**
     * 新增地址
     */
    public function address_addOp() {
		if (IS_API) {
			$model_address = model('address');
			$address_info = $this->_address_valid();
			$result = $model_address->add($address_info);
			if ($result) {
				output_data(array('address_id' => $result));
			} else {
				output_error('保存失败');
			}
		}
    }
    /**
     * 编辑地址
     */
    public function address_editOp() {
		if (IS_API) {
			$address_id = input('address_id', 0, 'intval');
			$model_address = model('address');
			//验证地址是否为本人
			$address_info = $model_address->getInfo(array('address_id' => $address_id));
			if ($address_info['uid'] != $this->member_info['uid']) {
				output_error('参数错误');
			}
			$address_info = $this->_address_valid();
			$result = $model_address->edit(array('address_id' => $address_id), $address_info);
			if ($result) {
				output_data('1');
			} else {
				output_error('保存失败');
			}
		}
    }
    /**
     * 验证地址数据
     */
    private function _address_valid() {
		$true_name = input('true_name', '', 'trim');
		$area_info = input('area_info', '', 'trim');
		$address = input('address', '', 'trim');
		$address_value = input('address_value', '', 'trim');
		$mob_phone = input('mob_phone', '', 'trim');
        $obj_validate = new lib\validate();
        $obj_validate->validateparam = array(array('input' => $true_name, 'require' => 'true', 'message' => '姓名不能为空'), array('input' => $area_info, 'require' => 'true', 'message' => '详细地址不能为空'), array('input' => $address, 'require' => 'true', 'message' => '地区不能为空'), array('input' => $mob_phone, 'require' => 'true', 'message' => '联系方式不能为空'));
        $error = $obj_validate->validate();
        if ($error != '') {
            output_error($error);
        }
        $data = array();
		$address_arr = strpos($address, ',') !== false ? explode(',', trim($address)) : explode(' ', trim($address));
		$address_value_arr = explode(',', trim($address_value));
		//lib\logging::write(var_export($address_arr, true));
        $data['uid'] = $this->member_info['uid'];
        $data['user_name'] = $true_name;
		$data['province_name'] = $address_arr[0];
		$data['city_name'] = $address_arr[1];
		$data['county_name'] = $address_arr[2];
        $data['detail_info'] = $area_info;
        $data['tel_number'] = $mob_phone;
		$data['sort_time'] = TIMESTAMP;
        $data['is_default'] = input('is_default', 0, 'intval');
		$data['province_id'] = $address_value_arr[0];
		$data['city_id'] = $address_value_arr[1];
        $data['area_id'] = $address_value_arr[2];
		$data['address'] = implode(' ', $address_arr);
        return $data;
    }
	private function _get_tree_list($list) {
		$temp = array();
		foreach ($list as $v) {
			$item = array();
			$item['text'] = $v['area_name'];
			$item['value'] = $v['area_id'];
			$item['children'] = array();
			$item['area_id'] = $v['area_id'];
			$item['area_parent_id'] = $v['area_parent_id'];
			$temp[$v['area_id']] = $item;
		}
		foreach ($temp as $k => $v) {
			$temp[$v['area_parent_id']]['children'][] = &$temp[$v['area_id']];
		}
		return isset($temp[0]['children']) ? $temp[0]['children'] : array();
	}
}