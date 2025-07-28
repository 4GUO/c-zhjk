<?php
namespace sellercenter\controller;
use lib;
class promotion_mansong extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
        $model_mansong = model('p_mansong');
        $condition = array();
        $condition['store_id'] = $this->store_id;
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
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'state' => $state, 'mansong_name' => $mansong_name), _url('promotion_mansong/index')));
		$this->assign('list', $list);
		$this->assign('mansong_state_array', $model_mansong->getMansongStateArray());
		$this->display();
	}
    public function mansong_addOp() {
		$model_mansong = model('p_mansong');
		$model_mansong_rule = model('p_mansong_rule');
		$near_time = $model_mansong->getMansongNewStartTime($this->store_id);
		if (empty($near_time)) {
			$near_time = time();
		}
		if (chksubmit()) {
			//验证输入
			$mansong_name = input('mansong_name', '');
			$start_time = strtotime(input('start_time', ''));
			$end_time = strtotime(input('end_time', ''));
			if (empty($mansong_name)) {
				output_error('请填写活动名称');
			}
			if (empty($start_time) || $start_time <= $near_time) {
				output_error('开始时间不能为空且不能早于' . date('Y-m-d H:i', $near_time));
			}
			if (empty($end_time)) {
				output_error('结束时间不能为空');
			}
			if ($start_time >= $end_time) {
				output_error('结束时间不能小于开始时间');
			}
			$p_mansong_rule = input('mansong_rule/a', array());
			if (empty($p_mansong_rule)) {
				output_error('满即送规则不能为空');
			}
			$param = array();
			$param['mansong_name'] = $mansong_name;
			$param['start_time'] = $start_time;
			$param['end_time'] = $end_time;
			$param['store_id'] = $this->store_id;
			$param['store_name'] = $this->store_info['name'];
			$param['member_id'] = $this->store_info['member_id'];
			$param['remark'] = input('remark', '', 'trim');
			$mansong_id = $model_mansong->add($param);
			if ($mansong_id) {
				$mansong_rule_array = array();
				foreach ($p_mansong_rule as $value) {
					list($price, $discount, $goods_id) = explode(',', $value);
					$mansong_rule = array();
					$mansong_rule['mansong_id'] = $mansong_id;
					$mansong_rule['price'] = $price;
					$mansong_rule['discount'] = $discount;
					$mansong_rule['goods_id'] = $goods_id;
					$mansong_rule_array[] = $mansong_rule;
				}
				//生成规则
				$result = $model_mansong_rule->insertAll($mansong_rule_array);
				$this->log('添加满即送活动，活动名称：' . $mansong_name);
				output_data(array('msg' => '操作成功', 'url' => _url('promotion_mansong/index')));
			} else {
				output_error('添加失败');
			}
		} else {
			$this->assign('start_time', $near_time);
			$this->display();
		}
    }
	/**
     * 满就送活动详细信息
     **/
    public function mansong_detailOp() {
        $mansong_id = input('mansong_id', 0, 'intval');
        $model_mansong = model('p_mansong');
        $model_mansong_rule = model('p_mansong_rule');
		$model_goods = model('shop_goods_common');
        $mansong_info = $model_mansong->getInfo(array('mansong_id' => $mansong_id, 'store_id' => $this->store_id));
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
	public function mansong_delOp() {
		$mansong_id = input('mansong_id', 0, 'intval');
        $model_mansong = model('p_mansong');
        $mansong_info = $model_mansong->getInfo(array('mansong_id' => $mansong_id, 'store_id' => $this->store_id));
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