<?php
/**
 * 账号日志
 **/
namespace userscenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class account_log extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function indexOp()
    {
        $model_log = model('account_log');
        $condition = array();
        $condition['uniacid'] = $this->uniacid;
		$log_users_name = input('log_users_name', '');
		$log_content = input('log_content', '');
        if (!empty($log_users_name)) {
            $condition['log_users_name'] = '%' . $log_users_name . '%';
        }
        if (!empty($log_content)) {
            $condition['log_content'] = '%' . $log_content . '%';
        }
		$add_time_from = input('add_time_from', '');
		$add_time_to = input('add_time_to', '');
		if($add_time_from){
			$condition['log_time >'] = strtotime($add_time_from);
		}
		if($add_time_to){
			$condition['log_time <'] = strtotime($add_time_to);
		}
        $log_list = $model_log->getList($condition, '*', 'item_id DESC', 20, input('page', 1, 'intval'));
        $this->assign('list', $log_list['list']);
		$this->assign('page', page($log_list['totalpage'], array('page' => input('page', 1, 'intval'), 'log_users_name' => $log_users_name, 'log_content' => $log_content, 'add_time_from' => $add_time_from, 'add_time_to' => $add_time_to), users_url('account_log/index')));
		$this->display();
    }
}