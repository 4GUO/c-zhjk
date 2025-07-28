<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class tihuo_buy
{
    /**
     * 会员信息
     * 
     * @var array
     */
    private $_member_info = array();
    /**
     * 下单数据
     * 
     * @var array
     */
    private $_order_data = array();
    /**
     * 表单数据
     * 
     * @var array
     */
    private $_post_data = array();
	/**
		购买前奏（下单资格检测）
	 */
	public function check_buy($uid = 0, $buy_num = 0, $goods_info = array(), $taocan_id = 0, $tihuoquan_id = 0){
		if($buy_num) {
			//根据goods_commonid。只需要find查询
			$taocan_goods = model('shop_goods_taocan_goods')->getInfo(array('taocan_id' => $taocan_id, 'goods_commonid' => $goods_info['goods_commonid']));
			$use_goods = model('shop_goods_tihuoquan_storage')->getInfo(array('uid' => $uid, 'taocan_id' => $taocan_id, 'tihuoquan_id' => $tihuoquan_id, 'goods_commonid' => $goods_info['goods_commonid']));
			$goods_storage = ($taocan_goods['goods_num'] ?? 0) - ($use_goods['goods_storage'] ?? 0);
			if($buy_num > $goods_storage){
				return callback(false, '[' . $goods_info['goods_name'] . ']库存不足');
			}
		}
		//一个提货券只能提一个套餐
		$tihuoquan_info = model('shop_goods_tihuoquan')->where(array('id' => $tihuoquan_id))->find();
		if (!empty($tihuoquan_info['taocan_id'])) {
			if ($tihuoquan_info['taocan_id'] != $taocan_id) {
				return callback(false, '同时只能提一个套餐');
			}
		} else {
			$cart = model('shop_tihuoquan_cart')->where(array('uid' => $uid, 'taocan_id !=' => $taocan_id))->find();
			if ($cart) {
				return callback(false, '同时只能提一个套餐');
			}
		}
		return callback(true);
	}
    /**
     * 购买第一步
     * 
     * @param unknown $cart_id       	
     * @param unknown $member_info  	
     * @return Ambigous <multitype:unknown, multitype:unknown >
     */
    public function buyStep1($cart_id, $member_info, $city_id = 0, $tc_id = 0, $tihuoquan_id = 0)
    {
        // 得到购买商品信息
        $result = $this->getGoodsList($cart_id, $member_info, $tc_id, $tihuoquan_id);
        if (!$result['state']) {
            return $result;
        }
		$goods_list = $result['data']['goods_list'];
        // 得到页面所需要数据：收货地址、发票、代金券、预存款、商品列表等信息
        $result = $this->getBuyCoreData($member_info, $goods_list, $city_id, array(), $tihuoquan_id);
        return $result;
    }
	/**
     * 购买第二步
     * 
     * @param array $post        	
     * @param array $member_info       	
     * @return array
     */
    public function buyStep2($post, $member_info)
    {
		$this->_member_info = $member_info;
        $this->_post_data = $post;
        try {
            // 第1步 表单验证
            $this->_createOrderStep1();
            // 第2步 得到购买商品信息
            $this->_createOrderStep2();
            // 第3步 得到购买相关金额计算等信息
            $this->_createOrderStep3();
            // 第4步 生成订单
            $this->_createOrderStep4();
            // 第5步 订单后续处理
            $this->_createOrderStep5();
            return callback(true, '', $this->_order_data);
        } catch (\Exception $e) {
            return callback(false, $e->getMessage());
        }
    }
	 /**
     * 订单生成前的表单验证与收货地址处理
     */
    private function _createOrderStep1()
    {
        $post = $this->_post_data;
        // 取得商品ID和购买数量
        $input_buy_items = $this->_parseItems($post['cart_id']);
        if (empty($input_buy_items)) {
            output_error('所购商品无效1');
        }
        // 验证收货地址
        $input_address_info = $post['address_info'];
        if (!$input_address_info) {
            output_error('请选择收货地址');
        } else {
			$address_list = model('address')->getList(array('uid' => $this->_member_info['uid']));
			$address_list = $address_list['list'];
			$input_address_info['uid'] = $this->_member_info['uid'];
			$input_address_info['sort_time'] = TIMESTAMP;
			$input_address_info['address'] = $input_address_info['province_name'] . ' ' . $input_address_info['city_name'] . ' ' . $input_address_info['county_name'];
			$check_id = 0;
			if ($address_list) {
				foreach($address_list as $val){
					if ($val['user_name'] == trim($input_address_info['user_name']) && $val['province_name'] == trim($input_address_info['province_name']) && $val['city_name'] == trim($input_address_info['city_name']) && $val['county_name'] == trim($input_address_info['county_name']) && $val['detail_info'] == trim($input_address_info['detail_info']) && $val['tel_number'] == trim($input_address_info['tel_number'])) {
						$check_id = $val['address_id'];
						break;
					}
				}
			}
			if ($check_id) {
				model('address')->edit(array('address_id' => $check_id), array('sort_time' => TIMESTAMP));
			} else {
				model('address')->add($input_address_info);
			}
        }
        // 收货地址城市编号
        $input_province_name = $input_address_info['province_name'];
		$input_city_name = $input_address_info['city_name'];
		$input_county_name = $input_address_info['county_name'];
		
        if (empty($post['paycode']) || !in_array($post['paycode'], array('online', 'wxpay_jsapi', 'wxapp', 'alipay', 'wxpay_h5'))) {
            //output_error('付款方式错误，请重新选择');
        }
        $input_pay_name = $post['paycode'];
        // 保存数据
        $this->_order_data['input_buy_items'] = $input_buy_items;
        $this->_order_data['input_province_name'] = $input_province_name;
		$this->_order_data['input_city_name'] = $input_city_name;
		$this->_order_data['input_county_name'] = $input_county_name;
        $this->_order_data['input_pay_name'] = $input_pay_name;
        $this->_order_data['input_pay_message'] = $post['message'];
        $this->_order_data['input_address_info'] = $input_address_info;
		$this->_order_data['voucher_info'] = $post['voucher_info'];
		$this->_order_data['tc_id'] = $post['tc_id'];
		$this->_order_data['tihuoquan_id'] = $post['tihuoquan_id'];
    }
	/**
     * 得到购买商品信息
     */
    private function _createOrderStep2()
    {
        $post = $this->_post_data;
		$result = $this->getGoodsList($post['cart_id'], $this->_member_info, $post['tc_id'], $post['tihuoquan_id']);
        if (!$result['state']) {
            output_error($result['msg']);
        }
		$goods_list = $result['data']['goods_list'];
        // 保存数据
        $this->_order_data['goods_list'] = $goods_list;
    }
	/**
     * 得到购买相关金额计算等信息
     */
    private function _createOrderStep3()
    {
        $goods_list = $this->_order_data['goods_list'];
		$result = $this->getBuyCoreData($this->_member_info, $goods_list, $this->_order_data['input_address_info']['city_id'], $this->_order_data['voucher_info'], $this->_order_data['tihuoquan_id']);
		if (!$result['state']) {
            output_error($result['msg']);
        } else {
            $result = $result['data'];
        }
        // 保存数据
		$this->_order_data['store_goods_list'] = $result['goods_list'];
		$this->_order_data['promotion_total'] = $result['promotion_total'];
		//满送信息
		$this->_order_data['store_premiums_list'] = $result['store_premiums_list'];
		$this->_order_data['store_mansong_rule_list'] = $result['store_mansong_rule_list'];
    }
	/**
     * 生成订单
     * 
     * @param array $input        	
     * @throws Exception
     * @return array array(支付单sn,订单列表)
     */
    private function _createOrderStep4()
    {
		try {
			$model = model();
			$model->beginTransaction();
			$pay_sn = $this->makePaySn($this->_member_info['uid']);
			$order_pay = array();
			$order_pay['pay_sn'] = $pay_sn;
			$order_pay['buyer_id'] = $this->_member_info['uid'];
			$order_pay_id = model('shop_order_pay')->add($order_pay);
			if (!$order_pay_id) {
				throw new \Exception('订单保存失败[未生成支付单]');
			}
			$voucher_info = $this->_order_data['voucher_info'];
			// 收货人信息
			list($reciver_info, $reciver_name) = $this->getReciverAddr($this->_order_data['input_address_info']);
			$input_pay_message = $this->_order_data['input_pay_message'];
			$store_goods_list = $this->_order_data['store_goods_list'];
			$promotion_total = $this->_order_data['promotion_total'];
			$store_order_list = array();//订单入库后，免查订单
			$order = array();
			$order_common = array();
			$order_goods = array();
			$i = 0;
			foreach ($store_goods_list as $store_id => $info) {
				$order_sn = $this->makeOrderSn($order_pay_id);
				//$order_amount = $info['sum_price'] + $info['freight_total'] - $promotion_total;
				$order_amount = $info['freight_total'];
				$this->_order_data['order_amount'] = $order_amount;
				$order[$i] = array(
					'pay_sn' => $pay_sn,
					'order_sn' => $order_sn,
					'store_id' => $store_id,
					'store_name' => $info['store_name'],
					'uid' => $this->_member_info['uid'],
					'member_name' => $this->_member_info['nickname'],
					'add_time' => TIMESTAMP,
					'payment_code' => $this->_order_data['input_pay_name'],
					'order_state' => ORDER_STATE_NEW,
					'order_amount' => $order_amount,
					'goods_amount' => $info['sum_price'],
					'goods_num' => $info['sum_num'],
					'shipping_fee' => $info['freight_total'],
					'promotions_amount' => $promotion_total,//满减优惠价格
					'tc_id' => $this->_order_data['tc_id'],
					'tihuoquan_id' => $this->_order_data['tihuoquan_id'],
				);
				$order_common[$i] = array(
					'order_sn' => $order_sn,
					'order_message' => $input_pay_message[$store_id],
					'reciver_info' => $reciver_info,
					'reciver_name' => $reciver_name,
					'reciver_province_name' => $this->_order_data['input_province_name'],
					'reciver_city_name' => $this->_order_data['input_city_name'],
					'reciver_county_name' => $this->_order_data['input_county_name'],
					'voucher_id' => $voucher_info[$store_id] ?? 0,
				);
				// 保存满减信息
				$store_mansong_rule_list = $this->_order_data['store_mansong_rule_list'];
				if (isset($store_mansong_rule_list[$store_id]) && is_array($store_mansong_rule_list[$store_id])) {
					$order_common[$i]['promotion_info'] = $store_mansong_rule_list[$store_id]['desc'];
				}
				$store_premiums_list = $this->_order_data['store_premiums_list'];
				if (isset($store_premiums_list[$store_id]) && is_array($store_premiums_list[$store_id])) {
					$zengpin_list = array();
					foreach ($store_premiums_list[$store_id] as $k => $v) {
						$zengpin_list[] = $v;
					}
					$order_common[$i]['zengpin_list'] = serialize($zengpin_list);
				}
				$store_order_list[$store_id] = array_merge($order[$i], $order_common[$i]);
				foreach ($info['goods'] as $goods_info) {
					if ($goods_info['goods_state'] != 1 || $goods_info['goods_storage'] <= 0) {
						throw new \Exception('商品' . $goods_info['goods_name'] . '已经下架或库存不足，请重新选择');
					}
					$order_goods[] = array(
						'order_sn' => $order_sn,
						'goods_commonid' => $goods_info['goods_commonid'],
						'goods_id' => $goods_info['goods_id'],
						'goods_name' => $goods_info['goods_name'],
						'goods_price' => $goods_info['goods_price'],
						'goods_num' => $goods_info['goods_num'],
						'goods_image' => $goods_info['goods_image'],
						'uid' => $this->_member_info['uid'],
						'gc_id' => $goods_info['gc_id'],
						'goods_costprice' => $goods_info['goods_costprice'],
						'add_time' => TIMESTAMP,
					);
				}
				$store_order_list[$store_id]['goods_list'] = $order_goods;
				$i++;
			}
			$r = model('shop_order')->insertAll($order);
			if (!$r) {
				throw new \Exception('订单保存失败[未生成订单数据]');
			}
			$r = model('shop_order_common')->insertAll($order_common);
			if (!$r) {
				throw new \Exception('订单保存失败[未生成订单扩展数据]');
			}
			$r = model('shop_order_goods')->insertAll($order_goods);//批量插入
			if (!$r) {
				throw new \Exception('订单保存失败[未生成商品数据]');
			}
			$model->commit();
        } catch (\Exception $e) {
            $model->rollback();
			output_error($e->getMessage());
        }
        // 保存数据
		$this->_order_data['pay_sn'] = $pay_sn;
        $this->_order_data['store_order_list'] = $store_order_list;
    }
	/**
     * 订单后续其它处理 变更库存和销量、更新使用的代金券状态、删除购物车中的商品
     */
    private function _createOrderStep5()
    {
		$tc_id = $this->_post_data['tc_id'];
		$tihuoquan_id = $this->_post_data['tihuoquan_id'];
        $ifcart = $this->_post_data['ifcart'];
        $input_buy_items = $this->_order_data['input_buy_items'];
        try {
            $model = model();
            $model->beginTransaction();
			// 删除购物车中的商品
			$this->delCart($ifcart, $this->_member_info['uid'], array_keys($input_buy_items));
			$model->commit();
			//处理优惠券
			$input_voucher_info = $this->_order_data['voucher_info'];
			if (!empty($input_voucher_info)) {
				logic('shop_queue')->editVoucherState($input_voucher_info, $this->_member_info['uid']);
			}
			
			//不需要支付运费的直接支付成功
			$store_order_list = $this->_order_data['store_order_list'];
    		$goods_list = array();
    		foreach ($store_order_list as $store_id => $order_info) {
    			foreach ($order_info['goods_list'] as $v) {
    				$goods_list[$v['goods_commonid']] = $v;
    			}
    		}
			$order_amount = $this->_order_data['order_amount'];
			$pay_sn = $this->_order_data['pay_sn'];
			$member_info = $this->_member_info;
			if(isset($order_amount) && $order_amount <= 0) {
			    $this->over_do($tc_id, $tihuoquan_id, $member_info, $goods_list);
			    //更新订单状态
                $update_order = array();
                $update_order['order_state'] = ORDER_STATE_PAY;
                $update_order['payment_time'] = TIMESTAMP;
                $update = model('shop_order')->edit(array('pay_sn' => $pay_sn, 'order_state' => ORDER_STATE_NEW), $update_order);
			}
        } catch (\Exception $e) {
            $model->rollBack();
        }
    }
	/**
     * 删除购物车商品
     * 
     * @param unknown $ifcart        	
     * @param unknown $cart_ids        	
     */
    public function delCart($ifcart, $uid, $cart_ids)
    {
        if (!$ifcart || !is_array($cart_ids)) {
            return;
        }
        $cart_id_str = implode(',', $cart_ids);
        if (preg_match('/^[\\d,]+$/', $cart_id_str)) {
            logic('shop_queue')->delTihuoCart(array('uid' => $uid, 'cart_ids' => $cart_ids));
        }
    }
	/**
     * 取得收货人地址信息
     * 
     * @param array $address_info        	
     * @return array
     */
    public function getReciverAddr($address_info = array())
    {
        $reciver_info['tel_phone'] = $address_info['tel_number'];
        $reciver_info['address'] = $address_info['province_name'] . ' ' . $address_info['city_name'] . ' ' . $address_info['county_name'] . ' ' . $address_info['detail_info'];
        $reciver_info['area'] = $address_info['province_name'] . ' ' . $address_info['city_name'] . ' ' . $address_info['county_name'];
        $reciver_info['street'] = $address_info['detail_info'];
        $reciver_info = serialize($reciver_info);
        $reciver_name = $address_info['user_name'];
        return array($reciver_info, $reciver_name);
    }
    /**
     * 处理提交过来的商品列表
     *
     * @param array $cart_id
     *        	购物车 商品id|商品数量
     * @param int $member_info
     *        	会员信息
     */
    public function getGoodsList($cart_id, $member_info, $tc_id, $tihuoquan_id) {
        // 取得POST ID和购买数量
		if(empty($cart_id)){
			return callback(false, '非法操作');
		}
        $buy_items = $this->_parseItems($cart_id);
        if (empty($buy_items)) {
            return callback(false, '所购商品无效2');
        }
		if (count($buy_items) > 50) {
            //防止大数据循环查询
            return callback(false, '一次最多只可购买50种商品');
        }
        $goods_ids = array_keys($buy_items);
        // 商品信息[得到最新商品属性及促销信息]
		$result = model('shop_goods')->getList(array('goods_id' => $goods_ids), 'goods_id,goods_commonid,goods_name,goods_price,goods_marketprice,goods_spec,goods_storage,goods_image,goods_price_vip,goods_costprice');
		$goods_list = $result['list'];
		unset($result);
		if (empty($goods_list)) {
            return callback(false, '请选择产品');
        }
		$goods_commonids = array();
		foreach ($goods_list as $v) {
			$goods_commonids[] = $v['goods_commonid'];
		}
		
		$result = model('shop_goods_common')->getList(array('goods_commonid' => $goods_commonids), 'goods_commonid,store_id,gc_id,gc_first_id,goods_state,goods_freight,buy_xiangou', 'goods_sort asc,goods_commonid asc');
		$goods_common_list = array();
		$store_ids = array();
		foreach ($result['list'] as $k => $v) {
			$goods_common_list[$v['goods_commonid']] = $v;
			$store_ids[] = $v['store_id'];
		}
		unset($result);
		$result = model('seller')->getList(array('id' => $store_ids), 'id,name');
		$store_list = array();
		foreach ($result['list'] as $k => $v) {
			$store_list[$v['id']] = $v;
		}
		unset($result);
		//限时折扣
		$condition = array();
        $condition['state'] = 1;
        $condition['start_time <'] = TIMESTAMP;
        $condition['end_time >'] = TIMESTAMP;
        $condition['goods_id'] = $goods_commonids;
        $xianshi_goods_list = model('p_xianshi_goods')->getGoodsExtendList($condition);
		$xianshi_goods_list = array_under_reset($xianshi_goods_list, 'goods_id');
		
		$store_goods_list = array();
		foreach($goods_list as $key => $val){
			$common_info = $goods_common_list[$val['goods_commonid']];
			$goods_info = array_merge($common_info, $val);
			$store_id = $common_info['store_id'];
			$goods_info['goods_image'] = tomedia($goods_info['goods_image']);
			$goods_info = logic('shop_goods')->get_goods_price($goods_info, $member_info['level_id']);
			$store_goods_list[$store_id]['store_id'] = $store_id;
			$store_goods_list[$store_id]['store_name'] = isset($store_list[$common_info['store_id']]) ? $store_list[$common_info['store_id']]['name'] : '';
			$goods_num = $buy_items[$goods_info['goods_id']];
			// 限时折扣
            if (isset($xianshi_goods_list[$val['goods_commonid']])) {
                $goods_info['goods_price'] = $xianshi_goods_list[$val['goods_commonid']]['xianshi_price'];
				$goods_info['lower_limit'] = $xianshi_goods_list[$val['goods_commonid']]['lower_limit'];
                $goods_info['xianshi_flag'] = true;
            } else {
                $goods_info['xianshi_flag'] = false;
            }
			$ckeck_result = $this->check_buy($member_info['uid'], $goods_num, $goods_info, $tc_id, $tihuoquan_id);
			if (!$ckeck_result['state']) {
				return callback(false, $ckeck_result['msg']);
			}
			$goods_info['goods_num'] = $goods_num;
			$store_goods_list[$store_id]['goods'][] = $goods_info;
		}
        return callback(true, '', array('goods_list' => $store_goods_list));
    }
    /**
     * 购买第一步：返回商品、促销、地址、发票等信息，然后交前台抛出
     * 
     * @param unknown $member_info        	
     * @param unknown $goods_list
     *        	店铺形式的商品信息
     * @return
     *
     */
    public function getBuyCoreData($member_info, $goods_list = array(), $city_id = 0, $store_voucher_info = array(), $tihuoquan_id = 0) {
		$voucher_ids = array();
		$store_goods_total = array();
		$goods_total_money = 0;
		$goods_num = 0;
		// 商品金额计算(分别对每个商品/优惠套装小计、每个店铺小计)
		foreach ($goods_list as $store_id => $info) {
			$sum_price = 0;
			$sum_num = 0;
			foreach($info['goods'] as $kk => $goods_data){
				$goods_sum = priceFormat($goods_data['goods_price'] * $goods_data['goods_num']);
				$sum_price += $goods_sum;
				$sum_num += $goods_data['goods_num'];
			}
			$goods_list[$store_id]['sum_price'] = $sum_price;
			$goods_list[$store_id]['sum_num'] = $sum_num;
			$goods_total_money += $sum_price;
			$goods_num += $sum_num;
			$store_goods_total[$store_id] = $sum_price;
			if (!empty($store_voucher_info[$store_id])) {
				$voucher_ids[] = $store_voucher_info[$store_id];
			}
		}
		//结算页面抛出代金券默认数据
		if (empty($store_voucher_info)) {
			list($voucher_list, $voucher_ids) = $this->getStoreAvailableVoucherList($store_goods_total, $member_info['uid']);
		}
		//优惠活动start
		$promotion_total = 0;
		//满送
		list($store_premiums_list, $store_mansong_rule_list) = $this->getMansongRuleCartListByTotal($store_goods_total);
		foreach($store_mansong_rule_list as $sid => $rule) {
			$promotion_total += $rule['discount'];
		}
		//代金券
		if (!empty($voucher_ids)) {
			$vouchers = model('voucher')->getList(array('voucher_id' => array_values($voucher_ids)), 'voucher_price');
			foreach ($vouchers['list'] as $k => $v) {
				$promotion_total += $v['voucher_price'];
			}
		}
		//优惠活动end
		list($goods_list, $freight_total) = $this->reCalcFreightTotal($member_info, $goods_list, $tihuoquan_id);
		$final_order_total = $freight_total;
        // 定义返回数组
        $result = array();
        $result['goods_list'] = $goods_list;
        $result['goods_total_money'] = priceFormat($goods_total_money);
        $result['mansong_rule_info'] = array();
		$result['final_order_total'] = $final_order_total;
		$result['final_order_num'] = $goods_num;
		$result['promotion_total'] = $promotion_total;
		$result['store_premiums_list'] = $store_premiums_list;
		$result['store_mansong_rule_list'] = $store_mansong_rule_list;
		$result['voucher_list'] = !empty($voucher_list) ? $voucher_list : array();
        return callback(true, '', $result);
    }
	/**
     * 取得店铺可用的代金券
     * 
     * @param array $store_goods_total  array(店铺ID=>商品总金额)
     * @return array
     */
    public function getStoreAvailableVoucherList($store_goods_total, $member_id) {
		$condition = array();
		$condition['voucher_end_date >'] = TIMESTAMP;
		$condition['voucher_state'] = 1;
		$condition['voucher_owner_id'] = $member_id;
		$condition['voucher_store_id'] = array_keys($store_goods_total);
		$vouresult = model('voucher')->getList($condition);
		$voucher_list = array_under_reset($vouresult['list'], 'voucher_t_id');
		$voucher_ids = array();
		foreach ($store_goods_total as $store_id => $goods_total) {
			foreach ($voucher_list as $t_id => $voucher) {
				if ($goods_total < $voucher['voucher_limit']) {
					unset($voucher_list[$t_id]);
				} else {
					$voucher_list[$t_id]['desc'] = sprintf('面额%s元 有效期至 %s ', $voucher['voucher_price'], date('Y-m-d', $voucher['voucher_end_date']));
					if ($voucher['voucher_limit'] > 0) {
						$voucher_list[$t_id]['desc'] .= sprintf(' 消费满%s可用', $voucher['voucher_limit']);
					}
					if (!isset($voucher_ids[$store_id])) {
						$voucher_ids[$store_id] = $voucher['voucher_id'];
					}
				}
			}
		}
		$voucher_list = array_under_reset($voucher_list, 'voucher_store_id', 2);
		return array($voucher_list, $voucher_ids);
    }
	/**
     * 取得店铺级优惠 - 跟据商品金额返回每个店铺当前符合的一条活动规则，如果有赠品，则自动追加到购买列表，价格为0
     * 
     * @param unknown $store_goods_total 每个店铺的商品金额小计，以店铺ID为下标
     * @return array($premiums_list, $mansong_rule_list) 分别为赠品列表[下标自增]，店铺满送规则列表[店铺ID为下标]
     */
    public function getMansongRuleCartListByTotal($store_goods_total) {
        if (empty($store_goods_total) || !is_array($store_goods_total)) {
            return array(array(), array());
        }
        $model_mansong = model('p_mansong');
        // 定义赠品数组，下标为店铺ID
        $premiums_list = array();
        // 定义满送活动数组，下标为店铺ID
        $mansong_rule_list = array();
        foreach ($store_goods_total as $store_id => $goods_total) {
            $rule_info = $model_mansong->getMansongRuleByStoreID($store_id, $goods_total);
            if (is_array($rule_info) && !empty($rule_info)) {
                // 即不减金额，也找不到促销商品时(已下架),此规则无效
                if (empty($rule_info['discount']) && empty($rule_info['mansong_goods_name'])) {
                    continue;
                }
                $rule_info['desc'] = $this->_parseMansongRuleDesc($rule_info);
                $rule_info['discount'] = priceFormat($rule_info['discount']);
                $mansong_rule_list[$store_id] = $rule_info;
                // 如果赠品在售,有库存,则追加到购买列表
                if (!empty($rule_info['mansong_goods_name']) && !empty($rule_info['goods_storage'])) {
                    $data = array();
                    $data['goods_id'] = $rule_info['goods_goods_id'];
                    $data['goods_name'] = $rule_info['mansong_goods_name'];
                    $data['goods_num'] = 1;
                    $data['goods_price'] = priceFormat(0);
                    $data['goods_image'] = $rule_info['goods_image'];
                    $data['goods_image_url'] = $rule_info['goods_image'];
                    $data['goods_storage'] = $rule_info['goods_storage'];
                    $premiums_list[$store_id][] = $data;
                }
            }
        }
        return array($premiums_list, $mansong_rule_list);
    }
	/**
	*  计算运费新方法
	*/
	public function reCalcFreightTotal($member_info, $goods_list = array(), $tihuoquan_id = 0) {
	    $freight_total = 0;
	    $check = model('shop_order')->where(array('uid' => $member_info['uid'], 'tihuoquan_id' => $tihuoquan_id, 'order_state >=' => ORDER_STATE_PAY))->find();
	    if ($check) {
	        $freight_total = config('tihuo_freight');
	    }
	    foreach ($goods_list as $store_id => $info) {
	        $goods_list[$store_id]['freight_total'] = priceFormat($freight_total);
	    }
		return array($goods_list, $freight_total);
	}
	
	/**
     * 得到所购买的id和数量
     */
    private function _parseItems($cart_id)
    {
        // 存放所购商品ID和数量组成的键值对
        $buy_items = array();
        if (is_array($cart_id)) {
            foreach ($cart_id as $value) {
                if (preg_match_all('/^(\\d{1,10})\\|(\\d{1,6})$/', $value, $match)) {
                    if (intval($match[2][0]) > 0) {
                        $buy_items[$match[1][0]] = $match[2][0];
                    }
                }
            }
        }
        return $buy_items;
    }
	/**
     * 拼装单条满即送规则页面描述信息
     * 
     * @param array $rule_info
     *        	满即送单条规则信息
     * @return string
     */
    private function _parseMansongRuleDesc($rule_info) {
        if (empty($rule_info) || !is_array($rule_info)) {
            return;
        }
        $discount_desc = !empty($rule_info['discount']) ? '减' . $rule_info['discount'] : '';
        $goods_desc = !empty($rule_info['mansong_goods_name']) && !empty($rule_info['goods_storage']) ? ' 送<a href=\'' . _url('goods/goods_info') . '?goods_id=' . $rule_info['goods_goods_id'] . '&client_type=wap' . '\' title=\'' . $rule_info['mansong_goods_name'] . '\' target=\'_blank\'>[赠品]</a>' : '';
        return sprintf('满%s%s%s', $rule_info['price'], $discount_desc, $goods_desc);
    }
	/**
     * 生成支付单编号(两位随机 + 从2000-01-01 00:00:00 到现在的秒数+微秒+会员ID%1000)，该值会传给第三方支付接口
     * 长度 =2位 + 10位 + 3位 + 3位 = 18位
     * 1000个会员同一微秒提订单，重复机率为1/100
     * 
     * @return string
     */
    public function makePaySn($member_id)
    {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (double) microtime() * 1000) . sprintf('%03d', (int) $member_id % 1000);
    }
	/**
     * 订单编号生成规则，n(n>=1)个订单表对应一个支付表，
     * 生成订单编号(年取1位 + $pay_id取13位 + 第N个子订单取2位)
     * 1000个会员同一微秒提订单，重复机率为1/100
     * 
     * @param $pay_id 支付表自增ID        	
     * @return string
     */
    public function makeOrderSn($pay_id)
    {
        // 记录生成子订单的个数，如果生成多个子订单，该值会累加
        static $num;
        if (empty($num)) {
            $num = 1;
        } else {
            $num++;
        }
        return date('y', time()) % 9 + 1 . sprintf('%013d', $pay_id) . sprintf('%02d', $num);
    }
	/**
     * 取得实物订单所需支付金额等信息
     * @param int $pay_sn
     * @param int $member_id
     * @return array
     */
    public function getRealOrderInfo($pay_sn, $member_id = null)
    {
        //验证订单信息
        $model_order = model('shop_order');
        $condition = array();
        $condition['pay_sn'] = $pay_sn;
        if (!empty($member_id)) {
            $condition['buyer_id'] = $member_id;
        }
        $order_pay_info = model('shop_order_pay')->getInfo($condition);
        if (empty($order_pay_info)) {
            return callback(false, '该支付单不存在');
        }
        $order_pay_info['subject'] = '订单_' . $order_pay_info['pay_sn'];
        $order_pay_info['order_type'] = 'freight_order';
		$condition = array();
        $condition['pay_sn'] = $pay_sn;
		if (!empty($member_id)) {
            $condition['uid'] = $member_id;
        }
		$condition['order_state'] = ORDER_STATE_NEW;
        $result = $model_order->getList($condition, '*', '', null, null, array('order_common','order_goods'));
		$order_list = $result['list'];
		if (empty($order_list)) {
			return callback(false, '订单信息不存在');
		}
        $order_pay_info['order_list'] = $order_list;
        return callback(true, '', $order_pay_info);
    }
    public function updateOrderAll($order_list, $payment_code, $trade_no = '', $paytime = '') {
	    $order_list = array_values($order_list);
		$model_order = model('shop_order');
		$model_goods = model('shop_goods');
		$pay_sn = $order_list[0]['pay_sn'];
		$buyer_id = $order_list[0]['uid'];
		$buyer_info = model('member')->getInfo(array('uid' => $buyer_id));
		$kucun_data = array();
		$kucun_data_where = array();
        try {
			$model = model();
            $model->beginTransaction();
            //lib\logging::write(var_export($order_list, true));
            $data = array();
            $data['api_pay_state'] = 1;
            $update = model('shop_order_pay')->edit($data, array('pay_sn' => $pay_sn));
            if (!$update) {
                throw new \Exception('更新支付单状态失败');
            }
            $logic_pd = logic('predeposit');
			$goods_ids = array();
            foreach ($order_list as $key => $order_info) {
                $order_id = $order_info['order_id'];
                if ($order_info['order_state'] != ORDER_STATE_NEW) {
                    continue;
                }
                //下单，支付被冻结的预存款
                $pd_amount = floatval($order_info['pd_amount']);
                if ($pd_amount > 0) {
                    $data_pd = array();
                    $data_pd['uid'] = $order_info['uid'];
                    $data_pd['member_name'] = $order_info['member_name'];
                    $data_pd['amount'] = $pd_amount;
                    $data_pd['order_sn'] = $order_info['order_sn'];
                    $logic_pd->changePd('order_comb_pay', $data_pd);
                }
            }
            //更新订单状态
            $update_order = array();
            $update_order['order_state'] = ORDER_STATE_PAY;
            $update_order['payment_time'] = !empty($paytime) ? $paytime : TIMESTAMP;
            $update_order['payment_code'] = $payment_code;
			$update_order['trade_no'] = $trade_no;
            $update = $model_order->edit(array('pay_sn' => $pay_sn, 'order_state' => ORDER_STATE_NEW), $update_order);
            if (!$update) {
                throw new \Exception('操作失败');
            }
            $model->commit();
            
        } catch (\Exception $e) {
            $model->rollback();
            return callback(false, $e->getMessage());
        }
        foreach ($order_list as $order_info) {
            //防止重复发送消息
            if ($order_info['order_state'] != ORDER_STATE_NEW) {
                continue;
            }
			//diy yewu
			$goods_list = array();
			foreach ($order_info['extend_order_goods'] as $v) {
    			$goods_list[$v['goods_commonid']] = $v;
    		}
			$this->over_do($order_info['tc_id'], $order_info['tihuoquan_id'], $buyer_info, $goods_list);
        }
        return callback(true, '操作成功');
	}
	private function over_do($tc_id, $tihuoquan_id, $member_info, $goods_list) {
	    //处理提货券库存
		$w = array();
		$w['uid'] = $member_info['uid'];
		$w['tihuoquan_id'] = $tihuoquan_id;
		$w['taocan_id'] = $tc_id;
		foreach($goods_list as $goods_commonid => $goods_info) {
			$w['goods_commonid'] = $goods_commonid;
			$storage_info = model('shop_goods_tihuoquan_storage')->where($w)->find();
			if ($storage_info) {
				model('shop_goods_tihuoquan_storage')->where(array('id' => $storage_info['id']))->update('goods_storage=goods_storage+' . $goods_info['goods_num']);
			} else {
				$add_data = $w;
				$add_data['goods_storage'] = $goods_info['goods_num'];
				model('shop_goods_tihuoquan_storage')->insert($add_data);
			}
		}
		//激活提货券动作
		$tihuoquan = model('shop_goods_tihuoquan')->where(array('id' => $tihuoquan_id, 'state' => 0))->find();
		if ($tihuoquan) {
		    model('shop_goods_tihuoquan')->where(array('id' => $tihuoquan_id, 'state' => 0))->update(array('used_time' => time()));
			//增加会员的激活提货券数量
			model('member')->where(array('uid' => $member_info['uid']))->update('can_tihuoquan_num=can_tihuoquan_num+1');
			model('distribute_account')->where(array('uid' => $member_info['uid']))->update('can_tihuoquan_num=can_tihuoquan_num+1');
			//edit by 20240428
			$dis_account = model('distribute_account')->getInfo(array('uid' => $member_info['uid']), 'dis_path');
		    $parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
		    if ($parent) {
		        model('member')->where(array('uid' => $parent))->update('can_tihuoquan_num=can_tihuoquan_num+1');
			    model('distribute_account')->where(array('uid' => $parent))->update('can_tihuoquan_num=can_tihuoquan_num+1');
		    }
			//合成分红券
			//asynRun('shop_queue/tihuoquan_to_fenhongquan', array('uid' => $member_info['uid']));
			logic('shop_queue')->tihuoquan_to_fenhongquan(array('uid' => $member_info['uid']));
			//分销
		}
		//更新提货券状态
		//没有goods_commonid，需要SUM查询
		$taocan_goods = model('shop_goods_taocan_goods')->field('SUM(goods_num) as num')->where(array('taocan_id' => $tc_id))->find();
		$use_goods = model('shop_goods_tihuoquan_storage')->field('SUM(goods_storage) as num')->where(array('uid' => $member_info['uid'], 'taocan_id' => $tc_id, 'tihuoquan_id' => $tihuoquan_id))->find();
		$goods_storage = ($taocan_goods['num'] ?? 0) - ($use_goods['num'] ?? 0);
		//lib\logging::write(var_export($taocan_goods, true));
		//lib\logging::write(var_export($use_goods, true));
		$state = 1;
		if ($goods_storage <= 0) {
			$state = 2;
		}
		model('shop_goods_tihuoquan')->where(array('id' => $tihuoquan_id))->update(array('state' => $state, 'taocan_id' => $tc_id));
		return true;
	}
}