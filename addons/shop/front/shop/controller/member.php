<?php
namespace shop\controller;
use base;
use lib;
class member extends control {
	protected $fans_info = array();
	protected $member_info = array();
	public function _initialize() {
		parent::_initialize();
		$uid = $this->getMemberIdIfExists();
		if (!$uid) {
		    if (in_array($this->_controller, array('user','shop_cart')) && in_array($this->_action, array('index','cart_list'))) {
		        output_error('', array('is_login' => 1, 'headimg' => tomedia(STATIC_URL . '/shop/img/default_user.png')));
		    } else {
		        output_error('', array('redirect' => '/pages/login/index'));
		    }
		}
		$member = model('member');
		$this->member_info = $member->getInfo(array('uid' => $uid));
		if ($this->member_info['status'] == 0) {
			output_error('账号审核中...');
		}
		if ($this->member_info['status'] == 2) {
			output_error('账号已被禁用');
		}
		if ($this->config['perfect_information'] == 1) {
			if (empty($this->member_info['mobile'])) {//未完善资料
				//output_error('', array('redirect' => '/pages/index/register_page'));
			}
		}
		if (empty($this->member_info['headimg'])) {
			$this->member_info['headimg'] = STATIC_URL . '/shop/img/default_user.png';
		}
		$this->member_info['headimg'] = tomedia($this->member_info['headimg']) . '?time=' . time();
		$this->member_info['nickname'] = empty($this->member_info['nickname']) ? $this->member_info['mobile'] : $this->member_info['nickname'];
		$this->member_info['uuid'] = config('uid_pre') . padNumber($this->member_info['uid']);
	}
	public function getOpenId()
    {
		$mb_user_token_info = model()->table('mb_user_token')->field('openid')->where(array('member_id' => $this->member_info['uid']))->find();
        return $mb_user_token_info['openid'];
    }
    public function setOpenId($openId)
    {
        
    }
    /**
	 * 第三方在线支付接口
	 */
    public function _new_pay_api($order_pay_info) {
        $inc_file = COMMON_PATH . '/vendor/WeChatDeveloper/include.php';
		if (!is_file($inc_file)) {
			output_error('支付SDK不存在');
		}
		require $inc_file;
		$model_mb_payment = model('mb_payment');
		$mb_payment_info = $model_mb_payment->where(array('payment_code' => $order_pay_info['payment_code'], 'payment_state' => 1))->find();
		if (!$mb_payment_info) {
			output_error('支付方式不存在');
		}
		$pay_sn = $order_pay_info['pay_sn'] . '_' . time();
        if ($order_pay_info['order_type'] == 'real_order') {
			$order_type = 'r';
		} else if ($order_pay_info['order_type'] == 'freight_order') {
			$order_type = 'f';
		} else if ($order_pay_info['order_type'] == 'pd_order') {
			$order_type = 'p';
		} else {
			$order_type = 'v';
		}
		$param = fxy_unserialize($mb_payment_info['payment_config']);
		if ($order_pay_info['payment_code'] == 'wxapp') {
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
            // 创建接口实例
            $wechat = \WeChat\Pay::instance($config);
            
            $options = array(
                'body' => $order_pay_info['subject'],
                'out_trade_no' => $pay_sn . '_' . $order_type,
                'total_fee' => (int) (100 * $order_pay_info['api_pay_amount']),
                'openid' => $this->member_info['openid'],
                'trade_type' => 'JSAPI',
                'notify_url' => _url('index/payment/notify', array('payment_code' => 'wxapp'), false, true),
                'spbill_create_ip' => get_server_ip(),
                'nonce_str' => md5(rand()),
            );
            try {
                // 生成预支付码
                $result = $wechat->createOrder($options);
                // 创建JSAPI参数签名
                $options = $wechat->createParamsForJsApi($result['prepay_id']);
                // @todo 把 $options 传到前端用js发起支付就可以了
                output_data($options);
            } catch (\Exception $e) {
                // 出错啦，处理下吧
                output_error($e->getMessage());
            }
		} else if (in_array($order_pay_info['payment_code'], array('wxpay', 'wxpay_jsapi', 'wxpay_saoma', 'wxpay_h5'))) {
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
            // 创建接口实例
            $wechat = \WeChat\Pay::instance($config);
			$options = array(
                'body' => $order_pay_info['subject'],
                'out_trade_no' => $pay_sn . '_' . $order_type,
                'total_fee' => (int) (100 * $order_pay_info['api_pay_amount']),
                'openid' => $this->member_info['openid'],
                'notify_url' => _url('index/payment/notify', array('payment_code' => $order_pay_info['payment_code']), false, true),
                'spbill_create_ip' => get_server_ip(),
                'nonce_str' => md5(rand()),
            );
            if ($order_pay_info['payment_code'] == 'wxpay_h5') {
                unset($options['openid']);
			    $options['trade_type'] = 'MWEB';
			    $options['scene_info'] = json_encode(array('h5_info' => array('type' => 'Wap', 'wap_url' => (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], 'wap_name' => '订单支付')));
			} else if ($order_pay_info['payment_code'] == 'wxpay_jsapi') {
			    $options['trade_type'] = 'JSAPI';
			} else {
			    $options['trade_type'] = '';
			}
            try {
                // 生成预支付码
                $result = $wechat->createOrder($options);
                if ($order_pay_info['payment_code'] == 'wxpay_h5') {
                    $result['order_type'] = $order_type;
                    if (!empty($result['err_code_des'])) {
						output_error($result['err_code_des']);
					}
                    output_data($result);
                }
                // 创建JSAPI参数签名
                $options = $wechat->createParamsForJsApi($result['prepay_id'] ?? '');
                // @todo 把 $options 传到前端用js发起支付就可以了
                $options['order_type'] = $order_type;
                output_data($options);
            } catch (\Exception $e) {
                // 出错啦，处理下吧
                output_error($e->getMessage());
            }
		} else if (in_array($order_pay_info['payment_code'], array('alipay'))) {
		    $config = array(
                // 沙箱模式
                'debug' => false,
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
                'notify_url' => _url('index/payment/notify', array('payment_code' => $order_pay_info['payment_code']), false, true),
                // 网页支付回跳地址
                'return_url' => _url('index/payment/return', array('payment_code' => $order_pay_info['payment_code']), false, true),
            );
		    $pay = \AliPay\Wap::instance($config);
		    $options = array(
                'out_trade_no' => $pay_sn . '_' . $order_type,
                'total_amount' => priceFormat($order_pay_info['api_pay_amount']),
                'subject' => $order_pay_info['subject'],
            );
            $result = $pay->apply($options);
            output_data($result);
		} else {
		    lib\logging::write(var_export($order_pay_info['payment_code'] . '支付代码不存在', true));
		}
    }
}