<?php
namespace shop\controller;
class store extends home {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if (IS_API) {
			
			$return = array(
				'title' => '店铺列表',
			);
			output_data($return);
		}
	}
	public function store_listOp() {
		if (IS_API) {
			$longitude = input('longitude', 0);
			$latitude = input('latitude', 0);
			$city_id = input('city_id', 0, 'intval');
			$area_id = input('area_id', 0, 'intval');
			$where['state'] = 1;
			if ($city_id) {
				$where['city_id'] = $city_id;
			}
			if ($area_id) {
				$where['area_id'] = $area_id;
			}
			$sc_id = input('sc_id', 0, 'intval');
			if ($sc_id) {
				$where['sc_id'] = $sc_id;
			}
			$result = model('seller')->get_gps_seller_list($where, $latitude, $longitude, 'id,name,logo,region,address', 20, input('page', 1, 'intval'), 3, false);
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			$list = $result['list'];
			unset($result);
			$return = array(
				'title' => '店铺列表',
				'list' => $list,
				'totalpage' => $totalpage,
				'hasmore' => $hasmore,
			);
			output_data($return);
		}
	}
	public function store_infoOp() {
		if (IS_API) {
			$store_id = input('store_id', 0, 'intval');
			if ($store_id <= 0) {
				output_error('参数错误');
			}
			$store_online_info = model('seller')->where(array('id' => $store_id, 'state' => 1))->find();
			if (empty($store_online_info)) {
				output_error('店铺不存在或未开启');
			}
			// 如果已登录 判断该店铺是否已被收藏
			if ($memberId = $this->getMemberIdIfExists()) {
				$c = (int) model('favorites')->where(array('member_id' => $memberId, 'fav_type' => 'store', 'fav_id' => $store_id))->total();
				$store_online_info['is_favorate'] = $c > 0;
			} else {
				$store_online_info['is_favorate'] = false;
			}
			$store_online_info['banner'] = tomedia($store_online_info['banner']);
			$store_online_info['logo'] = tomedia($store_online_info['logo']);
			$list_temp = model('swiper')->getList(array('store_id' => $store_id, 'module' => 'index'), 'image');
			foreach ($list_temp['list'] as $k => $v) {
				$list_temp['list'][$k]['image'] = tomedia($list_temp['list'][$k]['image']);
			}
			$return = array(
				'title' => $store_online_info['name'],
				'store_info' => $store_online_info,
				'imgUrls' => $list_temp['list'],
			);
			output_data($return);
		}
	}
	public function store_classOp() {
		if (IS_API) {
			$result = model('seller_class')->getList();
			$return = array(
				'title' => '店铺分类',
				'list' => $result['list']
			);
			output_data($return);
		}
	}
	/**
     * 获取城市列表
     */
    public function get_city_listOp() {
		if (IS_API) {
			$city_ids_Arr = model('seller')->field('city_id,area_id')->order('city_id asc,area_id asc')->select();
			$city_ids = $area_ids = array();
			foreach ($city_ids_Arr as $k => $val) {
				$city_ids[] = $val['city_id'];
				$area_ids[] = $val['area_id'];
			}

			$city_list = model('fxy_area')->where(array('area_id' => $city_ids))->select();
			$area_list = model('fxy_area')->where(array('area_id' => $area_ids))->select();
			$city_arr = array();
			foreach ($city_list as $key => $value) {
				$city_arr[$key]['city_id'] = $value['area_id'];
				$city_arr[$key]['districtLevel'] = 'CITY';
				$city_arr[$key]['districtName'] = $value['area_name'];
				$city_arr[$key]['firstLetter'] = $this->getFirstCharter($value['area_name']);
				$city_arr[$key]['serviceStatus'] = 'N';
				foreach ($area_list as $k => $v) {
					if ($v['area_parent_id'] == $value['area_id']) {
						$item = array(
							'area_id' => $v['area_id'],
							'area_name' => $v['area_name'],
						);
						$city_arr[$key]['areas'][] = $item;
					}
				}
			}
			output_data(array('list' => $city_arr));
		}
    }
	/**
     * 获取首字母
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    function getFirstCharter($str) {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str[0]);
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str[0]);
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
        if ($asc >= - 20319 && $asc <= - 20284) return 'A';
        if ($asc >= - 20283 && $asc <= - 19776) return 'B';
        if ($asc >= - 19775 && $asc <= - 19219) return 'C';
        if ($asc >= - 19218 && $asc <= - 18711) return 'D';
        if ($asc >= - 18710 && $asc <= - 18527) return 'E';
        if ($asc >= - 18526 && $asc <= - 18240) return 'F';
        if ($asc >= - 18239 && $asc <= - 17923) return 'G';
        if ($asc >= - 17922 && $asc <= - 17418) return 'H';
        if ($asc >= - 17417 && $asc <= - 16475) return 'J';
        if ($asc >= - 16474 && $asc <= - 16213) return 'K';
        if ($asc >= - 16212 && $asc <= - 15641) return 'L';
        if ($asc >= - 15640 && $asc <= - 15166) return 'M';
        if ($asc >= - 15165 && $asc <= - 14923) return 'N';
        if ($asc >= - 14922 && $asc <= - 14915) return 'O';
        if ($asc >= - 14914 && $asc <= - 14631) return 'P';
        if ($asc >= - 14630 && $asc <= - 14150) return 'Q';
        if ($asc >= - 14149 && $asc <= - 14091) return 'R';
        if ($asc >= - 14090 && $asc <= - 13319) return 'S';
        if ($asc >= - 13318 && $asc <= - 12839) return 'T';
        if ($asc >= - 12838 && $asc <= - 12557) return 'W';
        if ($asc >= - 12556 && $asc <= - 11848) return 'X';
        if ($asc >= - 11847 && $asc <= - 11056) return 'Y';
        if ($asc >= - 11055 && $asc <= - 10247) return 'Z';
        return null;
    }
	/**
     * 店铺商品分类
     */
    public function store_goods_classOp() {
		if (IS_API) {
			$store_id = input('store_id', 0, 'intval');
			if ($store_id <= 0) {
				output_error('参数错误');
			}
			$store_online_info = model('seller')->where(array('id' => $store_id, 'state' => 1))->find();
			if (empty($store_online_info)) {
				output_error('店铺不存在或未开启');
			}
			$store_goods_class = model('store_goods_class')->getShowTreeList($store_id);
			output_data(array('store_info' => $store_online_info, 'store_goods_class' => array_values($store_goods_class)));
		}
    }
	/**
     * 店铺活动
     */
    public function store_promotionOp() {
        $model_xianshi = model('p_xianshi');
        $promotion['promotion'] = $condition = array();
		$store_id = input('store_id', 0, 'intval');
        $condition['store_id'] = $store_id;
        $result = $model_xianshi->getList($condition);
		$xianshi = array();
        if (!empty($result['list'])) {
            foreach ($result['list'] as $key => $value) {
				$value = $model_xianshi->getXianshiExtendInfo($value);
                $value['start_time_text'] = date('Y-m-d', $value['start_time']);
                $value['end_time_text'] = date('Y-m-d', $value['end_time']);
				$xianshi[] = $value;
            }
            $promotion['promotion']['xianshi'] = $xianshi;
        }
		unset($result);
        $model_mansong = model('p_mansong');
        $mansong = $model_mansong->getMansongInfoByStoreID($store_id);
        if (!empty($mansong)) {
            $mansong['start_time_text'] = date('Y-m-d', $mansong['start_time']);
            $mansong['end_time_text'] = date('Y-m-d', $mansong['end_time']);
            $promotion['promotion']['mansong'] = $mansong;
        }
        output_data($promotion);
    }
}