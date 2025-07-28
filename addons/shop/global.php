<?php
defined('GLOBAL_PATH') or define('GLOBAL_PATH', str_replace('\\', '/', dirname(__FILE__)));
defined('COMMON_PATH') or define('COMMON_PATH', GLOBAL_PATH . '/common');
defined('SAFE_CONST') or define('SAFE_CONST', true);
defined('TIMESTAMP') or define('TIMESTAMP', time());
defined('CHARSET') or define('CHARSET', 'UTF-8');
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
defined('IS_CLI') or define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
define('UPLOADFILES_PATH', BASE_PATH . '/public/uploadfiles');
defined('STATIC_PATH') or define('STATIC_PATH', BASE_PATH . '/public/static');
/**
 *  订单状态
 */
//已取消
defined('ORDER_STATE_CANCEL') or define('ORDER_STATE_CANCEL', 0);
//已产生但未支付
defined('ORDER_STATE_NEW') or define('ORDER_STATE_NEW', 10);
//已支付
defined('ORDER_STATE_PAY') or define('ORDER_STATE_PAY', 20);
//已发货
defined('ORDER_STATE_SEND') or define('ORDER_STATE_SEND', 30);
//已收货，交易成功
defined('ORDER_STATE_SUCCESS') or define('ORDER_STATE_SUCCESS', 40);

//未付款订单，自动取消的天数
defined('ORDER_AUTO_CANCEL_DAY') or define('ORDER_AUTO_CANCEL_DAY', 3);
//自动收货时间
defined('ORDER_AUTO_RECEIVE_DAY') or define('ORDER_AUTO_RECEIVE_DAY', 7);

//兑换码支持过期退款，可退款的期限，默认为7天
defined('CODE_INVALID_REFUND') or define('CODE_INVALID_REFUND', 7);