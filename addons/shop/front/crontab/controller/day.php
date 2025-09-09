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
    /**
     * 批量处理提货券转分红券
     */
    public function batch_tihuoquan_to_fenhongquanOp()
    {
        $pass = input('ps', '');
        if ($pass != md5('123987')) {
            return;
        }

        // 记录开始时间
        $start_time = time();
        $log_data = array(
            'start_time' => date('Y-m-d H:i:s', $start_time),
            'processed_count' => 0,
            'success_count' => 0,
            'error_count' => 0,
            'errors' => array(),
            'success_users' => array()
        );

        // 分批处理，每批处理100个用户
        $batch_size = 100;
        $processed_count = 0;
        $success_count = 0;
        $error_count = 0;

        // 循环处理所有符合条件的用户
        while (true) {
            // 查找所有can_tihuoquan_num > 1的用户
            $condition = array('can_tihuoquan_num >' => 1);
            $member_list = model('member')->getList($condition, 'uid,nickname,mobile,can_tihuoquan_num', 'uid ASC', $batch_size, 1);

            if (empty($member_list['list'])) {
                break; // 没有更多数据，退出循环
            }

            foreach ($member_list['list'] as $member_info) {
                $processed_count++;

                try {
                    // 调用提货券转分红券方法
                    $result = logic('shop_queue')->tihuoquan_to_fenhongquan(array('uid' => $member_info['uid']));

                    if ($result) {
                        $success_count++;
                        $log_data['success_users'][] = array(
                            'uid' => $member_info['uid'],
                            'nickname' => $member_info['nickname'],
                            'mobile' => $member_info['mobile'],
                            'can_tihuoquan_num' => $member_info['can_tihuoquan_num'],
                            'process_time' => date('Y-m-d H:i:s')
                        );
                    } else {
                        $error_count++;
                        $log_data['errors'][] = array(
                            'uid' => $member_info['uid'],
                            'nickname' => $member_info['nickname'],
                            'mobile' => $member_info['mobile'],
                            'error' => '处理失败',
                            'process_time' => date('Y-m-d H:i:s')
                        );
                    }
                } catch (\Exception $e) {
                    $error_count++;
                    $log_data['errors'][] = array(
                        'uid' => $member_info['uid'],
                        'nickname' => $member_info['nickname'],
                        'mobile' => $member_info['mobile'],
                        'error' => $e->getMessage(),
                        'process_time' => date('Y-m-d H:i:s')
                    );
                }
            }

            // 如果这批数据少于批次大小，说明已经处理完所有数据
            if (count($member_list['list']) < $batch_size) {
                break;
            }
        }

        // 记录结束时间和统计信息
        $end_time = time();
        $log_data['end_time'] = date('Y-m-d H:i:s', $end_time);
        $log_data['execution_time'] = $end_time - $start_time . '秒';
        $log_data['processed_count'] = $processed_count;
        $log_data['success_count'] = $success_count;
        $log_data['error_count'] = $error_count;

        // 输出结果
        $result = array(
            'msg' => '批量提货券转分红券任务执行完成',
            'processed_count' => $processed_count,
            'success_count' => $success_count,
            'error_count' => $error_count,
            'execution_time' => $log_data['execution_time'],
            'log_data' => $log_data
        );

        // 如果是API请求，返回JSON格式
        if (input('is_api', 0)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {
            // 普通请求，输出HTML格式
            echo '<pre>' . print_r($result, true) . '</pre>';
        }
    }
}
