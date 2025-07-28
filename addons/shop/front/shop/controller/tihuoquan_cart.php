<?php
namespace shop\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class tihuoquan_cart extends member
{
    public function __construct()
    {
        parent::_initialize();
    }
    /**
     * 购物车列表
     */
    public function cart_listOp()
    {
		if (IS_API) {
		    $tc_id = input('tc_id', 0, 'intval');
		    $tihuoquan_id = input('tihuoquan_id', 0, 'intval');
		    
			$result = $this->get_cart_list($tihuoquan_id, $tc_id);
			$result['title'] = '购物车';
			output_data($result);
		}
    }
    /**
     * 购物车添加
     */
    public function cart_addOp() {
		if(IS_API){
			$goods_id = input('goods_id', 0, 'intval');
			$tc_id = input('tc_id', 0, 'intval');
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			$quantity = input('num', 0, 'intval');
			if ($goods_id <= 0 || $quantity <= 0) {
				output_error('参数错误');
			}
			$model_cart = model('shop_tihuoquan_cart');
			$result = logic('shop_goods')->get_shop_goods_info($goods_id, $this->member_info['level_id']);
			$goods_info = $result['goods_info'];
			// 验证是否可以购买
			if (empty($goods_info)) {
				output_error('商品已下架或不存在');
			}
			
			$cart_info = $model_cart->getInfo(array('goods_id' => $goods_id, 'uid' => $this->member_info['uid']));
			$result = logic('tihuo_buy')->check_buy($this->member_info['uid'], ($cart_info['goods_num'] ?? 0) + $quantity, $goods_info, $tc_id, $tihuoquan_id);
			if (!$result['state']) {
				output_error($result['msg']);
			}
			if(!$cart_info){
				$param = array();
				$param['uid'] = $this->member_info['uid'];
				$param['store_id'] = $goods_info['store_id'];
				$param['goods_commonid'] = $goods_info['goods_commonid'];
				$param['goods_id'] = $goods_info['goods_id'];
				$param['goods_name'] = $goods_info['goods_name'];
				$param['goods_price'] = $goods_info['goods_price'];
				$param['goods_image'] = $goods_info['goods_image'];
				$param['store_name'] = $goods_info['store_name'];
				$param['taocan_id'] = $tc_id;
				$param['tihuoquan_id'] = $tihuoquan_id;
				$result = $model_cart->add($param, $quantity);
			}else{
				$param = array();
				$param['goods_num'] = $cart_info['goods_num'] + $quantity;
				$result = $model_cart->edit($param, array('goods_id' => $goods_id, 'uid' => $this->member_info['uid']));
			}
			if ($result) {
				$cart_array = $this->get_cart_list($tihuoquan_id, $tc_id);
				output_data($cart_array);
			} else {
				output_error('操作失败');
			}
		}
    }
	/**
     * 更新购物车购买数量
     */
    public function cart_edit_quantityOp()
    {
		if(IS_API){
			$model_cart = model('shop_tihuoquan_cart');
			$cart_id = input('cart_id', 0, 'intval');
			$quantity = input('num', 0, 'intval');
			$tc_id = input('tc_id', 0, 'intval');
			$tihuoquan_id = input('tihuoquan_id', 0, 'intval');
			if ($cart_id && $quantity <= 0) {
				$result = $model_cart->del(array('cart_id' => $cart_id, 'uid' => $this->member_info['uid']));
				if($result){
					$cart_array = $this->get_cart_list($tihuoquan_id, $tc_id);
					output_data($cart_array);
				}
			}
			
			$cart_info = $model_cart->getInfo(array('cart_id' => $cart_id, 'uid' => $this->member_info['uid']));
			if (!$cart_info) {
			    output_error('商品已经失效');
			}
			// 检查是否为本人购物车
			if ($cart_info) {
				if ($cart_info['uid'] != $this->member_info['uid']) {
					output_error('参数错误');
				}
			}
			
			$result = logic('shop_goods')->get_shop_goods_info($cart_info['goods_id'], $this->member_info['level_id']);
			$goods_info = $result['goods_info'];
			unset($result);
			// 验证是否可以购买
			if (empty($goods_info)) {
				output_error('商品已下架或不存在');
			}
			// 检查库存是否充足
			$result = logic('tihuo_buy')->check_buy($this->member_info['uid'], $quantity, $goods_info, $tc_id, $tihuoquan_id);
			if (!$result['state']) {
				output_error($result['msg']);
			}
			unset($result);
			$data = array();
			$data['goods_num'] = $quantity;
			$update = $model_cart->edit($data, array('cart_id' => $cart_id));
			$cart_array = $this->get_cart_list($tihuoquan_id, $tc_id);
			output_data($cart_array);
		}
    }
	private function get_cart_list($tihuoquan_id, $tc_id){
		$model_cart = model('shop_tihuoquan_cart');
		$condition = array('uid' => $this->member_info['uid']);
		if ($tihuoquan_id) {
	        $condition['tihuoquan_id'] = $tihuoquan_id;
	    }
	    if ($tc_id) {
	        $condition['taocan_id'] = $tc_id;
	    }
        $cart_list = $model_cart->getList($condition);
		$cart_list_real = array();
		foreach($cart_list as $vv){
			$cart_list_real[$vv['store_id']][] = $vv;
		}
		//unset($cart_list);
		$logic_shop_goods = logic('shop_goods');
        $total_money = 0;
        $cart_a = array();
		$selectedAll = 1;
		foreach ($cart_list_real as $store_id => $v) {
			$sum_price = 0;
			$sum_num = 0;
			foreach($v as $kk => $vv){
				$goods_array = $logic_shop_goods->get_shop_goods_info($vv['goods_id'], $this->member_info['level_id']);
				$goods_data = isset($goods_array['goods_info']) ? $goods_array['goods_info'] : array();
				$cart_a[$store_id]['store_id'] = $store_id;
				$cart_a[$store_id]['store_name'] = $vv['store_name'];
				$goods_data['cart_id'] = $vv['cart_id'];
				$goods_data['goods_num'] = $vv['goods_num'];
				$goods_data['selected'] = $vv['selected'];
				if (isset($goods_data['goods_spec']) && $goods_data['goods_spec'] == 'N;') {
					$goods_data['goods_spec'] = '';
				}
				$cart_a[$store_id]['goods'][] = $goods_data;
				if ($vv['selected']) {
					$goods_sum = priceFormat($vv['goods_price'] * $vv['goods_num']);
					$sum_price += $goods_sum;
					$sum_num += $vv['goods_num'];
				} else {
					$selectedAll = 0;
				}
			}
			$cart_a[$store_id]['sum_price'] = $sum_price;
			$cart_a[$store_id]['sum_num'] = $sum_num;
			$total_money += $sum_price;
		}
		sort($cart_a);
        return array('cart_list' => $cart_a, 'total_money' => priceFormat($total_money), 'selectedAll' => $selectedAll, 'cart_count' => count($cart_list));
	}
}