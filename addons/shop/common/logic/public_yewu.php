<?php
/**
 * 业务模块处理
 *
 */
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class public_yewu
{
	public function deal_public($member_id, $goods_commonids = array(), $pay_amount = 0) {
		$add_flag = false;
		if (config('public_cometype') == 1 && $pay_amount >= config('public_come_money')) {
			$add_flag = true;
		}
		if (config('public_cometype') == 2) {
			$goods_ids = explode(',', trim(config('public_goods_ids'), ','));
			$check_intersect = array_intersect($goods_commonids, $goods_ids);
			if (!empty($check_intersect)) {
				$add_flag = true;
			}
		}
		if (config('public_cometype') == 3) {
			$add_flag = true;
		}
		if ($add_flag) {
			$member_info = model('member')->getInfo(array('uid' => $member_id));
			$this->add_public_account($member_info);
		}
	}
	public function add_public_account($member_info) {
		if (empty($member_info['uid'])) {
			return false;
		}
		if(config('public_multi') == 0) {
			$public_check = model('distributor_gp')->getInfo(array('member_id' => $member_info['uid']),'status','ralate_id DESC');
			if(!empty($public_check)) {
				return true;
			}
		}
		$model_member = model('member');
		if ($member_info['inviter_id'] == 0) {//顶级判断是否首次排位
			$my_gp = model('distributor_gp')->getInfo(array('member_id' => $member_info['uid']), 'ralate_id', 'ralate_id DESC');
			if (!empty($my_gp)) {//禁止顶级多次排位
				return false;
			}
		}
		//获得坐标、根id、公排上级id、公排上级父路径
		$position = $this->get_position_gj($member_info['uid'], $member_info['inviter_id'], config('public_times'));
		
		if (empty($position)) {
			return false;
		}
		
		$public = array();
		$public['member_id'] = $member_info['uid'];
		$public['rootid'] = $position['rootid'];
		$public['inviterid'] = $member_info['inviter_id'];
		$public['parentpath'] = $position['parentpath'];
		$public['parentid'] = $position['parentid'];
		$public['distributor_y'] = $position['distributor_y'];
		$public['distributor_x'] = $position['distributor_x'];
		$public['status'] = 1;
		$public['addtime'] = time();
		
		$message_data = array();
		$message_data[] = array(
			'member_id' => $member_info['uid'],
			'text' => '您已成功卡位，位置第' . $position['distributor_y'] . '级第' . $position['distributor_x'] . '个'
		);
		
		$flag = true;
		$model = model();
		$model->beginTransaction();
		
		//插入排位记录
		$relate_id = model('distributor_gp')->add($public);
		$flag = $flag && $relate_id;
		
		$commission_data = array();
		
		//向上级发送红包
		if (!empty(config('public_commission')) && !empty($position['parentpath'])) {
			
			$parent = explode(',', trim($position['parentpath'], ','));
			$parent = array_reverse($parent);
			if (count($parent) > config('public_bonus_level')) {
				$parent = array_slice($parent, 0, config('public_bonus_level'));
			}
			$rewardss = config('public_commission') ? fxy_unserialize(config('public_commission')) : array();
			
			foreach($parent as $key => $value) {
				$commission_data[] = array(
					'record_id' => $relate_id,
					'member_id' => $value,
					'detail_type' => 'level',
					'detail_level' => $key + 1,
					'detail_bonus' => empty($rewardss[$key + 1]) ? 0 : $rewardss[$key + 1],
					'detail_desc' => '会员[' . $member_info['nickname'] . ']排位到你的' . ($key + 1) . '级，获得见点奖红包' . (empty($rewardss[$key + 1]) ? 0 : $rewardss[$key + 1]) . '元',
					'detail_addtime' => time(),
					'detail_status' => 1,
				);
				if (!empty($rewardss[$key + 1])) {
					$message_data[] = array(
						'member_id' => $value,
						'text' => '会员[' . $member_info['nickname'] . ']排位到你的' . ($key + 1) . '级，获得见点奖红包' . (empty($rewardss[$key + 1]) ? 0 : $rewardss[$key + 1]) . '元'
					);
				}
			}
		}
		//直接推荐奖
		if (!empty(config('public_inviter')) && config('public_inviter') > 0 && !empty($member_info['inviter_id'])) {
			$commission_data[] = array(
				'record_id' => $relate_id,
				'member_id' => $member_info['inviter_id'],
				'detail_type' => 'invite',
				'detail_level' => 0,
				'detail_bonus' => config('public_inviter'),
				'detail_desc' => '你推荐的会员[' . $member_info['nickname'] . ']进行排位,获得推荐奖红包' . config('public_inviter') . '元',
				'detail_addtime' => time(),
				'detail_status' => 1,
			);
			$message_data[] = array(
				'member_id' => $member_info['inviter_id'],
				'text' => '你推荐的会员[' . $member_info['nickname'] . ']进行排位,获得推荐奖红包' . config('public_inviter') . '元'
			);
		}
		
		//懒人奖
		if (!empty(config('public_parent')) && config('public_parent') > 0 && !empty($position['parentid'])) {
			$commission_data[] = array(
				'record_id' => $relate_id,
				'member_id' => $position['parentid'],
				'detail_type' => 'parent',
				'detail_level' => 0,
				'detail_bonus' => config('public_parent'),
				'detail_desc' => '会员[' . $member_info['nickname'] . ']排位到你的下级,获得懒人奖红包' . config('public_parent') . '元',
				'detail_addtime'=>time(),
				'detail_status' => 1,
			);
			$message_data[] = array(
				'member_id' => $position['parentid'],
				'text' => '会员[' . $member_info['nickname'] . ']排位到你的下级,获得懒人奖红包' . config('public_parent') . '元'
			);
		}
		
		//感恩奖
		if (!empty(config('public_out_open')) && !empty(config('public_out_level')) && ($position['distributor_x'] % (pow(config('public_times'), config('public_out_level')))) == 0) {
			$condition_1['distributor_y'] = $position['distributor_y'] - config('public_out_level');
			$condition_1['distributor_x'] = $position['distributor_x'] / (pow(config('public_times'), config('public_out_level')));
			$condition_1['status'] = 1;
			$chuju_info = model('distributor_gp')->getInfo($condition_1, 'ralate_id,member_id');
			if (!empty($chuju_info)) {
				$result = model('distributor_gp')->edit(array('ralate_id' => $chuju_info['ralate_id']), array('status' => 0));
				$flag = $flag && $result;
				unset($result);
				
				$message_data[] = array(
					'member_id' => $chuju_info['member_id'],
					'text' => '你已出局!欢迎复投,重新卡位'
				);
				
				$chuju_member = $model_member->getInfo(array('uid' => $chuju_info['member_id']));
				if (!empty($chuju_member)) {
					if (!empty($chuju_member['inviter_id']) && !empty(config('public_thankful'))) {
						$commission_data[] = array(
							'record_id' => $relate_id,
							'member_id' => $chuju_member['inviter_id'],
							'detail_type' => 'thankful',
							'detail_level' => 0,
							'detail_bonus' => config('public_thankful'),
							'detail_desc' => '你推荐的会员[' . $chuju_member['nickname'] . ']出局,获得感恩奖' . config('public_thankful') . '元',
							'detail_addtime' => time(),
							'detail_status' => 1,
						);
						$message_data[] = array(
							'member_id' => $chuju_member['inviter_id'],
							'text' => '你推荐的会员[' . $chuju_member['nickname'] . ']出局,获得感恩奖' . config('public_thankful') . '元'
						);
					}
				}
			}			
		}
		
		
		if (!empty($commission_data)) {
			$result = model('distributor_gp_detail')->insertAll($commission_data);
			$flag = $flag && $result;
		}
		
		if ($flag) {
			$model->commit();
			
			/*发送卡位相关消息*/
			$access_token = logic('weixin_token')->get_access_token(config());
			if (!empty($message_data)) {
				logic('weixin_message')->sendpublicmess($access_token, config(), $message_data);
			}
		} else {
			$model->rollback();
		}
		return $flag;
	}
	/*
	*排位获取坐标、上级等信息(国际公排)
	*/
	private function get_position_gj($member_id, $inviter_id, $times = 2) {
		$poistion = array();
		//获取最后一个公排信息
		$last = model('distributor_gp')->getInfo(array(), 'distributor_y,distributor_x,rootid', 'distributor_y desc,distributor_x desc');
		if (empty($last['distributor_y'])) {
			return array(
				'rootid' => $member_id,
				'parentpath' => '',
				'parentid' => 0,
				'distributor_y' => 1,
				'distributor_x' => 1
			);
		}
		
		$poistion['rootid'] = $last['rootid'];
		if ($last['distributor_x'] >= pow($times, ($last['distributor_y'] - 1))) {
			$poistion['distributor_y'] = $last['distributor_y'] + 1;
			$poistion['distributor_x'] = 1;
		} else {
			$poistion['distributor_y'] = $last['distributor_y'];
			$poistion['distributor_x'] = $last['distributor_x'] + 1;
		}
		
		//获取上级坐标
		$condition['distributor_y'] = $poistion['distributor_y'] - 1;
		if (fmod($poistion['distributor_x'], $times) == 0) {
			$condition['distributor_x'] = intval($poistion['distributor_x'] / $times);
		} else {
			$condition['distributor_x'] = intval($poistion['distributor_x'] / $times) + 1;
		}
		$parent = model('distributor_gp')->getInfo($condition, 'parentpath,member_id');
		if (empty($parent)) {
			return array();
		}
		
		$poistion['parentid'] = $parent['member_id'];
		$poistion['parentpath'] = empty($parent['parentpath']) ? ',' . $parent['member_id'] . ',' : $parent['parentpath'] . $parent['member_id'] . ',';
		return $poistion;
	}
	/*
	 *排位获取坐标、上级等信息(团队公排)
	 */
	private function get_position_team($user_id, $inviter_id, $times = 2) {
		//第一级
		if (empty($inviter_id)) {
			return array(
				'rootid' => $user_id,
				'parentpath' => '',
				'parentid' => 0,
				'distributor_y' => 1,
				'distributor_x' => 1
			);
		}
		
		//获得推荐人信息
		$gp_info = model('distributor_gp')->getInfo(array('member_id' => $inviter_id), '*', 'ralate_id asc');
		if (empty($gp_info)) {
			return array();
		}
		
		$poistion = array();
		$poistion['rootid'] = empty($gp_info['rootid']) ? $inviter_id : $gp_info['rootid'];
		
		//获得该推荐人的每层下级会员数和最大x坐标;rootid 根id，同一个rootid表示在同一条金字塔线上
		
		$result = model()->query('select distributor_y,MAX(distributor_x) as distributor_x,count(distributor_x) as num from ims_fxy_distributor_gp where rootid=' . $gp_info['rootid'] . ' and distributor_y>' . $gp_info['distributor_y'] . ' and distributor_x>=power(' . $times . ',distributor_y-' . $gp_info['distributor_y'] . ')*(' . $gp_info['distributor_x'] . '-1)+1 and distributor_x<=power(' . $times . ',distributor_y-' . $gp_info['distributor_y'] . ')*' . $gp_info['distributor_x'] . ' group by distributor_y order by distributor_y asc');
		if (empty($result) || $result == false) {//无下级（自动滑落到推荐人第一个位置）
			$poistion['parentid'] = $inviter_id;
			$poistion['distributor_y'] = $gp_info['distributor_y'] + 1;
			$poistion['distributor_x'] = $times * ($gp_info['distributor_x'] - 1) + 1;
			$poistion['parentpath'] = empty($gp_info['parentpath']) ? ',' . $inviter_id . ',' : $gp_info['parentpath'] . $inviter_id . ',';
			return $poistion;
		}
		
		foreach($result as $key => $value) {
			$num = pow($times, ($value['distributor_y'] - $gp_info['distributor_y']));//团队当前层最大人数
			if ($value['num'] < $num) {//团队当前层没有排满
				$poistion['distributor_y'] = $value['distributor_y'];
				if ($value['num'] == $value['distributor_x']) {//团队最左边叉树情况
					$poistion['distributor_x'] = $value['distributor_x']+1;
				} else {
					$condition_c['rootid'] = $gp_info['rootid'];
					$min_x_c = pow($times, ($poistion['distributor_y'] - $gp_info['distributor_y'])) * ($gp_info['distributor_x'] - 1) + 1;
					$max_x_c = pow($times, ($poistion['distributor_y'] - $gp_info['distributor_y'])) * $gp_info['distributor_x'];
					$condition_c['distributor_y'] = $poistion['distributor_y'];
					$condition_c['distributor_x >='] = $min_x_c;
					$condition_c['distributor_x <='] = $max_x_c;
					$result_temp = model('distributor_gp')->field('distributor_x')->where($condition_c)->order('distributor_x ASC')->limit($value['num'])->select();
					$childs_cc = array();
					foreach ($result_temp as $tep) {
						$childs_cc[] = intval($tep['distributor_x']);
					}
					
					for ($i = $min_x_c; $i <= $max_x_c; $i++) {
						if (!in_array($i, $childs_cc)) {
							$poistion['distributor_x'] = $i;
							break;
						}
					}					
				}
				break;
			}
		}
		
		if (empty($poistion['distributor_y'])) {//没获取到坐标（滑落到团队下面第一个）
			$poistion['distributor_y'] = $result[count($result) - 1]['distributor_y'] + 1;
			$poistion['distributor_x'] = pow($times, ($poistion['distributor_y'] - $gp_info['distributor_y'])) * ($gp_info['distributor_x'] - 1) + 1;
		}
		//获取上级坐标
		$condition['distributor_y'] = $poistion['distributor_y'] - 1;
		if (fmod($poistion['distributor_x'], $times) == 0) {
			$condition['distributor_x'] = intval($poistion['distributor_x'] / $times);
		} else {
			$condition['distributor_x'] = intval($poistion['distributor_x'] / $times) + 1;
		}
		$condition['rootid'] = $gp_info['rootid'];
		$parent = model('distributor_gp')->getInfo($condition, 'parentpath,member_id');
		if (empty($parent)) {
			return array();
		}
		
		$poistion['parentid'] = $parent['member_id'];
		$poistion['parentpath'] = empty($parent['parentpath']) ? ',' . $parent['member_id'] . ',' : $parent['parentpath'] . $parent['member_id'] . ',';
		return $poistion;
	}
}