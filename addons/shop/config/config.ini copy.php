<?php
defined('SAFE_CONST') or exit('Access Invalid!');
$config['default_filter'] = '';// input方法默认参数过滤方法
$config['db']['deploy'] = 0; // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
$config['db']['rw_separate'] = true;// 数据库读写是否分离 主从式有效
$config['db']['master_num'] = 1;// 读写分离后 主服务器数量
$config['db']['slave_no'] = '';// 指定从服务器序号
$config['db']['read_master'] = true; // 数据写入后自动读取主服务器
$config['db']['dsn'] = '';
$config['db']['dbhost']  = '127.0.0.1';
$config['db']['dbport'] = '3306';
$config['db']['dbuser']  = 'ttm_douyakeji_ne';
$config['db']['dbpwd']  = 'KNzTRPPXtD8pzfRe';
$config['db']['dbname'] = 'ttm_douyakeji_ne';
$config['db']['dbcharset'] = 'utf8mb4';
$config['db']['dbprefix'] = 'ims_';
$config['db']['break_reconnect'] = false;// 是否需要断线重连
$config['db']['break_match_str'] = array();
$config['db']['db_params'] = array();
$config['db']['db_fields_cache'] = false;
$config['db']['db_debug'] = false;
$config['debug'] = true;
$config['md5key'] = 'y4CwHNWfJoxqF5jyFsRbXETL_fTAefILykTLzgEsCSBbjCORj7RKmFO73_28W2j_Kzc';//加密盐，不可修改
$config['image_max_filesize'] = 5 * 1024;//单位是kb 默认2M
$config['file_max_filesize'] = 5 * 1024;//单位是kb 默认5M
$config['cache'] = array(
	'open' => false, #数据库缓存开关
    'default' => [
		'type' => 'redis',
        'host' => '127.0.0.1',
		'port' => 6379,
		'expire' => 0,
		'prefix' => 'ims',
    ],
	'file' => [
		'type' => 'file',
		'expire' => 0,
		'prefix' => 'ims',
    ],
    'redis' => [
        'type' => 'redis',
        'host' => '127.0.0.1',
		'port' => 6379,
		'expire' => 0,
		'prefix' => 'ims',
    ],
);
$config['url_router_on'] = true;
//URL映射，可用于隐藏真实URL
$config['url_map_rules'] = array(
    
);
$config['module_map_rules'] = array(
	'index' => array(
		'APP_PATH' => 'front',
		'BIND_MODULE' => 'shop',
	),
	'api' => array(
		'APP_PATH' => 'front',
		'BIND_MODULE' => 'api',
	),
    'boss' => array(
		'APP_PATH' => 'users',
		'BIND_MODULE' => 'userscenter',
	),
	'store' => array(
		'APP_PATH' => 'seller',
		'BIND_MODULE' => 'sellercenter',
	),
);
//客服
$config['node_chat'] = true;
//ES//
$config['db_config_elasticsearch'] = array (
	'db_type' => 'elasticsearch', 
	'db_host' => '127.0.0.1',
	'db_port' => '9200',
	'db_index' => 'article', 
	'db_table' => 'article'
);
$config['uid_pre'] = 'ZH';
return $config;

