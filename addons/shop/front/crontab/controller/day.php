<?php
/**
 * 任务计划 - 天执行的任务
 */
namespace crontab\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class day extends control
{
    /**
     * 默认方法
     */
    public function indexOp()
    {
		$pass = input('ps', '');
		if ($pass != md5('123987')) {
			return;
		}
        //订单自动完成
        $this->_order_auto_complete();
    }
    /**
     * 订单自动完成
     */
    private function _order_auto_complete()
    {
        //实物订单发货后，超期自动收货完成
        $_break = false;
        $model_order = model('shop_order');
        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) {
                break;
            }
			$condition = array();
			$condition['order_state'] = ORDER_STATE_SEND;
			$condition['lock_state'] = 0;
			$condition['delay_time <'] = TIMESTAMP - ORDER_AUTO_RECEIVE_DAY * 86400;
            $result = $model_order->getList($condition, '*', 'delay_time asc', 100, 1, array('order_common', 'order_goods'));
			$order_list = $result['list'];
			unset($result);
            if (empty($order_list)) {
                break;
            }
            foreach ($order_list as $order_info) {
                $result = $model_order->changeOrderStateReceive($order_info, 'system', '系统', '超期未收货系统自动完成订单');
                if (!$result['state']) {
                    $this->log('实物订单超期未收货自动完成订单失败SN:' . $order_info['order_sn']);
                    $_break = true;
                    break;
                }
            }
        }
    }
}