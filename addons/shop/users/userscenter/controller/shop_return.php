<?php
namespace userscenter\controller;
use lib;
class shop_return extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_return = model('shop_refund_return');
		$where = array();
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
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
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'order_sn' => $order_sn, 'status' => $status, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_return/index')));
        $this->assign('list', $return_list);
		
		$mapping_fans = $member_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['buyer_id'], $uids)){
					$uids[] = $r['buyer_id'];
				}
			}
			
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,truename,mobile');
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
		$model_return = model('shop_refund_return');
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
				if($refund_info['order_id'] > 0){
					//model('shop_order')->edit(array('order_id' => $refund_info['order_id']), array('lock_state' => 0));
				}
				$member_info = model('member')->getInfo(array('uid' => $refund_info['buyer_id']));
				$order_info = model('shop_order')->getInfo(array('order_id' => $refund_info['order_id']), array('order_goods', 'order_common'));
				if (empty($order_info['order_id'])) {
					output_error('相关订单不存在！');
				}
				if (!empty($member_info['openid']) && $order_info['payment_code'] == 'wxapp') {//小程序模板消息
					
				}
				model('shop_order')->where(array('order_id' => $refund_info['order_id']))->update(array('lock_state' => 0));
                output_data(array('msg' => '编辑成功', 'url' => users_url('shop_return/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$refund_id = input('get.refund_id', 0, 'intval');
			$refund_info = $model_return->getInfo(array('refund_id' => $refund_id));
			if ($refund_info['refund_state'] != 1) {
				output_error('该记录不是待处理状态！');
			}
			$this->assign('refund_info', $refund_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	
	public function pay_recordOp(){
		if(IS_API){
			$model_return = model('shop_refund_return');
			$refund_id = input('get.refund_id', 0, 'intval');
			$refund_type = input('get.refund_type', 'online');
			$refund_info = $model_return->getInfo(array('refund_id' => $refund_id));
			if($refund_info['refund_state'] != 1){
				output_error('该记录不是待处理状态！');
			}
			$member_info = model('member')->getInfo(array('uid' => $refund_info['buyer_id']));
			$order_info = model('shop_order')->getInfo(array('order_id' => $refund_info['order_id']), array('order_goods', 'order_common'));
			if (empty($order_info['order_id'])) {
				output_error('相关订单不存在！');
			}
			
			$flag = true;
			$model = model();
            $model->beginTransaction();
			try {
    			if ($refund_type == 'online') {
    			    $inc_file = COMMON_PATH . '/vendor/WeChatDeveloper/include.php';
                    if (!is_file($inc_file)) {
                        throw new \Exception('支付SDK不存在');
                    }
                    require $inc_file;
    				if ($order_info['trade_no'] && $order_info['payment_code'] == 'wxapp') {
    					$model_mb_payment = model('mb_payment');
    					$mb_payment_info = $model_mb_payment->where(array('payment_code' => 'wxapp', 'payment_state' => 1))->find();
    					if (!$mb_payment_info) {
    						throw new \Exception('小程序支付方式未开启');
    					}
    					$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
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
                        $refund_fee = ($order_info['order_amount'] - $order_info['pd_amount']) * 100;
    					$transaction_id = $order_info['trade_no'];
    					$wechat = \WeChat\Pay::instance($config);
                        $options = array(
                            'transaction_id' => $transaction_id,
                            'out_refund_no' => $order_info['order_sn'],
                            'total_fee' => $refund_fee,
                            'refund_fee' => $refund_fee,
                        );
                        $payment_return = $wechat->createRefund($options);
                        if ($payment_return['return_code'] != 'SUCCESS') {
                            throw new \Exception($prepay_result['return_msg']);
                        }
                		
                		if ($payment_return['result_code'] != 'SUCCESS') {
                			throw new \Exception($prepay_result['err_code_des']);
                		}
    				} else if ($order_info['trade_no'] && in_array($order_info['payment_code'], array('wxpay', 'wxpay_jsapi', 'wxpay_saoma', 'wxpay_h5'))) {
    				    $mb_payment_info = model('mb_payment')->where(array('payment_code' => $order_info['payment_code'], 'payment_state' => 1))->find();
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
    				    $refund_fee = ($order_info['order_amount'] - $order_info['pd_amount']) * 100;
    					$transaction_id = $order_info['trade_no'];
    					$wechat = \WeChat\Pay::instance($config);
                        $options = array(
                            'transaction_id' => $transaction_id,
                            'out_refund_no' => $order_info['order_sn'],
                            'total_fee' => $refund_fee,
                            'refund_fee' => $refund_fee,
                        );
                        $payment_return = $wechat->createRefund($options);
                        if ($payment_return['return_code'] != 'SUCCESS') {
                            throw new \Exception($prepay_result['return_msg']);
                        }
                		
                		if ($payment_return['result_code'] != 'SUCCESS') {
                			throw new \Exception($prepay_result['err_code_des']);
                		}
    				} else {
    				    if ($order_info['trade_no'] && $order_info['payment_code'] == 'alipay') {
    				        $model_mb_payment = model('mb_payment');
    						$mb_payment_info = $model_mb_payment->where(array('payment_code' => $order_info['payment_code'], 'payment_state' => 1))->find();
    						if (!$mb_payment_info) {
    							throw new \Exception('支付方式未开启');
    						}
    						$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
    				        $config = array(
                                // 沙箱模式
                                'debug' => true,
                                // 签名类型（RSA|RSA2）
                                'sign_type' => 'RSA2',
                                // 应用ID
                                'appid' => $payment_config['appid'],
                                // 应用私钥的内容 (1行填写，特别注意：这里的应用私钥通常由支付宝密钥管理工具生成)
                                'private_key' => $payment_config['private_key'],
                                // 支付宝公钥内容 (1行填写，特别注意：这里不是应用公钥而是支付宝公钥，通常是上传应用公钥换取支付宝公钥，在网页可以复制)
                                'public_key' => $payment_config['public_key'],
                                // 应用公钥的内容（新版资金类接口转 app_cert_sn）
                                'app_cert' => '',
                                // 支付宝根证书内容（新版资金类接口转 alipay_root_cert_sn）
                                'root_cert' => '',
                                // 支付成功通知地址
                                'notify_url' => '',
                                // 网页支付回跳地址
                                'return_url' => '',
                            );
                            $refund_fee = priceFormat($order_info['order_amount'] - $order_info['pd_amount']);
                            $out_trade_no = $order_info['trade_no'];
    				        $pay = \AliPay\App::instance($config);
    				        $payment_return = $pay->refund($out_trade_no, $refund_fee);
    				        if (empty($payment_return['code']) || $payment_return['code'] != 10000) {
                                throw new \Exception($payment_return['sub_msg']);
                            }
    					} else if ($order_info['payment_code'] == 'predeposit') {
    						$yue_data = array(
    							'uniacid' => $this->uniacid,
    							'amount' => $order_info['order_amount'],
    							'order_sn' => $order_info['order_sn'],
    							'uid' => $order_info['uid'],
    							'member_name' => $order_info['member_name']
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
    					'uid' => $order_info['uid'],
    					'member_name' => $order_info['member_name']
    				);
    				$result = logic('predeposit')->changePd('refund', $yue_data);
    			}
    			$update_data = array(
    				'refund_state' => 3,
    				'admin_time' => time()
    			);
    			$state = $model_return->edit(array('refund_id' => $refund_id), $update_data);
    			if ($state) {
    				//库存恢复，扣除业绩
    				$model_goods = model('shop_goods');
    				$goods_num = 0;
    				foreach($order_info['extend_order_goods'] as $k => $v) {
    					$flag = $model_goods->where(array('goods_id' => $v['goods_id'], 'goods_salenum >=' => $v['goods_num']))->update('goods_salenum=goods_salenum-' . $v['goods_num'] . ',goods_storage=goods_storage+' . $v['goods_num']);
    					$goods_num = $goods_num + $v['goods_num'];
    				}
    				logic('store_bill')->cancle_supply_commiss_order($order_info);
    				logic('yewu')->team_performance_minus($order_info, $goods_num);
    				logic('yewu')->self_performance_minus($order_info, $goods_num);
    				
    				logic('yewu')->update_goods_other_state($order_info['order_sn'], ORDER_STATE_CANCEL);
    	        	logic('yewu')->update_goods_commission_state($order_info['order_sn'], ORDER_STATE_CANCEL);
    				if (!empty($order_info['extend_order_common'])) {
    					$order_common = $order_info['extend_order_common'];
    				} else {
    					$order_common = model('shop_order_common')->where(array('order_sn' => $order_info['order_sn']))->find();
    				}
    				model('voucher')->edit(array('voucher_id' => $order_common['voucher_id']), array('voucher_state' => 1));
    				$model->commit();
    				if ($refund_type == 'online') {
        				output_data('退款成功');
        			} else {
        				output_data(array('url' => users_url('shop_return/index')));
        			}
    			} else {
    				throw new \Exception('退款失败！');
    			}
			} catch (\Exception $e) {
				$model->rollBack();
				output_error($e->getMessage());
			}
		}
	}
}