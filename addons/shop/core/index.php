<?php
namespace base;
//PHP环境监测
header('Content-Type:text/html;charset=utf-8');
if(version_compare(PHP_VERSION, '5.5.0', '<')) die('require PHP > 5.5.0 !');
date_default_timezone_set('PRC');
isset($_SESSION) OR session_start();
//引入基础
include(dirname(dirname(__FILE__)) . '/global.php');
defined('FRAMEWORK_PATH') or define('FRAMEWORK_PATH', __DIR__);
require FRAMEWORK_PATH . '/helpers/core.php';
config(require_cache(GLOBAL_PATH . '/config/config.ini.php'));
//安全设置项
defined('MD5_KEY') or define('MD5_KEY', '!@#$%^&*)(*&^%$#@!');
defined('COOKIE_KEY') or define('COOKIE_KEY', 'wx:ims');
defined('LOGIN_KEY') or define('LOGIN_KEY', 'login:ims');
defined('SESSION_EXPIRE') or define('SESSION_EXPIRE', 3600);
//WEB
if (!IS_CLI) {
	defined('SITE_URL') or define('SITE_URL', (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']);
	defined('UPLOADFILES_URL') or define('UPLOADFILES_URL', SITE_URL . '/uploadfiles');
	defined('STATIC_URL') or define('STATIC_URL', SITE_URL . '/static');
	defined('NODE_URL') or define('NODE_URL', SITE_URL . ':8090');
}
defined('IS_API') or define('IS_API', ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || input('get.is_api', 0, 'intval') || input('post.is_api', 0, 'intval')) ? true : false);
//辅助
require_cache(FRAMEWORK_PATH . '/helpers/extend.php');
//启动
spl_autoload_register('autoload');
base::start();
?>