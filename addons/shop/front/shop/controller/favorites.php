<?php
namespace shop\controller;
use lib;
class favorites extends member {
	public function __construct() {
		parent::_initialize();
	}
	/**
     * 收藏列表
     */
    public function favorites_listOp() {
        $model_favorites = model('favorites');
		$type = input('type', 'store');
		if ($type == 'store') {
			$this->title = '我收藏的店铺';
		} else {
			$this->title = '我收藏的商品';
		}
		$where['member_id'] = $this->member_info['uid'];
		$where['fav_type'] = $type;
		$result = $model_favorites->getList($where, '*', 'log_id desc', 20, input('page', 1, 'intval'));
		$list = array();
		foreach ($result['list'] as $k => $v) {
			$v['fav_time'] = date('Y/m/d', $v['fav_time']);
			$list[] = $v;
		}
		$return = array(
			'title' => $this->title,
			'list' => $list,
			'totalpage' => $result['totalpage'],
			'hasmore' => $result['hasmore'],
		);
		output_data($return);
    }
    /**
     * 添加收藏
     */
    public function favorites_addOp() {
        $fav_id = input('fav_id', 0, 'intval');
        if ($fav_id <= 0) {
            output_error('参数错误');
        }
		$type = input('type', 'store');
        $favorites_model = model('favorites');
        //判断是否已经收藏
        $favorites_info = $favorites_model->getInfo(array('member_id' => $this->member_info['uid'], 'fav_type' => $type, 'fav_id' => $fav_id));
        if (!empty($favorites_info)) {
            output_error('您已经收藏过了');
        }
        //添加收藏
        $insert_arr = array();
        $insert_arr['member_id'] = $this->member_info['uid'];
        $insert_arr['member_name'] = $this->member_info['nickname'];
        $insert_arr['fav_id'] = $fav_id;
        $insert_arr['fav_type'] = $type;
        $insert_arr['fav_time'] = time();
		if ($type == 'store') {
			$store_info = model('seller')->getInfo(array('id' => $fav_id));
			$insert_arr['fav_name'] = $store_info['name'];
			$insert_arr['fav_image'] = $store_info['logo'];
		} else {
			$goods_info = model('shop_goods')->getInfo(array('goods_id' => $fav_id));
			$insert_arr['fav_name'] = $store_info['goods_name'];
			$insert_arr['fav_image'] = $store_info['goods_image'];
			$insert_arr['log_price'] = $store_info['goods_price'];
		}
        $result = $favorites_model->add($insert_arr);
        if ($result) {
			if ($type == 'store') {
				//增加收藏数量
				$store_model = model('seller');
				$store_model->edit(array('id' => $fav_id), 'store_collect=store_collect+1');
			}
            output_data('1');
        } else {
            output_error('收藏失败');
        }
    }
    /**
     * 删除收藏
     */
    public function favorites_delOp() {
        $fav_id = input('fav_id', 0, 'intval');
        if ($fav_id <= 0) {
            output_error('参数错误');
        }
		$type = input('type', 'store');
        $model_favorites = model('favorites');
        $condition = array();
		$condition['member_id'] = $this->member_info['uid'];
        $condition['fav_type'] = $type;
        $condition['fav_id'] = $fav_id;
       
        //判断是否已经收藏
        $favorites_info = $model_favorites->getInfo($condition);
        if (empty($favorites_info)) {
            output_error('收藏取消失败');
        }
        $model_favorites->del($condition);
		if ($type == 'store') {
			//增加收藏数量
			$store_model = model('seller');
			$store_model->edit(array('id' => $fav_id, 'store_collect >' => 0), 'store_collect=store_collect-1');
		}
        output_data('1');
    }
}