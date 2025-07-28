<?php
namespace userscenter\controller;
use lib;
class withdraw_record extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
	    $is_export = input('is_export', 0, 'intval');
	    $export_data = [];
		$where = array();
        $where['uniacid'] = $this->uniacid;
		$keyword = input('get.keyword', '');
		if ($keyword) {
			$search_uids = array();
			$search_uids[] = 0;
			$result = model('member')->getList(array('uniacid' => $this->uniacid, 'nickname' => '%' . trim($keyword) . '%'), 'uid');
			foreach($result['list'] as $r){
				$search_uids[] = $r['uid'];
			}
			$where['uid'] = $search_uids;
        }
		$status = input('get.status', 0, 'intval');
		if ($status) {
			$where['record_status'] = $status - 1;
        }
		$code = input('get.code', '');
		if ($code) {
			$where['method_code'] = $code;
        }
		$query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['record_addtime >='] = $start_unixtime;
        }
		if ($end_unixtime > 0) {
			$where['record_addtime <='] = $end_unixtime;
        }
        $export_page = input('get.export_page', 1, 'intval');
		$export_page_num = 100;
		if ($is_export) {
		    $list = model('withdraw_record')->getList($where, '*', 'record_id DESC', $export_page_num, $export_page);
		} else {
		    $list = model('withdraw_record')->getList($where, '*', 'record_id DESC', 10, input('page', 1, 'intval'));
		}
		$export_total = model('withdraw_record')->where($where)->total();
		$export_totalpage = ceil($export_total / $export_page_num);//总计页数
		$this->assign('totalpage', $export_totalpage);
		
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval'), 'keyword' => $keyword, 'status' => $status, 'code' => $code, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('withdraw_record/index')));
		$this->assign('list', $list['list']);
        $export_data['list'] = $list['list'];
		$member_list = array();
		if(!empty($list['list'])){
			$uids = array();
			foreach($list['list'] as $r){
				if(!in_array($r['uid'], $uids)){
					$uids[] = $r['uid'];
				}
			}
			
			$result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,truename,mobile');
			if(!empty($result['list']) && is_array($result['list'])){
				foreach($result['list'] as $rr){
				    $rr['headimg'] =  !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png';
					$member_list[$rr['uid']] = $rr;
				}
			}
			unset($result);
		}

		$this->assign('member_list', $member_list);
        $export_data['member_list'] = $member_list;
		//获取提现方式
		$method_list = model('withdraw_method')->getList(array('uniacid' => $this->uniacid), 'method_code, method_name');
		$this->assign('method_list', $method_list['list']);
		$export_data['method_list'] = $method_list['list'];
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
        $fileName = '提现记录【' . $export_page . '页】';
        $fileType = 'xlsx';
        $arrHeader = array(
			'会员信息',
			'总额',
			'手续费',
			'转入余额',
			'实转金额',
			'提现方式',
			'状态',
			'提现时间',
		);
        $count = count($arrHeader);  //计算表头数量
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        for ($i = 65; $i < $count + 65; $i++) {//数字转字母从65开始，循环设置表头：
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $arrHeader[$i - 65]);
        }
        //填充数据
        foreach ((array) $data['list'] as $k => $val) {
			$k += 2;
			$member = '';
			if(!empty($data['member_list'][$val['uid']]['nickname'])) {
			    $member .= $data['member_list'][$val['uid']]['nickname'];
			}
			if(!empty($data['member_list'][$val['uid']]['mobile'])) {
			    $member .= '[' . $data['member_list'][$val['uid']]['mobile'] . ']';
			}
			$sheet->setCellValueExplicit('A' . $k, $member, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			
            $sheet->setCellValue('B' . $k, $val['record_total']);
            $sheet->setCellValue('C' . $k, $val['record_fee']);
            $sheet->setCellValue('D' . $k, $val['record_yue']);
            $sheet->setCellValue('E' . $k, $val['record_amount']);
            $method_info = $val['method_title'] . PHP_EOL;
			if(!in_array($val['method_code'], array('wxzhuanzhang','wxhongbao','yue'))) {
				$method_info .= $val['method_name'] . PHP_EOL . $val['method_no'];
			    if($val['method_code'] != 'alipay'){
    				$method_info .= $val['method_bank'] . PHP_EOL . $val['method_position'];
				}
			}
			
			$sheet->setCellValue('F' . $k, $method_info);
			$record_status = '';
			if($val['record_status']==0){
			    $record_status = '待处理';
			}elseif($val['record_status']==1){
			    $record_status = '已执行';
			}elseif($val['record_status']==2){
			    $record_status = '已驳回';
            }
            $sheet->setCellValue('G' . $k, $record_status);
			$sheet->setCellValue('H' . $k, date('Y-m-d H:i:s', $val['record_addtime']));
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
	public function rejectOp() {
		$model_record = model('withdraw_record');
		if (chksubmit()) {
			$record_id = input('record_id', 0, 'intval');
			$record_info = $model_record->getInfo(array('uniacid' => $this->uniacid, 'record_id' => $record_id));
			if($record_info['record_status'] > 0){
				output_error('该记录不是待处理状态！');
			}
			$update_array = array();
			$update_array['record_status'] = 2;
			$update_array['record_note'] = input('note', '');			
            $state = $model_record->edit(array('record_id' => input('record_id', 0, 'intval')), $update_array);
            if ($state) {
                model('member')->where(array('uid' => $record_info['uid']))->update('available_predeposit=available_predeposit+' . $record_info['record_total']);
                output_data(array('msg' => '编辑成功', 'url' => users_url('withdraw_record/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$record_id = input('get.record_id', 0, 'intval');
			$record_info = $model_record->getInfo(array('uniacid' => $this->uniacid, 'record_id' => $record_id));
			$this->assign('record_info', $record_info);
			
			if($record_info['record_status'] > 0) {
				output_error('该记录不是待处理状态！');
			}
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	
	public function dealOp(){
		$model_record = model('withdraw_record');
		$record_id = input('get.record_id', 0, 'intval');
		$record_info = $model_record->getInfo(array('uniacid' => $this->uniacid, 'record_id' => $record_id));
		if(empty($record_info['record_id'])){
			output_error('记录不存在！');
		}
		if($record_info['record_status'] > 0){
			output_error('该记录不是待处理状态！');
		}
		
		$member_info = model('member')->getInfo(array('uid' => $record_info['uid']),'nickname');
		
		if($record_info['record_yue']>0){
			$yue_data = array(
				'uniacid' => $this->uniacid,
				'amount' => $record_info['record_yue'],
				'order_sn' => $record_id,
				'uid' => $record_info['uid'],
				'member_name' => empty($member_info['nickname']) ? '' : $member_info['nickname']
			);
			$result = logic('predeposit')->changePd('commission_come', $yue_data);
		}
		$state = $model_record->edit(array('record_id' => $record_id), array('record_status' => 1));
        if ($state) {
            output_data(array('msg' => '处理成功', 'url' => users_url('withdraw_record/index')));
        } else {
			output_error('处理失败！');
        }
	}
	
	public function pay_recordOp(){
		if (IS_API) {
			$model_record = model('withdraw_record');
			$record_id = input('get.record_id', 0, 'intval');
			$record_info = $model_record->getInfo(array('uniacid' => $this->uniacid, 'record_id' => $record_id));
			if(empty($record_info['record_id'])){
				output_error('记录不存在！');
			}
			if($record_info['record_status'] > 0){
				output_error('该记录不是待处理状态！');
			}
			$model = model();
            $model->beginTransaction();
			try {
    			$member_info = model('member')->getInfo(array('uid' => $record_info['uid']), 'openid,nickname');
    			if ($record_info['method_code'] == 'wxzhuanzhang' || $record_info['method_code'] == 'wxhongbao') {
    			    if ($member_info['openid'] == '') {
    					throw new \Exception('提现的会员不是微信用户！');
    				}
    				$inc_file = COMMON_PATH . '/vendor/WeChatDeveloper/include.php';
                    if (!is_file($inc_file)) {
                        throw new \Exception('支付SDK不存在');
                    }
                    require $inc_file;
                    
    				if ($record_info['client_type'] == 'wxapp') {
    					$mb_payment_info = model('mb_payment')->where(array('payment_code' => 'wxapp', 'payment_state' => 1))->find();
    					$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
    					if (empty($payment_config)) {
    						throw new \Exception('小程序支付方式未开启');
    					}
    					$config = array(
                            'token' => config('wechat_token'),
                            'appid' => config('wxappid'),
                            'appsecret' => config('wxappsecret'),
                            'encodingaeskey' => config('wechat_encoding'),
                            // 配置商户支付参数（可选，在使用支付功能时需要）
                            'mch_id' => $payment_config['mchid'],
                            'mch_key' => $payment_config['signkey'],
                            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                            'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                            'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                            // 缓存目录配置（可选，需拥有读写权限）
                            'cache_path' => '',
                        );
    				} else {
    					$mb_payment_info = model('mb_payment')->where(array('payment_code' => 'wxpay_jsapi', 'payment_state' => 1))->find();
						$payment_config = fxy_unserialize($mb_payment_info['payment_config']);
						if (empty($payment_config)) {
							throw new \Exception('wxpay_jsapi支付方式未开启');
						}
						$config = array(
                            'token' => config('wechat_token'),
                            'appid' => config('wechat_appid'),
                            'appsecret' => config('wechat_appsecret'),
                            'encodingaeskey' => config('wechat_encoding'),
                            // 配置商户支付参数（可选，在使用支付功能时需要）
                            'mch_id' => $payment_config['partnerId'],
                            'mch_key' => $payment_config['apiKey'],
                            // 配置商户支付双向证书目录（可选，在使用退款|打款|红包时需要）
                            'ssl_key' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_key_pem'),
                            'ssl_cer' => $_SERVER['DOCUMENT_ROOT'] . config('apiclient_cert_pem'),
                            // 缓存目录配置（可选，需拥有读写权限）
                            'cache_path' => '',
                        );
    				}
    				// 创建接口实例
                    $wechat = \WeChat\Pay::instance($config);
                    $partner_trade_no = $config['mch_id'] . date('YmdHis') . rand(1000, 9999);
					$options = array(
                        'partner_trade_no' => $partner_trade_no,
                        'openid' => $member_info['openid'],
                        'check_name' => 'NO_CHECK',
                        'amount' => (int) ($record_info['record_amount'] * 100),
                        'desc' => '提成发放',
                        'spbill_create_ip' => get_server_ip(),
                    );
                    $payment_return = $wechat->createTransfers($options);
                    //lib\logging::write(var_export($payment_return, true));
                    if ($payment_return['return_code'] != 'SUCCESS') {
                        throw new \Exception($payment_return['return_msg']);
                    }
                    if ($payment_return['result_code'] != 'SUCCESS') {
                    	throw new \Exception($payment_return['err_code_des']);
                    }
                    //$result = $wechat->queryTransfers($partner_trade_no);
                    //lib\logging::write(var_export($result, true));
    			}
    			if ($record_info['record_yue'] > 0) {
    				$yue_data = array(
    					'uniacid' => $this->uniacid,
    					'amount' => $record_info['record_yue'],
    					'order_sn' => $record_id,
    					'uid' => $record_info['uid'],
    					'member_name' => empty($member_info['nickname']) ? '' : $member_info['nickname']
    				);
    				$result = logic('predeposit')->changePd('commission_come', $yue_data);
    			}
    			
    			$update_data = array(
    				'record_status' => 1,
					'record_outtradeno' => $payment_return['partner_trade_no'],
					'record_tradeno' => $payment_return['payment_no'],
					'record_tradetime' => time(),
    			);
			    $state = $model_record->edit(array('record_id' => $record_id), $update_data);
			    if ($state) {
			        $model->commit();
				    output_data('转账成功');
			    } else {
				    throw new \Exception('转账失败！');
			    }
			} catch (\Exception $e) {
				$model->rollBack();
				output_error($e->getMessage());
			}
		}
	}
}