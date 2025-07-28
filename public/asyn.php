<?php
	define('ADDONS_NAME', 'shop');
	define('BIND_MODULE', 'asyn');
	//入口文件的根目录
	define('INDEX_PATH', str_replace('\\', '/', __DIR__));
	define('BASE_PATH', dirname(INDEX_PATH));
	/**
	 * 数据目录设置
	 * 安全期间，建议安装调试完成后移动到非WEB目录
	 */
	define ('DATA_PATH', BASE_PATH . '/data');
	/**
	 * 应用目录设置
	 * 安全期间，建议安装调试完成后移动到非WEB目录
	 */
	define ('APP_PATH', BASE_PATH . '/addons/' . ADDONS_NAME . '/front');
	/**
	 * 核心目录设置
	 * 可移动到WEB以外的目录
	 */
	require BASE_PATH . '/addons/' . ADDONS_NAME . '/core/index.php';