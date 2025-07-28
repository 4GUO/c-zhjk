<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class store_bill
{
    //付款增加记录
	public function ins_supply_commiss_order($order_info) {
        $add_money = 0;
        if (empty($order_info['store_id'])) {
            return true;
        } else {
			$order_goods_list = $order_info['extend_order_goods'];
			if (empty($order_goods_list)) {
				return true;
			}
			$ins_data = array();
			$commiss_money = $total_money = 0;
			foreach ($order_goods_list as $order_goods_info) {
				$commiss_money += priceFormat($order_goods_info['goods_costprice'] * $order_goods_info['goods_num']);
				$total_money += priceFormat($order_goods_info['goods_price'] * $order_goods_info['goods_num']);
			}
			$total_money = $total_money - $order_info['promotions_amount'] + $order_info['shipping_fee'];
			$commiss_money = $commiss_money - $order_info['promotions_amount'] + $order_info['shipping_fee'];
			$type = 1;
			$ins_data = array(
				'store_id' => $order_info['store_id'],
				'order_id' => $order_info['order_id'],
				'order_sn' => $order_info['order_sn'],
				'state' => 0,
				'total_money' => $total_money,
				'commiss_money' => $commiss_money,
				'addtime' => time(),
				'type' => $type,
			);
            model('store_commiss_order')->insert($ins_data);
            return true;
        }
    }
	//付款增加记录
	public function ins_supply_commiss_vr_order($order_info) {
        $add_money = 0;
        if (empty($order_info['store_id'])) {
            return true;
        } else {
            /**
             每单结算 商品成本价60  订单优惠总价3  运费6 。 实结 (60 * 数量)-3+6
             *
             */
            //独立商家
			$money = priceFormat(($order_info['goods_costprice'] * $order_info['goods_num']) - $order_info['promotions_amount']);
			$total_money = priceFormat(($order_info['goods_price'] * $order_info['goods_num']) - $order_info['promotions_amount']);
			if ($money <= 0) {
				$money = 0;
			}
			$type = 2;
			$ins_data = array(
				'store_id' => $order_info['store_id'],
				'order_id' => $order_info['order_id'],
				'order_sn' => $order_info['order_sn'],
				'state' => 0,
				'total_money' => $total_money,
				'commiss_money' => $money,
				'addtime' => time(),
				'type' => $type,
			);
            model('store_commiss_order')->insert($ins_data);
            return true;
        }
    }
	public function cancle_supply_commiss_order($order_info) {
		$flag = model('store_commiss_order')->edit(array('order_id' => $order_info['order_id'], 'order_sn' => $order_info['order_sn'], 'state' => 0), array('state' => 2));
		if ($flag) {
			return true;
		}
		return false;
	}
	//商家结算 //确认收货完成
	public function send_supply_commission($order_info) {
		if (empty($order_info['store_id'])) {
			return true;
		}
		$result = model('store_commiss_order')->getList(array('order_id' => $order_info['order_id'], 'order_sn' => $order_info['order_sn'], 'state' => 0));
        $list = $result['list'];
		$inc_money = 0;
        foreach ($list as $commiss) {
            if ($commiss['state'] == 0) {
				$inc_money += $commiss['commiss_money'];
            }
        }
		if ($inc_money > 0) {
			$comiss_info = model('store_commiss')->getInfo(array('store_id' => $order_info['store_id']));
			if (empty($comiss_info)) {
				$ins_data = array();
				$ins_data['store_id'] = $order_info['store_id'];
				$ins_data['money'] = 0;
				$ins_data['dongmoney'] = 0;
				$ins_data['getmoney'] = 0;
				model('store_commiss')->add($ins_data);
			}
			model('store_commiss')->edit(array('store_id' => $order_info['store_id']), 'money=money+' . $inc_money);
		}
		model('store_commiss_order')->edit(array('order_id' => $order_info['order_id'], 'order_sn' => $order_info['order_sn'], 'state' => 0), array('state' => 1));
    }
}