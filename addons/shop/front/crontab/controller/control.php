<?php
namespace crontab\controller;
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
	public function shutdown()
    {
        exit('success at ' . date('Y-m-d H:i:s', TIMESTAMP) . PHP_EOL);
    }
	/**
     * 记录日志
     * @param unknown $content 日志内容
     * @param boolean $if_sql 是否记录SQL
     */
    protected function log($content, $if_sql = true)
    {
        if ($if_sql) {
            $log = lib\logging::read();
            if (!empty($log) && is_array($log)) {
                $content .= end($log);
            }
        }
        lib\logging::write('queue\\' . $content);
    }
}
?>