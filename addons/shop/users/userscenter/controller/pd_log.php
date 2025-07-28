<?php
namespace userscenter\controller;
use lib;
class pd_log extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_pd = model('pd_log');
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
			$where['lg_member_id'] = $search_uids;
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
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('pd_log/index')));
        $this->assign('list', $list['list']);
		
		$mapping_fans = $member_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['lg_member_id'], $uids)){
					$uids[] = $r['lg_member_id'];
				}
			}
			$result = model('member')->getList(array('uid' => $uids), 'uid,truename,mobile,nickname,headimg,available_predeposit');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$mapping_fans[$rr['uid']] = array('nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png');
					$member_list[$rr['uid']] = $rr;
				}
			}
			unset($result);
		}

		$this->assign('member_list', $member_list);
		$this->assign('mapping_fans', $mapping_fans);
		//剩余
		$total_shengyu = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_shengyu', $total_shengyu['total']);
		//累计充值
		$where['lg_av_amount >'] = 0;
		$where['lg_type'] = array('sys_add_money', 'recharge');
		$total_chongzhi = $model_pd->field('SUM(lg_av_amount) as total')->where($where)->find();
		$this->assign('total_chongzhi', $total_chongzhi['total']);
		$this->display();
	}
	
	public function addOp() {
		if(chksubmit()) {
			$obj_validate = new lib\validate();
			$obj_validate->validateparam = array(array('input' => input('member_id', ''), 'require' => 'true', 'message' => '请填写正确的会员ID'), array('input' => input('pointsnum', ''), 'require' => 'true', 'validator' => 'Compare', 'operator' => ' >= ', 'to' => 1, 'message' => '请填写正确的金额'));
			$error = $obj_validate->validate();
			if ($error != '') {
				output_error($error);
			}
			$money = abs(floatval(input('pointsnum', '')));
			if ($money <= 0) {
				output_error('输入的金额必需大于0');
			}
			//查询会员信息
			$member_id = intval(trim(input('member_id', ''), config('uid_pre')));
			$member_info = model('member')->getInfo(array('uid' => $member_id));
			if (!is_array($member_info) || count($member_info) <= 0) {
				output_error('会员不存在');
			}
			$available_predeposit = floatval($member_info['available_predeposit']);
			$freeze_predeposit = floatval($member_info['freeze_predeposit']);
			$operatetype = input('operatetype', 0, 'intval');
			if ($operatetype == 2 && $money > $available_predeposit) {
				output_error('预存款不足，会员当前预存款' . $available_predeposit);
			}
			if ($operatetype == 3 && $money > $available_predeposit) {
				output_error('可冻结预存款不足，会员当前预存款' . $available_predeposit);
			}
			if ($operatetype == 4 && $money > $freeze_predeposit) {
				output_error('可恢复冻结预存款不足，会员当前冻结预存款' . $freeze_predeposit);
			}
			$model_pd = logic('predeposit');
			$order_sn = $model_pd->makeSn($member_id);
			$admininfo['name'] = input('session.username', '');
			$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $member_info['nickname'] . '】预存款，金额为' . $money . ',编号为' . $order_sn;
			$admin_act = 'sys_add_money';
			switch ($_POST['operatetype']) {
				case 1:
					$admin_act = 'sys_add_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $member_info['nickname'] . '】预存款【增加】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 2:
					$admin_act = 'sys_del_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $member_info['nickname'] . '】预存款【减少】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 3:
					$admin_act = 'sys_freeze_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $member_info['nickname'] . '】预存款【冻结】，金额为' . $money . ',编号为' . $order_sn;
					break;
				case 4:
					$admin_act = 'sys_unfreeze_money';
					$log_msg = '管理员【' . $admininfo['name'] . '】操作会员【' . $member_info['nickname'] . '】预存款【解冻】，金额为' . $money . ',编号为' . $order_sn;
					break;
				default:
					output_error('操作失败');
					break;
			}
			$data = array();
			$data['uniacid'] = $this->uniacid;
			$data['uid'] = $member_info['uid'];
			$data['member_name'] = $member_info['nickname'];
			$data['amount'] = $money;
			$data['order_sn'] = $order_sn;
			$data['admin_name'] = $admininfo['name'];
			$data['pdr_sn'] = $order_sn;
			$model_pd->changePd($admin_act, $data);
			output_data(array('msg' => '调节成功', 'url' => users_url('pd_log/index')));    
		}else{
			$this->display();
		}
	}
	
    public function checkmemberOp()
    {
        $name = intval(trim(input('name', ''), config('uid_pre')));
        if (!$name) {
            echo '';
            die;
        }
        if (strtoupper(CHARSET) == 'GBK') {
            $name = core\language::getGBK($name);
        }
        $obj_member = model('member');
        $member_info = $obj_member->getInfo(array('uid' => $name));
        if (is_array($member_info) && count($member_info) > 0) {
            echo json_encode(array('id' => $member_info['uid'], 'name' => $member_info['nickname'], 'available_predeposit' => $member_info['available_predeposit'], 'freeze_predeposit' => $member_info['freeze_predeposit']));
        } else {
            echo '';
            die;
        }
    }
	public function orderOp() {
		$model_pd = model('pd_recharge');
		$where = array();
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['pdr_add_time >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['pdr_add_time <='] = $end_unixtime;
        }
        $where['order_type'] = 1;
        $pdr_payment_state = input('get.pdr_payment_state', 0, 'intval');
		if($pdr_payment_state){
			$where['pdr_payment_state'] = $pdr_payment_state - 1;
		}
        $result = $model_pd->getList($where, '*', 'pdr_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('pd_log/order')));
		$list = array();
		foreach ($result['list'] as $k => $v) {
			$v['imgs'] = $v['imgs'] ? fxy_unserialize($v['imgs']) : array();
			$list[] = $v;
		}
        $this->assign('list', $list);
		
		$mapping_fans = array();
		if(!empty($result['list'])){
			$uids = array();
			foreach($result['list'] as $r){
				if(!in_array($r['pdr_member_id'], $uids)){
					$uids[] = $r['pdr_member_id'];
				}
			}
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
					$mapping_fans[$rr['uid']] = array('nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png');
				}
			}
			unset($result);
		}
		$this->assign('mapping_fans', $mapping_fans);
		$this->display();
	}
	public function order_checkOp() {
		$id = input('id', 0, 'intval');
		$model_pd = model('pd_recharge');
		$where['pdr_id'] = $id;
		$result = $model_pd->getInfo($where);
		if ($result) {
			$where['pdr_payment_state'] = 0;
			$flag = $model_pd->where($where)->update(array('pdr_payment_state' => 1, 'pdr_payment_time' => TIMESTAMP));
			if ($flag) {
				$member_info = model('member')->getInfo(array('uid' => $result['pdr_member_id']));
				$order_sn = logic('predeposit')->makeSn($member_info['uid']);
				//变更会员预存款
				$data = array();
				$data['uniacid'] = $this->uniacid;
				$data['uid'] = $member_info['uid'];
				$data['member_name'] = $member_info['nickname'];
				$data['amount'] = $result['pdr_amount'];
				$data['pdr_sn'] = $result['pdr_sn'];
				logic('predeposit')->changePd('recharge', $data);
				$access_token = logic('weixin_token')->get_access_token($this->config);
				logic('weixin_message')->addpd($access_token, $this->config, $member_info['weixin_unionid']);
				output_data(array('url' => users_url('pd_log/order')));
			} else {
				output_error('操作失败');
			}
		}
	}
	public function order_delOp(){
		$id = input('id', 0, 'intval');
		$model_pd = model('pd_recharge');
		$where['pdr_id'] = $id;
		$state = $model_pd->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('pd_log/order')));
        } else {
			output_error('删除失败！');
        }
    }
}
?>