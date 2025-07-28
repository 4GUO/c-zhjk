<?php
namespace asyn\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class index extends control {
    /**
     * 默认方法
     */
    public function indexOp() {
		ignore_user_abort(true);  //忽略客户端中断
        set_time_limit(600);//最大执行10分钟
		$post = $this->checkPost();
        if ($post === false) {
            $this->log('验证失败');
            exit;
        }
        try {
            $logicArr = explode('/', $post['rule']);
			unset($post['rule']);
            $logic = logic($logicArr[0]);
            $fun = $logicArr[1];
            $res = $logic->$fun($post);
            if ($res !== true) {
                $this->log('执行失败:' . $res);
            }
        } catch (\Exception $e) {
            $this->log('执行异常：' . $e->getMessage());
        }
        exit;
    }
}