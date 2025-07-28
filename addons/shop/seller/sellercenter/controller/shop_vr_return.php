<?php
namespace sellercenter\controller;
use lib;
class shop_vr_return extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_return = model('shop_vr_refund_return');
		$where = array();
		$where['store_id'] = $this->store_id;
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			$search_uids = array();
			$search_uids[] = 0;
			$result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
			foreach($result['list'] as $r){
				$search_uids[] = $r['uid'];
			}
			$where['buyer_id'] = $search_uids;
        }
		
		$order_sn = input('get.order_sn', '');
		if($order_sn){
			$where['order_sn'] = $order_sn;
		}
		
		$status = input('get.status', 0, 'intval');
		if($status){
			$where['refund_state'] = $status;
		}
		
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['add_time >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['add_time <='] = $end_unixtime;
        }
		$return_list = array();
        $list = $model_return->getList($where, '*', 'add_time DESC', 20, input('get.page', 1, 'intval'));
		if(!empty($list['list'])){
			foreach($list['list'] as $r){
				$r['state_text'] = $model_return->_orderState($r);
				$return_list[] = $r;
			}
		}
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'order_sn' => $order_sn, 'status' => $status, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_vr_return/index')));
        $this->assign('list', $return_list);
		
		$mapping_fans = $member_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['buyer_id'], $uids)){
					$uids[] = $r['buyer_id'];
				}
			}
			
			$result = model('fans')->getList(array('uid' => $uids), 'uid, nickname, tag');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$arr = fxy_unserialize(base64_decode($rr['tag']));
					$mapping_fans[$rr['uid']] = array('nickname' => $rr['nickname'], 'headimg' => $arr['headimgurl']);
				}
			}
			unset($result);
			
			$result = model('member')->getList(array('uid' => $uids), 'uid,truename,mobile');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$member_list[$rr['uid']] = $rr;
				}
			}
			unset($result);
		}
		
		$this->assign('member_list', $member_list);
		$this->assign('mapping_fans', $mapping_fans);
		$this->display();
	}
	
	public function rejectOp(){
		$model_return = model('shop_vr_refund_return');
		if(chksubmit()){
			$refund_id = input('refund_id', 0, 'intval');
			$refund_info = $model_return->getInfo(array('refund_id' => $refund_id));
			if($refund_info['refund_state'] != 1){
				output_error('该记录不是待处理状态！');
			}
			$update_array = array();
			$update_array['refund_state'] = 4;
			$update_array['admin_message'] = input('note', '');			
            $state = $model_return->edit(array('refund_id' => $refund_id), $update_array);
            if ($state) {
				$member_info = model('member')->getInfo(array('uid' => $refund_info['buyer_id']));
				$order_info = model('shop_vr_order')->getInfo(array('order_id' => $refund_info['order_id']));
				if (empty($order_info['order_id'])) {
					output_error('相关订单不存在！');
				}
				if (!empty($member_info['openid']) && $order_info['payment_code'] == 'wxapp') {//小程序模板消息
					$wxapp_form_id_info = model('wxapp_form_id')->getInfo(array('openid' => $member_info['openid']), '*', 'id DESC');
					if(!$wxapp_form_id_info){
						$wxapp_form_id_info['id'] = '';
						$wxapp_form_id_info['openid'] = '';
						$wxapp_form_id_info['form_id'] = '';
					}
					$page = ADDONS_NAME . '/pages/refund_return_list/index';
					$touser = $member_info['openid'];
					$template_id = $this->config['refund_pass_template_id'];
					$emphasis_keyword = '';
					
					/*
					订单编号
					{{keyword1.DATA}}

					退款金额
					{{keyword2.DATA}}

					拒绝原因
					{{keyword3.DATA}}
					*/
					$data = array(
						'keyword1' => array(
							'value' => $refund_info['order_sn'],
							'color' => '#4a4a4a'
						),
						'keyword2' => array(
							'value' => $refund_info['refund_amount'],
							'color' => '#4a4a4a'
						),
						'keyword3' => array(
							'value' => input('note', ''),
							'color' => '#4a4a4a'
						),
					);
					send_wxapp_tpl_msg($template_id, $touser, $data, $wxapp_form_id_info['form_id'], $emphasis_keyword, $page, $this->uniacid, $this->config['wxappid'], $this->config['wxappsecret']);
				}
                output_data(array('msg' => '编辑成功', 'url' => users_url('shop_vr_return/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$refund_id = input('get.refund_id', 0, 'intval');
			$refund_info = $model_return->getInfo(array('refund_id' => $refund_id));
			if($refund_info['refund_state'] != 1){
				output_error('该记录不是待处理状态！');
			}
			$this->assign('refund_info', $refund_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	
	public function pay_recordOp(){
		if(IS_API){
			$model_return = model('shop_vr_refund_return');
			$refund_id = input('get.refund_id', 0, 'intval');
			$refund_type = input('get.refund_type', 'online');
			$refund_info = $model_return->getInfo(array('refund_id' => $refund_id));
			if($refund_info['refund_state'] != 1){
				output_error('该记录不是待处理状态！');
			}
			
			$order_info = model('shop_vr_order')->getInfo(array('order_id' => $refund_info['order_id']));
			if (empty($order_info['order_id'])) {
				output_error('相关订单不存在！');
			}
			
			$flag = true;
			if ($refund_type == 'online') {
				if ($order_info['trade_no'] && $order_info['payment_code'] == 'wxapp') {
					$inc_file = COMMON_PATH . '/vendor/pay/wxapp/wxapp.php';
					if (!is_file($inc_file)) {
						output_error('支付接口不存在');
					}
					require $inc_file;
					$model_mb_payment = model('mb_payment');
					$mb_payment_info = $model_mb_payment->where(array('payment_code' => $order_info['payment_code'], 'payment_state' => 1))->find();
					if (!$mb_payment_info) {
						output_error('支付方式未开启');
					}
					$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
					$payment_config['amount'] = $order_info['order_amount'] - $order_info['pd_amount'];
					$payment_config['trade_no'] = $order_info['trade_no'];
					$payment_api = new \wxapp();
					$payment_return = $payment_api->Refundpay($payment_config);
					if (!empty($payment_return['error'])) {
						output_error($payment_return['error']);
					}
				} else {
					if ($order_info['trade_no'] && in_array($order_info['payment_code'], array('wxpay', 'wxpay_jsapi', 'wxpay_saoma', 'wxpay_h5'))) {
						//退款订单详细
						$refund_amount = $order_info['order_amount'] - $order_info['pd_amount'];
						//本次在线退款总金额
						if ($refund_amount > 0) {
							$model_mb_payment = model('mb_payment');
							$mb_payment_info = $model_mb_payment->where(array('payment_code' => $order_info['payment_code'], 'payment_state' => 1))->find();
							if (!$mb_payment_info) {
								output_error('支付方式未开启');
							}
							$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
							define('WXPAY_APPID', $payment_config['appId']);
							define('WXPAY_MCHID', $payment_config['partnerId']);
							define('WXPAY_KEY', $payment_config['apiKey']);
							$upload_file_dir = $this->_upload_file_dir;
							$upload_file_url = $this->_upload_file_url;
							$apiclientcert_file = str_replace($upload_file_url, $upload_file_dir, $payment_config['apiclientcert']);
							$apiclientkey_file = str_replace($upload_file_url, $upload_file_dir, $payment_config['apiclientkey']);
							define('WXPAY_SSLCERT', $apiclientcert_file);
							define('WXPAY_SSLKEY', $apiclientkey_file);
							$total_fee = $refund_amount * 100;
							//微信订单实际支付总金额(在线支付金额,单位为分)
							$refund_fee = $refund_amount * 100;
							//本次微信退款总金额(单位为分)
							$api_file = COMMON_PATH . '/vendor/refund/wxpay/WxPay.Api.php';
							if (!is_file($api_file)) {
								output_error('退款接口不存在');
							}
							require $api_file;
							$input = new \WxPayRefund();
							$input->SetTransaction_id($order_info['trade_no']);
							//微信订单号
							$input->SetTotal_fee($total_fee);
							$input->SetRefund_fee($refund_fee);
							$input->SetOut_refund_no($refund_info['refund_sn']);
							//退款批次号
							$input->SetOp_user_id(\WxPayConfig::MCHID);
							$data = \WxPayApi::refund($input);
							if (!empty($data) && $data['return_code'] == 'SUCCESS') {
								//请求结果
								if ($data['result_code'] == 'SUCCESS') {
									//业务结果
									
									
								} else {
									output_error('微信退款错误,' . $data['err_code_des']);
									//错误描述
								}
							} else {
								output_error('微信接口错误,' . $data['return_msg']);
								//返回信息
							}
						}
					} else if ($order_info['trade_no'] && $order_info['payment_code'] == 'alipay') {
						//退款订单详细
						$refund_amount = $order_info['order_amount'] - $order_info['pd_amount'];
						//本次在线退款总金额
						if ($refund_amount > 0) {
							$model_mb_payment = model('mb_payment');
							$mb_payment_info = $model_mb_payment->where(array('payment_code' => $order_info['payment_code'], 'payment_state' => 1))->find();
							if (!$mb_payment_info) {
								output_error('支付方式未开启');
							}
							$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
							$alipay_config = array();
							$alipay_config['seller_email'] = $payment_config['alipay_account'];
							$alipay_config['partner'] = $payment_config['alipay_partner'];
							$alipay_config['key'] = $payment_config['alipay_key'];
							$alipay_config['notify_url'] = users_url('notify_refund/alipay');
							$api_file = COMMON_PATH . '/vendor/refund/alipay/alipay.class.php';
							if (!is_file($api_file)) {
								output_error('退款接口不存在');
							}
							require $api_file;
							$alipaySubmit = new \AlipaySubmit($alipay_config);
							
							$parameter = getPara($alipay_config);
							$batch_no = $refund_info['refund_sn'];
							$b_date = substr($batch_no, 0, 8);
							if ($b_date != date('Ymd')) {
								$batch_no = date('Ymd') . substr($batch_no, 8);
								//批次号。支付宝要求格式为：当天退款日期+流水号。
								$model_return->edit(array('refund_id' => $refund_id), array('batch_no' => $batch_no));
							}
							$parameter['batch_no'] = $batch_no;
							$parameter['detail_data'] = $order['trade_no'] . '^' . $refund_amount . '^协商退款';
							//数据格式为：原交易号^退款金额^理由
							$pay_url = $alipaySubmit->buildRequestParaToString($parameter);
							header('Location: ' . $pay_url); exit();
						}
					} else if ($order_info['payment_code'] == 'predeposit') {
						$yue_data = array(
							'uniacid' => $this->uniacid,
							'amount' => $order_info['order_amount'],
							'order_sn' => $order_info['order_sn'],
							'uid' => $order_info['buyer_id'],
							'member_name' => $order_info['buyer_name']
						);
						$result = logic('predeposit')->changePd('refund', $yue_data);
					}
				}
			}
			if ($order_info['pd_amount'] > 0) {
				$yue_data = array(
					'uniacid' => $this->uniacid,
					'amount' => $order_info['pd_amount'],
					'order_sn' => $order_info['order_sn'],
					'uid' => $order_info['buyer_id'],
					'member_name' => $order_info['buyer_name']
				);
				$result = logic('predeposit')->changePd('refund', $yue_data);
			}
			$update_data = array(
				'refund_state' => 3,
				'admin_time' => time()
			);
			$state = $model_return->edit(array('refund_id' => $refund_id), $update_data);
			if ($state) {
				$member_info = model('member')->getInfo(array('uid' => $refund_info['buyer_id']));
				if (!empty($member_info['openid']) && $order_info['payment_code'] == 'wxapp') {//小程序模板消息
					$wxapp_form_id_info = model('wxapp_form_id')->getInfo(array('openid' => $member_info['openid']), '*', 'id DESC');
					if(!$wxapp_form_id_info){
						$wxapp_form_id_info['id'] = '';
						$wxapp_form_id_info['openid'] = '';
						$wxapp_form_id_info['form_id'] = '';
					}
					$page = ADDONS_NAME . '/pages/refund_return_list/index';
					$touser = $member_info['openid'];
					$template_id = $this->config['refund_success_template_id'];
					$emphasis_keyword = '';
					
					/*
					订单号
					{{keyword1.DATA}}

					退款金额
					{{keyword2.DATA}}

					退款理由
					{{keyword3.DATA}}
					*/
					$data = array(
						'keyword1' => array(
							'value' => $refund_info['order_sn'],
							'color' => '#4a4a4a'
						),
						'keyword2' => array(
							'value' => $refund_info['refund_amount'],
							'color' => '#4a4a4a'
						),
						'keyword3' => array(
							'value' => $refund_info['buyer_message'],
							'color' => '#4a4a4a'
						),
					);
					send_wxapp_tpl_msg($template_id, $touser, $data, $wxapp_form_id_info['form_id'], $emphasis_keyword, $page, $this->uniacid, $this->config['wxappid'], $this->config['wxappsecret']);
				}
				//库存恢复，扣除业绩
				$model_goods = model('shop_goods');
				$model_goods->where(array('goods_id' => $order_info['goods_id'], 'goods_salenum >=' => $order_info['goods_num']))->update('goods_salenum=goods_salenum-' . $order_info['goods_num'] . ',goods_storage=goods_storage+' . $order_info['goods_num']);
				$goods_num = $order_info['goods_num'];
				logic('store_bill')->cancle_supply_commiss_order($order_info);
				logic('yewu')->team_performance_minus($order_info, $goods_num);
				logic('yewu')->self_performance_minus($order_info, $goods_num);
				
				logic('yewu')->update_goods_other_state($order_info['order_sn'], ORDER_STATE_CANCEL);
	        	logic('yewu')->update_goods_commission_state($order_info['order_sn'], ORDER_STATE_CANCEL);
				if ($refund_type == 'online') {
					output_data('退款成功');
				} else {
					output_data(array('url' => users_url('shop_vr_return/index')));
				}

			} else {
				output_error('退款失败！');
			}
		}
	}
}