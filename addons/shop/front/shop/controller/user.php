<?php
namespace shop\controller;
use lib;
class user extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if (IS_API) {
			$model_order = model('shop_order');
			$ORDER_STATE_NEW = $model_order->where(array('uid' => $this->member_info['uid'], 'order_state' => ORDER_STATE_NEW, 'lock_state' => 0, 'is_spike' => 0, 'is_points' => 0))->total();
			$ORDER_STATE_PAY = $model_order->where(array('uid' => $this->member_info['uid'], 'order_state' => ORDER_STATE_PAY, 'lock_state' => 0, 'is_spike' => 0, 'is_points' => 0))->total();
			$ORDER_STATE_SEND = $model_order->where(array('uid' => $this->member_info['uid'], 'order_state' => ORDER_STATE_SEND, 'lock_state' => 0, 'is_spike' => 0, 'is_points' => 0))->total();
			$ORDER_STATE_SUCCESS = $model_order->where(array('uid' => $this->member_info['uid'], 'order_state' => ORDER_STATE_SUCCESS, 'evaluation_state' => 0, 'lock_state' => 0, 'is_spike' => 0, 'is_points' => 0))->total();
			//退款统计
			$result = model('shop_refund_return')->getList(array('buyer_id' => $this->member_info['uid'], 'refund_state' => 1), 'order_sn');
			$refund_order_ids = array();
			if($result['list']){
				foreach($result['list'] as $k => $v){
					$refund_order_ids[] = $v['order_sn'];
				}
			}
			unset($result);
			$order_goods_list = array();
			if ($refund_order_ids) {
				$order_goods_list = $model_order->getOrderGoodsList(array('order_sn' => $refund_order_ids));
			}
			
			$order_data = array(
				'state_new' => $ORDER_STATE_NEW, 
				'state_pay' => $ORDER_STATE_PAY, 
				'state_send' => $ORDER_STATE_SEND, 
				'state_success' => $ORDER_STATE_SUCCESS,
				'state_refund' => count($order_goods_list),
			);
			$account_info = model('distribute_account')->getInfo(array('uid' => $this->member_info['uid']));
			$member_info = array_merge($account_info, $this->member_info);
			$inviter_info = model('member')->getInfo(array('uid' => $this->member_info['inviter_id']), 'nickname') ?: array();
			if (!$inviter_info) {
				$inviter_info['nickname'] = '总店';
			}
			
			$level_info = model('vip_level')->getInfo(array('id' => $this->member_info['level_id']), 'id,level_name,level_sort');
			if (!$level_info) {
				$level_info['level_name'] = '不存在';
				$level_info['level_sort'] = 0;
			}
			if (!$inviter_info) {
				$inviter_info['nickname'] = '总店';
			}
			
			
			//分销总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$get_all_rewards = $this->get_rewards($where);
			
			//公排总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$get_all_public_rewards = $this->get_public_rewards($where);
			
			//分销+公排可提现
			$f_p_all_rewards = $get_all_rewards['rewards'] + $get_all_public_rewards['rewards'];
			$get_used_rewards = $this->get_user_use_money($this->member_info['uid']);
			$f_p_can_rewards = priceFormat($f_p_all_rewards - $get_used_rewards);
			$member_info['f_p_can_rewards'] = $f_p_can_rewards;
			
