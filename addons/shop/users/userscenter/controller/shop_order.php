<?php
namespace userscenter\controller;
use lib;
class shop_order extends control {
	public function __construct() {
		parent::_initialize();
	}

	public function indexOp(){
	    $is_export = input('is_export', 0, 'intval');
	    $export_data = [];
		$model_order = model('shop_order');
		$where = array();
		$where['tihuoquan_id'] = 0;
		$where['is_spike'] = 0;
		$where['is_points'] = 0;
		$where['is_del'] = 0;
		$buyer_name = input('buyer_name', '');
		if($buyer_name){
			$where['member_name'] = '%' . $buyer_name . '%';
		}
		$order_sn = input('order_sn', '');
		if($order_sn){
			$where['order_sn'] = $order_sn;
		}
		$allow_state_array = array('state_new', 'state_pay', 'state_send', 'state_success', 'state_cancel');
		$state_type = input('state_type', '');
        if (in_array($state_type, $allow_state_array)) {
            $where['order_state'] = str_replace($allow_state_array, array(ORDER_STATE_NEW, ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS, ORDER_STATE_CANCEL), $state_type);
        }
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date) : null;
        $end_unixtime = $if_end_date ? strtotime($query_end_date) : null;
        if ($start_unixtime || $end_unixtime) {
            $where['add_time >='] = $start_unixtime;
			$where['add_time <='] = $end_unixtime;
        }
		$where['lock_state'] = 0;

		$export_page = input('get.export_page', 1, 'intval');
		$export_page_num = 100;
		if ($is_export) {
		    $list = $model_order->getList($where, '*', 'order_id desc', $export_page_num, $export_page, array('order_common', 'order_goods', 'member'));
		} else {
		    $list = $model_order->getList($where, '*', 'order_id desc', 20, input('page', 1, 'intval'), array('order_common', 'order_goods', 'member'));
		}
		$export_total = $model_order->where($where)->total();
		$export_totalpage = ceil($export_total / $export_page_num);//总计页数
		$this->assign('totalpage', $export_totalpage);

