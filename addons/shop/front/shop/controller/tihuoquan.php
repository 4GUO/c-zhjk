<?php
namespace shop\controller;
use lib;
class tihuoquan extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if(IS_API){
			$this->title = $this->config['name'];
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			$tihuoquan_info = model('shop_goods_tihuoquan')->getInfo(array('id' => $tihuoquan_id));
			$where['tc_state'] = 1;
			//如果已经提过，再次提只显示提过的套餐
			if (!empty($tihuoquan_info['taocan_id'])) {
				$where['tc_id'] = $tihuoquan_info['taocan_id'];
			}
			$goods_class = model('shop_goods_taocan')->getList($where);
			$model_cart = model('shop_tihuoquan_cart');
			$cart_count = $model_cart->getCartNum(array('uid' => $this->member_info['uid'], 'tihuoquan_id' => $tihuoquan_id));
			output_data(array('class_list' => $goods_class['list'], 'cart_count' => $cart_count));
		}
	}
	public function goods_listOp() {
		if(IS_API){
			$where = array();
			$tc_id = input('tc_id', 0, 'intval');
			$taocan_info = model('shop_goods_taocan')->getInfo(array('tc_id' => $tc_id));
			$where = array();
			$res = model('shop_goods_taocan_goods')->getList(array('taocan_id' => $taocan_info['tc_id']));
			$taocan_goods = array();
			foreach ($res['list'] as $v) {
				$taocan_goods[$v['goods_commonid']] = $v;
			}
			$where['goods_commonid'] = array_keys($taocan_goods);
			$order_by = 'goods_commonid asc,goods_sort asc';

			$where['goods_state'] = 1;
			$result = model('shop_goods_common')->getList($where, '*', $order_by, 20, input('page', 1, 'intval'));
			$goods_common_list = array();
			foreach ($result['list'] as $k => $v) {
				$goods_common_list[$v['goods_commonid']] = $v;
			}
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			unset($result);
			$result = model('shop_goods')->getList(array('goods_commonid' => array_keys($goods_common_list)), '*', 'instr(\',' . implode(',', array_keys($goods_common_list)) . ',\',concat(\',\',goods_commonid,\',\'))');
			$goods_list = array();
			foreach ($result['list'] as $k => $v) {
				if (isset($goods_list[$v['goods_commonid']])) {
					continue;
				}
				$common_info = $goods_common_list[$v['goods_commonid']];
				$goods_list[$v['goods_commonid']] = array_merge($common_info, $v);
			}
			unset($result);
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			$res = model('shop_goods_tihuoquan_storage')->getList(array('taocan_id' => $taocan_info['tc_id'], 'tihuoquan_id' => $tihuoquan_id));
			$use_goods = array();
			foreach ($res['list'] as $v) {
				$use_goods[$v['goods_commonid']] = $v;
			}
			//减去购物车的（实现傻逼效果）
			$condition = array('uid' => $this->member_info['uid']);
			$condition['taocan_id'] = $taocan_info['tc_id'];
    		if ($tihuoquan_id) {
    	        $condition['tihuoquan_id'] = $tihuoquan_id;
    	    }
            $cart_list = model('shop_tihuoquan_cart')->getList($condition);
            $use_goods2 = array();
            foreach ($cart_list as $v) {
                $use_goods2[$v['goods_commonid']] = $v['goods_num'];
            }
			foreach($goods_list as $key => $val) {
				$val = logic('shop_goods')->get_goods_price($val, $this->member_info['level_id']);
				$val['goods_image'] = tomedia($val['goods_image']);
				$val['goods_num'] = ($taocan_goods[$val['goods_commonid']]['goods_num'] ?? 0) - ($use_goods[$val['goods_commonid']]['goods_storage'] ?? 0) - ($use_goods2[$val['goods_commonid']] ?? 0);
				$goods_list[$key] = $val;
			}
			$return = array(
				'title' => '选择提货商品',
				'goods_list' => array_values($goods_list),
				'totalpage' => $totalpage,
				'hasmore' => $hasmore,
			);
			output_data($return);
		}
	}
	public function goods_infoOp() {
		if (IS_API) {
			$goods_commonid = input('goods_commonid', 0, 'intval');
			$goods_id = input('goods_id', 0, 'intval');
			$tc_id = input('tc_id', 0, 'intval');
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			$result = logic('shop_goods')->get_shop_goods_info($goods_id, $this->member_info['level_id']);
			$model_cart = model('shop_tihuoquan_cart');
			$cart_count = $model_cart->getCartNum(array('uid' => $this->member_info['uid']));
			if($result){
				//有goods_commonid条件，只需find查询出库存
				$taocan_goods = model('shop_goods_taocan_goods')->getInfo(array('taocan_id' => $tc_id, 'goods_commonid' => $goods_commonid));
				$use_goods = model('shop_goods_tihuoquan_storage')->getInfo(array('uid' => $this->member_info['uid'], 'taocan_id' => $tc_id, 'tihuoquan_id' => $tihuoquan_id, 'goods_commonid' => $goods_commonid));
				$result['goods_info']['goods_num'] = ($taocan_goods['goods_num'] ?? 0) - ($use_goods['goods_storage'] ?? 0);
				$result['cart_count'] = $cart_count;
				output_data($result);
			} else {
				output_error('商品不存在');
			}
		}
	}
	public function tihuoquan_listOp() {
		$model = model('shop_goods_tihuoquan');
		$where = array();
		$where['uid'] = $this->member_info['uid'];
		$state = input('state', 0, 'intval');
		if ($state) {
			$where['state'] = $state - 1;
		}
		$list = $model->where($where)->select();
		foreach($list as $k => $v) {
			$v['add_time'] = date('Y-m-d H:i', $v['edit_time']);
			$list[$k] = $v;
		}
		$return = array(
			'title' => '我的提货券',
			'list' => $list,
		);
		output_data($return);
	}
	public function tihuoquan_buyOp() {
		$level_list = logic('yewu')->get_level_list('*', 'level_sort DESC');
		$account_info = model('distribute_account')->getInfo(array('uid' => $this->member_info['uid']));
		$level_info = $level_list[$account_info['level_id']];
		$parent = explode(',', trim($account_info['dis_path'], ','));
		$parent = array_reverse($parent);
		$result = model('member')->getList(array('uid' => $parent));
		$parent_list = array();
		foreach ($result['list'] as $k => $v) {
			$parent_list[$v['uid']] = $v;
		}
		unset($result);

		//获取出售人
		$seller_info = array();
		foreach($parent as $v) {
			$p_info = $parent_list[$v] ?? array();
			if (!$p_info) {
				continue;
			}
			$l_info = $level_list[$p_info['level_id']] ?? array();
			if ($l_info['level_sort'] > $level_info['level_sort']) {
				$seller_info = $p_info;
				break;
			}
		}
		if (!$seller_info) {
			output_error('请联系平台购买！');
		}
		$member_method_info = model('withdraw_method_member')->where(array('uid' => $seller_info['uid'], 'is_default' =>1))->find();
		$last_widthdraw_info = array();
		if ($member_method_info) {
			if ($member_method_info['method_code'] == 'wxzhuanzhang') {
				$last_widthdraw_info['method_code'] = 1;
				$last_widthdraw_info['last_weixin_account'] = $member_method_info['method_no'];
				$last_widthdraw_info['last_weixin_realname'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_weixin_ercode'] = tomedia($member_method_info['ercode']);
			} else if ($member_method_info['method_code'] == 'alipay') {
				$last_widthdraw_info['method_code'] = 2;
				$last_widthdraw_info['last_alipay_account'] = $member_method_info['method_no'];
				$last_widthdraw_info['last_alipay_name'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_alipay_ercode'] = tomedia($member_method_info['ercode']);
			} else if ($member_method_info['method_code'] == 'bank') {
				$last_widthdraw_info['method_code'] = 3;
				$last_widthdraw_info['last_bank_bankname'] = $member_method_info['method_bank'];
				$last_widthdraw_info['last_bank_name'] = $member_method_info['method_name'];
				$last_widthdraw_info['last_bank_account'] = $member_method_info['method_no'];
			}
		}
		$return = array(
			'title' => '购买提货券',
			'seller_info' => $seller_info,
			'user_method' => $last_widthdraw_info,
		);
		output_data($return);
	}
	public function zhuanzeng_preOp() {
	    $account = input('account', '', 'trim');
		if (!$account) {
			output_error('请填写对方手机号');
		}
		$to_member_info = model('member')->getInfo(array('mobile' => $account));
		if (!$to_member_info) {
			output_error('手机号不存在');
		}
		if ($to_member_info['uid'] == $this->member_info['uid']) {
			output_error('不能给自己转账');
		}
		output_data(array('msg' => $to_member_info['mobile'] . PHP_EOL . $to_member_info['nickname'] . PHP_EOL . config('uid_pre') . padNumber($to_member_info['uid']) . PHP_EOL . '【是否确认转提货券给对方？】'));
	}
	public function zhuanzengOp() {
		$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
		$account = input('account', '', 'trim');
		if (!$account) {
			output_error('请填写对方手机号');
		}
		$to_member_info = model('member')->getInfo(array('mobile' => $account));
		if (!$to_member_info) {
			output_error('手机号不存在');
		}
		if ($to_member_info['uid'] == $this->member_info['uid']) {
			output_error('不能给自己转账');
		}
		$quan_info = model('shop_goods_tihuoquan')->where(array('id' => $tihuoquan_id, 'uid' => $this->member_info['uid'], 'state' => 0))->find();
		if (!$quan_info) {
			output_error('提货券无效');
		}
		try {
			$model = model();
			$model->beginTransaction();
			$to_data = array(
				'uniacid' => $this->uniacid,
				'lg_member_id' => $to_member_info['uid'],
				'lg_member_name' => $to_member_info['nickname'],
				'lg_type' => 'income',
				'tihuoquan_id' => $tihuoquan_id,
				'lg_add_time' => time(),
				'lg_desc' => '收到' . $this->member_info['nickname'] . '的转赠',
				'relation_uid' => $this->member_info['uid'],
			);
			model('shop_goods_tihuoquan_log')->add($to_data);
			$from_data = array(
				'uniacid' => $this->uniacid,
				'lg_member_id' => $this->member_info['uid'],
				'lg_member_name' => $this->member_info['nickname'],
				'lg_type' => 'outcome',
				'tihuoquan_id' => $tihuoquan_id,
				'lg_add_time' => time(),
				'lg_desc' => '给' . $to_member_info['nickname'] . '转赠',
				'relation_uid' => $to_member_info['uid'],
			);
			model('shop_goods_tihuoquan_log')->add($from_data);
			$flag = model('shop_goods_tihuoquan')->where(array('id' => $quan_info['id']))->update(array('uid' => $to_member_info['uid'], 'edit_time' => time()));
			if (!$flag) {
				throw new \Exception('转赠失败');
			}
			$model->commit();
			output_data('1');
        } catch (\Exception $e) {
			// 出错啦，处理下吧
			$model->rollback();
			output_error($e->getMessage());
		}
	}
	public function zhuanzeng_log_listOp() {
		if (IS_API) {
			$model_log = model('shop_goods_tihuoquan_log');
			$this->title = '转赠记录';
			$start_time = input('start_time', '');
			$end_time = input('end_time', '');
			
			$where = array(
			    'uniacid' => $this->uniacid,
				'lg_member_id' => $this->member_info['uid'],
			);
			if($start_time){
				$where['lg_add_time >='] = strtotime($start_time);
			}
			if($end_time){
				$where['lg_add_time <='] = strtotime($end_time . ' 23:59:59');
			}
			$detail_list_tmp = $model_log->getList($where, '*', 'lg_id desc', 20, input('page', 1, 'intval'));
			$detail_list = $detail_list_tmp['list'];
			$uids = array();
			foreach($detail_list as $k => $r) {
				//$uids[] = $r['lg_member_id'];
				$uids[] = $r['relation_uid'];
			}
			$members = model('member')->where(array('uid' => $uids))->select();
			$members = array_under_reset($members, 'uid');
			foreach($detail_list as $k => $r){
			    $detail_list[$k]['mobile'] = isset($members[$r['relation_uid']]) ? $members[$r['relation_uid']]['mobile'] : '';
				$detail_list[$k]['lg_add_time'] = date('Y/m/d H:i:s', $r['lg_add_time']);
			}
			$return = array(
				'title' => $this->title,
				'list' => $detail_list,
				'totalpage' => $detail_list_tmp['totalpage'],
				'hasmore' => $detail_list_tmp['hasmore'],
			);
			output_data($return);
		}
	}
}