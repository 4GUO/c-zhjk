<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_goods {    
    public function __construct() {
		
    }
	public function get_shop_goods_info($goods_id, $level_id = 0, $field = '*') {
		$goods_info = array();
		$goods_info = model('shop_goods')->getInfo(array('goods_id' => $goods_id), $field);
		if(empty($goods_info['goods_id'])){
			return array();
		}
		
		$goodscommon_info = model('shop_goods_common')->getInfo(array('goods_commonid' => $goods_info['goods_commonid']), '*,goods_name as goods_common_name');
		if(empty($goodscommon_info['goods_commonid'])){
			return array();
		}
		
		$store_info = model('seller')->getInfo(array('id' => $goodscommon_info['store_id']), 'id,name,member_id');
		$goodscommon_info['store_name'] = isset($store_info['name']) ? $store_info['name'] : '';
		$goods_info = array_merge($goodscommon_info, $goods_info);
		$goods_info['goods_image'] = tomedia($goods_info['goods_image']);
		$spec_value = $goodscommon_info['spec_value'] ? fxy_unserialize($goodscommon_info['spec_value']) : array();
        $spec_name = $goodscommon_info['spec_name'] ? fxy_unserialize($goodscommon_info['spec_name']) : array();
		$goods_info['goods_spec'] = $goods_info['goods_spec'] ? fxy_unserialize($goods_info['goods_spec']) : array();
		$goods_info['spec_lists'] = array();
		if($spec_name){
			foreach($spec_name as $spec_key => $spec_val) {
				$spec_array = array();
				if(!empty($spec_value[$spec_key])){
					foreach($spec_value[$spec_key] as $kkk => $vvv){
						$spec_array[] = array(
							'value_id' => $kkk,
							'value' => $vvv,
							'selected' => empty($goods_info['goods_spec'][$kkk]) ? 0 : 1
						);
					}
				}
				$goods_info['spec_lists'][] = array(
					'spec_id' => $spec_key,
					'spec_name' => $spec_val,
					'spec_values' => $spec_array
				);
				
				unset($spec_array);
			}
		}
		$goods_info['mobile_body'] = $goodscommon_info['mobile_body'] ? htmlspecialchars_decode($goodscommon_info['mobile_body']) : '';
		$goods_info['goods_video'] = $goodscommon_info['goods_video'] ? tomedia($goodscommon_info['goods_video']) : '';
		$goods_info['goods_video_poster'] = $goodscommon_info['goods_video_poster'] ? tomedia($goodscommon_info['goods_video_poster']) : '';
		$goods_info['virtual_indate_str'] = $goodscommon_info['virtual_indate'] ? date('Y-m-d', $goodscommon_info['virtual_indate']) : '';
		unset($goodscommon_info);
		
		$goods_info = $this->get_goods_price($goods_info, $level_id);
		
		//限时折扣
		$condition = array();
        $condition['state'] = 1;
        $condition['start_time <'] = TIMESTAMP;
        $condition['end_time >'] = TIMESTAMP;
		$condition['goods_id'] = $goods_info['goods_commonid'];
		$xianshi_goods = model('p_xianshi_goods')->getInfo($condition);
		if (!empty($xianshi_goods)) {
			$goods_info['xianshi_title'] = $xianshi_goods['xianshi_title'];
			$goods_info['lower_limit'] = $xianshi_goods['lower_limit'];
			$goods_info['xianshi_explain'] = $xianshi_goods['xianshi_explain'];
			$goods_info['down_price'] = priceFormat($goods_info['goods_price'] - $xianshi_goods['xianshi_price']);
			$goods_info['old_goods_price'] = $goods_info['goods_price'];
			$goods_info['goods_price'] = $xianshi_goods['xianshi_price'];
			$goods_info['end_times'] = $xianshi_goods['end_time'] - time() > 0 ? $xianshi_goods['end_time'] - time() : 0;
			$countdown_date = lib\timer::time2string($goods_info['end_times']);
			$goods_info['countdown_date'] = $countdown_date;
			$goods_info['xianshi_flag'] = true;
		} else {
			$goods_info['xianshi_flag'] = false;
		}
		if (!empty($goods_info['is_spike'])) {
			$goods_info['end_times'] = $goods_info['spike_end_time'] - time() > 0 ? $goods_info['spike_end_time'] - time() : 0;
			$countdown_date = lib\timer::time2string($goods_info['end_times']);
			$goods_info['countdown_date'] = $countdown_date;
		}
		//满即送
        $mansong_info = $goods_info['is_virtual'] == 1 ? array() : model('p_mansong')->getMansongInfoByStoreID($goods_info['store_id']);
		$goods_info['mansong_info'] = $mansong_info;
		//获得产品属性
		$spec_list = array();
		$goods_array = model('shop_goods')->getList(array('goods_commonid' => $goods_info['goods_commonid']), 'goods_id,goods_spec,goods_image');
		if(!empty($goods_array['list']) && is_array($goods_array['list'])){
			foreach($goods_array['list'] as $k => $v){
				if(!empty($v['goods_spec'])){
					$sp_array = fxy_unserialize($v['goods_spec']);
					$key_temp = implode('|', array_keys($sp_array));
					$spec_list[$key_temp] = $v['goods_id'];
					unset($sp_array);
					unset($key_temp);
				}
			}
		}
		unset($goods_array);
		
		$imgUrls = array();
		$result = model('shop_goods_images')->getList(array('goods_commonid' => $goods_info['goods_commonid'], 'color_id' => $goods_info['color_id']), 'goods_image', 'is_default desc,goods_image_sort asc');
		foreach($result['list'] as $r){
			if (is_ssl()) {
				$imgUrls[] = str_replace('http:', 'https:', tomedia($r['goods_image']));
			} else {
				$imgUrls[] = tomedia($r['goods_image']);
			}
		}
		return array('goods_info' => $goods_info, 'imgUrls' => $imgUrls, 'spec_list' => $spec_list, 'store_info' => $store_info);
	}
	
	public function get_goods_price($goods_info, $level_id){
		if(empty($goods_info['goods_price_vip'])){
			return $goods_info;
		}
		$price_vip = fxy_unserialize($goods_info['goods_price_vip']);
		if(empty($price_vip[$level_id])){
			return $goods_info;
		}
		
		$goods_info['goods_price'] = $price_vip[$level_id];
		
		foreach($price_vip as $lid => $price){
			if($lid >= $level_id){
				break;
			}
		}
		//$goods_info['goods_marketprice'] = $goods_info['yeji_price'] > 0 ? $goods_info['yeji_price'] : $goods_info['goods_marketprice'];
		return $goods_info;
	}
}