			$return = array(
				'title' => $this->config['name'],
				'member_info' => $member_info,
				'order_data' => $order_data,
				'inviter_info' => $inviter_info,
				'level_info' => $level_info,
				'telphone' => config('telphone'),
			);
			output_data($return);
		}
	}
	public function pd_logOp() {
		if (IS_API) {
			$model_pd_log = model('pd_log');
			$this->title = '余额明细';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$where = array(
			    'uniacid' => $this->uniacid,
				'lg_member_id' => $this->member_info['uid'],
			);
			if($start_time){
				$where['lg_add_time >='] = strtotime($start_time);
			}
			if($end_time){
				$where['lg_add_time <='] = strtotime($end_time . ' 23:59:59');
			}
			$detail_list_tmp = $model_pd_log->getList($where, '*', 'lg_id desc', 20, input('page', 1, 'intval'));
			$detail_list = $detail_list_tmp['list'];
			foreach($detail_list as $k => $r){
				$detail_list[$k]['lg_add_time'] = date('Y/m/d H:i:s', $r['lg_add_time']);
			}
			$return = array(
				'title' => $this->title,
				'list' => $detail_list,
				'totalpage' => $detail_list_tmp['totalpage'],
				'hasmore' => $detail_list_tmp['hasmore'],
				'available_predeposit' => $this->member_info['available_predeposit'],
			);
			output_data($return);
		}
	}
	public function pd_chongzhi_pageOp() {
		if (IS_API) {
			$this->title = '余额充值';
			$return = array(
				'title' => $this->title,
				'available_predeposit' => $this->member_info['available_predeposit'],
			);
			output_data($return);
		}
	}
	public function pd_chongzhiOp() {
		if (IS_API) {
			$pdr_amount = abs(floatval(input('amount', 0)));
			$payment_code = input('payment_code', '', 'trim');
			if ($pdr_amount <= 0) {
				output_error('充值金额为大于或者等于0.01的数字');
			}
			$data = array();
			$data['uniacid'] = $this->uniacid;
			$data['pdr_sn'] = $pay_sn = logic('predeposit')->makeSn($this->member_info['uid']);
			$data['pdr_member_id'] = $this->member_info['uid'];
			$data['pdr_member_name'] = $this->member_info['nickname'];
			$data['pdr_amount'] = $pdr_amount;
			$data['pdr_add_time'] = TIMESTAMP;
			$insert = model('pd_recharge')->insert($data);
			if ($insert) {
				$only_payment_list = $this->get_payment_list();
				$data = array(
					'pay_sn' => $pay_sn,
					'order_amount' => $pdr_amount,
					'payment_list' => $only_payment_list,
				);
				output_data($data);
			}
		}
	}
	/**
     * 站内余额充值支付
     */
	public function pd_chongzhi_payOp() {
		header('Content-type: text/html; charset=' . CHARSET);
        $order_sn = input('order_sn', '', 'trim');
        if (!preg_match('/^\\d{18}$/', $order_sn)) {
			output_error('订单号错误');
        }
		$payment_code = input('payment_code', '', 'trim');
        if (!in_array($payment_code, array('alipay', 'wxpay_jsapi', 'wxpay_h5', 'wxapp'))) {
			output_error('请选择正确的支付方式');
        }
		// 查询支付单信息
		$pd_info = model('pd_recharge')->where(array('pdr_sn' => $order_sn, 'pdr_member_id' => $this->member_info['uid']))->find();
		if (empty($pd_info)) {
			output_error('该订单不存在');
		}
		if (intval($pd_info['pdr_payment_state'])) {
			output_error('您的订单已经支付，请勿重复支付');
		}
        $pay_info = array(
			'pay_sn' => $order_sn,
			'order_type' => 'pd_order',
			'api_pay_amount' => priceFormat($pd_info['pdr_amount']),
			'payment_code' => $payment_code,
			'subject' => '余额充值',
		);
        // 第三方API支付
		$pay_info['pay_check'] = input('pay_check', 0);
        $this->_new_pay_api($pay_info);
	}
	public function upload_headimgOp() {
		$uid = $this->member_info['uid'];
        // 上传图片
		$default_dir = UPLOADFILES_PATH . '/headimg/';
		$file_name = $uid . '.jpg';
        $upload = new \lib\uploadfile();
        $upload->set('default_dir', $default_dir);
		$upload->set('file_name', $file_name);
        $upload->set('max_size', config('image_max_filesize'));
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
		$file = input('name', 'file');
        $result = $upload->upfile($file);
        if (!$result) {
			output_error($upload->error);
        }
		$default_url = UPLOADFILES_URL . '/headimg/';
		$fullname = $default_dir . $file_name;
		//缩略图
		list($width, $height) = getimagesize($fullname);
		$resizeImage = new lib\resizeimage();
		$resizeImage->newImg($fullname, $width, $height, 360, '.', dirname($fullname), true);
		
		$file_url = $default_url . $file_name;
		if (config('attachment_open')) {
    		$attachment_type = config('attachment_host_type');
    		if ($attachment_type == 2) {
    			save_image_to_qiniu($fullname, $file_url);
    		}
		}
		$data['file_url'] = $file_url . '?time=' . time();
		$poster_img_file = UPLOADFILES_PATH . '/poster/poster4/poster_' . $uid . '.jpg';
		if (file_exists($poster_img_file)) {
			unlink($poster_img_file);
		}
		model('member')->edit(array('uid' => $uid), array('headimg' => $file_url));
		output_data($data);
	}
	public function setting_pageOp() {
		if (IS_API) {
			$config = array(
				'sms_status' => $this->config['sms_status']
			);
			output_data(array('title' => '个人信息', 'member_info' => $this->member_info, 'config' => $config));
		}
	}
	public function setting_edit_itemOp() {
		$k_name = input('k_name', '', 'trim');
		$v_value = input('v_value', '', 'trim');
		$data = array();
		if ($k_name == 'mobile') {
			$obj_validate = new lib\validate();
			$obj_validate->validateparam = array(array('input' => $v_value, 'require' => 'true', 'validator' => 'mobile', 'message' => '请填写正确的手机号'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$member_info = model('member')->getInfo(array('mobile' => $v_value));
			if ($member_info) {
				output_error('手机号已经存在');
			}
		} else if ($k_name == 'nickname') {
			$v_value = filterEmoji($v_value);
			if (!$v_value) {
                output_error('昵称不能为空');
            }
			$data['truename'] = $v_value;
		} else if ($k_name == 'paypwd') {
			if(!$v_value){
				output_error('请填写支付密码');
			}
			$v_value = f_hash($v_value);
		} else if ($k_name == 'password') {
			if(!$v_value){
				output_error('请填写登录密码');
			}
			$v_value = f_hash($v_value);
		}
		if ($k_name == 'mobile' || $k_name == 'password' || $k_name == 'paypwd') {
			if ($k_name == 'mobile') {
				$mobile = $v_value;
				$log_type = 6;
			}
			if ($k_name == 'password') {
				$mobile = $this->member_info['mobile'];
				$log_type = 5;
			}
			if ($k_name == 'paypwd') {
				$mobile = $this->member_info['mobile'];
				$log_type = 4;
			}
			$vcode = input('vcode', '', 'trim');
			if (!$vcode && $this->config['sms_status'] == 1) {
				output_error('验证码不能为空');
			}
			if ($this->config['sms_status'] == 1) {
				$model_sms_log = model('sms_log');
				$condition['log_phone'] = $mobile;
				$condition['log_type'] = $log_type;
				$sms_log = $model_sms_log->getInfo($condition);
				if (empty($sms_log) || $sms_log['add_time'] < TIMESTAMP - 1800) {
					// 半小时内进行验证为有效
					output_error('动态码错误或已过期，重新输入！');
				}
				if ($sms_log['log_captcha'] != $vcode){
					output_error('验证码不正确！');
				}
			}
		}
		$data[$k_name] = $v_value;
		$flag = model('member')->edit(array('uid' => $this->member_info['uid']), $data);
		output_data(1);
	}
	public function points_logOp() {
		if (IS_API) {
			$points_model = model('points_log');
			$this->title = '积分明细';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$where = array(
				'pl_memberid' => $this->member_info['uid'],
			);
			if($start_time){
				$where['pl_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['pl_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			$detail_list_tmp = $points_model->getList($where, '*', 'pl_id desc', 20, input('page', 1, 'intval'));
			$detail_list = $detail_list_tmp['list'];
			foreach($detail_list as $k => $r){
				$detail_list[$k]['pl_addtime'] = date('Y/m/d H:i:s', $r['pl_addtime']);
			}
			$return = array(
				'title' => $this->title ?: '积分明细',
				'list' => $detail_list,
				'totalpage' => $detail_list_tmp['totalpage'],
				'hasmore' => $detail_list_tmp['hasmore'],
				'points' => $this->member_info['points'],
			);
			output_data($return);
		}
	}
	//签到
	public function signinOp() {
		if (IS_API) {
			//检验是否能签到
			$totime = strtotime(date('Ymd'));
			$condition = array();
			$condition['pl_memberid'] = $this->member_info['uid'];
			$condition['adddate'] = $totime;
			$condition['pl_stage'] = 'signin';
			$points_model = model('points_log');
			$signin_flag = $points_model->where($condition)->total();
			$this->title = '签到领积分';
			base\token::$config['token_name'] = 'usersignin';
			list($token_name, $token_key, $token_value) = base\token::getToken();
			$return = array(
				'title' => $this->title,
				'points' => $this->member_info['points'],
				'points_signin' => config('points_signin'),
				'signin_flag' => $signin_flag,
				'token_name' => $token_name,
				'token_value' => $token_key . '_' . $token_value,
			);
			output_data($return);
		}
	}
	public function signin_addOp() {
		$totime = strtotime(date('Ymd'));
        $condition = array();
        $condition['pl_memberid'] = $this->member_info['uid'];
		$condition['adddate'] = $totime;
		$condition['pl_stage'] = 'signin';
        
        $points_model = model('points_log');
        $check = $points_model->where($condition)->total();
        if (!empty($check)) {
            output_error('已签到');
        }
		base\token::$config['token_name'] = 'usersignin';
		if (!base\token::checkToken()) {
			output_error('已签到');
		}
        $points_signin = intval(config('points_signin'));
        $insertarr['pl_memberid'] = $this->member_info['uid'];
        $insertarr['pl_membername'] = $this->member_info['nickname'];
        $insertarr['pl_points'] = $points_signin;
        $insertarr['pl_addtime'] = time();
        $return = $points_model->savePointsLog('signin', $insertarr, true);
        if ($return) {
            $points_signin = $points_signin + $this->member_info['points'];
            output_data(array('points' => $points_signin));
        }
    }
	public function signin_logOp() {
		if (IS_API) {
			$points_model = model('points_log');
			$where = array(
				'pl_memberid' => $this->member_info['uid'],
				'pl_stage' => 'signin',
			);
			$detail_list_tmp = $points_model->getList($where, '*', 'pl_id desc', 20, input('page', 1, 'intval'));
			$detail_list = array();
			foreach($detail_list_tmp['list'] as $key => $value) {
				$detail_list[$key]['sl_id'] = $value['pl_id'];
				$detail_list[$key]['sl_memberid'] = $value['pl_memberid'];
				$detail_list[$key]['sl_membername'] = $value['pl_membername'];
				$detail_list[$key]['sl_addtime'] = $value['pl_addtime'];
				$detail_list[$key]['sl_points'] = $value['pl_points'];
				$detail_list[$key]['sl_desc'] = $value['pl_desc'];
				$detail_list[$key]['sl_addtime_text'] = date('Y-m-d H:i:s', $value['pl_addtime']);
			}
			$return = array(
				'list' => $detail_list,
				'totalpage' => $detail_list_tmp['totalpage'],
				'hasmore' => $detail_list_tmp['hasmore'],
			);
			output_data($return);
		}
	}
	//地区代理申请
	public function apply_agent_pageOp() {
	    $result = model('area_level')->getList(array('uniacid' => $this->uniacid), '*', 'level_sort DESC');
		foreach($result['list'] as $level_info){
			$level_list[] = array(
			    'name' => $level_info['level_name'],
			    'id' => $level_info['id'],
		    );
		}
		$result = model('area')->getList();
		$list = $result['list'];
		unset($result);
		$area_json = $this->_get_tree_list($list);
		$reurn = array(
		    'title' => '区域代理申请', 
		    'level_list' => $level_list,
		    'area_json' => $area_json,
		);
		output_data($reurn);
	}
	public function apply_agent_submitOp() {
	    $model_account = model('area_account');
	    $check = $model_account->getInfo(array('uid' => $this->member_info['uid']));
	    if ($check && $check['status'] == 0) {
	        output_error('你的申请还在审核，请勿重复提交');
	    }
	    if ($check && $check['status'] == 1) {
	        output_error('你已经是代理了，请勿重复提交');
	    }
	    $account_array = array();
		$account_array['uniacid'] = $this->uniacid;
		$account_array['uid'] = $this->member_info['uid'];
		$address = input('address', '', 'trim');
		$address_value = input('address_value', '', 'trim');
		if (!$address) {
			output_error('请选择地区');
		}
		$address_arr = strpos($address, ',') !== false ? explode(',', trim($address)) : explode(' ', trim($address));
		$address_value_arr = explode(' ', trim($address_value));
		$check_where['province_name'] = $account_array['province_name'] = $address_arr[0];
		$check_where['city_name'] = $account_array['city_name'] = $address_arr[1];
		$check_where['county_name'] = $account_array['county_name'] = $address_arr[2];
		//$account_array['province_id'] = $address_value_arr[0];
		//$account_array['city_id'] = $address_value_arr[1];
        //$account_array['area_id'] = $address_value_arr[2];
		$account_array['area_info'] = $address;
		$check_where['level_id'] = $account_array['level_id'] = input('level_id', 0, 'intval');
		$check_where['status'] = 1;
		$check = $model_account->where($check_where)->total();
		if ($check) {
			output_error('您申请的地区代理已经存在了，请勿重新申请');
		}
		$account_array['add_time'] = time();
		$state = $model_account->add($account_array); 
		output_data(1);
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
	public function pd_zhuanzhang_pageOp(){
		if(IS_API){
			$this->title = '转账';
			$return = array(
				'title' => $this->title,
				'available_predeposit' => $this->member_info['available_predeposit'],
			);
			output_data($return);
		}
	}
	public function pd_zhuanzhangOp() {
		if (IS_API) {
			$logic_pd = logic('predeposit');
			$money = abs(floatval(input('amount', 0)));
			$to_uid = intval(trim(input('mobile', ''), config('uid_pre')));
			$pay_pass = input('pay_pass', '', 'trim');
			if (!$to_uid) {
				output_error('请填写对方ID号');
			}
			if ($money <= 0) {
				output_error('请填写正确的转账金额');
			}
			if (!$pay_pass || f_hash($pay_pass) != $this->member_info['paypwd']) {
				output_error('支付密码不正确');
			}
			if ($to_uid == $this->member_info['uid']) {
				output_error('禁止给自己转账');
			}
			$to_member = model('member')->where(array('uid' => $to_uid))->find();
			if (!$to_member) {
				output_error('账号不存在');
			}
			$to_account = model('distribute_account')->where(array('uid' => $to_member['uid']))->find();
			$to_dis_path = explode(',', $to_account['dis_path']);
			$from_account = model('distribute_account')->where(array('uid' => $this->member_info['uid']))->find();
			$from_dis_path = explode(',', $from_account['dis_path']);
			$dis_path = array_merge($to_dis_path, $from_dis_path);
			if (!in_array($this->member_info['uid'], $dis_path)) {
			    //output_error('只能给自己这一条线的上下级转账');
			}
			if ($this->member_info['available_predeposit'] < $money) {
				output_error('您的余额不足');
			}
			$data_pd = array();
			$data_pd['uid'] = $this->member_info['uid'];
			$data_pd['member_name'] = $this->member_info['nickname'];
			$data_pd['admin_name'] = $to_member['nickname'];
			$data_pd['amount'] = $money;
			$data_pd['desc'] = '转出给' . $to_member['nickname'] . '【ID：' . (config('uid_pre') . padNumber($to_uid)) . '，手机号：' . $to_member['mobile'] . '】';
			$insert = $logic_pd->changePd('zhuanzhang_out', $data_pd);
			if ($insert) {
				$data_pd = array();
				$data_pd['uid'] = $to_member['uid'];
				$data_pd['member_name'] = $to_member['nickname'];
				$data_pd['admin_name'] = $this->member_info['nickname'];
				$data_pd['amount'] = $money;
				$data_pd['desc'] = '[' . $this->member_info['nickname'] . ']转入';
				$insert = $logic_pd->changePd('zhuanzhang_in', $data_pd);
				output_data('转账成功');
			}
			output_error('操作失败');
		}
	}
	public function offline_pd_chongzhi_pageOp() {
		if (IS_API) {
			$this->title = '线下余额充值';
			$return = array(
				'title' => $this->title,
				'available_predeposit' => $this->member_info['available_predeposit'],
				'bank_name' => config('bank_name'),
				'bank_address' => config('bank_address'),
				'bank_username' => config('bank_username'),
				'bank_no' => config('bank_no'),
				'offline_type' => config('offline_type'),
				'zhifubao_account' => config('zhifubao_account'),
				'zhifubao_name' => config('zhifubao_name'),
				'zhifubao_ercode' => tomedia(config('zhifubao_ercode')),
				'weixin_ercode' => tomedia(config('weixin_ercode')),
			);
			output_data($return);
		}
	}
	public function offline_pd_chongzhiOp() {
		if (IS_API) {
			$pdr_amount = abs(floatval(input('amount', 0)));
			$payment_code = input('payment_code', '', 'trim');
			if ($pdr_amount <= 0) {
				output_error('充值金额为大于或者等于0.01的数字');
			}
			//付款凭证
			$images = trim(input('images', ''), '{,}');
			if (empty($images)) {
				output_error('请上传付款凭证');
			}
			$images = explode(',', $images);
			if (empty($images)) {
				output_error('请上传付款凭证');
			}
			$data = array();
			$data['uniacid'] = $this->uniacid;
			$data['pdr_sn'] = $pay_sn = logic('predeposit')->makeSn($this->member_info['uid']);
			$data['pdr_member_id'] = $this->member_info['uid'];
			$data['pdr_member_name'] = $this->member_info['nickname'];
			$data['pdr_amount'] = $pdr_amount;
			$data['pdr_add_time'] = TIMESTAMP;
			$data['imgs'] = serialize($images);
			$data['order_type'] = 1;
			$insert = model('pd_recharge')->insert($data);
			if ($insert) {
				/*$access_token = logic('weixin_token')->get_access_token($this->config);
				logic('weixin_message')->addpdToadmin($access_token, $this->config);*/
				$data = array(
					'pay_sn' => $pay_sn,
					'order_amount' => $pdr_amount,
				);
				output_data($data);
			}
		} else {
			$this->assign('title', '余额充值');
			$this->display();
		}
	}
	public function s_up_img_updateOp() {
		if (IS_API) {
			$member_id = $this->member_info['uid'];
			$file_name = input('get.name', '') ? input('get.name', '') : input('post.name', '');
			
			if (!empty($_FILES[$file_name]['name'])) {
				$upload = new lib\uploadfile();
				$upload->set('default_dir', front_upload_img_dir($member_id) . '/offline_pd_chongzhi/' . $upload->getSysSetPath());
				$upload->set('max_size', config('image_max_filesize'));
				$upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
				$upload->set('fprefix', $member_id);
				$result = $upload->upfile($file_name);
				if (!$result) {
					output_error($upload->error);
				} else {
					$data['src'] = front_upload_img_url($member_id) . '/offline_pd_chongzhi/' . $upload->getSysSetPath() . $upload->file_name;
					list($width, $height) = fxy_getimagesize($data['src']);
					$data['width'] = $width;
					$data['height'] = $height;
					output_data($data);
				}
			} else {
				output_error('缺少参数');
			}
		}
	}
	public function kefuOp() {
	    $info = array(
	        'telphone' => config('telphone'),
	        'kf_ercode' => tomedia(config('kf_ercode')),
	    );
		output_data(array('info' => $info));
	}
}