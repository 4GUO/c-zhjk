<?php
namespace userscenter\controller;
use lib;
class promotion_mansong extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
        $model_mansong = model('p_mansong');
        $condition = array();
		$store_name = input('store_name', '');
        if (!empty($store_name)) {
            $condition['store_name'] = '%' . $store_name . '%';
        }
		$mansong_name = input('mansong_name', '');
        if (!empty($mansong_name)) {
            $condition['mansong_name'] = '%' . $mansong_name . '%';
        }
		$state = input('state', 0, 'intval');
        if (!empty($state)) {
            $condition['state'] = $state;
        }
		$result = $model_mansong->getList($condition, '*', 'state desc,end_time desc', 10, input('get.page', 1, 'intval'));
        $list = array();
		foreach ($result['list'] as $v) {
			$list[] = $model_mansong->getMansongExtendInfo($v);
		}
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'state' => $state, 'mansong_name' => $mansong_name, 'store_name' => $store_name), _url('promotion_mansong/index')));
		$this->assign('list', $list);
		$this->assign('mansong_state_array', $model_mansong->getMansongStateArray());
		$this->display();
	}
	/**
     * 满就送活动详细信息
     **/
    public function infoOp() {
        $mansong_id = input('mansong_id', 0, 'intval');
        $model_mansong = model('p_mansong');
        $model_mansong_rule = model('p_mansong_rule');
		$model_goods = model('shop_goods_common');
        $mansong_info = $model_mansong->getInfo(array('mansong_id' => $mansong_id));
        if (empty($mansong_info)) {
            output_error('参数错误');
        }
        $this->assign('mansong_info', $mansong_info);
        $param = array();
        $param['mansong_id'] = $mansong_id;
        $result = $model_mansong_rule->getList(array('mansong_id' => $mansong_id));
		$rule_list = array();
		foreach ($result['list'] as $v) {
			$goods_id = intval($v['goods_id']);
			if (!empty($goods_id)) {
				$goods_info = $model_goods->getInfo(array('goods_commonid' => $goods_id));
				if (!empty($goods_info)) {
					if (empty($v['mansong_goods_name'])) {
						$v['mansong_goods_name'] = $goods_info['goods_name'];
					}
					$v['goods_image'] = $goods_info['goods_image'];
					$v['goods_id'] = $goods_id;
					$v['goods_url'] = 'javascript:;';
				}
			}
			$rule_list[] = $v;
		}
        $this->assign('list', $rule_list);
        $this->display();
    }
	public function cancelOp() {
		$mansong_id = input('mansong_id', 0, 'intval');
        $model_mansong = model('p_mansong');
        $mansong_info = $model_mansong->getInfo(array('mansong_id' => $mansong_id));
        if (empty($mansong_info)) {
            output_error('参数错误');
        }
        $condition = array();
        $condition['mansong_id'] = $mansong_id;
        $result = $model_mansong->edit($condition, array('state' => 3));
        if ($result) {
            $this->log('取消满即送活动，活动名称：' . $mansong_info['mansong_name']);
            output_data(array('url' => _url('promotion_mansong/index')));
        } else {
            output_error('操作失败！');
        }
    }
	public function delOp() {
		$mansong_id = input('mansong_id', 0, 'intval');
        $model_mansong = model('p_mansong');
        $mansong_info = $model_mansong->getInfo(array('mansong_id' => $mansong_id));
        if (empty($mansong_info)) {
            output_error('参数错误');
        }
        $condition = array();
        $condition['mansong_id'] = $mansong_id;
        $result = $model_mansong->del($condition);
        if ($result) {
			$model_mansong_rule = model('p_mansong_rule');
			$model_mansong_rule->del($condition);
            $this->log('删除满即送活动，活动名称：' . $mansong_info['mansong_name']);
            output_data(array('url' => _url('promotion_mansong/index')));
        } else {
            output_error('删除失败！');
        }
    }
}