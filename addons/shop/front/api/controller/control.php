<?php
namespace api\controller;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class control extends base\controller
{
	protected $uniacid;
	protected $config;
	protected $sign = '';
	public function _initialize() {
		parent::_initialize();
		$this->client_type = input('client_type', 'wap');
		$this->uniacid = input('get.i', 1, 'intval') ? input('get.i', 1, 'intval') : input('post.i', 1, 'intval');
		$config = model('config')->where(array('uniacid' => $this->uniacid))->find();
		$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
		$wechat_setting = $wechat_setting ?: array();
		$this->config = array_merge($config, $wechat_setting);
		config($this->config);
		$this->sign = input('get.sign', '') ? input('get.sign', '') : input('post.sign', '');
		$sign_para = array_merge(input('get.'), input('post.'));
		if (IS_API) {
			// 注释掉签名验证
			/*
			if(input('get.sign') != getSign($sign_para, MD5_KEY)){
				//output_error('接口签名错误');
			}
			*/
		}
	}
	protected function register($response, $type) {
		$model_fans = model('fans');
		if (isset($response['inviter_id'])) {
			$oid = $response['inviter_id'];
		} else {
			$oid = input('get.oid', 0, 'intval');
		}
		$response['oid'] = $oid;
		$result = $model_fans->register($response, $type, $this->uniacid);
		if (!$result['state']) {
            return false;
        } else {
			return $result['data']['fanid'];
		}
	}
	public function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
}