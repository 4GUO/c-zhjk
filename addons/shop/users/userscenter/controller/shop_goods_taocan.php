<?php
namespace userscenter\controller;
use lib;
class shop_goods_taocan extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
	    $where = array();
		$list_temp = model('shop_goods_taocan')->getList($where, '*', 'tc_sort asc');
		$tc_list = array();
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function publishOp() {
		$model_class = model('shop_goods_taocan');
		if (chksubmit()) {
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(
				array('input' => input('tc_name', '', 'trim'), 'require' => 'true', 'message' => '套餐名称不能为空'),
			);
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$tc_id = input('id', 0, 'intval');
			$class_array = array();
            $class_array['tc_name'] = input('tc_name', '');
            $class_array['tc_state'] = input('tc_state', 0, 'intval');
            $class_array['tc_sort'] = input('tc_sort', 9999, 'intval');
			$class_array['tc_image'] = input('image_path', '');
			if (!$tc_id) {
				$state = $model_class->add($class_array);
				$tc_id = $state;
			} else {
				$state = $model_class->edit(array('tc_id' => input('id', 0, 'intval')), $class_array);
			}
			$goods_nums = input('goods_nums', array());
			$res = model('shop_goods_taocan_goods')->where(array('taocan_id' => $tc_id))->select();
            //更新和插入的商品(有则更新数量，没有则插入)
			$up_taocan_goods = $ins_taocan_goods = array();
			foreach ($goods_nums as $goods_commonid => $goods_num) {
				foreach ($res as $v) {
					if($goods_commonid == $v['goods_commonid']) {
						$up_taocan_goods[] = array(
							'id' => $v['id'],
							'goods_num' => $goods_num,
						);
						unset($goods_nums[$goods_commonid]);
						break;
					}
				}
			}
			if ($up_taocan_goods) {
				$sql = batchUpdate('ims_fxy_shop_goods_taocan_goods', $up_taocan_goods, 'id');
				model()->query($sql, 'update');
			}
			//剩余的$goods_nums则为新增
			foreach ($goods_nums as $goods_commonid => $goods_num) {
				$ins_taocan_goods[] = array(
					'taocan_id' => $tc_id,
					'goods_commonid' => $goods_commonid,
					'goods_num' => $goods_num,
				);
			}
			if ($ins_taocan_goods) {
				model('shop_goods_taocan_goods')->insertAll($ins_taocan_goods);
			}
			output_data(array('msg' => '保存成功', 'url' => users_url('shop_goods_taocan/index')));
		} else {
			$tc_id = input('get.id', 0, 'intval');
			$class_info = model('shop_goods_taocan')->getInfo(array('tc_id' => $tc_id));			
			$this->assign('class_info', $class_info);
			$goods = model('shop_goods_taocan_goods')->where(array('taocan_id' => $tc_id))->select();
			$goods_commonids = array();
			foreach ($goods as $v) {
				$goods_commonids[$v['goods_commonid']] = $v;
			}
			$goods_list = array();
			$res = model('shop_goods_common')->where(array('goods_commonid' => array_keys($goods_commonids)))->select();
			foreach ($res as $v) {
				$v['goods_num'] = $goods_commonids[$v['goods_commonid']]['goods_num'] ?? 0;
				$goods_list[] = $v;
			}
			$this->assign('goods_list', $goods_list);
			$this->display();
		}
	}
	public function delOp() {
		$model_class = model('shop_goods_taocan');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$check = model('shop_goods_taocan_goods')->where(array('taocan_id' => $id_array))->find();
		if ($check) {
			output_error('该套餐下有商品，不能删除');
		}
		$where = array();
		$where['tc_id'] = $id_array;
		$state = $model_class->del($where);
        if ($state) {
            output_data(array('url' => users_url('shop_goods_taocan/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function edit_goods_numOp() {
		$goods_commonid = input('goods_commonid', 0, 'intval');
		$taocan_id = input('taocan_id', 0, 'intval');
		$goods_num = input('goods_num', 0, 'intval');
		$check = model('shop_goods_taocan_goods')->where(array('taocan_id' => $taocan_id, 'goods_commonid' => $goods_commonid))->find();
		if ($check) {
			model('shop_goods_taocan_goods')->where(array('taocan_id' => $taocan_id, 'goods_commonid' => $goods_commonid))->update(array('goods_num' => $goods_num));
		}
		output_data('1');
	}
	public function del_goodsOp() {
		$goods_commonid = input('goods_commonid', 0, 'intval');
		$taocan_id = input('taocan_id', 0, 'intval');
		$check = model('shop_goods_taocan_goods')->where(array('taocan_id' => $taocan_id, 'goods_commonid' => $goods_commonid))->find();
		if ($check) {
			model('shop_goods_taocan_goods')->where(array('taocan_id' => $taocan_id, 'goods_commonid' => $goods_commonid))->delete();
		}
		output_data('1');
	}
	public function selectViewOp() {
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
	public function selectGoodsOp() {
		if (IS_API) {
			$model_goods_common = model('shop_goods_common');
			$model_goods = model('shop_goods');
			$keyw = input('goods_name', '');
			$condition = array();
			$condition['goods_state'] = 1;
			if ($keyw) {
				$condition['goods_name'] = '%' . $keyw . '%';
			}
			
			$result = $model_goods_common->getList($condition, 'goods_commonid,goods_image,goods_name', 'goods_commonid DESC', 18, input('page', 1, 'intval'));
			$goods_common_list = array();
			foreach ($result['list'] as $k => $v) {
				$goods_common_list[$v['goods_commonid']] = $v;
			}
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			unset($result);
			$result = $model_goods->getList(array('goods_commonid' => array_keys($goods_common_list)), 'goods_id,goods_commonid,goods_marketprice,goods_price', 'goods_id asc');
			$list = array();
			foreach ($result['list'] as $k => $v) {
				if (isset($list[$v['goods_commonid']])) {
					continue;
				}
				$common_info = $goods_common_list[$v['goods_commonid']];
				$v['link'] = uni_url('/pages/goods/goods_info', array('goods_id' => $v['goods_id']));
				$list[$v['goods_commonid']] = array_merge($common_info, $v);
			}
			unset($result);
			output_data(array('list' => array_values($list), 'totalpage' => $totalpage, 'page_html' => page($totalpage, array('page' => input('get.page', 1, 'intval')), users_url('shop_goods/selectGoods'), true)));
		}
	}
}