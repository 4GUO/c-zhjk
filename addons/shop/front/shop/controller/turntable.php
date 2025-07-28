<?php
namespace shop\controller;
use lib;
class turntable extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$config = model('turntable_config')->where(array('uniacid' => $this->uniacid))->find();
		if (IS_API) {
			$list = $this->gift_list();
			$config['turntable_rule_tip'] = nl2br($config['turntable_rule_tip']);
			$return = array(
				'list' => array_values($list),
				'config' => $config,
			);
			output_data($return);
		} else {
			$this->assign('title', $config['turntable_name']);
			$this->assign('turntable_rule_tip', nl2br($config['turntable_rule_tip']));
			$this->assign('rulercolor', $config['rulercolor']);
			$this->display();
		}
	}
	public function getGiftOp(){
		if (IS_API) {
			$config = model('turntable_config')->where(array('uniacid' => $this->uniacid))->find();
			$lastWeek = lib\timer::lastWeek();
			$condition['payment_time >='] = $lastWeek[0];
			$condition['payment_time <='] = $lastWeek[1];
			$condition['uid'] = $this->member_info['uid'];
			$order = model('fxy_shop_order')->field('order_amount,order_state,lock_state')->where($condition)->find();
			if (empty($order)) {
				output_error('您上周没有消费，暂无抽奖资格');
			} else if ($order['order_state'] < ORDER_STATE_PAY || $order['lock_state'] != 0) {
				output_error('您上周没有消费，暂无抽奖资格');
			}
			if ($this->member_info['turntable_cishu'] >= $config['turntable_cishu']) {
				output_error('您的抽奖次数不足');
			}
			if ($config['turntable_day_start'] && $config['turntable_day_end']) {
				if ($config['turntable_day_start'] > date('N') || $config['turntable_day_end'] < date('N')) {
					output_error($config['turntable_tip']);
				}
			} else if ($config['turntable_day_start'] == 0 && $config['turntable_day_end']) {
				if($config['turntable_day_end'] < date('N')){
					output_error($config['turntable_tip']);
				}
			} else if ($config['turntable_day_start'] && $config['turntable_day_end'] == 0) {
				if ($config['turntable_day_start'] > date('N')) {
					output_error($config['turntable_tip']);
				}
			}
			if (!empty($config['turntable_time_start']) && !empty($config['turntable_time_end'])) {
				if (strtotime($config['turntable_time_start']) > time() || strtotime($config['turntable_time_end']) < time()) {
					output_error($config['turntable_tip']);
				}
			}
			
			$list = $this->gift_list();
			//找出空奖
			$empty_result = array();
			foreach ($list as $k => $v) {
				$reward_ratio = (float)$v['reward_ratio'];
				if (empty($reward_ratio)) {
					$v['error_msg'] = '';
					$empty_result = $v;
					break;
				}
			}
			//奖励
			$percent = floor(lcg_value() * 100);
			$totalPercent = 0;
			$result = array();
			foreach ($list as $k => $v) {
				$totalPercent += floor($v['useingpercent']);
				if ($percent <= $totalPercent) {
					$v['error_msg'] = '';
					$result = $v;
					break;
				}
			}
			if (!empty($result['reward_ratio'])) {
				if ($result['stock'] <= $result['usestock']) {
					//库存已经抢完，返回空奖
					$result = $empty_result;
					lib\logging::write(var_export('库存已经抢完，返回空奖', true));
				} else {
					$flag = model('turntable_item')->where(array('id' => $result['id'], 'stock >' => $result['usestock']))->update('usestock=usestock+1');
					if ($flag) {
						$check = model('turntable_item')->field('stock,usestock')->where(array('id' => $result['id']))->find();
						if ($check['stock'] <= $check['usestock']) {
							//如果没有库存了，则重新分配中奖概率
							model('turntable_item')->where(array('id' => $empty_result['id']))->update('useingpercent=useingpercent+' . $result['useingpercent']);
							model('turntable_item')->where(array('id' => $result['id']))->update(array('useingpercent' => 0));
						}
						$r = logic('yewu')->turntable_reward($result, $this->member_info, $config);
						if (!$r['state']) {
							output_error($r['msg']);
						}
					} else {
						//返回空奖励（防止并发）
						$result = $empty_result;
						lib\logging::write(var_export('如果没有库存了，则重新分配中奖概率，并返回空奖励（防止零界点）', true));
					}
					
				}
			} else {
				//没有找到中奖项或是找到的中奖项的奖励为空，则返回谢谢参与
				$result = $empty_result;
				lib\logging::write(var_export('没有找到中奖项或是找到的中奖项的奖励为空，则返回谢谢参与', true));
			}
			//记录抽奖次数
			model('member')->where(array('uid' => $this->member_info['uid']))->update('turntable_cishu=turntable_cishu+1');
			output_data($result);
		}
	}
	private function gift_list() {
		$result = model('turntable_item')->where(array('uniacid' => $this->uniacid))->order('sort ASC')->select();
		$list = array();
		foreach ($result as $k => $v) {
			$list[$v['id']] = array(
				'id' => $v['id'],
				'name' => $v['title'],
				'image' => !empty($v['thumb']) ? $v['thumb'] : null,
				'rank' => $k + 1,
				'percent' => (float)$v['percent'],
				'useingpercent' => (float)$v['useingpercent'],
				'color' => $v['color'],
				'reward_ratio' => (float)$v['reward_ratio'],
				'stock' => $v['stock'],
				'usestock' => $v['usestock'],
			);
		}
		unset($result);
		return $list;
	}
	public function commission_recordOp() {
		if (IS_API) {
			$this->title = '中奖记录';
			$model_distribute_detail = model('distribute_turntable_record_detail');
			$lists = array();
			//$lastWeek = lib\timer::lastWeek();
			//$where['detail_addtime >='] = $lastWeek[0];
			//$where['detail_addtime <='] = $lastWeek[1];
			$where['detail_status'] = array(ORDER_STATE_NEW,ORDER_STATE_PAY,ORDER_STATE_SEND,ORDER_STATE_SUCCESS);
			$data = $model_distribute_detail->getList($where, '*', 'detail_id desc', 18, input('page', 1, 'intval'));
			foreach($data['list'] as $key => $value){
				$lists[] = array(
					'uid' => $value['uid'],
					'record_status' => $value['detail_status'],
					'desc' => $value['detail_desc2'] . '，获得奖励' . $value['detail_bonus'] . '元',
					'addtime' => date('Y-m-d H:i:s', $value['detail_addtime']),
				);
			}
			$mapping_fans = array();
			if(!empty($data['list'])){
				$uids = array();
				foreach($data['list'] as $r){
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
			$return = array(
				'title' => $this->title,
				'mapping_fans' => $mapping_fans,
				'list' => $lists,
				'totalpage' => $data['totalpage'],
				'hasmore' => $data['hasmore'],
			);
			unset($data);
			output_data($return);
		} else {
			$this->display();
		}
	}
}
?>