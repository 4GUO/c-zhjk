<?php
namespace asyn\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class control extends base\controller {
	protected $uniacid;
	public function __construct() {
		parent::_initialize();
		$this->uniacid = input('get.i', 1, 'intval') ? input('get.i', 1, 'intval') : input('post.i', 1, 'intval');
		$config = model('config')->where(array('uniacid' => $this->uniacid))->find();
		$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
		$wechat_setting = $wechat_setting ?: array();
		$this->config = array_merge($config, $wechat_setting);
		config($this->config);
		register_shutdown_function(array($this, 'shutdown'));
	}
	public function shutdown() {
        exit('success at ' . date('Y-m-d H:i:s', TIMESTAMP) . PHP_EOL);
    }
	/**
     * 记录日志
     * @param unknown $content 日志内容
     *
     */
    protected function log($content) {
        lib\logging::write('asyn\\' . $content);
    }
	protected function checkPost() {
        $post = $_POST;
        // 注释掉签名验证，直接返回POST数据
        return $post;
        
        /*
        //验证请求是否有效
        $sign = isset($post['sign']) ? $post['sign'] : '';
        if (empty($sign)){
           return false;
        }
        unset($post['sign']);
        if (time() - $post['postsigntime'] > $post['timeout']) {
           return false;
        }
        $query = http_build_query($post);
        $_sign = md5($query . config('md5key'));
        if ($_sign != $sign){
           return false;
        }
        unset($post['postsigntime']);
        unset($post['timeout']);
        return $post;
        */
    }
}