<?php
/**
 * 任务计划 - yue执行的任务
 */
namespace crontab\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class month extends control
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
		//logic('yewu')->jiaquan_fenhong();
		//零售商城月度加权分红
		#logic('yewu')->yue_deal_fenhong();
    }
}