<?php
namespace sellercenter\controller;
use lib;
class store_voucher extends control {
	const EXPORT_SIZE = 1000;
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		//检查过期的代金券模板状态设为失效
        $this->check_voucher_template_expire();
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        //领取方式
        $gettype_arr = $model_voucher->getVoucherGettypeArray();
		//状态
		$templatestate_arr = $model_voucher_template->getTemplateStateArray();
		//会员等级
		$member_grade = logic('yewu')->get_level_list();
        //查询列表
        $condition = array();
        //领取方式查询
        $gettype_sel = input('gettype_sel', '');
        if ($gettype_sel) {
            $condition['voucher_t_gettype'] = $gettype_arr[$gettype_sel]['sign'];
        }
        $condition['voucher_t_store_id'] = $this->store_id;
		$txt_keyword = input('txt_keyword', '');
        if ($txt_keyword) {
            $condition['voucher_t_title'] = '%' . trim($txt_keyword) . '%';
        }
        $select_state = input('select_state', 0, 'intval');
        if ($select_state) {
            $condition['voucher_t_state'] = $select_state;
        }
		$txt_startdate = input('txt_startdate', '');
        if ($txt_startdate) {
            $condition['voucher_t_end_date >='] = strtotime($txt_startdate);
        }
		$txt_enddate = input('txt_enddate', '');
        if ($txt_enddate) {
            $condition['voucher_t_start_date <='] = strtotime($txt_enddate);
        }
		$result = $model_voucher_template->getList($condition, '*', 'voucher_t_id desc', 10, input('get.page', 1, 'intval'));
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'gettype_sel' => $gettype_sel, 'txt_keyword' => $txt_keyword, 'select_state' => $select_state, 'txt_startdate' => $txt_startdate, 'txt_enddate' => $txt_enddate), _url('store_voucher/index')));
        $list = array();
		foreach ($result['list'] as $k => $v) {
			//领取方式
			if ($v['voucher_t_gettype']) {
				foreach ($gettype_arr as $gtype_k => $gtype_v) {
					if ($v['voucher_t_gettype'] == $gtype_v['sign']) {
						$v['voucher_t_gettype_key'] = $gtype_k;
						$v['voucher_t_gettype_text'] = $gtype_v['name'];
					}
				}
			}
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
		//领取方式
        $this->assign('gettype_arr', $gettype_arr);
		//状态
		$this->assign('templatestate_arr', $templatestate_arr);
        $this->assign('list', $list);
        $this->display();
	}
	/*
     * 把代金券模版设为失效
     */
    private function check_voucher_template_expire($voucher_template_id = '') {
        $where_array = array();
        if (empty($voucher_template_id)) {
            $where_array['voucher_t_end_date <'] = time();
        } else {
            $where_array['voucher_t_id'] = $voucher_template_id;
        }
		$templatestate_arr = model('voucher_template')->getTemplateStateArray();
        $where_array['voucher_t_state'] = $templatestate_arr['usable'][0];
        model('voucher_template')->where($where_array)->update(array('voucher_t_state' => $templatestate_arr['disabled'][0]));
    }
    public function publishOp() {
		$tid = input('tid', 0, 'intval');
		//查询面额列表
        $pricelist = model('voucher_price')->order('voucher_price asc')->select();
        if (empty($pricelist)) {
			web_error('平台未设置代金券可用面额！', _url('store_voucher/index'));
        }
		//模板状态
		$templatestate_arr = model('voucher_template')->getTemplateStateArray();
		//领取方式
        $gettype_array = model('voucher')->getVoucherGettypeArray();
		//会员等级
		$member_grade = logic('yewu')->get_level_list();
		if (chksubmit()) {
			$txt_template_title = input('txt_template_title', '');
			$txt_template_total = input('txt_template_total', 0, 'intval');
			$select_template_price = input('select_template_price', 0);
			$txt_template_describe = input('txt_template_describe', '');
			$gettype_sel = input('gettype_sel', '');
			//验证输入
			$obj_validate = new lib\validate();
            $validate_arr[] = array('input' => $txt_template_title, 'require' => 'true', 'validator' => 'Length', 'min' => '1', 'max' => '50', 'message' => '模版名称不能为空且不能大于50个字符');
            $validate_arr[] = array('input' => $txt_template_total, 'require' => 'true', 'validator' => 'Number', 'min' => '1', 'message' => '可发放数量不能为空且必须为整数');
            $validate_arr[] = array('input' => $select_template_price, 'require' => 'true', 'validator' => 'Number', 'message' => '模版面额不能为空且必须为整数，且面额不能大于限额');
            $validate_arr[] = array('input' => $txt_template_describe, 'require' => 'true', 'validator' => 'Length', 'min' => '1', 'max' => '255', 'message' => '模版描述不能为空且不能大于255个字符');
			$validate_arr[] = array('input' => $gettype_sel, 'require' => 'true', 'message' => '请选择领取方式');
			$obj_validate->validateparam = $validate_arr;
            $error = $obj_validate->validate();
			//金额验证
            $price = intval($select_template_price) > 0 ? intval($select_template_price) : 0;
            foreach ($pricelist as $k => $v) {
                if ($v['voucher_price'] == $price) {
                    $chooseprice = $v;
                    //取得当前选择的面额记录
                }
            }
            if (empty($chooseprice)) {
                $error .= '平台代金券面额设置出现问题，请联系客服帮助解决';
            }
			$txt_template_limit = input('txt_template_limit', 0, 'intval');
            $limit = floatval($txt_template_limit) > 0 ? floatval($txt_template_limit) : 0;
            if ($limit > 0 && $price >= $limit) {
                $error .= '模版面额不能为空且必须为整数，且面额不能大于限额';
            }
            //验证卡密代金券发放数量
            $gettype = trim($gettype_sel);
            if ($gettype == 'pwd') {
                if ($txt_template_total > 1000) {
                    $error .= '领取方式为卡密兑换的代金券，发放总数不能超过1000张';
                }
            }
            if ($error) {
                output_error($error);
            } else {
                $insert_arr = array();
                $insert_arr['voucher_t_title'] = trim($txt_template_title);
                $insert_arr['voucher_t_desc'] = trim($txt_template_describe);
                $insert_arr['voucher_t_start_date'] = time();
                //默认代金券模板的有效期为当前时间
				$txt_template_enddate = input('txt_template_enddate', '');
                if ($txt_template_enddate) {
                    $enddate = strtotime($txt_template_enddate);
                    $insert_arr['voucher_t_end_date'] = $enddate;
                } else {
                    //如果没有添加有效期则默认为套餐的结束时间
                    $insert_arr['voucher_t_end_date'] = time() + 2592000;
                }
                $insert_arr['voucher_t_price'] = $price;
                $insert_arr['voucher_t_limit'] = $limit;
                $insert_arr['voucher_t_store_id'] = $this->store_id;
                $insert_arr['voucher_t_storename'] = $this->store_info['name'];
                $insert_arr['voucher_t_sc_id'] = 0;
                $insert_arr['voucher_t_creator_id'] = $this->store_info['member_id'];
                $insert_arr['voucher_t_state'] = input('tstate', $templatestate_arr['usable'][0], 'intval');
                $insert_arr['voucher_t_total'] = intval($txt_template_total) > 0 ? intval($txt_template_total) : 0;
                $insert_arr['voucher_t_giveout'] = 0;
                $insert_arr['voucher_t_used'] = 0;
                $insert_arr['voucher_t_add_date'] = time();
                $insert_arr['voucher_t_points'] = $gettype == 'points' ? $chooseprice['voucher_defaultpoints'] : 0;
                $insert_arr['voucher_t_eachlimit'] = input('eachlimit', 0, 'intval');
                //自定义图片
                $insert_arr['voucher_t_customimg'] = input('voucher_t_customimg', '');
                //领取方式
                $insert_arr['voucher_t_gettype'] = in_array($gettype, array_keys($gettype_array)) ? $gettype_array[$gettype]['sign'] : $gettype_array[model('voucher')::VOUCHER_GETTYPE_DEFAULT]['sign'];
                $insert_arr['voucher_t_isbuild'] = 0;
                //会员级别
                $mgrade_limit = input('mgrade_limit', 0, 'intval');
                $insert_arr['voucher_t_mgradelimit'] = in_array($mgrade_limit, array_keys($member_grade)) ? $mgrade_limit : 0;
				if (!$tid) {
					
					$rs = model('voucher_template')->add($insert_arr);
					//生成卡密代金券
                    if ($gettype == 'pwd') {
                        logic('shop_queue')->build_pwdvoucher($rs);
                    }
				} else {
					$rs = model('voucher_template')->edit(array('voucher_t_id' => $tid, 'store_id' => $this->store_id), $insert_arr);
					//生成卡密代金券
                    if ($gettype == 'pwd') {
                        logic('shop_queue')->build_pwdvoucher($tid);
                    }
				}
                if ($rs) {
					output_data(array('msg' => '操作成功', 'url' => _url('store_voucher/index')));
				} else {
					output_error('操作失败');
				}
            }
		} else {
			if ($tid) {
				//查询模板信息
				$where = array();
				$where['voucher_t_id'] = $tid;
				$where['voucher_t_store_id'] = $this->store_id;
				$where['voucher_t_state'] = $templatestate_arr['usable'][0];
				$where['voucher_t_giveout <='] = 0;
				$where['voucher_t_end_date >'] = time();
				$t_info = model('voucher_template')->getInfo($where);
				$this->assign('t_info', $t_info);
			}
			$this->assign('gettype_arr', $gettype_array);
			$this->assign('templatestate_arr', $templatestate_arr);
            $this->assign('member_grade', $member_grade);
            $this->assign('pricelist', $pricelist);
			$this->display();
		}
    }
	public function infoOp() {
		$t_id = input('tid', 0, 'intval');
        if ($t_id <= 0) {
            web_error('参数错误', _url('store_voucher/index'));
        }
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        //查询模板信息
        $where = array();
        $where['voucher_t_id'] = $t_id;
        $where['voucher_t_store_id'] = $this->store_id;
        $t_info = $model_voucher_template->getInfo($where);
        $this->assign('t_info', $t_info);
		//会员等级
		$member_grade = logic('yewu')->get_level_list();
		$this->assign('member_grade', $member_grade);
        $this->display();
	}
	/*
     * 代金券模版列表
     */
    public function voucherlistOp() {
		if (chksubmit()) {
			$t_id = input('tid', 0, 'intval');
			$model_voucher = model('voucher');
			$model_voucher_template = model('voucher_template');
			//查询代金券模板
			$where = array();
			$where['voucher_t_id'] = $t_id;
			$where['voucher_t_store_id'] = $this->store_id;
			$t_info = $model_voucher_template->getInfo($where);
			$voucher_list = array();
			$where = array();
			$where['voucher_t_id'] = $t_id;
			$result = $model_voucher->getList($where, '*', 'voucher_owner_id asc,voucher_state asc,voucher_id asc', 20, input('page', 1, 'intval'));
			$totalpage = $result['totalpage'];
			if ($result['list']) {
				$voucherstate_arr = $model_voucher->getVoucherStateArray();
				$voucher_list = array();
				foreach ($result['list'] as $k => $v) {
					//卡密
					$v['voucher_pwd'] = $model_voucher_template->get_voucher_pwd($v['voucher_pwd2']);
					//代金券状态文字
					$v['voucher_state_text'] = $voucherstate_arr[$v['voucher_state']];
					//领取时间
					$v['voucher_active_date'] = $v['voucher_owner_id'] > 0 ? date('Y-m-d H:i:s', $v['voucher_active_date']) : '';
					$voucher_list[] = $v;
				}
			}
			output_data(array('list' => array_values($voucher_list), 'totalpage' => $totalpage, 'page_html' => page($totalpage, array('page' => input('get.page', 1, 'intval'), 'tid' => $t_id, 'form_submit' => 'ok'), _url('store_voucher/voucherlist'), true), 't_info' => $t_info));
		} else {
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
    }
	/**
     * 导出
     */
    public function voucher_exportOp() {
        $t_id = input('tid', 0, 'intval');
        if ($t_id <= 0) {
            output_error('参数错误');
        }
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        //查询代金券模板
        $where = array();
        $where['voucher_t_id'] = $t_id;
        $where['voucher_t_store_id'] = $this->store_id;
        $t_info = $model_voucher_template->getInfo($where);
        if (!$t_info) {
            output_error('参数错误');
        }
        $where = array();
        $where['voucher_t_id'] = $t_id;
		$curpage = input('curpage', null);
        if (!is_numeric($curpage)) {
            $count = $model_voucher->where($where)->total();
            $array = array();
            if ($count > self::EXPORT_SIZE) {
                //显示下载链接
                $page = ceil($count / self::EXPORT_SIZE);
                for ($i = 1; $i <= $page; $i++) {
                    $limit1 = ($i - 1) * self::EXPORT_SIZE + 1;
                    $limit2 = $i * self::EXPORT_SIZE > $count ? $count : $i * self::EXPORT_SIZE;
                    $array[$i] = $limit1 . ' ~ ' . $limit2;
                }
                $this->assign('list', $array);
                $this->assign('murl', _url('store_voucher/voucher_export'));
                $this->display();
            } else {
                //如果数量小，直接下载
				$result = $model_voucher->getList($where, '*', 'voucher_owner_id asc,voucher_state asc,voucher_id asc', self::EXPORT_SIZE, 1);
                $this->createExcel($result['list'], $t_info);
            }
        } else {
            $curpage = input('curpage', 1, 'intval');
			$result = $model_voucher->getList($where, '*', 'voucher_owner_id asc,voucher_state asc,voucher_id asc', self::EXPORT_SIZE, $curpage);
            $this->createExcel($result['list'], $t_info);
        }
    }
    /**
     * 生成excel
     *
     * @param array $data
     */
    private function createExcel($data = array(), $t_info = array()) {
        $excel_obj = new lib\excel();
        $excel_data = array();
        //设置样式
        $excel_obj->setStyle(array('id' => 's_title', 'Font' => array('FontName' => '宋体', 'Size' => '12', 'Bold' => '1')));
        //header
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '代金券编码');
        if ($t_info['voucher_t_gettype'] == 2) {
            $excel_data[0][] = array('styleid' => 's_title', 'data' => '卡密');
        }
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '代金券名称');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '有效期');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '面额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '订单限额');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '所属会员');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '领取时间');
        $excel_data[0][] = array('styleid' => 's_title', 'data' => '使用状态');
        //代金券模板名称
        $voucher_t_title = '';
        //data
        $model_voucher = model('voucher');
		$model_voucher_template = model('voucher_template');
        $voucherstate_arr = $model_voucher->getVoucherStateArray();
        foreach ((array) $data as $k => $info) {
            $voucher_t_title = $info['voucher_title'];
            $info['voucher_pwd'] = $model_voucher_template->get_voucher_pwd($info['voucher_pwd2']);
            $tmp = array();
            $tmp[] = array('data' => $info['voucher_code']);
            if ($t_info['voucher_t_gettype'] == 2) {
                $tmp[] = array('data' => $info['voucher_pwd']);
            }
            $tmp[] = array('data' => $info['voucher_title']);
            $info['expirydatetext'] = date('Y-m-d', $info['voucher_start_date']) . '~' . date('Y-m-d', $info['voucher_end_date']);
            $tmp[] = array('data' => $info['expirydatetext']);
            $tmp[] = array('data' => $info['voucher_price']);
            $tmp[] = array('data' => $info['voucher_limit']);
            $tmp[] = array('data' => $info['voucher_owner_name'] ? $info['voucher_owner_name'] : '');
            if ($info['voucher_owner_id'] > 0) {
                $info['voucher_active_date'] = date('Y-m-d H:i:s', $info['voucher_active_date']);
            } else {
                $info['voucher_active_date'] = '';
            }
            $tmp[] = array('data' => $info['voucher_active_date']);
            $info['voucher_state_text'] = $voucherstate_arr[$info['voucher_state']];
            $tmp[] = array('data' => $info['voucher_state_text']);
            $excel_data[] = $tmp;
        }
        $excel_data = $excel_obj->charset($excel_data, CHARSET);
        $excel_obj->addArray($excel_data);
        $excel_obj->addWorksheet($excel_obj->charset('代金券模板(' . $voucher_t_title . ')', CHARSET));
        $excel_obj->generateXML($excel_obj->charset('代金券模板(' . $voucher_t_title . ')的代金券及其卡密', CHARSET) . input('curpage', 1, 'intval'));
    }
	/**
     * 删除代金券
     */
    public function delOp() {
        $t_id = input('tid', 0, 'intval');
        //查询模板信息
        $where = array();
        $where['voucher_t_id'] = $t_id;
        $where['voucher_t_store_id'] = $this->store_id;
        $where['voucher_t_giveout <='] = 0;
        //会员没领取过代金券才可删除
        $t_info = model('voucher_template')->getInfo($where);
        if (empty($t_info)) {
            output_error('无权删除');
        }
        $rs = model('voucher_template')->del(array('voucher_t_id' => $t_info['voucher_t_id']));
		if ($rs) {
			output_data(array('msg' => '操作成功', 'url' => _url('store_voucher/index')));
		} else {
			output_error('操作失败！');
		}
    }
}