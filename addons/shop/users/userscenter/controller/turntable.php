<?php
namespace userscenter\controller;
class turntable extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list = model('turntable_item')->where(array('uniacid' => $this->uniacid))->order('sort ASC')->select();
		$this->assign('list', $list);
		$this->display();
	}
	public function addOp(){
		$model_class = model('turntable_item');
		if(chksubmit()){
			$class_array = array();
            $class_array['title'] = input('post.title', '');
            $class_array['thumb'] = input('post.thumb', '');
            $class_array['uniacid'] = $this->uniacid;
            $class_array['percent'] = input('post.percent', '');
			$class_array['useingpercent'] = input('post.percent', '');
			$class_array['reward_ratio'] = input('post.reward_ratio', '');
			$class_array['stock'] = input('post.stock', 0);
			$class_array['sort'] = input('post.sort', 1, 'intval');
			$class_array['color'] = input('post.color', '');
            $state = $model_class->insert($class_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('turntable/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_class = model('turntable_item');
		if(chksubmit()){
			$class_array = array();
            $class_array['title'] = input('post.title', '');
            $class_array['thumb'] = input('post.thumb', '');
            $class_array['percent'] = input('post.percent', '');
			$class_array['useingpercent'] = input('post.percent', '');
			$class_array['reward_ratio'] = input('post.reward_ratio', '');
			$class_array['stock'] = input('post.stock', 0);
			$class_array['usestock'] = 0;
			$class_array['sort'] = input('post.sort', 1, 'intval');
			$class_array['color'] = input('post.color', '');
            $state = $model_class->where(array('id' => input('post.id', 0, 'intval')))->update($class_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('turntable/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$id = input('get.id', 0, 'intval');
			$class_info = model('turntable_item')->where(array('id' => $id))->find();
			$this->assign('info', $class_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('turntable_item');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('turntable/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function configOp(){
		if(chksubmit()){
			$turntable_time_start = str_replace('：', ':', input('turntable_time_start', ''));
			$turntable_time_end = str_replace('：', ':', input('turntable_time_end', ''));
			$data = array(
				'turntable_name' => input('turntable_name', ''),
				'turntable_rule_tip' => input('turntable_rule_tip', ''),
				'week_sales_bili' => input('week_sales_bili', 0),
				'fontcolor' => input('fontcolor', ''),
				'outercolor' => input('outercolor', ''),
				'innercolor' => input('innercolor', ''),
				'pointcolor1' => input('pointcolor1', ''),
				'pointcolor2' => input('pointcolor2', ''),
				'rulercolor' => input('rulercolor', ''),
				'turntable_day_start' => input('turntable_day_start', ''),
				'turntable_day_end' => input('turntable_day_end', ''),
				'turntable_time_start' => $turntable_time_start,
				'turntable_time_end' => $turntable_time_end,
				'turntable_tip' => input('turntable_tip', ''),
				'turntable_cishu' => input('turntable_cishu', 0, 'intval'),
			);
			$state = model('turntable_config')->where(array('uniacid' => $this->uniacid))->update($data);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('turntable/config')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$config = model('turntable_config')->where(array('uniacid' => $this->uniacid))->find();
			$this->assign('config', $config);
			$this->display();
		}
	}
	public function award_listOp() {
		$distribute_detail = model('distribute_turntable_record_detail');
		$where = array();
        $where['uniacid'] = $this->uniacid;
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			$search_uids = array();
			$search_uids[] = 0;
			$result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
			foreach($result['list'] as $r){
				$search_uids[] = $r['uid'];
			}
			$where['uid'] = $search_uids;
        }
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['detail_addtime >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['detail_addtime <='] = $end_unixtime;
        }
        $list = $distribute_detail->getList($where, '*', 'detail_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('turntable/award_list')));
        $this->assign('list', $list['list']);
		
		$mapping_fans = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['uid'], $uids)){
					$uids[] = $r['uid'];
				}
			}
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$mapping_fans[$rr['uid']] = array('nickname' => $rr['nickname'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png', 'mobile' => $rr['mobile']);
				}
			}
			unset($result);
		}
		$this->assign('mapping_fans', $mapping_fans);
		$this->display();
	}
}