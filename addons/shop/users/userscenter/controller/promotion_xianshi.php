<?php
namespace userscenter\controller;
use lib;
class promotion_xianshi extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
        $model_xianshi = model('p_xianshi');
        $condition = array();
        $store_name = input('store_name', '');
        if (!empty($store_name)) {
            $condition['store_name'] = '%' . $store_name . '%';
        }
		$xianshi_name = input('xianshi_name', '');
        if (!empty($xianshi_name)) {
            $condition['xianshi_name'] = '%' . $xianshi_name . '%';
        }
		$state = input('state', 0, 'intval');
        if (!empty($state)) {
            $condition['state'] = $state;
        }
		$result = $model_xianshi->getList($condition, '*', 'state desc,end_time desc', 10, input('get.page', 1, 'intval'));
        $list = array();
		foreach ($result['list'] as $v) {
			$list[] = $model_xianshi->getXianshiExtendInfo($v);
		}
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'state' => $state, 'xianshi_name' => $xianshi_name, 'store_name' => $store_name), users_url('promotion_xianshi/index')));
		$this->assign('list', $list);
		$this->assign('xianshi_state_array', $model_xianshi->getXianshiStateArray());
		$this->display();
	}
	/**
     * 活动详细信息
     **/
    public function infoOp() {
        $xianshi_id = input('xianshi_id', 0, 'intval');
        $model_xianshi = model('p_xianshi');
        $model_xianshi_goods = model('p_xianshi_goods');
        $xianshi_info = $model_xianshi->getInfo(array('xianshi_id' => $xianshi_id));
        if (empty($xianshi_info)) {
            web_error('参数错误');
        }
        $this->assign('xianshi_info', $xianshi_info);
        //获取限时折扣商品列表
        $condition = array();
        $condition['xianshi_id'] = $xianshi_id;
        $xianshi_goods_list = $model_xianshi_goods->getList($condition);
        $this->assign('xianshi_goods_list', $xianshi_goods_list['list']);
        $this->display();
    }
	public function cancelOp() {
		$model = model('p_xianshi');
		$xianshi_id = input('xianshi_id', 0, 'intval');
		$where = array();
		$where['xianshi_id'] = $xianshi_id;
		$state = $model->edit($where, array('state' => 3));
        if ($state) {
			model('p_xianshi_goods')->edit(array('xianshi_id' => $xianshi_id), array('state' => 3));
            output_data(array('url' => _url('promotion_xianshi/index')));
        } else {
			output_error('操作失败！');
        }
    }
	public function delOp() {
		$model = model('p_xianshi');
		$xianshi_id = input('xianshi_id', 0, 'intval');
		$where = array();
		$where['xianshi_id'] = $xianshi_id;
		$state = $model->where($where)->delete();
        if ($state) {
			$model_xianshi_goods = model('p_xianshi_goods');
            $model_xianshi_goods->del($where);
            output_data(array('url' => _url('promotion_xianshi/index')));
        } else {
			output_error('删除失败！');
        }
    }
}