<?php
/**
 * 支付回调
 *
 */
namespace shop\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class payment extends base\controller
{
    private $payment_code;
	private $config;
    public function __construct() {
		parent::_initialize();
		$this->assign('cur_controller', $this->_controller);
		$this->assign('cur_action', $this->_action);
        $this->payment_code = input('get.payment_code');
		$config = model('config')->find();
		$wechat_setting = model('weixin_wechat')->find();
		$wechat_setting = $wechat_setting ?: array();
		$this->config = array_merge($config, $wechat_setting);
		config($this->config);
    }
    /**
     * 支付宝回调
     */
    public function returnOp()
    {
        unset($_GET['c']);
        unset($_GET['a']);
        unset($_GET['payment_code']);
        if (!empty($_GET['out_trade_no'])) {
            //验证成功
			$tmp = explode('_', $_GET['out_trade_no']);
			$out_trade_no = $tmp[0];
			$order_type = $tmp[2];
			if ($order_type == 'r') {
				$check = model('shop_order')->where(array('trade_no' => $_GET['trade_no']))->total();
			} else if ($order_type == 'f') {
				$check = model('shop_order')->where(array('trade_no' => $_GET['trade_no']))->total();
			} else if ($order_type == 'p') {
				$check = model('pd_recharge')->where(array('pdr_trade_sn' => $_GET['trade_no']))->total();
			} else if ($order_type == 'v') {
				$check = model('shop_vr_order')->where(array('trade_no' => $_GET['trade_no']))->total();
			}
            if ($check) {
                $this->assign('result', 'success');
                $this->assign('message', '支付成功');
				$this->assign('return_url', uni_url('/pages/user/index', array(), true));//url自定义
            } else {
                $this->assign('result', 'fail');
                $this->assign('message', '订单状态更新失败');
            }
        } else {
            //验证失败
            $this->assign('result', 'fail');
            $this->assign('message', '支付失败');
        }
		
        $this->display('payment_message');
    }
    /**
     * 支付提醒
     */
    public function notifyOp()
    {
        $inc_file = COMMON_PATH . '/vendor/WeChatDeveloper/include.php';
		require $inc_file;
        $param = $this->_get_payment_config();
        try {
            if ($this->payment_code == 'wxapp') {
    		    $config = array(
                    'token' => config('wechat_token'),
                    'appid' => config('wxappid'),
                    'appsecret' => config('wxappsecret'),
                    'encodingaeskey' => config('wechat_encoding'),
                    // 配置商户支付参数（可选，在使用支付功能时需要）
                    'mch_id' => $param['mchid'],
                    'mch_key' => $param['signkey'],
                    // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                    'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                    'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                    // 缓存目录配置（可选，需拥有读写权限）
                    'cache_path' => '',
                );
                $wechat = \WeChat\Pay::instance($config);
                $data = $wechat->getNotify();
                lib\logging::write(var_export($data, true));
                if ($data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS') {
                    // @todo 去更新下原订单的支付状态
                    $this->_update_order($data['out_trade_no'], $data['transaction_id'], isset($data['total_fee']) ? $data['total_fee'] : 0);
                    // 返回接收成功的回复
                    ob_clean();
                    echo $wechat->getNotifySuccessReply();
                }
            } else if (in_array($this->payment_code, array('wxpay', 'wxpay_jsapi', 'wxpay_saoma', 'wxpay_h5'))) {
    		    $config = array(
                    'token' => config('wechat_token'),
                    'appid' => config('wechat_appid'),
                    'appsecret' => config('wechat_appsecret'),
                    'encodingaeskey' => config('wechat_encoding'),
                    // 配置商户支付参数（可选，在使用支付功能时需要）
                    'mch_id' => $param['partnerId'],
                    'mch_key' => $param['apiKey'],
                    // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                    'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                    'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                    // 缓存目录配置（可选，需拥有读写权限）
                    'cache_path' => '',
                );
    		    $wechat = \WeChat\Pay::instance($config);
                $data = $wechat->getNotify();
                lib\logging::write(var_export($data, true));
                if ($data['return_code'] === 'SUCCESS' && $data['result_code'] === 'SUCCESS') {
                    // @todo 去更新下原订单的支付状态
                    $this->_update_order($data['out_trade_no'], $data['transaction_id'], isset($data['total_fee']) ? $data['total_fee'] : 0);
                    // 返回接收成功的回复
                    ob_clean();
                    echo $wechat->getNotifySuccessReply();
                }
    		} else if (in_array($this->payment_code, array('alipay'))) {
    		    $config = array(
                    // 沙箱模式
                    'debug' => true,
                    // 签名类型（RSA|RSA2）
                    'sign_type' => 'RSA2',
                    // 应用ID
                    'appid' => $param['appid'],
                    // 应用私钥的内容 (1行填写，特别注意：这里的应用私钥通常由支付宝密钥管理工具生成)
                    'private_key' => $param['private_key'],
                    // 支付宝公钥内容 (1行填写，特别注意：这里不是应用公钥而是支付宝公钥，通常是上传应用公钥换取支付宝公钥，在网页可以复制)
                    'public_key' => $param['public_key'],
                    // 应用公钥的内容（新版资金类接口转 app_cert_sn）
                    'app_cert' => '',
                    // 支付宝根证书内容（新版资金类接口转 alipay_root_cert_sn）
                    'root_cert' => '',
                    // 支付成功通知地址
                    'notify_url' => '',
                    // 网页支付回跳地址
                    'return_url' => '',
                );
                $pay = \AliPay\App::instance($config);
                $data = $pay->notify();
                //lib\logging::write(var_export($data, true));
                if (in_array($data['trade_status'], array('TRADE_SUCCESS', 'TRADE_FINISHED'))) {
                    // @todo 更新订单状态，支付完成
                    $result = $this->_update_order($data['out_trade_no'], $data['trade_no'], 0);
                    if (!empty($result['state'])) {
                        echo 'success';
                        die;
                    }
                } else {
                    file_put_contents('notify.txt', '收到异步通知\r\n', FILE_APPEND);
                }
    		} else {
    		    lib\logging::write(var_export($this->payment_code . '没有回调处理', true));
    		}
        } catch (\Exception $e) {
            // 出错啦，处理下吧
            echo $e->getMessage() . PHP_EOL;
        }
    }
    /**
     * 获取支付接口信息
     */
    private function _get_payment_config()
    {
        //读取接口配置信息
		$model_mb_payment = model('mb_payment');
		$mb_payment_info = $model_mb_payment->where(array('payment_code' => $this->payment_code))->find();
		return fxy_unserialize($mb_payment_info['payment_config']);
    }
    /**
     * 更新订单状态
     */
    private function _update_order($out_trade_no, $trade_no, $total_fee = 0)
    {		
        $tmp = explode('_', $out_trade_no);
        $out_trade_no = $tmp[0];
		$order_type = $tmp[2];
        if ($order_type == 'r') { //商城实物订单
			$shop_type_a = model('shop_order')->getInfo(array('pay_sn' => $out_trade_no), array(), 'is_spike,is_points');
			$logic = logic('shop_buy');
			if (!empty($shop_type_a['is_spike'])) {
				$logic = logic('spike_shop_buy');
			}
			if (!empty($shop_type_a['is_points'])) {
				$logic = logic('points_shop_buy');
			}
			$result = $logic->getRealOrderInfo($out_trade_no);
			if (!$result['state']) {
				return callback(false, $result['msg']);
			}
            if (intval($result['data']['api_pay_state'])) {
                return array('state' => true);
            }
			$result = model('shop_order')->getList(array('pay_sn' => $out_trade_no), '*', '', null, null, array('order_common','order_goods'));
			$order_list = $result['list'];
			$result = $logic->updateOrderAll($order_list, $this->payment_code, $trade_no);
			//计算本次需要在线支付的订单总金额
			$api_pay_amount = 0;
			if (!empty($order_list)) {
				foreach ($order_list as $order_info) {
					$api_pay_amount += floatval($order_info['order_amount']) - floatval($order_info['pd_amount']);
				}
			}
        } else if ($order_type == 'f') {
            $shop_type_a = model('shop_order')->getInfo(array('pay_sn' => $out_trade_no), array());
			$logic = logic('tihuo_buy');
			$result = $logic->getRealOrderInfo($out_trade_no);
			if (!$result['state']) {
				return callback(false, $result['msg']);
			}
            if (intval($result['data']['api_pay_state'])) {
                return array('state' => true);
            }
			$result = model('shop_order')->getList(array('pay_sn' => $out_trade_no), '*', '', null, null, array('order_common','order_goods'));
			$order_list = $result['list'];
			$result = $logic->updateOrderAll($order_list, $this->payment_code, $trade_no);
			//计算本次需要在线支付的订单总金额
			$api_pay_amount = 0;
			if (!empty($order_list)) {
				foreach ($order_list as $order_info) {
					$api_pay_amount += floatval($order_info['order_amount']) - floatval($order_info['pd_amount']);
				}
			}
        } else if ($order_type == 'p') {
			$order_info = model('pd_recharge')->where(array('pdr_sn' => $out_trade_no))->find();
			if ($total_fee > 0 && $total_fee < $order_info['pdr_amount'] * 100) {
				return callback(false, '非法操作');
			}
			$result = $this->_update_pd_order($out_trade_no, $trade_no, $order_info);
		} else if ($order_type == 'v') {
			$result = logic('shop_vr_buy')->getOrderInfo($out_trade_no);
			if (!$result['state']) {
				return array('state' => true);
			}
			$result = model('shop_vr_order')->getList(array('order_sn' => $out_trade_no));
			$order_list = $result['list'];
			$result = logic('shop_vr_buy')->updateOrderAll($order_list, $this->payment_code, $trade_no);
			//计算本次需要在线支付的订单总金额
			$api_pay_amount = 0;
			if (!empty($order_list)) {
				foreach ($order_list as $order_info) {
					$api_pay_amount += floatval($order_info['order_amount']) - floatval($order_info['pd_amount']);
				}
			}
		}
        return array('state' => true);
    }
	/**
     * 支付成功后修改充值订单状态
     * @param unknown $out_trade_no
     * @param unknown $trade_no
     * @throws Exception
     * @return multitype:unknown
     */
    private function _update_pd_order($out_trade_no, $trade_no, $order_info)
    {
        $condition = array();
        $condition['pdr_sn'] = $order_info['pdr_sn'];
        $condition['pdr_payment_state'] = 0;
        $update = array();
        $update['pdr_payment_state'] = 1;
        $update['pdr_payment_time'] = TIMESTAMP;
        $update['pdr_payment_code'] = $this->payment_code;
        $update['pdr_trade_sn'] = $trade_no;
        $model_pd = model('pd_recharge');
        try {
            $model_pd->beginTransaction();
            $pdnum = $model_pd->where(array('pdr_sn' => $order_info['pdr_sn'], 'pdr_payment_state' => 1))->total();
            if (intval($pdnum) > 0) {
                throw new \Exception('订单已经处理');
            }
            //更改充值状态
            $state = $model_pd->where($condition)->update($update);
            if (!$state) {
                throw new \Exception('更新充值状态失败');
            }
            //变更会员预存款
            $data = array();
			$data['uniacid'] = $order_info['uniacid'];
            $data['uid'] = $order_info['pdr_member_id'];
            $data['member_name'] = $order_info['pdr_member_name'];
            $data['amount'] = $order_info['pdr_amount'];
            $data['pdr_sn'] = $order_info['pdr_sn'];
            logic('predeposit')->changePd('recharge', $data);
            $model_pd->commit();
            return callback(true);
        } catch (\Exception $e) {
            $model_pd->rollback();
            return callback(false, $e->getMessage());
        }
    }
	public function payment_resultOp() {
		$this->assign('title' ,'支付结果');
		$this->display();
	}
	public function payment_result_failedOp() {
		$this->assign('title' ,'支付失败');
		$this->display();
	}
}