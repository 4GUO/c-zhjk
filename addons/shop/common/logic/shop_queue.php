<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_queue
{
	/**
     * 删除购物车
     * @param unknown $cart
     */
    public function delCart($cart) {
        if (!is_array($cart['cart_ids']) || empty($cart['uid'])) {
            return callback(true);
        }
        $del = model('shop_cart')->del(array('uid' => $cart['uid'], 'goods_id' => $cart['cart_ids']));
        if (!$del) {
            return callback(false, '删除购物车数据失败');
        } else {
            return callback(true);
        }
    }
	/**
     * 虚拟订单发送兑换码
     * @param unknown $param
     * @return boolean
     */
    public function sendVrCode($param) {
        if (empty($param) && !is_array($param)) {
            return callback(true);
        }
        $condition = array();
        $condition['order_id'] = $param['order_id'];
        $condition['buyer_id'] = $param['buyer_id'];
        $condition['vr_state'] = 0;
        $condition['refund_lock'] = 0;
        $code_list = model('shop_vr_order')->getOrderCodeList($condition, 'vr_code,vr_indate,vr_state,refund_lock,vr_usetime');
        if (empty($code_list)) {
            return callback(true);
        }
        $content = '';
        foreach ($code_list as $v) {
            $content .= $v['vr_code'] . ',';
        }
        $message = '【' . $param['name'] . '】您的虚拟兑换码是：' . rtrim($content, ',') . '。';
        $sms = new lib\sms();
        $result = $sms->send($param['buyer_phone'], $message);
        if (!$result) {
            return callback(false, '兑换码发送失败order_id:' . $param['order_id']);
        } else {
            return callback(true);
        }
    }
	/**
     * 生成卡密代金券
     */
    public function build_pwdvoucher($t_id) {
        $t_id = intval($t_id);
        if ($t_id <= 0) {
            return callback(false, '参数错误');
        }
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        //查询代金券详情
        $where = array();
        $where['voucher_t_id'] = $t_id;
        $gettype_arr = $model_voucher->getVoucherGettypeArray();
        $where['voucher_t_gettype'] = $gettype_arr['pwd']['sign'];
        $where['voucher_t_isbuild'] = 0;
        $where['voucher_t_state'] = 1;
        $t_info = $model_voucher_template->getInfo($where);
        $t_total = intval($t_info['voucher_t_total']);
        if ($t_total <= 0) {
            return callback(false, '代金券模板信息错误');
        }
        while ($t_total > 0) {
            $is_succ = false;
            $insert_arr = array();
            $step = $t_total > 1000 ? 1000 : $t_total;
            for ($t = 0; $t < $step; $t++) {
                $voucher_code = $model_voucher_template->get_voucher_code(0);
                if (!$voucher_code) {
                    continue;
                }
                $voucher_pwd_arr = $model_voucher_template->create_voucher_pwd($t_info['voucher_t_id']);
                if (!$voucher_pwd_arr) {
                    continue;
                }
				list($mdf_pwd, $encrypt_pwd) = $voucher_pwd_arr;
                $tmp = array();
                $tmp['voucher_code'] = $voucher_code;
                $tmp['voucher_t_id'] = $t_info['voucher_t_id'];
                $tmp['voucher_title'] = $t_info['voucher_t_title'];
                $tmp['voucher_desc'] = $t_info['voucher_t_desc'];
                $tmp['voucher_start_date'] = $t_info['voucher_t_start_date'];
                $tmp['voucher_end_date'] = $t_info['voucher_t_end_date'];
                $tmp['voucher_price'] = $t_info['voucher_t_price'];
                $tmp['voucher_limit'] = $t_info['voucher_t_limit'];
                $tmp['voucher_store_id'] = $t_info['voucher_t_store_id'];
                $tmp['voucher_state'] = 1;
                $tmp['voucher_active_date'] = time();
                $tmp['voucher_owner_id'] = 0;
                $tmp['voucher_owner_name'] = '';
                $tmp['voucher_order_id'] = 0;
				//md5
                $tmp['voucher_pwd'] = $mdf_pwd;
                //encrypt
                $tmp['voucher_pwd2'] = $encrypt_pwd;
                $insert_arr[] = $tmp;
                $t_total--;
            }
            $result = $model_voucher->insertAll($insert_arr);
            if ($result && $is_succ == false) {
                $is_succ = true;
            }
        }
        //更新代金券模板
        if ($is_succ) {
            $model_voucher_template->edit(array('voucher_t_id' => $t_info['voucher_t_id']), array('voucher_t_isbuild' => 1));
            return callback(true);
        } else {
            return callback(false);
        }
    }
	/**
     * 更新使用的代金券状态
     * @param $input_voucher_list
     * @throws Exception
     */
    public function editVoucherState($voucher_arr, $uid) {
		$model_voucher = model('voucher');
		$voucher_ids = array_values($voucher_arr);
		if (empty($voucher_ids)) {
			return callback(true); 
		}
		$voucher_list = $model_voucher->where(array('voucher_id' => $voucher_ids, 'voucher_owner_id' => $uid))->select();
		foreach ($voucher_list as $voucher_info) {
			$update = $model_voucher->edit(array('voucher_id' => $voucher_info['voucher_id'], 'voucher_owner_id' => $voucher_info['voucher_owner_id']), array('voucher_state' => 2));
			if (!$update) {
				return callback(false, '更新代金券状态失败vcode:' . $voucher_info['voucher_code']);
			}
		}
        return callback(true);
    }
	/**
     * 删除提货购物车
     * @param unknown $cart
     */
    public function delTihuoCart($cart) {
        if (!is_array($cart['cart_ids']) || empty($cart['uid'])) {
            return callback(true);
        }
        $del = model('shop_tihuoquan_cart')->del(array('uid' => $cart['uid'], 'goods_id' => $cart['cart_ids']));
        if (!$del) {
            return callback(false, '删除购物车数据失败');
        } else {
            return callback(true);
        }
    }
    //合成分红券
    public function tihuoquan_to_fenhongquan($param) {
		$lianchuang_level = model('vip_level')->field('id')->where(array('level_default' => 0))->order('level_sort DESC')->find();
		$dis_account = model('distribute_account')->getInfo(array('uid' => $param['uid']), 'dis_path');
		$parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
		$parent = array_reverse($parent);
		if (!$parent) {
		    return true;
		}
		//获取分销商的级别
		$distributor_levels = array();
		$result = model('distribute_account')->getList(array('uid' => $parent), 'uid,level_id');
		foreach ($result['list'] as $rr) {
			$distributor_levels[$rr['uid']] = $rr['level_id'];
		}
		unset($result);
		foreach ($parent as $uid) {
			if ($distributor_levels[$uid] == $lianchuang_level['id']) {
				$invite_list = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('inviter_id' => $uid))->select();
				if (count($invite_list) < 3) {
				    continue;
				}
				//需要消减提货券数量的人
				$up_accounts = array();
				foreach ($invite_list as $v) {
					if (count($up_accounts) >= 3) {
						break;
					}
					if ($v['can_tihuoquan_num'] > 0) {
						//算上直推的人
						$up_accounts[] = $v;
					}
				}
				if (count($up_accounts) >= 3) {
					$lc_uid = $uid;
					break;
				}
			}
		}
		//var_dump($up_accounts);exit;
        //lib\logging::write(var_export($up_accounts, true));
		if (!empty($lc_uid) && !empty($up_accounts)) {
			foreach($up_accounts as $account) {
				model('member')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
				model('distribute_account')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
			}
			//lib\logging::write(var_export($lc_uid, true));
			model('member')->where(array('uid' => $lc_uid))->update('fenhong_quan=fenhong_quan+1,total_fenhong_quan=total_fenhong_quan+1');
			
		}
		return true;
	}
    //合成分红券
    public function tihuoquan_to_fenhongquan20240530($param) {
		$lianchuang_level = model('vip_level')->field('id')->where(array('level_default' => 0))->order('level_sort DESC')->find();
		$dis_account = model('distribute_account')->getInfo(array('uid' => $param['uid']), 'dis_path');
		$parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
		$parent = array_reverse($parent);
		if (!$parent) {
		    return true;
		}
		//获取分销商的级别
		$distributor_levels = array();
		$result = model('distribute_account')->getList(array('uid' => $parent), 'uid,level_id');
		foreach ($result['list'] as $rr) {
			$distributor_levels[$rr['uid']] = $rr['level_id'];
		}
		unset($result);
		foreach ($parent as $uid) {
			if ($distributor_levels[$uid] == $lianchuang_level['id']) {
				$invite_list = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('inviter_id' => $uid))->select();
				if (count($invite_list) < 3) {
				    continue;
				}
				//需要消减提货券数量的人
				$up_accounts = array();
				foreach ($invite_list as $v) {
					if (count($up_accounts) >= 3) {
						break;
					}
					if ($v['can_tihuoquan_num'] > 0) {
						//算上直推的人
						$up_accounts[] = $v;
					} else {
						//否则就是在团队里面找
                        $res = model('distribute_account')->field('uid')->where(array('can_tihuoquan_num >' => 0, 'dis_path' =>  '%,' . $v['uid'] . ',%'))->find();
                        
                        if ($res) {
                            $up_accounts[] = $res;
                        }
					}
				}
				if (count($up_accounts) >= 3) {
					$lc_uid = $uid;
					break;
				}
			}
		}
		//var_dump($up_accounts);exit;
        //lib\logging::write(var_export($up_accounts, true));
		if (!empty($lc_uid) && !empty($up_accounts)) {
			foreach($up_accounts as $account) {
				model('member')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
				model('distribute_account')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
			}
			//lib\logging::write(var_export($lc_uid, true));
			model('member')->where(array('uid' => $lc_uid))->update('fenhong_quan=fenhong_quan+1,total_fenhong_quan=total_fenhong_quan+1');
		}
		return true;
	}
    //合成分红券
	public function tihuoquan_to_fenhongquan_20240429($param) {
		$lianchuang_level = model('vip_level')->field('id')->where(array('level_default' => 0))->order('level_sort DESC')->find();
		$dis_account = model('distribute_account')->getInfo(array('uid' => $param['uid']), 'dis_path');
		$parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
		//包括自己
		$parent[] = $param['uid'];
		$parent = array_reverse($parent);
		if (!$parent) {
		    return true;
		}
		//获取分销商的级别
		$distributor_levels = array();
		$result = model('distribute_account')->getList(array('uid' => $parent));
		foreach ($result['list'] as $rr) {
			$distributor_levels[$rr['uid']] = $rr;
		}
		unset($result);
		$lc_uids = [];
		foreach ($parent as $uid) {
			if ($distributor_levels[$uid]['level_id'] == $lianchuang_level['id']) {
			    if ($distributor_levels[$uid]['can_tihuoquan_num'] >= 3) {
			        $lc_uids[] = $uid;
			    }
			}
		}
		if (!empty($lc_uids)) {
			model('member')->where(array('uid' => $lc_uids, 'can_tihuoquan_num >=' => 3))->update('can_tihuoquan_num=can_tihuoquan_num-3');
			model('distribute_account')->where(array('uid' => $lc_uids, 'can_tihuoquan_num >=' => 3))->update('can_tihuoquan_num=can_tihuoquan_num-3');
			//lib\logging::write(var_export($lc_uids, true));
			model('member')->where(array('uid' => $lc_uids))->update('fenhong_quan=fenhong_quan+1,total_fenhong_quan=total_fenhong_quan+1');
		}
		return true;
	}
	//合成分红券(旧版)
	public function tihuoquan_to_fenhongquan_old($param) {
		$lianchuang_level = model('vip_level')->field('id')->where(array('level_default' => 0))->order('level_sort DESC')->find();
		$dis_account = model('distribute_account')->getInfo(array('uid' => $param['uid']), 'dis_path');
		$parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
		$parent = array_reverse($parent);
		if (!$parent) {
		    return true;
		}
		//获取分销商的级别
		$distributor_levels = array();
		$result = model('distribute_account')->getList(array('uid' => $parent), 'uid,level_id');
		foreach ($result['list'] as $rr) {
			$distributor_levels[$rr['uid']] = $rr['level_id'];
		}
		unset($result);
		foreach ($parent as $uid) {
			if ($distributor_levels[$uid] == $lianchuang_level['id']) {
				$invite_list = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('inviter_id' => $uid))->select();
				if (count($invite_list) < 3) {
				    continue;
				}
				//需要消减提货券数量的人
				$up_accounts = array();
				foreach ($invite_list as $v) {
					if (count($up_accounts) >= 3) {
						break;
					}
					if ($v['can_tihuoquan_num'] > 0) {
						//算上直推的人
						$up_accounts[] = $v;
					} else {
						//否则就是在团队里面找
                        $res = model('distribute_account')->field('uid')->where(array('can_tihuoquan_num >' => 0, 'dis_path' =>  '%,' . $v['uid'] . ',%'))->find();
                        
                        if ($res) {
                            $up_accounts[] = $res;
                        }
					}
				}
				if (count($up_accounts) >= 3) {
					$lc_uid = $uid;
					break;
				}
			}
		}
		//var_dump($up_accounts);exit;
        //lib\logging::write(var_export($up_accounts, true));
		if (!empty($lc_uid) && !empty($up_accounts)) {
			foreach($up_accounts as $account) {
				model('member')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
				model('distribute_account')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
			}
			//lib\logging::write(var_export($lc_uid, true));
			$flag = model('member')->where(array('uid' => $lc_uid))->update('fenhong_quan=fenhong_quan+1,total_fenhong_quan=total_fenhong_quan+1');
			if ($flag) {
			    //向上面所有的联创送激活券
			    $dis_a = model('distribute_account')->getInfo(array('uid' => $lc_uid), 'dis_path');
		        $parent_path = $dis_a['dis_path'] ? explode(',', trim($dis_a['dis_path'], ',')) : array();
		        $parent_path = array_reverse($parent_path);
		        foreach ($parent_path as $v) {
		            if ($distributor_levels[$v] == $lianchuang_level['id']) {
		                continue;
		            }
		            model('member')->where(array('uid' => $v))->update('can_tihuoquan_num=can_tihuoquan_num+3');
				    model('distribute_account')->where(array('uid' => $v))->update('can_tihuoquan_num=can_tihuoquan_num+3');
				    $this->tihuoquan_to_fenhongquan_old(array('uid' => $v));
		        }
			}
		}
		return true;
	}
}