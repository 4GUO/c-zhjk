<?php
namespace sellercenter\controller;
use lib;
class seller extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function floworderOp() {
		$where = array();
        $where['store_id'] = $this->store_id;
		$list = model('store_commiss_order')->getList($where, '*', 'id DESC', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval')), _url('seller/floworder')));
		$this->assign('list', $list['list']);
		$this->display();
	}
	public function tixianlistOp() {
		$supply_commiss = model('store_commiss')->getInfo(array('store_id' => $this->store_id));
        if (empty($supply_commiss)) {
            $gy_supply_commiss_data = array();
            $gy_supply_commiss_data['store_id'] = $this->store_id;
            $gy_supply_commiss_data['money'] = 0;
            $gy_supply_commiss_data['dongmoney'] = 0;
            $gy_supply_commiss_data['getmoney'] = 0;
            model('store_commiss')->add($gy_supply_commiss_data);
            $supply_commiss = array();
            $supply_commiss['money'] = 0;
            $supply_commiss['dongmoney'] = 0;
            $supply_commiss['getmoney'] = 0;
        }
		$this->assign('supply_commiss', $supply_commiss);
		
		$where = array();
        $where['store_id'] = $this->store_id;
		$list = model('store_tixian_order')->getList($where, '*', 'id DESC', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval')), _url('seller/tixianlist')));
		$this->assign('list', $list['list']);
		$this->display();
	}
	public function tixian_formOp() {
		$member_info = model('member')->where(array('uid' => $this->store_info['member_id']))->find();
		$supply_commiss = model('store_commiss')->getInfo(array('store_id' => $this->store_id));
		$this->assign('supply_commiss', $supply_commiss);
		$result = model('withdraw_method')->getList(array('method_status' => 1), '*', 'method_id asc');
		$withdraw_method_list = $result['list'];
		$this->assign('withdraw_method_list', $withdraw_method_list);
		$this->assign('member_info', $member_info);
		$this->display();
	}
	public function tixian_form_submitOp() {
		$supply_commiss = model('store_commiss')->getInfo(array('store_id' => $this->store_id));
		$supply_apply_type = input('supply_apply_type', '');
		$weixin_account = input('weixin_account', '');
		$alipay_account = input('alipay_account', '');
		$card_name = input('card_name', '');
		$card_account = input('card_account', '');
		$card_username = input('card_username', '');
		$ti_money = input('money', 0, 'floatval');
		if ($ti_money <= 0) {
			output_error('最低提现大于0元');
		}
		if ($ti_money > $supply_commiss['money']) {
			output_error('当前最多提现' . $supply_commiss['money']);
		}
		$ins_data = array();
		$ins_data['member_id'] = $this->store_info['member_id'];
		$ins_data['store_id'] = $this->store_id;
		$ins_data['money'] = $ti_money;
		$ins_data['type'] = $supply_apply_type;
		$ins_data['state'] = 0;
		$ins_data['shentime'] = 0;
		$ins_data['is_send_fail'] = 0;
		$ins_data['fail_msg'] = '';
		if ($supply_apply_type == 'wxzhuanzhang') {
			$ins_data['bankname'] = '微信零钱';
			$ins_data['bankaccount'] = $weixin_account;
			$ins_data['bankusername'] = '';
		} else if ($supply_apply_type == 'alipay') {
			$ins_data['bankname'] = '支付宝';
			$ins_data['bankaccount'] = $alipay_account;
			$ins_data['bankusername'] = '';
		} else if ($supply_apply_type == 'bank') {
			$ins_data['bankname'] = $card_name;
			$ins_data['bankaccount'] = $card_account;
			$ins_data['bankusername'] = $card_username;
		}
		$ins_data['addtime'] = time();
		model('store_tixian_order')->add($ins_data);
		model('store_commiss')->edit(array('store_id' => $this->store_id), 'money=money-' . $ti_money);
		model('store_commiss')->edit(array('store_id' => $this->store_id), 'dongmoney=dongmoney+' . $ti_money);
		output_data(array('msg' => '提交成功', 'url' => _url('seller/tixian_form')));
	}
}