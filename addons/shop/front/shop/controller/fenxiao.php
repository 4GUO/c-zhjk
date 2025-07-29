<?php
namespace shop\controller;
use lib;
class fenxiao extends member {
	public function __construct() {
		parent::_initialize();
		if (IS_API) {
			if (!$this->member_info['is_distributor']) {
				output_error('您无推广权限，请购买商品', array('redirect' => '/pages/user/index'));
			}
		}
	}
	public function indexOp() {
		if (IS_API) {
		    //分销今日收入
			$today = lib\timer::today();
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$where['detail_addtime >='] = $today[0];
			$where['detail_addtime <='] = $today[1];
			$get_today_rewards = $this->get_rewards($where);
			//分销总收入
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$get_all_rewards = $this->get_rewards($where);
			//累计获得提货券的数量
			$get_all_tihuoquan = model('shop_goods_tihuoquan')->where(array('uid' => $this->member_info['uid']))->total();
			$array = array(
				'title' => '分享中心',
				'get_today_rewards' => priceFormat($get_today_rewards['rewards']),
				'get_all_rewards' => priceFormat($get_all_rewards['rewards']),
				'bonus_name_goods' => $this->config['bonus_name_goods'],
				'get_all_tihuoquan' => $get_all_tihuoquan,
				'get_all_fenhong_quan' => $this->member_info['total_fenhong_quan'],//累计获得分红券的数量
				'get_fenhong_quan' => $this->member_info['fenhong_quan'],//获得的分红券，但是还未分红的分红券数量
				'get_used_fenhong_quan' => $this->member_info['total_fenhong_quan'] - $this->member_info['fenhong_quan'],
			);
			output_data($array);
		}
	}
	public function commission_recordOp() {
		if (IS_API) {
			$type = input('type', 0, 'intval');
			if ($type == 1) {
				$this->title = '平级奖励';
			} else {
				$this->title = '报单奖励';
			}
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_detail_other');
			//状态
			$_STATUS = array(
				ORDER_STATE_NEW => '未付款',
				ORDER_STATE_PAY => '已付款',
				ORDER_STATE_SEND => '已发货',
				ORDER_STATE_SUCCESS => '已完成'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if ($type) {
				$where['detail_type'] = $type;
			}
			if($start_time){
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['detail_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			$r = model('distribute_detail_other')->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime'])
				);
			}
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
				//'set' => $this->config,
			);
			output_data($return);
		}
	}
	public function fenhong_recordOp() {
		if (IS_API) {
			$this->title = '分红';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_fenhong_record_detail');
			//状态
			$_STATUS = array(
				ORDER_STATE_NEW => '未付款',
				ORDER_STATE_PAY => '已付款',
				ORDER_STATE_SEND => '已发货',
				ORDER_STATE_SUCCESS => '已完成'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if($start_time){
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['detail_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			
			$type = input('type', 0, 'intval');
			if ($type) {
			    $where['type'] = $type;
			}
			if ($type == 1) {
			    $this->title = '分红';
			}
			if ($type == 2) {
			    $this->title = '绩效奖励';
			}
			if ($type == 3) {
			    $this->title = '零售分红';
			}
			$r = model('distribute_fenhong_record_detail')->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime'])
				);
			}
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
				//'set' => $this->config,
			);
			output_data($return);
		}
	}
	//商品分销
	public function goods_commission_recordOp() {
		if(IS_API){
			$this->title = $this->config['bonus_name_goods'] . '奖励';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_detail');
			//状态
			$_STATUS = array(
				ORDER_STATE_NEW => '未付款',
				ORDER_STATE_PAY => '已付款',
				ORDER_STATE_SEND => '已发货',
				ORDER_STATE_SUCCESS => '已完成'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if($start_time){
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['detail_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			$r = model('distribute_detail')->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime'])
				);
			}
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
				'set' => $this->config,
			);
			output_data($return);
		}
	}
	public function area_commission_recordOp() {
		if (IS_API) {
			$area_account = model('area_account')->getInfo(array('uid' => $this->member_info['uid']));
			if (!$area_account) {
				output_error('您还不是代理，无权查看！', array('redirect' => '/pages/fenxiao/index'));
			}
			$this->title = '地区代理奖励明细';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_area_record_detail');
			//状态
			$_STATUS = array(
				ORDER_STATE_NEW => '未付款',
				ORDER_STATE_PAY => '已付款',
				ORDER_STATE_SEND => '已发货',
				ORDER_STATE_SUCCESS => '已完成'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if($start_time){
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['detail_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			$r = $model_distribute_detail->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime'])
				);
			}
			
			$area_level = model('area_level')->getInfo(array('id' => $area_account['level_id']));
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
				'area_level' => $area_level,
				//'set' => $this->config,
			);
			output_data($return);
		}
	}
	public function turntable_commission_recordOp() {
		if(IS_API){
			$this->title = '活动奖励';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_turntable_record_detail');
			//状态
			$_STATUS = array(
				ORDER_STATE_NEW => '未付款',
				ORDER_STATE_PAY => '已付款',
				ORDER_STATE_SEND => '已发货',
				ORDER_STATE_SUCCESS => '已完成'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = ORDER_STATE_SUCCESS;
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if($start_time){
				$where['detail_addtime >='] = strtotime($start_time);
			}
			if($end_time){
				$where['detail_addtime <='] = strtotime($end_time . ' 23:59:59');
			}
			$r = $model_distribute_detail->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime']),
				);
			}
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
			);
			output_data($return);
		}
	}
	
	//复购见单奖励记录
	public function fgjdjl_recordOp() {
		if(IS_API){
			$this->title = '复购见单奖励';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			$model_distribute_detail = model('distribute_fgjdjl_record_detail');
			//状态
			$_STATUS = array(
				10 => '已发放',
				20 => '已退回'
			);
			
			$lists = array();
			
			$total_money = 0;
			//获得佣金记录
			$where = array();
			$where['detail_status'] = 10; // 已发放
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			if($start_time){
				$where['detail_addtime >='] = $start_time . ' 00:00:00';
			}
			if($end_time){
				$where['detail_addtime <='] = $end_time . ' 23:59:59';
			}
			$r = $model_distribute_detail->getInfo($where, 'SUM(detail_bonus) as money');
			$total_money = empty($r['money']) ? 0 : $r['money'];
			$where['detail_status'] = array(10, 20); // 已发放和已退回
			$record_list = $model_distribute_detail->getList($where, '*', 'detail_id desc', 20, input('page', 1, 'intval'));
			foreach($record_list['list'] as $key=>$value){
				$lists[] = array(
					'money' => $value['detail_bonus'],
					'status' => $_STATUS[$value['detail_status']],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc'],
					'addtime' => $value['detail_addtime'],
					'order_sn' => $value['order_sn'],
					'order_amount' => $value['order_amount'],
					'user_orders' => $value['user_orders'],
					'parent_orders' => $value['parent_orders'],
					'from_nickname' => $value['from_nickname']
				);
			}
			$return = array(
				'title' => $this->title,
				'total_money' => $total_money,
				'list' => $lists,
				'totalpage' => $record_list['totalpage'],
				'hasmore' => $record_list['hasmore'],
			);
			output_data($return);
		}
	}
	
	/*提现部分*/
	public function widthdraw_method_listOp() {
		if (IS_API) {
			$type = input('type', 0, 'intval');
			$where = array();
			//分销+公排
			$where = array();
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$get_all_rewards = $this->get_rewards($where);
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$get_all_public_rewards = $this->get_public_rewards($where);
			//分销+公排总奖励
			$f_p_all_rewards = $get_all_rewards['rewards'] + $get_all_public_rewards['rewards'];
			//分销+公排已提现
			$get_used_rewards = $this->get_user_use_money($this->member_info['uid']);
			//分销+公排可提现
			$f_p_can_rewards = priceFormat($f_p_all_rewards - $get_used_rewards);
			$sys_list = model('withdraw_method')->where(array('uniacid' => $this->uniacid, 'method_status' => 1))->select();
			
			\base\token::$config['token_name'] = 'widthdrawtoken';
			list($token_name, $token_key, $token_value) = \base\token::getToken();
			$return = array(
				'title' => '提现', 
				'sys_list' => $sys_list, 
				'total_bouns' => priceFormat($this->member_info['available_predeposit']),
				'token_name' => $token_name,
				'token_value' => $token_key . '_' . $token_value,
			);
			output_data($return);
		}
	}
	public function get_last_widthdraw_methodOp() {
		$method_code = input('method_code', '');
		$method_info = model('withdraw_method')->where(array('uniacid' => $this->uniacid, 'method_code' => $method_code))->find();
		$member_method_info = model('withdraw_method_member')->getInfo(array('uid' => $this->member_info['uid'], 'method_code' => $method_code));
		if ($member_method_info) {
			$member_method_info = model('withdraw_method_member')->getInfo(array('uid' => $this->member_info['uid'], 'method_code' => $method_code));
			if ($member_method_info['method_code'] == 'wxzhuanzhang') {
				$last_widthdraw_info['last_weixin_account'] = $member_method_info['method_no'];
				$last_widthdraw_info['last_weixin_realname'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_weixin_ercode'] = $member_method_info['ercode'];
				$last_widthdraw_info['is_default'] = $member_method_info['is_default'];
			} else if ($member_method_info['method_code'] == 'alipay') {
				$last_widthdraw_info['last_alipay_account'] = $member_method_info['method_no'];
				$last_widthdraw_info['last_alipay_name'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_alipay_ercode'] = $member_method_info['ercode'];
				$last_widthdraw_info['is_default'] = $member_method_info['is_default'];
			} else if ($member_method_info['method_code'] == 'bank') {
				$last_widthdraw_info['last_bank_bankname'] = $member_method_info['method_bank'];
				$last_widthdraw_info['last_bank_name'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_bank_account'] = $member_method_info['method_no'];
				$last_widthdraw_info['is_default'] = $member_method_info['is_default'];
			}
		} else {
			$last_widthdraw_info = model('withdraw_record')->where(array('method_code' => $method_code, 'uid' => $this->member_info['uid']))->order('record_id DESC')->find();
			if ($last_widthdraw_info) {
				if ($last_widthdraw_info['method_code'] == 'wxzhuanzhang') {
					$last_widthdraw_info['last_weixin_account'] = $last_widthdraw_info['method_no'];
					$last_widthdraw_info['last_weixin_realname'] = $last_widthdraw_info['method_name'];
					$last_widthdraw_info['last_weixin_ercode'] = $last_widthdraw_info['ercode'];
				} else if ($last_widthdraw_info['method_code'] == 'alipay') {
					$last_widthdraw_info['last_alipay_account'] = $last_widthdraw_info['method_no'];
					$last_widthdraw_info['last_alipay_name'] = $last_widthdraw_info['method_name'];
					$last_widthdraw_info['last_alipay_ercode'] = $last_widthdraw_info['ercode'];
				} else if ($last_widthdraw_info['method_code'] == 'bank') {
					$last_widthdraw_info['last_bank_bankname'] = $last_widthdraw_info['method_bank'];
					$last_widthdraw_info['last_bank_name'] = $last_widthdraw_info['method_name'];
					$last_widthdraw_info['last_bank_account'] = $last_widthdraw_info['method_no'];
				}
				$last_widthdraw_info['is_default'] = 1;
			} else {
				$last_widthdraw_info = array();
			}
		}
		$return = array(
			'method_info' => $method_info, 
			'last_widthdraw_info' => $last_widthdraw_info, 
		);
		output_data($return);
	}
	public function s_up_img_updateOp() {
		if (IS_API) {
			$method_code = input('get.method_code', '', 'trim') ? input('get.method_code', '', 'trim') : input('post.method_code', '', 'trim');
			$uid = $this->member_info['uid'];
			// 上传图片
			$default_dir = UPLOADFILES_PATH . '/widthdraw_method/';
			$file_name = $method_code . '_' . $uid . '.jpg';
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
			$default_url = UPLOADFILES_URL . '/widthdraw_method/';
			$fullname = $default_dir . $file_name;
			
			$file_url = $default_url . $file_name;
			if (config('attachment_open')) {
				$attachment_type = config('attachment_host_type');
				if ($attachment_type == 2) {
					save_image_to_qiniu($fullname, $file_url);
				}
			}
			$data['file_url'] = $file_url;
			output_data($data);
		}
	}
	public function widthdraw_save_methodOp() {
		if (IS_API) {
			$method_code = input('method_code', '', 'trim');
			if (!in_array($method_code, array('wxzhuanzhang', 'alipay', 'bank'))) {
				output_error('请选择收款方式！');
			}
			switch($method_code) {
				case 'wxzhuanzhang':
					$method_title = '微信';
					$method_no = input('weixinaccount', '', 'trim');
					$method_name = input('weixinusername', '', 'trim');
					$ercode = input('weixin_ercode', '', 'trim');
					if (!$method_no) {
						output_error('微信号不能为空');
					}
					if (!$method_name) {
						output_error('收款人不能为空');
					}
					if (!$ercode) {
						output_error('请上传收款码');
					}
					break;
				case 'alipay':
					$method_title = '支付宝';
					$method_no = input('alipayaccount', '', 'trim');
					$method_name = input('alipayusername', '', 'trim');
					$ercode = input('alipay_ercode', '', 'trim');
					if (!$method_no) {
						output_error('支付宝账号不能为空');
					}
					if (!$method_name) {
						output_error('收款人不能为空');
					}
					if (!$ercode) {
						output_error('请上传收款码');
					}
					break;
				case 'bank':
					$method_title = '银行卡';
					$method_no = input('bankaccount', '', 'trim');
					$method_name = input('bankusername', '', 'trim');
					$method_bank = input('bankname', '', 'trim');
					$ercode = '';
					if (!$method_no) {
						output_error('银行卡账号不能为空');
					}
					if (!$method_name) {
						output_error('收款人不能为空');
					}
					break;
			}
			$is_default = input('is_default', false) ? 1 : 0;
			if ($is_default) {
				model('withdraw_method_member')->where(array('uid' => $this->member_info['uid']))->update(array('is_default' => 0));
			}
			$user_method = array(
				'uniacid' => $this->uniacid,
				'uid' => $this->member_info['uid'],
				'method_code' => $method_code,
				'method_title' => $method_title,
				'method_no' => $method_no,
				'method_name' => $method_name,
				'method_bank' => isset($method_bank) ? $method_bank : '',
				'method_position' => isset($method_position) ? $method_position : '',
				'ercode' => $ercode,
				'is_default' => $is_default,
			);
			$member_method_info = model('withdraw_method_member')->getInfo(array('uid' => $this->member_info['uid'], 'method_code' => $method_code));
			if ($member_method_info) {
				model('withdraw_method_member')->where(array('uid' => $this->member_info['uid'], 'method_code' => $method_code))->update($user_method);
			} else {
				model('withdraw_method_member')->add($user_method);
			}
			output_data('1');
		}
	}
	public function widthdraw_supplyOp() {
		if (IS_API) {
			if ($this->config['tixian_day_start'] && $this->config['tixian_day_end']) {
				if ($this->config['tixian_day_start'] > date('j') || $this->config['tixian_day_end'] < date('j')) {
					output_error($this->config['tixian_tip']);
				}
			} else if ($this->config['tixian_day_start'] == 0 && $this->config['tixian_day_end']) {
				if($this->config['tixian_day_end'] < date('j')){
					output_error($this->config['tixian_tip']);
				}
			} else if ($this->config['tixian_day_start'] && $this->config['tixian_day_end'] == 0) {
				if ($this->config['tixian_day_start'] > date('j')) {
					output_error($this->config['tixian_tip']);
				}
			}
			$model_distribute_detail = model('distribute_detail');
			$money = input('money', 0);
			if (empty($money)) {
				output_error('金额不能为空');
			}
			
			if (!is_numeric($money)) {
				output_error('提现金额请填写数字');
			}
			
			$total = priceFormat($money);
			if ($total < 0.01) {
				output_error('提现金额不得小于0.01元');
			}
			$type = input('type', 1, 'intval');
			$where = array();
			$where['uniacid'] = $this->uniacid;
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = array(ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$get_all_rewards = $this->get_rewards($where);
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['detail_status'] = 1;
			$get_all_public_rewards = $this->get_public_rewards($where);
			//分销+公排总奖励
			//$f_p_all_rewards = $get_all_rewards['rewards'] + $get_all_public_rewards['rewards'];
			//分销+公排已提现
			//$get_used_rewards = $this->get_user_use_money($this->member_info['uid']);
			//分销+公排可提现
			//$f_p_can_rewards = priceFormat($f_p_all_rewards - $get_used_rewards);
			//if ($f_p_can_rewards <= 0 || $f_p_can_rewards < $total) {
			//	output_error('可提现金额不足');
			//}
			if ($this->member_info['available_predeposit'] < $total) {
				output_error('可提现金额不足');
			}
			
			$method_code = input('method_code', '', 'trim');
			if (!in_array($method_code, array('wxzhuanzhang', 'alipay', 'bank'))) {
				output_error('请选择提现方式！');
			}
			switch($method_code) {
				case 'wxzhuanzhang':
					$method_title = '微信提现';
					$method_no = '';
					$method_name = input('weixinusername', '', 'trim');
					break;
				case 'alipay':
					$method_title = '支付宝提现';
					$method_no = input('alipayaccount', '', 'trim');
					$method_name = input('alipayusername', '', 'trim');
					break;
				case 'bank':
					$method_title = '银行卡提现';
					$method_no = input('bankaccount', '', 'trim');
					$method_name = input('bankusername', '', 'trim');
					$method_bank = input('bankname', '', 'trim');
					break;
			}
			$user_method = array(
				'method_code' => $method_code,
				'method_title' => $method_title,
				'method_no' => $method_no,
				'method_name' => $method_name,
				'method_bank' => isset($method_bank) ? $method_bank : '',
				'method_position' => isset($method_position) ? $method_position : '',
			);
			
			$admin_method = model('withdraw_method')->getInfo(array('uniacid' => $this->uniacid, 'method_code' => $user_method['method_code'], 'method_status' => 1));
			if (!$admin_method) {
				output_error('系统未开放此提现方式');
			}
			
			//最小值限制
			if ($admin_method['method_min'] > 0) {
				if($admin_method['method_min'] > $total){
					output_error('最小提现金额为' . $admin_method['method_min'] . '元');
					exit;
				}
			}
			
			//最大值限制
			if ($admin_method['method_max'] > 0) {
				if($admin_method['method_max'] < $total){
					output_error('最大提现金额为' . $admin_method['method_max'] . '元');
					exit;
				}
			}
			
			//获得手续费
			$fee = 0;
			if ($admin_method['method_fee'] > 0) {
				$fee = $total * $admin_method['method_fee'] * 0.01;
				$fee = priceFormat($fee);
			}
			
			//获得转入余额
			$yue = 0;
			if ($admin_method['method_yue'] > 0) {
				$yue = $total * $admin_method['method_yue'] * 0.01;
				$yue = priceFormat($yue);
			}
			
			//第三方需转账金额
			$amount = $total - $fee - $yue;
			if ($user_method['method_code']== 'wxzhuanzhang' && $amount < 1) {
				output_error('微信转账提现金额需大于1元');
			}
			
			//是否需要审核
			$is_check = $admin_method['method_check'];
			
			$model = model();
            $model->beginTransaction();
			try {
				$record_data = array(
					'uniacid' => $this->uniacid,
					'uid' => $this->member_info['uid'],
					'method_code' => $user_method['method_code'],
					'method_title' => $user_method['method_title'],
					'method_name' => $user_method['method_name'],
					'method_no' => $user_method['method_no'],
					'method_bank' => $user_method['method_bank'],
					'method_position' => $user_method['method_position'],
					'record_total' => $total,
					'record_fee' => $fee,
					'record_yue' => $yue,
					'record_amount' => $amount,
					'record_addtime' => time(),
					'record_status' => 0,
					'type' => $type,
				);
				$record_id = model('withdraw_record')->add($record_data);
				if ($record_id) {
					//微信红包处理
					if(($user_method['method_code'] == 'wxhongbao' || $user_method['method_code'] == 'wxzhuanzhang') && $is_check == 0) {
					    if ($this->member_info['openid'] == '') {
							throw new \Exception('你不是微信用户！');
						}
						$inc_file = COMMON_PATH . '/vendor/WeChatDeveloper/include.php';
                    	if (!is_file($inc_file)) {
                    		throw new \Exception('支付SDK不存在');
                    	}
                    	require $inc_file;
						if ($this->client_type == 'wxapp') {
							$mb_payment_info = model('mb_payment')->where(array('payment_code' => 'wxapp', 'payment_state' => 1))->find();
							$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
							if (empty($payment_config)) {
								throw new \Exception('小程序支付方式未开启');
							}
							$config = array(
                                'token' => config('wechat_token'),
                                'appid' => config('wxappid'),
                                'appsecret' => config('wxappsecret'),
                                'encodingaeskey' => config('wechat_encoding'),
                                // 配置商户支付参数（可选，在使用支付功能时需要）
                                'mch_id' => $payment_config['mchid'],
                                'mch_key' => $payment_config['signkey'],
                                // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                                'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                                'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                                // 缓存目录配置（可选，需拥有读写权限）
                                'cache_path' => '',
                            );
						} else {
							$mb_payment_info = model('mb_payment')->where(array('payment_code' => 'wxpay_jsapi', 'payment_state' => 1))->find();
							$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
							if (empty($payment_config)) {
								throw new \Exception('wxpay_jsapi支付方式未开启');
							}
							$config = array(
                                'token' => config('wechat_token'),
                                'appid' => config('wechat_appid'),
                                'appsecret' => config('wechat_appsecret'),
                                'encodingaeskey' => config('wechat_encoding'),
                                // 配置商户支付参数（可选，在使用支付功能时需要）
                                'mch_id' => $payment_config['partnerId'],
                                'mch_key' => $payment_config['apiKey'],
                                // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                                'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                                'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                                // 缓存目录配置（可选，需拥有读写权限）
                                'cache_path' => '',
                            );
						}
						// 创建接口实例
                        $wechat = \WeChat\Pay::instance($config);
                        $partner_trade_no = $config['mch_id'] . date('YmdHis') . rand(1000, 9999);
						$options = array(
                            'partner_trade_no' => $partner_trade_no,
                            'openid' => $this->member_info['openid'],
                            'check_name' => 'NO_CHECK',
                            'amount' => (int) ($record_data['record_amount'] * 100),
                            'desc' => '提成发放',
                            'spbill_create_ip' => get_server_ip(),
                        );
                        $payment_return = $wechat->createTransfers($options);
                        //lib\logging::write(var_export($payment_return, true));
                        if ($payment_return['return_code'] != 'SUCCESS') {
                            throw new \Exception($payment_return['return_msg']);
                        }
                    	if ($payment_return['result_code'] != 'SUCCESS') {
                    		throw new \Exception($payment_return['err_code_des']);
                    	}
                        //$result = $wechat->queryTransfers($partner_trade_no);
                        //lib\logging::write(var_export($result, true));
                        
                        
						$record_data1 = array(
							'record_status' => 1,
							'record_outtradeno' => $payment_return['partner_trade_no'],
							'record_tradeno' => $payment_return['payment_no'],
							'record_tradetime' => time(),
							'record_tradetype' => $user_method['method_code']
						);
						$result = model('withdraw_record')->edit(array('record_id' => $record_id), $record_data1);
						if ($yue > 0) {//余额处理
							$yue_data = array(
								'uniacid' => $this->uniacid,
								'amount' => $yue,
								'order_sn' => $record_id,
								'uid' => $this->member_info['uid'],
								'member_name' => $this->member_info['nickname']
							);
							$result = logic('predeposit')->changePd('commission_come', $yue_data);
						}
						\base\token::$config['token_name'] = 'widthdrawtoken';
            			if (!\base\token::checkToken()) {
            				//throw new \Exception('token验证失败，请刷新重试');
            			}
            			$logic_pd = logic('predeposit');
                		$data_pd = array();
                		$data_pd['uid'] = $this->member_info['uid'];
                		$data_pd['member_name'] = '';
                		$data_pd['amount'] = $total;
                		$data_pd['lg_desc'] = '申请提现';
                		$logic_pd->changePd('commission_out', $data_pd);
						$model->commit();
						output_error('提现成功，请及时查收', array('redirect' => '/pages/fenxiao/withdraw_record'));
					} else {
					    \base\token::$config['token_name'] = 'widthdrawtoken';
            			if (!\base\token::checkToken()) {
            				//throw new \Exception('token验证失败，请刷新重试');
            			}
            			$logic_pd = logic('predeposit');
            			$data_pd = array();
                		$data_pd['uid'] = $this->member_info['uid'];
                		$data_pd['member_name'] = '';
                		$data_pd['amount'] = $total;
                		$data_pd['lg_desc'] = '申请提现';
                		$logic_pd->changePd('commission_out', $data_pd);
						$model->commit();
						output_data('提现成功,等待管理员审核');
					}
				} else {
					throw new \Exception('提交失败！');
				}
			} catch (\Exception $e) {
				$model->rollBack();
				output_error($e->getMessage());
			}
		}
	}
	
	public function withdraw_recordOp() {
		if (IS_API) {
			$this->title = '奖金提现明细';
			$model_withdraw_record = model('withdraw_record');
			$status = input('status', 0, 'intval');
			$where = array(
			    'uniacid' => $this->uniacid,
				'uid' => $this->member_info['uid'],
			);
			if (!empty($status)) {
				$where['record_status'] = $status - 1;
			}
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			if ($start_time) {
				$where['record_addtime >='] = strtotime($start_time);
			}
			if ($end_time) {
				$where['record_addtime <='] = strtotime($end_time);
			}
			$record_list_tmp = $model_withdraw_record->getList($where, '*', 'record_addtime desc', 20, input('page', 1, 'intval'));
			$record_list = $record_list_tmp['list'];
			foreach($record_list as $k => $r){
				$record_list[$k]['record_addtime'] = date('Y/m/d H:i:s', $r['record_addtime']);
				if ($r['record_status'] == 0) {
					$record_list[$k]['status_desc'] = '申请中';
				}
				if ($r['record_status'] == 1) {
					$record_list[$k]['status_desc'] = '已执行';
				}
				if ($r['record_status'] == 2) {
					$record_list[$k]['status_desc'] = '已驳回';
				}
			}
			$return = array(
				'title' => $this->title,
				'list' => $record_list,
				'totalpage' => $record_list_tmp['totalpage'],
				'hasmore' => $record_list_tmp['hasmore'],
			);
			output_data($return);
		}
	}
	public function inviter_listOp() {
		if (IS_API) {
			$level_list = logic('yewu')->get_level_list('*');
			$where['inviter_id'] = $this->member_info['uid'];
			$this->title = '我的朋友';
			$inviter_list = array();
			$result = model('member')->getList($where, '*', 'uid asc', 30, input('page', 1, 'intval'));
			$uids = [];
			if (!empty($result['list']) && is_array($result['list'])) {
				foreach ($result['list'] as $v) {
					$uids[] = $v['uid'];
				}
				$account_result = model('distribute_account')->getList(array('uid' => $uids), 'inviter_num,team_num,team_performance_num,team_performance_money,self_performance_num', 'uid asc');
				$account_list = $account_result['list'];
				foreach ($result['list'] as $kk => $rr) {
					$rr['inviter_num'] = $account_list[$kk]['inviter_num'];
					$rr['team_num'] = $account_list[$kk]['team_num'];
					$rr['team_performance_num'] = $account_list[$kk]['team_performance_num'];
					$rr['team_performance_money'] = $account_list[$kk]['team_performance_money'];
					$rr['self_performance_num'] = $account_list[$kk]['self_performance_num'];
					$rr['level_name'] = $level_list[$rr['level_id']]['level_name'];
					$rr['add_time'] = date('Y-m-d H:i:s', $rr['add_time']);
					$inviter_list[] = $rr;
				}
			}
			$account_info = model('distribute_account')->getInfo(array('uid' => $this->member_info['uid']), 'inviter_num,team_num,team_performance_num,team_performance_money,self_performance_num');
			$return = array(
				'title' => $this->title,
				'list' => $inviter_list,
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
				'account_info' => $account_info,
			);
			unset($result);
			output_data($return);
		}
	}
	public function team_listOp() {
		if (IS_API) {
			$childs_all = array();
			$my_term_list = array();
			
			$pagesize = 10;
			$curpage = input('page', 1, 'intval');
			$level = input('level', 1, 'intval');
			
			$model_dis = model('distribute_account');
			//分销层级
			$dis_setting['dis_name'] = '会员';
			$dis_level_arr = array('一','二','三','四','五','六','七','八','九');
			$level_list = array();
			for ($j = 0; $j < config('distributor_level_goods'); $j++) {
				$level_list[$j] = array(
					'id' => $j + 1,
					'name' => $dis_level_arr[$j] . '级' . $dis_setting['dis_name']
				);
			}
			
			$str = ',' . $this->member_info['uid'] . ',';
			$result = $model_dis->getList(array('dis_path' => '%' . $str . '%'));
			//var_dump($result['list']);
			if (!$result['list']) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'total' => 0, 'cur_num' => 0, 'level_list' => $level_list));
			}
			//团队总人数
			$total = count($result['list']);
			$cur_num = 0;
			foreach($result['list'] as $value) {
				if ($level > 0) {
					$arr = explode(',', trim($value['dis_path'], ','));
					$arr = array_reverse($arr);
					$position = array_search($this->member_info['uid'], $arr);
					if ($position == $level - 1) {
						$childs_all[] = $value;
					}
				} else {
					$childs_all[] = $value;
				}
			}
			$count = count($childs_all);
			
			if ($count == 0) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			}
			
			$cur_num = $count;
			
			$hasmore = $count > $curpage * $pagesize ? true : false;
			$length = $count > $curpage * $pagesize ? $pagesize : $count - ($curpage - 1) * $pagesize;
			
			$my_term_list = array_slice($childs_all, (($curpage - 1) * $pagesize), $length);
			
			if (empty($my_term_list)) {
				output_data(array('term_list' => array(), 'hasmore' => false, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			}
			
			$memberids = array();
			foreach($my_term_list as $k_t => $v_t) {
				$memberids[] = $v_t['uid'];
			}
			
			//获取分销商级别
			$dis_levels = array();
			$result = model('vip_level')->getList(array(), 'level_name,id' ,'level_sort asc');
			foreach($result['list'] as $key => $val) {
				$dis_levels[$val['id']] = $val['level_name'];
			}
			
			//获取会员信息
			$members = array();
			$result = model('member')->getList(array('uid' => $memberids), 'uid,headimg,nickname,mobile');
			if (!$result['list']) {
				output_data(array('term_list' => array(),'hasmore' => false, 'total' => $total, 'cur_num' => 0, 'level_list' => $level_list));
			} else {
				foreach($result['list'] as $kk => $vv) {
					$members[$vv['uid']] = $vv;
				}
			}
			//var_dump($my_term_list);
			
			$lists = array();
			//组合数组
			foreach($my_term_list as $mid => $meminfo) {
			    $lists[$mid]['uuid'] = config('uid_pre') . padNumber($meminfo['uid']);
				$lists[$mid]['uid'] = $meminfo['uid'];
				$lists[$mid]['add_time'] = date('Y-m-d H:i:s', $meminfo['add_time']);
				$lists[$mid]['level_name'] = empty($dis_levels[$meminfo['level_id']]) ? '暂无！' : $dis_levels[$meminfo['level_id']];
				$lists[$mid]['nickname'] = empty($members[$meminfo['uid']]) ? '暂无！' : $members[$meminfo['uid']]['nickname'];
				$lists[$mid]['mobile'] = empty($members[$meminfo['uid']]) ? '暂无！' : $members[$meminfo['uid']]['mobile'];
				$lists[$mid]['avatar'] = empty($members[$meminfo['uid']]['headimg']) ? STATIC_URL . '/shop/img/default_user.png' : $members[$meminfo['uid']]['headimg'];
				$lists[$mid]['team_num'] = $meminfo['team_num'];
				//自己未提货数量
				$lists[$mid]['new_tihuoquan_num'] = model('shop_goods_tihuoquan')->where(array('uid' => $meminfo['uid'], 'state' => 0))->total();
				//自己已激活提货数量
				$lists[$mid]['used_tihuoquan_num'] = model('shop_goods_tihuoquan')->where(array('uid' => $meminfo['uid'], 'state' => array(1,2)))->total();
				$lists[$mid]['can_tihuoquan_num'] = $meminfo['can_tihuoquan_num'];
				//团队未合成
				$res = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('dis_path' =>  '%,' . $meminfo['uid'] . ',%'))->select();
				$uids = [];
				$team_can_tihuoquan_num = 0;
				foreach($res as $v) {
				    $uids[] = $v['uid'];
				    $team_can_tihuoquan_num += $v['can_tihuoquan_num'];
				}
			    $lists[$mid]['new_team_tihuoquan_num'] = model('shop_goods_tihuoquan')->where(array('uid' => $uids, 'state' => 0))->total();
			    $lists[$mid]['used_team_tihuoquan_num'] = model('shop_goods_tihuoquan')->where(array('uid' => $uids, 'state' => array(1,2)))->total();
			    //$lists[$mid]['team_can_tihuoquan_num'] = $team_can_tihuoquan_num + $meminfo['can_tihuoquan_num'];//加让自己的
			    $lists[$mid]['team_can_tihuoquan_num'] = $meminfo['can_tihuoquan_num'];//显示自己的
			}
			output_data(array('term_list' => $lists, 'hasmore' => $hasmore, 'total' => $total, 'cur_num' => $cur_num, 'level_list' => $level_list));
		}	
	}
}