		$order_list = array();
		$shipping_express_ids = array();
		if(!empty($list['list'])){
			foreach ($list['list'] as $value) {
			    if (!empty($value['extend_order_common']['shipping_express_id'])) {
					$shipping_express_ids[] = $value['extend_order_common']['shipping_express_id'];
				}
				//显示取消订单
				$value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $value);
				//显示发货
				$value['if_send'] = $model_order->getOrderOperateState('send', $value);
				//显示调整费用
				$value['if_modify_price'] = $model_order->getOrderOperateState('modify_price', $value);
				//显示调整订单费用
				$value['if_spay_price'] = $model_order->getOrderOperateState('spay_price', $value);
				//显示锁定中
				$value['if_lock'] = $model_order->getOrderOperateState('lock', $value);
				//显示物流跟踪
				$value['if_deliver'] = $model_order->getOrderOperateState('deliver', $value);
				$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
				$value['goods_count'] = count($value['extend_order_goods']);
				$value['payment_name'] = orderPaymentName($value['payment_code']);
				$order_list[] = $value;
			}
		}
		$this->assign('list', $order_list);
		$export_data['list'] = $order_list;
		$this->assign('page', page(isset($list['totalpage']) ? $list['totalpage'] : 0, array('page' => input('get.page', 1, 'intval'), 'state_type' => $state_type, 'buyer_name' => $buyer_name, 'order_sn' => $order_sn, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('shop_order/index')));
		//物流
		$express_list = array();
		if ($shipping_express_ids) {
			$result = model('express')->getList(array('id' => $shipping_express_ids));
			foreach ($result['list'] as $k => $v) {
				$express_list[$v['id']] = $v;
			}
			unset($result);
		}
		$this->assign('express_list', $express_list);
		$export_data['express_list'] = $express_list;
		if ($is_export) {
			$this->new_createExcel($export_data, $export_page);
		} else {
			$this->display();
		}
	}
	/**
     * 生成excel
     *
     * @param array $data
     */
    private function new_createExcel($data = array(), $export_page = 1) {
		require_once COMMON_PATH . '/vendor/phpoffice/vendor/autoload.php';
        $fileName = '零售订单【' . $export_page . '页】';
        $fileType = 'xlsx';
        $arrHeader = array(
			'订单编号',
			'会员ID',
			'订单状态',
			'收货人',
			'手机号码',
			'省',
			'城市',
			'区域',
			'省市区',
			'地址',
			'快递名称',
			'发货单号',
			'支付方式',
			'订单金额',
			'商品',
			'下单时间'
		);
        $count = count($arrHeader);  //计算表头数量
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($i = 65; $i < $count + 65; $i++) {//数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $arrHeader[$i - 65]);
        }
        //填充数据
		//物流
		$express_name_arr = array();
		$result = model('express')->getList(array());
		foreach ($result['list'] as $k => $v) {
			$express_name_arr[] = $v['e_name'];
		}
		unset($result);
		$express_name_list = implode(',', $express_name_arr);
		foreach ((array) $data['list'] as $k => $v) {
			$k += 2;
			$sheet->setCellValueExplicit('A' . $k, $v['order_sn'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('B' . $k, $v['uid']);
            $sheet->setCellValue('C' . $k, $v['state_desc']);
            $sheet->setCellValue('D' . $k, $v['extend_order_common']['reciver_name']);
            $sheet->setCellValue('E' . $k, $v['extend_order_common']['reciver_info']['tel_phone']);
			$area = $v['extend_order_common']['reciver_info']['area'];
			$area_arr = explode(' ', $area);
			$sheet->setCellValue('F' . $k, isset($area_arr[0]) ? $area_arr[0] : '');
            $sheet->setCellValue('G' . $k, isset($area_arr[1]) ? $area_arr[1] : '');
			$sheet->setCellValue('H' . $k, isset($area_arr[2]) ? $area_arr[2] : '');
            $sheet->setCellValue('I' . $k, $area);
			$sheet->setCellValue('J' . $k, $v['extend_order_common']['reciver_info']['street']);
            $express = isset($data['express_list'][$v['extend_order_common']['shipping_express_id']]) ? $data['express_list'][$v['extend_order_common']['shipping_express_id']]['e_name'] : '';

			$objValidation1 = $sheet->getCell('K' . $k)->getDataValidation();
			$objValidation1->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
				->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
				->setAllowBlank(false)
				->setShowInputMessage(true)
				->setShowErrorMessage(true)
				->setShowDropDown(true)
				->setErrorTitle('输入的值有误')
				->setError('您输入的值不在下拉框列表内.')
				->setPromptTitle('')
				->setPrompt('')
				->setFormula1('"' . $express_name_list . '"'); // 设置为变量内容
			$sheet->setCellValue('K' . $k, $express);
			$sheet->setCellValueExplicit('L' . $k, $v['shipping_code'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('M' . $k, $v['payment_name']);
			$sheet->setCellValue('N' . $k, priceFormat($v['order_amount']));
            $goods_names = '';
			foreach($v['extend_order_goods'] as $kk => $vv) {
				$goods_names .= '【' . $vv['goods_name'] . '数量：' . $vv['goods_num'] . '，单价' . $vv['goods_price'] . '元' . '】' . PHP_EOL;
			}
			$sheet->setCellValue('O' . $k, $goods_names);
			$sheet->setCellValue('P' . $k, $v['add_time']);
        }
        //utf-8转unicode格式
        //$fileName = iconv('UTF-8', 'UCS-2BE', $fileName);
        $len = strlen($fileName);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $fileName[$i];
            $c2 = $fileName[$i + 1];
            if (ord($c) > 0) {
                $str .= '\u' . base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= '\u' . str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header('Data-Type: binary');
        //前端导出数据根据这个unicode格式解析为中文
        header('Data-Filename: ' . $str);
        header('Content-Disposition: attachment;filename=' . $fileName . '.' . $fileType);
        header('Cache-Control: max-age=0');
        header('Access-Control-Expose-Headers:Data-Type,Data-Filename');
        ob_clean();
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
	}
	/**
     * 订单详情
     *
     */
    public function show_orderOp(){
		$model_order = model('shop_order');
        $order_id = input('order_id', 0, 'intval');
		if ($order_id <= 0) {
			$order_sn = input('order_sn', '', 'trim');
			if ($order_sn) {
				$order_info = $model_order->getInfo(array('order_sn' => $order_sn));
				$order_id = $order_info['order_id'];
			} else {
				web_error('参数有误！', users_url('shop_order/index'));
			}
        }
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！', users_url('shop_order/index'));
        }
        $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        //显示锁定中
        $order_info['if_lock'] = $model_order->getOrderOperateState('lock', $order_info);
        //显示调整运费
        $order_info['if_modify_price'] = $model_order->getOrderOperateState('modify_price', $order_info);
        //显示调整价格
        $order_info['if_spay_price'] = $model_order->getOrderOperateState('spay_price', $order_info);
        //显示取消订单
        $order_info['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        //显示发货
        $order_info['if_send'] = $model_order->getOrderOperateState('send', $order_info);
        //显示物流跟踪
        $order_info['if_deliver'] = $model_order->getOrderOperateState('deliver', $order_info);
        //显示系统自动取消订单日期

		$refund_state = '';
		if($order_info['if_lock']){
			$model_return = model('shop_refund_return');
			$refund_info = $model_return->getInfo(array('order_id' => $order_id));
			if(!empty($refund_info['order_id'])){
				$refund_state = $model_return->_orderState($refund_info);
			}
		}
		$this->assign('refund_state', $refund_state);

        if ($order_info['order_state'] == ORDER_STATE_NEW) {
            $order_info['order_cancel_day'] = $order_info['add_time'] + ORDER_AUTO_CANCEL_DAY * 24 * 3600;
        }

        //显示快递信息
        if ($order_info['shipping_code'] != '') {
			if(!empty($order_info['extend_order_common']['shipping_express_id'])){
				$express = model('express')->getInfo(array('id' => $order_info['extend_order_common']['shipping_express_id']));
				$order_info['express_info']['e_code'] = $express['e_code'];
				$order_info['express_info']['e_name'] = $express['e_name'];
				$order_info['express_info']['e_url'] = $express['e_url'];
			} else {
				$order_info['express_info']['e_code'] = '';
				$order_info['express_info']['e_name'] = '不需要物流';
				$order_info['express_info']['e_url'] = '';
			}
        }
        //显示系统自动收获时间
        if ($order_info['order_state'] == ORDER_STATE_SEND) {
            $order_info['order_confirm_day'] = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 24 * 3600;
        }
        //如果订单已取消，取得取消原因、时间，操作人
        if ($order_info['order_state'] == ORDER_STATE_CANCEL) {
            $order_info['close_info'] = $model_order->getOrderLogInfo(array('order_id' => $order_info['order_id']), 'log_id desc');
        }
        if (empty($order_info['zengpin_list'])) {
            $order_info['goods_count'] = count($order_info['extend_order_goods']);
        } else {
            $order_info['goods_count'] = count($order_info['extend_order_goods']) + 1;
        }
        $this->assign('order_info', $order_info);

        //发货信息
        if (!empty($order_info['extend_order_common']['daddress_id'])) {
            $daddress_info = model('shop_daddress')->getInfo(array('address_id' => $order_info['extend_order_common']['daddress_id']));
            $this->assign('daddress_info', $daddress_info);
        }
        $this->display();
    }

	/**
     * 取消订单
     *
     */
    public function order_cancelOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('buyer_cancel', $order_info);
        if (!$if_allow) {
            output_error('无权操作！');
        }
		if (chksubmit()) {
			$msg = input('state_info1', '') != '' ? input('state_info1', '') : input('state_info', '');
            $result = $model_order->changeOrderStateCancel($order_info, 'admin', input('session.username', ''), $msg);
			output_data(array('msg' => '取消成功', 'url' => users_url('shop_order/index')));
		} else {
			$this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}

    }

    /**
     * 修改运费
     * @param unknown $order_info
     */
    public function shipping_priceOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('modify_price', $order_info);
        if (!$if_allow) {
            return callback(false, '无权操作');
        }
        if (chksubmit()) {
			$shipping_fee = input('shipping_fee', '') == '' ? 0 : priceFormat(input('shipping_fee', ''));
            $result = $model_order->changeOrderShipPrice($order_info, 'admin', input('session.username', ''), $shipping_fee);
			output_data(array('msg' => '修改成功', 'url' => users_url('shop_order/index')));
        } else {
            $this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }

	/**
     * 修改商品价格
     * @param unknown $order_info
     */
    public function order_priceOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		$if_allow = $model_order->getOrderOperateState('spay_price', $order_info);
        if (!$if_allow) {
            return callback(false, '无权操作');
        }
        if (chksubmit()) {
			$order_price = input('order_price', '') == '' ? 0 : priceFormat(input('order_price', ''));
            $result = $model_order->changeOrderSpayPrice($order_info, 'admin', input('session.username', ''), $order_price);
			output_data(array('msg' => '修改成功', 'url' => users_url('shop_order/index')));
        } else {
            $this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }

	/**
     * 打印订单
     * @param unknown $order_info
     */
    public function print_orderOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }

		$goods_new_list = array();
        $goods_all_num = 0;
        $goods_total_price = 0;

        if (!empty($order_info['extend_order_goods'])) {
            $i = 1;
            foreach ($order_info['extend_order_goods'] as $k => $v) {
                $v['goods_name'] = str_cut($v['goods_name'], 100);
                $goods_all_num += $v['goods_num'];
                $v['goods_all_price'] = $v['goods_num'] * $v['goods_price'];
                $goods_total_price += $v['goods_all_price'];
                $goods_new_list[ceil($i / 15)][$i] = $v;
                $i++;
            }
        }

		$config = model('config')->getInfo();

        //优惠金额
        $promotion_amount = $goods_total_price - $order_info['goods_amount'];
		$this->assign('promotion_amount', $promotion_amount);
        $this->assign('goods_all_num', $goods_all_num);
        $this->assign('goods_total_price', $goods_total_price);
        $this->assign('goods_list', $goods_new_list);
        $this->assign('order_info', $order_info);
		$this->assign('config', $config);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
	/**
     * 系统收到货款
     * @throws Exception
     */
    public function receive_payOp() {
		$model_order = model('shop_order');
        $logic_order = logic('shop_buy');
		if (chksubmit()) {
			$order_id = input('order_id', 0, 'intval');
			$order_info = $model_order->getInfo(array('order_id' => $order_id));
			$if_allow = $model_order->getOrderOperateState('system_receive_pay', $order_info);
			if (!$if_allow) {
				output_error('无权操作');
			}
			$result = $model_order->getList(array('pay_sn' => $order_info['pay_sn'], 'order_state' => ORDER_STATE_NEW), '*', '', null, null, array('order_common','order_goods'));
			$order_list = $result['list'];
			$payment_code = input('payment_code', '');
			if (!$payment_code) {
				output_error('请选择付款方式');
			}
			$trade_no = input('trade_no', '');
			if (!$trade_no) {
				output_error('请输入第三方支付流水单号');
			}
			$paytime = input('paytime', '');
			if (!$paytime) {
				output_error('请选择付款时间');
			}
			$paytime = strtotime($paytime);
			$result = $logic_order->updateOrderAll($order_list, $payment_code, $trade_no, $paytime);
			if (!$result['state']) {
				output_error($result['msg']);
			}
			output_data(array('msg' => '操作成功', 'url' => users_url('shop_order/index')));
		} else {
			$order_id = input('order_id', 0, 'intval');
			$order_info = $model_order->getInfo(array('order_id' => $order_id));
			$result = $model_order->getList(array('pay_sn' => $order_info['pay_sn'], 'order_state' => ORDER_STATE_NEW));
			$total_pay = 0;
			foreach ($result['list'] as $v) {
				$total_pay += $v['order_amount'];
			}
			$order_info['total_pay'] = priceFormat($total_pay);
			$this->assign('order_info', $order_info);
			$payment_list = model('mb_payment')->where(array('payment_state' => 1))->select();
			if (!empty($payment_list)) {
				foreach ($payment_list as $k => $value) {
					$payment_list[$k]['payment_config'] = fxy_unserialize($value['payment_config']);
					unset($payment_list[$k]['payment_id']);
					unset($payment_list[$k]['payment_config']);
				}
			}
			$this->assign('payment_list', $payment_list);
			$this->display();
		}
    }
    
    /**
     * 发放复购见单奖励
     */
    public function grant_rewardOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
        // 记录奖励发放日志
        $result = logic('yewu')->deal_fugou_reward($order_info);
        if($result !== true){
            output_error($result);
        }
        output_data(array('msg' => '奖励发放成功'));
    }

    /**
     * 收回复购见单奖励
     */
    public function revoke_rewardOp(){
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $order_info = $model_order->getInfo($condition, array('member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
        
        // 记录奖励收回日志
        $result = logic('yewu')->deal_fugou_revoke($order_info);
        if($result !== true){
            output_error($result);
        }
        output_data(array('msg' => '奖励收回成功'));
    }
}
