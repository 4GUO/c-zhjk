<?php
namespace userscenter\controller;
use lib;
class withdraw_method extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list_temp = model('withdraw_method')->getList(array('uniacid' => $this->uniacid), '*', 'method_id asc');
		if(empty($list_temp['list'])){
			$lists[] = array(
				'uniacid' => $this->uniacid,
				'method_code'=>'wxzhuanzhang',
				'method_name'=>'微信转账',
				'method_check'=>0,
				'method_status'=>1,
				'method_min'=>0,
				'method_max'=>0,
				'method_fee'=>0,
				'method_yue'=>0
			);
			$lists[] = array(
				'uniacid' => $this->uniacid,
				'method_code'=>'alipay',
				'method_name'=>'支付宝',
				'method_check'=>1,
				'method_status'=>1,
				'method_min'=>0,
				'method_max'=>0,
				'method_fee'=>0,
				'method_yue'=>0
			);
			model('fxy_withdraw_method')->insertAll($lists);
			$list_temp = model('withdraw_method')->getList(array('uniacid' => $this->uniacid), '*', 'method_id asc');
		}
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp(){
		$model_method = model('withdraw_method');
		if(chksubmit()){
			$name = trim(input('name', ''));
			if(empty($name)){
				output_error('请填写名称');
			}
			
			$update_array = array();
			$update_array['uniacid'] = $this->uniacid;			
			$update_array['method_status'] = intval(input('status', 0, 'intval'));
			$update_array['method_name'] = $name;
			$update_array['method_min'] = input('min', 0, 'intval') ? priceFormat(input('min', 0, 'intval')) : 0;
			$update_array['method_max'] = input('max', 0, 'intval') ? priceFormat(input('max', 0, 'intval')) : 0;
			$update_array['method_fee'] = input('fee', 0, 'intval') ? priceFormat(input('fee', 0, 'intval')) : 0;
			$update_array['method_yue'] = input('yue', 0, 'intval') ? priceFormat(input('yue', 0, 'intval')) : 0;			
            $method_id = $model_method->add($update_array);
            if ($method_id) {
				$method_code = 'bank_' . $method_id;
				$model_method->edit(array('method_id' => $method_id), array('method_code' => $method_code));
				output_data(array('msg' => '添加成功', 'url' => users_url('withdraw_method/index')));
            } else {
				output_error('添加失败！');
            }
		}else{			
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_method = model('withdraw_method');
		if(chksubmit()){
			$method_id = input('method_id', 0, 'intval');
			$method_info = $model_method->getInfo(array('uniacid' => $this->uniacid, 'method_id' => $method_id));	
			if(empty($method_info['method_id'])){
				output_error('提现方式不存在！');
			}
			$update_array = array();
			$update_array['method_status'] = intval(input('status', 0, 'intval'));
			$update_array['method_min'] = input('min', 0, 'intval') ? priceFormat(input('min', 0, 'intval')) : 0;
			$update_array['method_max'] = input('max', 0, 'intval') ? priceFormat(input('max', 0, 'intval')) : 0;
			$update_array['method_fee'] = input('fee', 0, 'intval') ? priceFormat(input('fee', 0, 'intval')) : 0;
			$update_array['method_yue'] = input('yue', 0, 'intval') ? priceFormat(input('yue', 0, 'intval')) : 0;	
			
			if($method_info['method_code'] == 'bank_' . $method_id){
				$name = trim(input('name', ''));
				if(empty($name)){
					output_error('请填写名称');
				}
				$update_array['method_name'] = $name;
			}elseif($method_info['method_code']=='wxhongbao' || $method_info['method_code']=='wxzhuanzhang'){
				$update_array['method_check'] = intval(input('check', 0, 'intval'));
			}			
            $state = $model_method->edit(array('method_id' => input('method_id', 0, 'intval')), $update_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('withdraw_method/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$method_id = input('get.method_id', 0, 'intval');
			$method_info = $model_method->getInfo(array('uniacid' => $this->uniacid, 'method_id' => $method_id));
			$this->assign('method_info', $method_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	
	public function delOp(){
		$model_method = model('withdraw_method');
		$method_id = input('get.method_id', 0, 'intval');
		$where = array();
		$where['method_id'] = $method_id;
		$where['uniacid'] = $this->uniacid;
		$state = $model_method->del($where);
        output_data(array('url' => users_url('withdraw_method/index')));
    }
}