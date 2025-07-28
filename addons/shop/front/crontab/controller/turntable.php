<?php
/**
 * 任务计划 - ri执行的任务
 */
namespace crontab\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class turntable extends control
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
		$do = false;
		$config = model('turntable_config')->where(array('uniacid' => $this->uniacid))->find();
		if ($config['turntable_day_start'] && $config['turntable_day_end']) {
			if ($config['turntable_day_start'] > date('N') || $config['turntable_day_end'] < date('N')) {
				$do = true;
			}
		} else if ($config['turntable_day_start'] == 0 && $config['turntable_day_end']) {
			if($config['turntable_day_end'] < date('N')){
				$do = true;
			}
		} else if ($config['turntable_day_start'] && $config['turntable_day_end'] == 0) {
			if ($config['turntable_day_start'] > date('N')) {
				$do = true;
			}
		}
		if (!empty($config['turntable_time_start']) && !empty($config['turntable_time_end'])) {
			if (strtotime($config['turntable_time_start']) > time() || strtotime($config['turntable_time_end']) < time()) {
				$do = $do && true;
			}
		}
		if ($do) {
			$model = model('turntable_item');
			$model->where(array('usestock >' => 0))->update(array('usestock' => 0));
			$list = $model->field('id,percent')->where(array('uniacid' => $this->uniacid))->select();
			foreach ($list as $k => $v) {
				$model->where(array('id' => $v['id']))->update(array('useingpercent' => $v['percent']));
			}
			model('member')->where(array('turntable_cishu >' => 0))->update(array('turntable_cishu' => 0));
		}
    }
}