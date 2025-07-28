<?php
/**
 * 任务计划 - 小时执行的任务
 */
namespace crontab\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class hour extends control
{
    /**
     * 默认方法
     */
    public function indexOp() {
		$pass = input('ps', '');
		if ($pass != md5('123987')) {
			return;
		}
		//订单自动取消
        $this->_order_auto_cancel();
        $this->_vr_order_auto_cancel();
    }
    /**
     * 订单自动取消
     */
    private function _order_auto_cancel()
    {
        $_break = false;
        $model_order = model('shop_order');
        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) {
                break;
            }
			$condition = array();
			$condition['order_state'] = ORDER_STATE_NEW;
			$condition['add_time <'] = TIMESTAMP - ORDER_AUTO_CANCEL_DAY * 86400;
            $result = $model_order->getList($condition, '*', 'add_time asc', 100, 1, array('order_common', 'order_goods'));
			$order_list = $result['list'];
			unset($result);
            if (empty($order_list)) {
                break;
            }
            foreach ($order_list as $order_info) {
                $result = $model_order->changeOrderStateCancel($order_info, 'system', '系统', '超期未支付系统自动取消订单');
                if (!$result['state']) {
                    $this->log('实物订单超期未支付系统自动取消订单失败SN:' . $order_info['order_sn']);
                    $_break = true;
                    break;
                }
            }
        }
    }
    /**
     * 订单自动取消
     */
    private function _vr_order_auto_cancel()
    {
        $_break = false;
        $model_order = model('shop_vr_order');
        //分批，每批处理100个订单，最多处理5W个订单
        for ($i = 0; $i < 500; $i++) {
            if ($_break) {
                break;
            }
			$condition = array();
			$condition['order_state'] = ORDER_STATE_NEW;
			$condition['add_time <'] = TIMESTAMP - ORDER_AUTO_CANCEL_DAY * 86400;
            $result = $model_order->getList($condition, '*', 'add_time asc', 100, 1);
			$order_list = $result['list'];
			unset($result);
            if (empty($order_list)) {
                break;
            }
            foreach ($order_list as $order_info) {
                $result = $model_order->changeOrderStateCancel($order_info, 'system', '系统', '超期未支付系统自动取消订单');
                if (!$result['state']) {
                    $this->log('虚拟订单超期未支付系统自动取消订单失败SN:' . $order_info['order_sn']);
                    $_break = true;
                    break;
                }
            }
        }
    }
}