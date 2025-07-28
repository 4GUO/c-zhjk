<?php
namespace userscenter\controller;
use lib;
class shop_evaluate extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_evaluate = model('shop_evaluate_goods');
		$where = array();
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			$where[$search_type] = '%' . trim($keyword) . '%';
        }
		
		$status = input('get.status', 0, 'intval');
		if($status > 0){
			$where['geval_state'] = $status - 1;
		}
		
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['geval_addtime >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['geval_addtime <='] = $end_unixtime;
        }
        $list = $model_evaluate->getList($where, '*', 'geval_addtime DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_evaluate/index')));
        $this->assign('list', $list['list']);
		$this->display();
	}
	
	public function editOp(){
		$model_evaluate = model('shop_evaluate_goods');
		$geval_id = input('geval_id', 0, 'intval');
		if ($geval_id <= 0) {
			web_error('参数不正确！', users_url('shop_evaluate/index'));
		}
		if (chksubmit()) {			
			$status = input('status', 0, 'intval');
			$model_evaluate->edit(array('geval_id' => $geval_id), array('geval_state' => $status));
			output_data(array('msg' => '操作成功', 'url' => users_url('shop_evaluate/index')));
		} else {
			$geval_info = $model_evaluate->getInfo(array('geval_id' => $geval_id));
			if (empty($geval_info['geval_id'])) {
				web_error('该评论不存在！', users_url('shop_evaluate/index'));
			}
			$geval_info['geval_image'] = empty($geval_info['geval_image']) ? array() : fxy_unserialize($geval_info['geval_image']);
			$this->assign('geval_info', $geval_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
}