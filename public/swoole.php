<?php
	defined('ADDONS_NAME') or define('ADDONS_NAME', 'shop');
	defined('BIND_MODULE') or define('BIND_MODULE', 'swoole');
	//入口文件的根目录
	defined('INDEX_PATH') or define('INDEX_PATH', str_replace('\\', '/', __DIR__));
	defined('BASE_PATH') or define('BASE_PATH', dirname(INDEX_PATH));
	/**
	 * 数据目录设置
	 * 安全期间，建议安装调试完成后移动到非WEB目录
	 */
	defined('DATA_PATH') or define ('DATA_PATH', BASE_PATH . '/data');
	/**
	 * 应用目录设置
	 * 安全期间，建议安装调试完成后移动到非WEB目录
	 */
	defined('APP_PATH') or define ('APP_PATH', BASE_PATH . '/addons/' . ADDONS_NAME . '/front');
	/**
	 * 核心目录设置
	 * 可移动到WEB以外的目录
	 */
	require BASE_PATH . '/addons/' . ADDONS_NAME . '/core/index.php';