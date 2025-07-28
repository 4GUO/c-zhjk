<?php
namespace userscenter\controller;
use lib;
class seller_pd_log extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$model_pd = model('seller_pd_log');
		$where = array();
		$keyword = input('get.keyword', '');
		$search_type = input('get.search_type', '');
        if ($keyword) {
			$search_ids[] = 0;
			$result = model('seller')->getList(array($search_type => '%' . trim($keyword) . '%'), 'id');
			foreach($result['list'] as $r){
				$search_ids[] = $r['id'];
			}
			$where['lg_store_id'] = $search_ids;
        }
		
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
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('seller_pd_log/index')));
        $this->assign('list', $list['list']);
		
		$store_list = array();
		if(!empty($list['list'])){
			$ids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['lg_store_id'], $ids)){
					$ids[] = $r['lg_store_id'];
				}
			}
			$result = model('seller')->getList(array('id' => $ids), 'id,name,logo');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$store_list[$rr['id']] = array('name' => $rr['name'], 'logo' => !empty($rr['logo']) ? $rr['logo'] : STATIC_URL . '/shop/img/default_user.png');
				}
			}
			unset($result);
		}

		$this->assign('store_list', $store_list);
		//剩余
		$total_shengyu = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_shengyu', $total_shengyu['total'] ?: 0);
		//累计充值
		$where['lg_type'] = array('sys_add_money', 'recharge');
		$total_chongzhi = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_chongzhi', $total_chongzhi['total'] ?: 0);
		$this->display();
	}
	
	public function addOp(){
		if(chksubmit()){
			$obj_validate = new lib\validate();
			$obj_validate->validateparam = array(array('input' => input('store_id', ''), 'require' => 'true', 'message' => '请填写正确的商家ID'), array('input' => input('pointsnum', ''), 'require' => 'true', 'validator' => 'Compare', 'operator' => ' >= ', 'to' => 1, 'message' => '请填写正确的金额'));
			$error = $obj_validate->validate();
			if ($error != '') {
				output_error($error);
			}
			$money = abs(floatval(input('pointsnum', '')));
			if ($money <= 0) {
				output_error('输入的金额必需大于0');
			}
			//查询商家信息
			$store_id = input('store_id', 0, 'intval');
			$seller_info = model('seller')->getInfo(array('id' => $store_id));
			if (!is_array($seller_info) || count($seller_info) <= 0) {
				output_error('商家不存在');
			}
			$available_predeposit = floatval($seller_info['available_predeposit']);
			$freeze_predeposit = floatval($seller_info['freeze_predeposit']);
			$operatetype = input('operatetype', 0, 'intval');
			if ($operatetype == 2 && $money > $available_predeposit) {
				output_error('预存款不足，商家当前预存款' . $available_predeposit);
			}
			if ($operatetype == 3 && $money > $available_predeposit) {
				output_error('可冻结预存款不足，商家当前预存款' . $available_predeposit);
			}
			if ($operatetype == 4 && $money > $freeze_predeposit) {
				output_error('可恢复冻结预存款不足，商家当前冻结预存款' . $freeze_predeposit);
			}
			$model_pd = logic('seller_predeposit');
			$order_sn = $model_pd->makeSn($store_id);
			$admininfo['name'] = input('session.username', '');
			$log_msg = '管理员【' . $admininfo['name'] . '】操作商家【' . $seller_info['name'] . '】预存款，金额为' . $money . ',编号为' . $order_sn;
			$admin_act = 'sys_add_money';
			switch ($_POST['operatetype']) {
				case 1:
					$admin_act = 'sys_add_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作商家【' . $seller_info['name'] . '】预存款【增加】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 2:
					$admin_act = 'sys_del_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $seller_info['name'] . '】预存款【减少】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 3:
					$admin_act = 'sys_freeze_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $seller_info['name'] . '】预存款【冻结】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 4:
					$admin_act = 'sys_unfreeze_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $seller_info['name'] . '】预存款【解冻】，金额为' . $money . ',编号为' . $order_sn;
					break;
				default:
					output_error('操作失败');
					break;
			}
			$data = array();
			$data['store_id'] = $seller_info['id'];
			$data['store_name'] = $seller_info['name'];
			$data['amount'] = $money;
			$data['order_sn'] = $order_sn;
			$data['admin_name'] = $admininfo['name'];
			$data['pdr_sn'] = $order_sn;
			$model_pd->changePd($admin_act, $data);
			output_data(array('msg' => '调节成功', 'url' => users_url('seller_pd_log/index')));    
		}else{
			$this->display();
		}
	}
	
    public function checkstoreOp()
    {
        $name = input('name', '');
        if (!$name) {
            echo '';
            die;
        }
        $obj_model = model('seller');
        $seller_info = $obj_model->getInfo(array('id' => $name));
        if (is_array($seller_info) && count($seller_info) > 0) {
            echo json_encode(array('id' => $seller_info['id'], 'name' => $seller_info['name'], 'available_predeposit' => $seller_info['available_predeposit'], 'freeze_predeposit' => $seller_info['freeze_predeposit']));
        } else {
            echo '';
            die;
        }
    }
}