<?php
namespace userscenter\controller;
use lib;
class store extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model = model('seller');
		$where = array();
		$keyword = input('get.keyword', '');
        if ($keyword) {
            $where['name'] = '%' . trim($keyword) . '%';
        }
        $list = $model->where($where)->select();
		$supply_ids = array();
        foreach ($list as $key => $val) {
			$supply_ids[] = $val['id'];
        }
		if ($supply_ids) {
			$result = model('shop_goods_common')->field('store_id')->where(array('store_id' => $supply_ids))->select();
		} else {
			$result = array();
		}
		$goods_array = array();
		foreach ($result as $k => $v) {
			if (!isset($goods_array[$v['store_id']])) {
				$goods_array[$v['store_id']] = 0;
			}
			$goods_array[$v['store_id']] += 1;
		}
		
		$this->assign('goods_array', $goods_array);
		$this->assign('list', $list);
		$this->display();
	}
	public function publishOp(){
		$model = model('seller');
		if (chksubmit()) {
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('name', ''), 'require' => 'true', 'message' => '商家名称不能为空'),array('input' => input('area_id', ''), 'require' => 'true', 'message' => '请选择地区'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$get_id = input('id', 0, 'intval');
			$common_array = array();
			$common_array['sc_id'] = input('sc_id', '');
			$common_array['name'] = input('name', '');
			$common_array['member_id'] = input('member_id', 0, 'intval');
			$common_array['logo'] = input('logo', '');
			$common_array['banner'] = input('banner', '');
			$common_array['product'] = input('product', '');
			$common_array['mobile'] = input('mobile', '');
			$common_array['state'] = input('state', 0, 'intval');
			$common_array['type'] = input('type', 0, 'intval');
			$common_array['commiss_bili'] = input('commiss_bili', 0);
			$common_array['province_id'] = input('province_id', 0, 'intval');
			$common_array['city_id'] = input('city_id', 0, 'intval');
			$common_array['area_id'] = input('area_id', 0, 'intval');
			$common_array['region'] = input('region', '');
			$common_array['address'] = input('address', '');
			$common_array['lon'] = input('lon', '');
			$common_array['lat'] = input('lat', '');
			if (!input('lon', '') || !input('lat', '')) {
				output_error('店铺经纬度坐标不能为空');
			}
			if (input('login_name', '')) {
				$common_array['login_name'] = input('login_name', '');
			}
			$login_password = input('login_password', '');
			if ($login_password) {
				$login_slat = random(6);
				$common_array['login_password'] = md5($login_password . $login_slat);
				$common_array['login_slat'] = $login_slat;
			}
			$common_array['qualifications'] = serialize(input('qualifications', ''));
			if ($get_id) {
				$id = $get_id;
				$model->where(array('id' => $id))->update($common_array);
			} else {
				$common_array['addtime'] = TIMESTAMP;
				$id = $model->insert($common_array); // 保存数据
			}
			if ($id) {
				output_data(array('msg' => '操作成功', 'url' => users_url('store/index')));
			} else {
				output_error('操作失败！');
			}
		}else{
			$id = input('id', 0, 'intval');
			if ($id) {
				$info = $model->where(array('id' => $id))->find();
				$this->assign('info', $info);
				$member_info = model('member')->where(array('uid' => $info['member_id']))->find();
				$this->assign('member_info', $member_info);
			}
			//分类
			$class_list = model('seller_class')->getList();
			$this->assign('class_list', $class_list['list']);
			$this->display();
		}
	}
	public function delOp() {
		$model = model('seller');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$state = $model->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('store/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function store_classOp() {
		$list_temp = model('seller_class')->getList();
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function class_publishOp() {
		$model_class = model('seller_class');
		$id = input('id', 0, 'intval');
		if (chksubmit()) {
			$class_array = array();
            $class_array['sc_name'] = input('sc_name', '');
            $class_array['sc_sort'] = input('sc_sort', 9999, 'intval');
			if ($id) {
				$state = $model_class->where(array('sc_id' => $id))->update($class_array);
			} else {
				$state = $model_class->insert($class_array);
			}
            if ($state) {
				output_data(array('msg' => '操作成功', 'url' => users_url('store/store_class')));
            } else {
				output_error('操作失败！');
            }
		} else {
			$info = $model_class->where(array('sc_id' => $id))->find();
			$this->assign('info', $info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function class_delOp() {
		$model_class = model('seller_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['sc_id'] = $id_array;
		
		$seller_info = model('seller')->where($where)->find();
		if(!empty($seller_info['id'])){
			output_error('该分类下有商家，不能删除');
		}
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('store/store_class')));
        } else {
			output_error('删除失败！');
        }
	}
	public function tixianlistOp() {
		$where = array();
        $where['state'] = 0;
		$list = model('store_tixian_order')->getList($where, '*', '', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval')), _url('store/tixianlist')));
		$this->assign('list', $list['list']);
		$store_ids = array();
		foreach ($list['list'] as $k => $v) {
			$store_ids[] = $v['store_id'];
		}
		$result = model('seller')->where(array('id' => $store_ids))->select();
		$store_list = array();
		foreach ($result as $k => $v) {
			$store_list[$v['id']] = $v;
		}
		$this->assign('store_list', $store_list);
		$this->display();
	}
	public function reject_tixianOp(){
		$model_record = model('store_tixian_order');
		if (chksubmit()) {
			$id = input('id', 0, 'intval');
			$record_info = $model_record->getInfo(array('id' => $id));
			if($record_info['state'] > 0){
				output_error('该记录不是待处理状态！');
			}
			$update_array = array();
			$update_array['state'] = 2;
			$update_array['fail_msg'] = input('note', '');			
            $state = $model_record->edit(array('id' => input('id', 0, 'intval')), $update_array);
            if ($state) {
				model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'money=money+' . $record_info['money']);
				model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'dongmoney=dongmoney-' . $record_info['money']);
                output_data(array('msg' => '编辑成功', 'url' => users_url('store/tixianlist')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$id = input('get.id', 0, 'intval');
			$record_info = $model_record->getInfo(array('id' => $id));
			$this->assign('record_info', $record_info);
			
			if($record_info['state'] > 0){
				output_error('该记录不是待处理状态！');
			}
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	
	public function deal_tixianOp(){
		$model_record = model('store_tixian_order');
		$id = input('get.id', 0, 'intval');
		$record_info = $model_record->getInfo(array('id' => $id));
		if(empty($record_info['id'])){
			output_error('记录不存在！');
		}
		if($record_info['state'] > 0){
			output_error('该记录不是待处理状态！');
		}
		$state = $model_record->edit(array('id' => $id), array('state' => 1, 'shentime' => time()));
        if ($state) {
			model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'getmoney=getmoney+' . $record_info['money']);
            model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'dongmoney=dongmoney-' . $record_info['money']);
			output_data(array('msg' => '处理成功', 'url' => users_url('store/tixianlist')));
        } else {
			output_error('处理失败！');
        }
	}
	
	public function pay_recordOp(){
		if(IS_API){
			$model_record = model('store_tixian_order');
			$id = input('get.id', 0, 'intval');
			$record_info = $model_record->getInfo(array('id' => $id));
			if(empty($record_info['id'])){
				output_error('记录不存在！');
			}
			if($record_info['state'] > 0){
				output_error('该记录不是待处理状态！');
			}	
			$config = model('config')->getInfo(array('uniacid' => $this->uniacid));
			if ($record_info['type'] == 'wxzhuanzhang' || $record_info['type'] == 'wxhongbao') {
				//模拟接口数据
				$record_info['uid'] = $record_info['member_id'];
				$record_info['method_code'] = $record_info['type'];
				$record_info['record_amount'] = $record_info['money'];
				$result = logic('weixin_pay')->commission_withdraw($record_info);
				if (!$result['state']) {
					output_error($result['msg']);
				} else {//发送成功
					$payment_return = $result['data'];
				}
			}
			$update_data = array(
				'state' => 1,
				'record_outtradeno' => $payment_return['outtradeno'],
				'record_tradeno' => $payment_return['tradeno'],
				'record_tradetime' => $payment_return['tradetime'],
				'shentime' => time(),
			);
			$state = $model_record->edit(array('record_id' => $record_id), $update_data);
			if ($state) {
				model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'getmoney=getmoney+' . $record_info['money']);
				model('store_commiss')->edit(array('store_id' => $record_info['store_id']), 'dongmoney=dongmoney-' . $record_info['money']);
				output_data('转账成功');
			} else {
				output_error('转账失败！');
			}
		}
	}
}