<?php
namespace shop\controller;
use lib;
class member_voucher extends member {
	public function __construct() {
		parent::_initialize();
	}
	/**
     * 代金券列表
     */
    public function indexOp() {
        if ($this->member_info['uid']) {
            $model_voucher = model('voucher');
			$model_voucher->checkVoucherExpire($this->member_info['uid']);
			$where['voucher_owner_id'] = $this->member_info['uid'];
			$voucher_state = input('voucher_state', 0, 'intval');
			$voucher_state_array = $model_voucher->getVoucherStateArray();
			if (intval($voucher_state) > 0 && array_key_exists($voucher_state, $voucher_state_array)) {
				$where['voucher_state'] = $voucher_state;
			}
			$result = $model_voucher->getList($where, '*', 'voucher_id desc', 20, input('page', 1, 'intval'));
			$store_ids = array();
			foreach ($result['list'] as $k => $v) {
				$store_ids[] = $v['voucher_store_id'];
			}
			if ($store_ids) {
				$tmp = model('seller')->getList(array('id' => $store_ids), 'id,name,logo');
				$store_list = array();
				foreach($tmp['list'] as $k => $v) {
					$store_list[$v['id']] = $v;
				}
			}
			$list = array();
			foreach ($result['list'] as $k => $v) {
				//代金券状态文字
                $v['voucher_state_text'] = $voucher_state_array[$v['voucher_state']];
				$v['voucher_end_date_text'] = date('Y-m-d', $v['voucher_end_date']);
				$v['store_id'] = $v['voucher_store_id'];
				$v['store_name'] = isset($store_list[$v['voucher_store_id']]['name']) ? $store_list[$v['voucher_store_id']]['name'] : '';
                $v['store_avatar_url'] = !empty($v['voucher_t_customimg']) ? $v['voucher_t_customimg'] : (isset($store_list[$v['voucher_store_id']]['logo']) ? $store_list[$v['voucher_store_id']]['logo'] : '');
				$list[] = $v;
			}
			$return = array(
				'title' => '代金券',
				'list' => $list,
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
			);
			output_data($return);
        } else {
            output_error('请登录！');
        }
    }
	/**
     * 领取免费/兑换代金券
     */
    public function voucher_freeexOp() {
        $t_id = input('tid', 0, 'intval');
		$store_id = input('store_id', 0, 'intval');
        if ($t_id <= 0) {
            output_error('代金券信息错误');
        }
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        //验证是否可领取代金券
        $data = $model_voucher_template->getCanChangeTemplateInfo($t_id, intval($this->member_info['uid']), 0);
        if ($data['state'] == false) {
            output_error($data['msg']);
        }
        try {
			$model = model();
            $model->beginTransaction();
            //添加代金券信息
            $data = $model_voucher->exchangeVoucher($data['info'], $this->member_info['uid'], $this->member_info['nickname']);
            if ($data['state'] == false) {
                throw new \Exception($data['msg']);
            }
            $model->commit();
            output_data('代金券领取成功');
        } catch (\Exception $e) {
            $model->rollback();
            output_error($e->getMessage());
        }
    }
	/**
     * 领取密码代金券
     */
    public function voucher_pwexOp() {
        if ($this->member_info['uid']) {
            if (!$this->check()) {
                output_error('验证码错误！');
            }
			$pwd_code = input('pwd_code', '');
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => $pwd_code, 'require' => 'true', 'message' => '请输入代金券卡密'));
            $error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$model_voucher = model('voucher');
			$model_voucher_template = model('voucher_template');
            // 查询代金券
            $where = array();
            $where['voucher_pwd'] = md5($pwd_code);
            $voucher_info = $model_voucher->getInfo($where);
            if (!$voucher_info) {
                output_error('代金券卡密错误');
            }
            if ($voucher_info['voucher_owner_id'] > 0) {
                output_error('该代金券卡密已被使用，不可重复领取');
            }
            $where = array();
            $where['voucher_id'] = $voucher_info['voucher_id'];
            $update_arr = array();
            $update_arr['voucher_owner_id'] = $this->member_info['uid'];
            $update_arr['voucher_owner_name'] = $this->member_info['nickname'];
            $update_arr['voucher_active_date'] = time();
            $result = $model_voucher->edit($where, $update_arr);
            if ($result) {
                // 更新代金券模板
                $model_voucher_template->edit(array('voucher_t_id' => $voucher_info['voucher_t_id']), 'voucher_t_giveout=voucher_t_giveout+1');
                output_data('代金券领取成功');
            } else {
                output_error('代金券领取失败');
            }
        } else {
            output_error('请登录！');
        }
    }
	protected function check() {
		$myhash = input('myhash', '');
		$captcha = input('captcha', '');
		$codekey = input('codekey', '');
        if (checkSeccode($myhash, $captcha, $codekey)) {
            return true;
        } else {
            return false;
        }
    }
}