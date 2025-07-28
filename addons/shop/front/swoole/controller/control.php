<?php
namespace swoole\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class control extends base\controller {
	protected $uniacid = 1;
	/**
	 * 进程server实例
	*/
	protected $_server;
	/**
	 * 端口号
	*/
	protected $_websocket_port = '9502';
	public function __construct() {
		parent::_initialize();
		$config = model('config')->where(array('uniacid' => $this->uniacid))->find();
		$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
		$wechat_setting = $wechat_setting ?: array();
		$this->config = array_merge($config, $wechat_setting);
		config($this->config);
		register_shutdown_function(array($this, 'shutdown'));
	}
	public function shutdown() {
        exit('exit at ' . date('Y-m-d H:i:s', TIMESTAMP) . PHP_EOL);
    }
	/**
     * 记录日志
     * @param unknown $content 日志内容
     *
     */
    protected function log($content) {
        lib\logging::write('swoole\\' . $content);
    }
    /**
     * 缓存管理
     * @param mixed $name 缓存名称
     * @param mixed $value 缓存值
     * @param mixed $options 缓存参数
     * @return mixed
     */
    protected function vkcache($name, $value = '', $options = null) {
        if ('' === $value) { // 获取缓存
            return $this->_server->cache->get($name);
        } elseif (is_null($value)) { // 删除缓存
            return $this->_server->cache->rm($name);
        } else { // 缓存数据
            if (is_array($options)) {
                $expire = isset($options['expire']) ? $options['expire'] : NULL;
            } else {
                $expire = is_numeric($options) ? $options : NULL;
            }
            return $this->_server->cache->set($name, $value, $expire);
        }
    }
    protected function https_post($url, $data = null) {
    	# 初始化一个cURL会话
    	$curl = curl_init();  
    	//设置请求选项, 包括具体的url
    	curl_setopt($curl, CURLOPT_URL, $url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);  //禁用后cURL将终止从服务端进行验证
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    	if (!empty($data)){
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  //设置具体的post数据
    	}
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
    	$response = curl_exec($curl);  //执行一个cURL会话并且获取相关回复
    	
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        echo $httpCode;
    	curl_close($curl);  //释放cURL句柄,关闭一个cURL会话
    	return $response;
    }
}