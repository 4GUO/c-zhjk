<?php
namespace sellercenter\controller;
use lib;
class promotion_xianshi extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
        $model_xianshi = model('p_xianshi');
        $condition = array();
        $condition['store_id'] = $this->store_id;
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
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'state' => $state, 'xianshi_name' => $xianshi_name), users_url('promotion_xianshi/index')));
		$this->assign('list', $list);
		$this->assign('xianshi_state_array', $model_xianshi->getXianshiStateArray());
		$this->display();
	}
    public function xianshi_addOp() {
		if (chksubmit()) {
			//验证输入
			$xianshi_name = input('xianshi_name', '');
			$start_time = strtotime(input('start_time', ''));
			$end_time = strtotime(input('end_time', ''));
			$lower_limit = input('lower_limit', 0, 'intval');
			if ($lower_limit <= 0) {
				$lower_limit = 1;
			}
			if (empty($xianshi_name)) {
				output_error('请填写活动名称');
			}
			if ($start_time >= $end_time) {
				output_error('结束时间不能小于开始时间');
			}
			//生成活动
			$model_xianshi = model('p_xianshi');
			$param = array();
			$param['xianshi_name'] = $xianshi_name;
			$param['xianshi_title'] = input('xianshi_title', '');
			$param['xianshi_explain'] = input('xianshi_explain', '');
			$param['start_time'] = $start_time;
			$param['end_time'] = $end_time;
			$param['store_id'] = $this->store_id;
			$param['store_name'] = $this->store_info['name'];
			$param['member_id'] = $this->store_info['member_id'];
			$param['lower_limit'] = $lower_limit;
			$result = $model_xianshi->add($param);
			if ($result) {
				$this->log('添加限时折扣活动，活动名称：' . $xianshi_name . '，活动编号：' . $result);
				// 添加计划任务
				//$this->addcron(array('exetime' => $param['end_time'], 'exeid' => $result, 'type' => 7), true);
				output_data(array('msg' => '操作成功', 'url' => _url('promotion_xianshi/index')));
			} else {
				output_error('添加失败');
			}
		}
		$this->display();
    }
	public function xianshi_editOp() {
		if (chksubmit()) {
			//验证输入
			$xianshi_name = input('xianshi_name', '');
			$lower_limit = input('lower_limit', 0, 'intval');
			if ($lower_limit <= 0) {
				$lower_limit = 1;
			}
			if (empty($xianshi_name)) {
				output_error('请填写活动名称');
			}
			$xianshi_id = input('xianshi_id', 0, 'intval');
			//生成活动
			$model_xianshi = model('p_xianshi');
			$param = array();
			$param['xianshi_name'] = $xianshi_name;
			$param['xianshi_title'] = input('xianshi_title', '');
			$param['xianshi_explain'] = input('xianshi_explain', '');
			$param['store_id'] = $this->store_id;
			$param['store_name'] = $this->store_info['name'];
			$param['member_id'] = $this->store_info['member_id'];
			$param['lower_limit'] = $lower_limit;
			$result = $model_xianshi->edit(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_id), $param);
			if ($result) {
				$param = array(
					'xianshi_name' => $xianshi_name,
					'xianshi_title' => input('xianshi_title', ''),
					'xianshi_explain' => input('xianshi_explain', ''),
					'lower_limit' => $lower_limit
				);
				model('p_xianshi_goods')->edit(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_id), $param);
				$this->log('编辑限时折扣活动，活动名称：' . $xianshi_name . '，活动编号：' . $result);
				output_data(array('msg' => '操作成功', 'url' => _url('promotion_xianshi/index')));
			} else {
				output_error('更新失败');
			}
		}
		$xianshi_id = input('xianshi_id', 0, 'intval');
		$info = model('p_xianshi')->getInfo(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_id));
		$this->assign('info', $info);
		$this->display();
    }
	public function xianshi_delOp() {
		$model = model('p_xianshi');
		$xianshi_id = input('xianshi_id', 0, 'intval');
		$where = array();
		$where['xianshi_id'] = $xianshi_id;
		$where['store_id'] = $this->store_id;
		$state = $model->where($where)->delete();
        if ($state) {
			$model_xianshi_goods = model('p_xianshi_goods');
            $model_xianshi_goods->del($where);
            output_data(array('url' => _url('promotion_xianshi/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function xianshi_manageOp() {
		$xianshi_id = input('xianshi_id', 0, 'intval');
		$info = model('p_xianshi')->getInfo(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_id));
		$this->assign('xianshi_info', $info);
		//获取限时折扣商品列表
        $condition = array();
        $condition['xianshi_id'] = $xianshi_id;
        $xianshi_goods_list = model('p_xianshi_goods')->getGoodsExtendList($condition);
        $this->assign('xianshi_goods_list', $xianshi_goods_list);
		$this->display();
	}
    /**
     * 限时折扣商品添加
     **/
    public function xianshi_goods_addOp() {
        $goods_commonid = input('goods_id', 0, 'intval');
        $xianshi_id = input('xianshi_id', 0, 'intval');
        $xianshi_price = input('xianshi_price', 0, 'floatval');
        $model_goods = model('shop_goods_common');
        $model_xianshi = model('p_xianshi');
        $model_xianshi_goods = model('p_xianshi_goods');
        $goods_common_info = $model_goods->getInfo(array('goods_commonid' => $goods_commonid));
        if (empty($goods_common_info) || $goods_common_info['store_id'] != $this->store_id) {
            output_error('商品不存在');
        }
		$goods_info = model('shop_goods')->getInfo(array('goods_commonid' => $goods_common_info['goods_commonid']), '*', 'goods_id asc');
        unset($goods_info['goods_name']);
		$goods_info = array_merge($goods_common_info, $goods_info);
		$xianshi_info = $model_xianshi->getInfo(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_id));
        if (!$xianshi_info) {
            output_error('活动不存在');
        }
        //检查商品是否已经参加同时段活动
        $condition = array();
        $condition['end_time >'] = $xianshi_info['start_time'];
        $condition['goods_id'] = $goods_commonid;
        $xianshi_goods = $model_xianshi_goods->getGoodsExtendList($condition);
        if (!empty($xianshi_goods)) {
			output_error('该商品已经参加了同时段活动');
        }
        //添加到活动商品表
        $param = array();
        $param['xianshi_id'] = $xianshi_info['xianshi_id'];
        $param['xianshi_name'] = $xianshi_info['xianshi_name'];
        $param['xianshi_title'] = $xianshi_info['xianshi_title'];
        $param['xianshi_explain'] = $xianshi_info['xianshi_explain'];
        $param['goods_id'] = $goods_info['goods_commonid'];
        $param['store_id'] = $goods_info['store_id'];
        $param['goods_name'] = $goods_info['goods_name'];
        $param['goods_price'] = $goods_info['goods_price'];
        $param['xianshi_price'] = $xianshi_price;
        $param['goods_image'] = $goods_info['goods_image'];
        $param['start_time'] = $xianshi_info['start_time'];
        $param['end_time'] = $xianshi_info['end_time'];
        $param['lower_limit'] = $xianshi_info['lower_limit'];
        $result = array();
        $xianshi_goods_id = $model_xianshi_goods->add($param);
        if ($xianshi_goods_id) {
			$xianshi_goods_info = $param;
			$xianshi_goods_info['xianshi_goods_id'] = $xianshi_goods_id;
			$xianshi_goods_info = $model_xianshi_goods->getXianshiGoodsExtendInfo($xianshi_goods_info);
			output_data(array('xianshi_goods' => $xianshi_goods_info));
            $this->log('添加限时折扣商品，活动名称：' . $xianshi_info['xianshi_name'] . '，商品名称：' . $goods_info['goods_name']);
            // 添加任务计划
            //$this->addcron(array('type' => 2, 'exeid' => $goods_info['goods_commonid'], 'exetime' => $param['start_time']));
        } else {
			output_error('操作失败');
        }
    }
    /**
     * 限时折扣商品价格修改
     **/
    public function xianshi_goods_price_editOp()
    {
        $xianshi_goods_id = input('xianshi_goods_id', 0, 'intval');
        $xianshi_price = input('xianshi_price', 0, 'floatval');
        $model_xianshi_goods = model('p_xianshi_goods');
        $xianshi_goods_info = $model_xianshi_goods->getInfo(array('xianshi_goods_id' => $xianshi_goods_id, 'store_id' => $this->store_id));
        if (!$xianshi_goods_info) {
			output_error('参数错误');
        }
        $update = array();
        $update['xianshi_price'] = $xianshi_price;
        $condition = array();
        $condition['xianshi_goods_id'] = $xianshi_goods_id;
        $result = $model_xianshi_goods->edit($condition, $update);
        if ($result) {
            $xianshi_goods_info['xianshi_price'] = $xianshi_price;
            $xianshi_goods_info = $model_xianshi_goods->getXianshiGoodsExtendInfo($xianshi_goods_info);
            $data['xianshi_price'] = $xianshi_goods_info['xianshi_price'];
            $data['xianshi_discount'] = $xianshi_goods_info['xianshi_discount'];
            $this->log('限时折扣价格修改为：' . $xianshi_goods_info['xianshi_price'] . '，商品名称：' . $xianshi_goods_info['goods_name']);
			output_data($data);
        } else {
			output_error('更新失败');
        }
    }
    /**
     * 限时折扣商品删除
     **/
    public function xianshi_goods_deleteOp() {
        $model_xianshi_goods = model('p_xianshi_goods');
        $model_xianshi = model('p_xianshi');
        $xianshi_goods_id = input('xianshi_goods_id', 0, 'intval');
        $xianshi_goods_info = $model_xianshi_goods->getInfo(array('xianshi_goods_id' => $xianshi_goods_id));
        if (!$xianshi_goods_info) {
			output_error('参数错误1');
        }
        $xianshi_info = $model_xianshi->getInfo(array('store_id' => $this->store_id, 'xianshi_id' => $xianshi_goods_info['xianshi_id']));
        if (!$xianshi_info) {
			output_error('参数错误2');
        }
        if (!$model_xianshi_goods->del(array('xianshi_goods_id' => $xianshi_goods_id))) {
            output_error('参数错误3');
        }
        $this->log('删除限时折扣商品，活动名称：' . $xianshi_info['xianshi_name'] . '，商品名称：' . $xianshi_goods_info['goods_name']);
		output_data('删除成功');
    }
}