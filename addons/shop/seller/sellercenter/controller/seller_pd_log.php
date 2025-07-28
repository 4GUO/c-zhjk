<?php
namespace sellercenter\controller;
use lib;
class seller_pd_log extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$model_pd = model('seller_pd_log');
		$where = array();
		$where['lg_store_id'] = $this->store_id;
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['lg_add_time >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['lg_add_time <='] = $end_unixtime;
        }
        $list = $model_pd->getList($where, '*', 'lg_add_time DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('seller_pd_log/index')));
        $this->assign('list', $list['list']);
		//剩余
		$total_shengyu = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_shengyu', $total_shengyu['total'] ?: 0);
		//累计充值
		$where['lg_av_amount >'] = 0;
		$total_chongzhi = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_chongzhi', $total_chongzhi['total'] ?: 0);
		$this->display();
	}
}