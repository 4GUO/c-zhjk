<?php
namespace userscenter\controller;
use lib;
class voucher extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$model_voucher_template = model('voucher_template');
		$where = array();
		$store_name = input('store_name', '');
        if ($store_name) {
            $where['voucher_t_storename'] = '%' . $store_name. '%';
        }
		$sdate = input('sdate', '');
		$edate = input('edate', '');
        if (!empty($sdate) && !empty($edate)) {
            $sdate = strtotime($sdate);
            $edate = strtotime($edate);
            $where['voucher_t_add_date >='] = $sdate;
			$where['voucher_t_add_date <='] = $edate;
        } else if (!empty($sdate)) {
            $sdate = strtotime($sdate);
            $where['voucher_t_add_date >='] = $sdate;
        } elseif (!empty($edate)) {
            $edate = strtotime($edate);
            $where['voucher_t_add_date <='] = $edate;
        }
        $state = input('state', 0, 'intval');
        if ($state) {
            $where['voucher_t_state'] = $state;
        }
		$recommend = input('recommend', 0, 'intval');
        if ($recommend) {
            $where['voucher_t_recommend'] = 1;
        }
		//状态
		$templatestate_arr = $model_voucher_template->getTemplateStateArray();
		$this->assign('templatestate_arr', $templatestate_arr);
		//会员等级
		$member_grade = logic('yewu')->get_level_list();
		$result = $model_voucher_template->getList($where, '*', 'voucher_t_state asc,voucher_t_id desc', 10, input('get.page', 1, 'intval'));
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'store_name' => $store_name, 'sdate' => $sdate, 'edate' => $edate, 'state' => $state, 'recommend' => $recommend), _url('voucher/index')));
        $list = array();
		foreach ($result['list'] as $k => $v) {
			//状态
			if ($v['voucher_t_state']) {
				foreach ($templatestate_arr as $tstate_k => $tstate_v) {
					if ($v['voucher_t_state'] == $tstate_v[0]) {
						$v['voucher_t_state_text'] = $tstate_v[1];
					}
				}
			}
			//会员等级
			$v['voucher_t_mgradelimittext'] = $member_grade[$v['voucher_t_mgradelimit']]['level_name'];
			$list[] = $v;
		}
		$this->assign('list', $list);
		//店铺
		$result = model('seller')->getList();
		$store_list = array();
		foreach ($result['list'] as $v) {
			$store_list[$v['id']] = $v;
		}
		$this->assign('store_list', $store_list);
        $this->display();
	}
	public function editOp() {
		$model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
		//模板状态
		$templatestate_arr = $model_voucher_template->getTemplateStateArray();
		$this->assign('templatestate_arr', $templatestate_arr);
		if (chksubmit()) {
			$t_id = input('tid', 0, 'intval');
			$insert_arr['voucher_t_state'] = input('tstate', $templatestate_arr['usable'][0], 'intval');
			$rs = $model_voucher_template->edit(array('voucher_t_id' => $t_id), $insert_arr);
			if ($rs) {
				output_data(array('msg' => '操作成功', 'url' => _url('voucher/index')));
			} else {
				output_error('操作失败');
			}
		} else {
			$t_id = input('tid', 0, 'intval');
			if ($t_id <= 0) {
				web_error('参数错误', _url('voucher/index'));
			}
			//查询模板信息
			$where = array();
			$where['voucher_t_id'] = $t_id;
			$t_info = $model_voucher_template->getInfo($where);
			$this->assign('t_info', $t_info);
			//会员等级
			$member_grade = logic('yewu')->get_level_list();
			$this->assign('member_grade', $member_grade);
			$this->display();
		}
	}
	public function configOp() {
		if (chksubmit()) {
			$data = array(
				'promotion_voucher_buyertimes_limit' => input('promotion_voucher_buyertimes_limit', 0, 'intval'),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('voucher/config')));
		}		
		$this->display();
	}
	public function pricelistOp() {
		//获得代金券金额列表
        $model = model('voucher_price');
		$result = $model->getList(array(), '*', 'voucher_price asc', 10, input('get.page', 1, 'intval'));
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval')), _url('voucher/pricelist')));
		$this->assign('list', $result['list']);
        $this->display();
	}
	public function priceaddOp() {
		if (chksubmit()) {
			$voucher_price_describe = input('voucher_price_describe', '');
			$voucher_price = input('voucher_price', '');
			$voucher_points = input('voucher_points', '');
            $obj_validate = new lib\validate();
            $validate_arr[] = array('input' => $voucher_price, 'require' => 'true', 'validator' => 'IntegerPositive', 'message' => '面额不能为空');
            $validate_arr[] = array('input' => $voucher_price_describe, 'require' => 'true', 'message' => '描述不能为空');
            $validate_arr[] = array('input' => $voucher_points, 'require' => 'true', 'validator' => 'IntegerPositive', 'message' => '积分不能为空');
            $obj_validate->validateparam = $validate_arr;
            $error = $obj_validate->validate();
            //验证面额是否存在
            $voucherprice_info = model('voucher_price')->where(array('voucher_price' => $voucher_price))->find();
            if (!empty($voucherprice_info)) {
                $error .= '面额不能重复';
            }
            if ($error != '') {
                output_error($error);
            } else {
                //保存
                $insert_arr = array(
					'voucher_price_describe' => trim($voucher_price_describe), 
					'voucher_price' => $voucher_price, 
					'voucher_defaultpoints' => $voucher_points
				);
                $rs = model('voucher_price')->add($insert_arr);
                if ($rs) {
					output_data(array('msg' => '操作成功', 'url' => users_url('voucher/pricelist')));
                } else {
                    output_error('操作失败！');
                }
            }
        } else {
            $this->display();
        }
	}
	public function priceeditOp() {
		if (chksubmit()) {
			$id = input('id', 0, 'intval');
			$voucher_price_describe = input('voucher_price_describe', '');
			$voucher_price = input('voucher_price', '');
			$voucher_points = input('voucher_points', '');
            $obj_validate = new lib\validate();
            $validate_arr[] = array('input' => $voucher_price, 'require' => 'true', 'validator' => 'IntegerPositive', 'message' => '面额不能为空');
            $validate_arr[] = array('input' => $voucher_price_describe, 'require' => 'true', 'message' => '描述不能为空');
            $validate_arr[] = array('input' => $voucher_points, 'require' => 'true', 'validator' => 'IntegerPositive', 'message' => '积分不能为空');
            $obj_validate->validateparam = $validate_arr;
            $error = $obj_validate->validate();
            //验证面额是否存在
            $voucherprice_info = model('voucher_price')->where(array('voucher_price' => $voucher_price))->find();
            if (!empty($voucherprice_info)) {
                $error .= '面额不能重复';
            }
            if ($error != '') {
                output_error($error);
            } else {
                //保存
                $insert_arr = array(
					'voucher_price_describe' => trim($voucher_price_describe), 
					'voucher_price' => $voucher_price, 
					'voucher_defaultpoints' => $voucher_points
				);
                $rs = model('voucher_price')->edit(array('voucher_price_id' => $id), $insert_arr);
                if ($rs) {
					output_data(array('msg' => '操作成功', 'url' => users_url('voucher/pricelist')));
                } else {
                    output_error('操作失败！');
                }
            }
        } else {
			$id = input('id', 0, 'intval');
			$info = model('voucher_price')->where(array('voucher_price_id' => $id))->find();
			$this->assign('info', $info);
            $this->display();
        }
	}
	/*
     * 删除代金券面额
     */
    public function pricedelOp() {
		$model = model('voucher_price');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['voucher_price_id'] = $id_array;
		$state = $model->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('voucher/pricelist')));
        } else {
			output_error('删除失败！');
        }
    }
}