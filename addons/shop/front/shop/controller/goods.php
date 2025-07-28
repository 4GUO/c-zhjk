<?php
namespace shop\controller;
use lib;
class goods extends member {
	public function __construct() {
		parent::_initialize();
		$uid = $this->getMemberIdIfExists();
		if ($uid) {
			$this->member_info = model('member')->where(array('uid' => $uid))->find();
		} else {
			$this->member_info['level_id'] = 0;
			$this->member_info['uid'] = 0;
		}
	}
	public function indexOp(){
		if(IS_API){
			$this->title = $this->config['name'];
			$goods_class = model('shop_goods_class')->getList(array('gc_parent_id' => 0, 'gc_state' => 1, 'is_spike' => 0, 'is_points' => 0), 'gc_id,gc_name');
			output_data(array('class_list' => $goods_class['list']));
		}
	}
	public function goods_listOp() {
		if (IS_API) {
			$where = array();
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$gc_id = input('gc_id', 0, 'intval');
			$k = input('k', '');
			$order = input('order', '');
			$min_price = input('min_price', '');
			$max_price = input('max_price', '');
			$activity_type = input('activity_type', '');
			$goods_class_info = model('shop_goods_class')->getInfo(array('gc_id' => $gc_id, 'gc_state' => 1, 'is_spike' => 0, 'is_points' => 0), 'gc_parent_id,gc_name,has_child');
			$child_class_list = model('shop_goods_class')->getList(array('gc_parent_id' => $gc_id, 'gc_state' => 1, 'is_spike' => 0, 'is_points' => 0), 'gc_id,gc_name,gc_image');
			if(!empty($goods_class_info)){
				if($goods_class_info['gc_parent_id'] == 0){
					$where['gc_first_id'] = $gc_id;
				}else{
					$where['gc_id'] = $gc_id;
				}
			}
			if($k){
				$where['goods_name'] = '%' . $k . '%';
			}
			
			/*store start*/
			$store_id = input('store_id', 0, 'intval');
			if ($store_id) {
				$where['store_id'] = $store_id;
			}
			if (!empty(input('stc_first_id', 0, 'intval'))) {
				$where['stc_first_id'] = input('stc_first_id', 0, 'intval');
			} else if (!empty(input('stc_id', 0, 'intval'))) {
				$where['stc_id'] = input('stc_id', 0, 'intval');
			}
			/*store end*/
			$order_by = 'goods_commonid asc,goods_sort asc';
			if($order){
				switch ($order) {
					case 'goods_salenum':
						$order_by = 'goods_salenum desc';
						break;
					case 'is_new':
						$where['is_new'] = 1;
						break;
					case 'goods_commend':
						$where['goods_commend'] = 1;
						break;
					case 'price_desc':
						$order_by = 'goods_price desc';
						break; 
					case 'price_asc':
						$order_by = 'goods_price asc';
						break; 
					case 'evaluation':
						$order_by = 'goods_evaluation desc';
						break; 
				}
			}
			if($min_price){
				$where['goods_price >='] = $min_price;
			}
			if($max_price){
				$where['goods_price <='] = $max_price;
			}
			$where['goods_state'] = 1;
			$result = model('shop_goods_common')->getList($where, 'goods_commonid,gc_first_id,goods_image,goods_name,buy_xiangou', $order_by, 20, input('page', 1, 'intval'));
			$goods_common_list = array();
			foreach ($result['list'] as $k => $v) {
				$goods_common_list[$v['goods_commonid']] = $v;
			}
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			unset($result);
			$result = model('shop_goods')->getList(array('goods_commonid' => array_keys($goods_common_list)), 'goods_id,goods_commonid,goods_marketprice,goods_price,goods_price_vip', 'instr(\',' . implode(',', array_keys($goods_common_list)) . ',\',concat(\',\',goods_commonid,\',\'))');
			$goods_list = array();
			foreach ($result['list'] as $k => $v) {
				if (isset($goods_list[$v['goods_commonid']])) {
					continue;
				}
				$common_info = $goods_common_list[$v['goods_commonid']];
				$goods_list[$v['goods_commonid']] = array_merge($common_info, $v);
			}
			unset($result);
			foreach($goods_list as $key => $val) {
				$val = logic('shop_goods')->get_goods_price($val, $this->member_info['level_id']);
				if ($val['buy_xiangou'] > 0) {
					$val['goods_price'] = $val['goods_price'] * $val['buy_xiangou'];
					$val['goods_marketprice'] = $val['goods_marketprice'] * $val['buy_xiangou'];
				}
				$val['goods_image'] = tomedia($val['goods_image']);
				$goods_list[$key] = $val;
			}
			$goods_list = $this->_goods_list_extend($goods_list);
			if ($activity_type == 'xianshi') {
				foreach ($goods_list as $k => $v) {
					if ($v['xianshi_flag'] == false) {
						unset($goods_list[$k]);
					}
				}
			}
			$return = array(
				'title' => isset($goods_class_info['gc_name']) ? $goods_class_info['gc_name'] : $this->config['name'],
				'gc_info' => $goods_class_info,
				'child_class_list' => $child_class_list['list'],
				'goods_list' => array_values($goods_list),
				'totalpage' => $totalpage,
				'hasmore' => $hasmore,
			);
			output_data($return);
		}
	}
	private function _goods_list_extend($goods_list) {
        // 获取商品列表编号数组
        $commonid_array = array();
        foreach ($goods_list as $key => $value) {
            $commonid_array[] = $value['goods_commonid'];
        }
        // 促销
		$condition = array();
        $condition['state'] = 1;
        $condition['start_time <'] = TIMESTAMP;
        $condition['end_time >'] = TIMESTAMP;
        $condition['goods_id'] = $commonid_array;
        $xianshi_goods_list = model('p_xianshi_goods')->getGoodsExtendList($condition);
		$xianshi_goods_list = array_under_reset($xianshi_goods_list, 'goods_id');
		foreach ($goods_list as $key => $value) {
            // 限时折扣
            if (isset($xianshi_goods_list[$value['goods_commonid']])) {
				$xianshi_goods = $xianshi_goods_list[$value['goods_commonid']];
                $goods_list[$key]['goods_price'] = $xianshi_goods['xianshi_price'];
                $goods_list[$key]['end_times'] = $xianshi_goods['end_time'] - time() > 0 ? $xianshi_goods['end_time'] - time() : 0;
				$countdown_date = lib\timer::time2string($goods_list[$key]['end_times']);
				$goods_list[$key]['countdown_date'] = $countdown_date;
				$goods_list[$key]['xianshi_flag'] = true;
            } else {
                $goods_list[$key]['xianshi_flag'] = false;
            }
        }
        return $goods_list;
    }
	public function goods_infoOp() {
		if (IS_API) {
			$goods_id = input('goods_id', 0, 'intval');
			$result = logic('shop_goods')->get_shop_goods_info($goods_id, $this->member_info['level_id']);
			$model_cart = model('shop_cart');
			$cart_count = $model_cart->getCartNum(array('uid' => $this->member_info['uid']));
			if($result){
			    if (!empty($result['goods_info']['is_spike']) || !empty($result['goods_info']['is_points'])) {
					output_error('商品信息有误');
				}
				$result['goods_info']['goods_evaluation_num'] = model('shop_evaluate_goods')->where(array('geval_goodsid' => $result['goods_info']['goods_commonid'], 'geval_state' => 1))->total();
				$result['cart_count'] = $cart_count;
				if (empty($result['goods_info']['goods_image'])) {
					$goods_img_width = 0;
					$goods_img_height = 0;
				} else {
					list($goods_img_width, $goods_img_height, $goods_img_type) = fxy_getimagesize($result['goods_info']['goods_image']);
				}
				$result['goods_img_width'] = $goods_img_width;
				$result['goods_img_height'] = $goods_img_height;
				$result['kefu_tel'] = $this->config['telphone'];
				if ($memberId = $this->getMemberIdIfExists()) {
					if (!$result['goods_info']['is_virtual']) {
						// 店铺优惠券
						$condition = array();
						$condition['voucher_t_gettype'] = array(1,3);
						$condition['voucher_t_state'] = 1;
						$condition['voucher_t_end_date >'] = time();
						$condition['voucher_t_store_id'] = $result['goods_info']['store_id'];
						$voucher_template = model('voucher_template')->getList($condition);
						if (!empty($voucher_template['list'])) {
							foreach ($voucher_template['list'] as $val) {
								$param = array();
								$param['voucher_t_id'] = $val['voucher_t_id'];
								$param['voucher_t_price'] = $val['voucher_t_price'];
								$param['voucher_t_limit'] = $val['voucher_t_limit'];
								$param['voucher_t_end_date'] = date('Y年m月d日', $val['voucher_t_end_date']);
								$param['voucher_t_gettype'] = $val['voucher_t_gettype'];
								$param['voucher_t_customimg'] = tomedia($val['voucher_t_customimg']);
								$result['voucher'][] = $param;
							}
						}
					}
				}
				$result['can_buy'] = true;
				$check = logic('shop_buy')->check_buy($memberId, 1, $result['goods_info']);
    			if (!$check['state']) {
    				$result['can_buy'] = false;
    			}
				output_data($result);
			} else {
				output_error('商品不存在');
			}
		}
	}
	public function mobile_bodyOp() {
		$goods_id = input('goods_id', 0, 'intval');
		$result = model('shop_goods')->getInfo(array('goods_id' => $goods_id), 'goods_commonid');
		$goods_common = model('shop_goods_common')->getInfo(array('goods_commonid' => $result['goods_commonid']), 'mobile_body');
		echo empty($goods_common['mobile_body']) ? '' : htmlspecialchars_decode(htmlspecialchars_decode($goods_common['mobile_body']));
	}
	public function evaluation_listOp() {
		if (IS_API) {
			$goods_commonid = input('goods_id', 0, 'intval');
			$model_goods = model('shop_goods_common');
			$goods_info = $model_goods->getInfo(array('goods_commonid' => $goods_commonid));
			$this->title = isset($goods_info['goods_name']) ? $goods_info['goods_name'] . '-评价' : '评价';
			$status = input('status', 0, 'intval');
			$model_evaluation = model('shop_evaluate_goods');
			$where = array();
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$where['geval_goodsid'] = $goods_commonid;
			$where['geval_state'] = 1;
			if ($status == 1) {
				$where['geval_scores'] = array(3,4,5);
			}
			if ($status == 2) {
				$where['geval_scores'] = array(1,2);
			}
			$list = $model_evaluation->getList($where, '*', 'geval_id desc', 20, input('page', 1, 'intval'));
			$evaluation_list = array();
			if (!empty($list['list'])) {
				foreach ($list['list'] as $value) {
					$value['geval_addtime'] = date('Y-m-d H:i:s', $value['geval_addtime']);
					$value['geval_image'] = fxy_unserialize($value['geval_image']);
					$evaluation_list[] = $value;
				}
			}
			$return = array(
				'title' => $this->title,
				'list' => $evaluation_list,
				'totalpage' => $list['totalpage'],
				'hasmore' => $list['hasmore'],
			);
			unset($list);
			output_data($return);
		}
	}
	public function class_listOp() {
		if (IS_API) {
			$class_list = array();
			$result = model('shop_goods_class')->getList(array('gc_parent_id' => 0, 'gc_state' => 1, 'is_spike' => 0, 'is_points' => 0));
			$class_list = $result['list'];
			output_data(array('class_list' => $class_list, 'title' => '分类'));
		}
	}
	public function class_two_listOp() {
		if (IS_API) {
			$gc_id = input('gc_id', 0, 'intval');
			$class_info = model('shop_goods_class')->getInfo(array('gc_id' => $gc_id, 'is_spike' => 0, 'is_points' => 0));
			$class_list = array();
			$result = model('shop_goods_class')->getList(array('gc_parent_id' => $gc_id, 'gc_state' => 1, 'is_spike' => 0, 'is_points' => 0));
			$class_list = $result['list'];
			output_data(array('class_info' => $class_info, 'class_list' => $class_list));
		}
	}
}