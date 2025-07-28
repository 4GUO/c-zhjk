<?php
namespace sellercenter\controller;
use lib;
class shop_deliver extends control {
	public function __construct() {
		parent::_initialize();
	}
    
    public function sendOp()
    {
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！', users_url('shop_order/index'));
        }
		$if_allow_send = $model_order->getOrderOperateState('send', $order_info);
        if (!$if_allow_send) {
            web_error('无权操作！', users_url('shop_order/index'));
        }
        
        if (chksubmit()) {
			$post = array(
				'order_id' => $order_id,
				'deliver_explain' => input('deliver_explain', ''),
				'shipping_express_id' => input('shipping_express_id', 0, 'intval'),
				'shipping_code' => input('shipping_code', '')
			);
            $result = $model_order->changeOrderSend($order_info, 'seller', input('session.sellername', ''), $post);
            if (!$result['state']) {
                output_error($result['msg']);
            } else {				
				output_data(array('msg' => '发货成功', 'url' => users_url('shop_order/index')));
            }
        } else {
			$this->assign('order_info', $order_info);
			//取发货地址
			$model_daddress = model('shop_daddress');
			if ($order_info['extend_order_common']['daddress_id'] > 0) {
				$daddress_info = $model_daddress->getInfo(array('address_id' => $order_info['extend_order_common']['daddress_id']));
			} else {
				//取默认地址
				$daddress_info = $model_daddress->getInfo(array('store_id' => $this->store_id), '*', 'is_default desc');
				$daddress_info = isset($daddress_info['address_id']) ? $daddress_info : array();
				//写入发货地址编号
				if(isset($daddress_info['address_id'])){		
					model('shop_order_common')->edit(array('daddress_id' => $daddress_info['address_id']), array('order_id' => $order_id));
				}				
			}
			$this->assign('daddress_info', $daddress_info);
			
			$result = model('express')->getList(array('e_state' => 1), '*', 'e_letter asc');
			$express_list = $result['list'];
			//如果是自提订单，只保留自提快递公司
			if (isset($order_info['extend_order_common']['reciver_info']['dlyp'])) {
				foreach ($express_list as $k => $v) {
					if ($v['e_zt_state'] == '0') {
						unset($express_list[$k]);
					}
				}
				$my_express_list = array_keys($express_list);
			}			
			$this->assign('express_list', $express_list);
			$this->display();
		}        
    }
    /**
     * 编辑收货地址
     * @return boolean
     */
    public function buyer_address_editOp()
    {
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！', users_url('shop_order/index'));
        }
		if (chksubmit()) {
			$reciver_info = array(
				'tel_phone' => input('new_tel_phone', ''),
				'address' => input('new_area', '') . ' ' . input('new_street', ''),
				'area' => input('new_area', ''),
				'street' => input('new_street', '')
			);
			$data = array();
			$data['reciver_name'] = input('new_reciver_name', '');
			$data['reciver_info'] = serialize($reciver_info);
			$state = model('shop_order_common')->edit($data, array('order_sn' => $order_info['order_sn']));
			if($state){
				$response = $data['reciver_name'] . '&nbsp' . $reciver_info['tel_phone'] . '&nbsp' . $reciver_info['address'];
			} else {
				$response = '';
			}			
			output_data(array('address' => $response));
		} else {
			$this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
    }
    
    /**
     * 选择发货地址
     * @return boolean
     */
    public function send_address_selectOp()
    {
		$model_order = model('shop_order');
		$order_id = input('order_id', 0, 'intval');
		$condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
		$order_info = $model_order->getInfo($condition);
		if (chksubmit()) {
			$daddress_id = input('daddress_id', 0, 'intval');
			if($daddress_id > 0){
				model('shop_order_common')->edit(array('daddress_id' => $daddress_id), array('order_sn' => $order_info['order_sn']));				
			}
			output_data('');
		} else {
			$list_temp = model('shop_daddress')->getList(array('store_id' => $this->store_id), '*', 'address_id desc');
			$this->assign('address_list', $list_temp['list']);
			$this->assign('order_id', $order_id);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}        
    }
	/**
     * 延迟收货
     */
    public function delay_receiveOp()
    {
        $order_id = input('order_id', 0, 'intval');
        $model_order = model('shop_order');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
		$condition['lock_state'] = 0;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }	
        if (chksubmit()) {
            $delay_date = input('delay_date', 0, 'intval');
            if (!in_array($delay_date, array(5, 10, 15))) {
                output_error('时间选择错误！');
            }			
			$delay_time = $order_info['delay_time'] + $delay_date * 3600 * 24;
            $update = $model_order->edit($condition, array('delay_time' => $delay_time));
            if ($update) {
				$delay_time = $delay_time + ORDER_AUTO_RECEIVE_DAY * 3600 * 24;
				output_data(array('msg' => '成功将最晚收货期限延迟到了' . date('Y-m-d H:i:s', $delay_time) . '&emsp;', 'url' => users_url('shop_order/show_order', array('order_id' => $order_id))));
            } else {
                output_error('延迟失败！');
            }
        } else {
			$delay_time = $order_info['delay_time'] + ORDER_AUTO_RECEIVE_DAY * 3600 * 24;
            $order_info['delay_time'] = $delay_time;
            $this->assign('order_info', $order_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }
    /**
     * 物流跟踪
     */
    public function search_deliverOp()
    {
        $order_sn = input('order_sn', '');
        if (!is_numeric($order_sn)) {
            web_error('订单号错误！');
        }
        $model_order = model('shop_order');
        $condition['order_sn'] = $order_sn;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            output_error('订单不存在！');
        }
		
		if(empty($order_info['extend_order_common']['shipping_express_id'])){
			output_error('该订单无需物流！');
		}
		
		if(empty($order_info['shipping_code'])){
			output_error('该订单未设置物流单号！');
		}
		
        $order_info['state_info'] = $model_order->_orderState($order_info);
		$order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        $this->assign('order_info', $order_info);
        //卖家发货信息
		if(!empty($order_info['extend_order_common']['daddress_id'])){
			$daddress_info = model('shop_daddress')->getInfo(array('address_id' => $order_info['extend_order_common']['daddress_id']));
		} else {
			$daddress_info = array();
		}        
        $this->assign('daddress_info', $daddress_info);
		
        //取得配送公司代码
        $express = model('express')->getInfo(array('id' => $order_info['extend_order_common']['shipping_express_id']));
        $this->assign('e_code', $express['e_code']);
        $this->assign('e_name', $express['e_name']);
        $this->assign('e_url', $express['e_url']);
        $this->assign('shipping_code', $order_info['shipping_code']);		
        $config = model('config')->getInfo();
        $content = logic('deliver')->kuaidi100($config, array('e_code' => $express['e_code'], 'shipping_code' => $order_info['shipping_code']));
		$error = '';
		if (empty($content)) {
			$error = '系统未配置物流信息';
		}
		if(!is_array($content)){
			$content = json_decode(htmlspecialchars_decode($content), true);
		}
        if (empty($content['status'])) {
			$error = $content['message'];
        }
		$output = array();
		if (!empty($content['data'])) {
			$content['data'] = array_reverse($content['data']);
			if (is_array($content['data'])) {
				foreach ($content['data'] as $k => $v) {
					if ($v['time'] == '') {
						continue;
					}
					$output[] = $v['time'] . '  ' . $v['context'];
				}
			}
		}
        if (empty($output)) {
            $error = '暂无结果';
        }
		$this->assign('shipping_express', $output);
		$this->assign('shipping_error', $error);
		
        $this->display();
    }
    
	/**
     * 选择发货物流公司
     * @return boolean
     */
    public function waybill_expressOp()
    {
		$order_id = input('order_id', 0, 'intval');
		$result = model('express')->getList(array('e_state' => 1), '*', 'e_letter asc');
		$this->assign('express_list', $result['list']);
		$this->assign('order_id', $order_id);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
	
    /**
     * 运单打印
     */
    public function waybill_printOp()
    {
        $order_id = input('order_id', 0, 'intval');
		$express_id = input('express_id', 0, 'intval');
		if (empty($express_id)) {
            web_error('请选择物流公司！');
        }
        $model_order = model('shop_order');
		$model_waybill = model('shop_waybill');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['store_id'] = $this->store_id;
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods', 'member'));
		if (empty($order_info['order_id'])) {
            web_error('订单不存在！');
        }
        $waybill_info = $model_waybill->getInfo(array('store_id' => $this->store_id, 'waybill_usable' => 1, 'express_id' => $express_id));
        if (empty($waybill_info['waybill_id'])) {
            web_error('请首先绑定打印模板', users_url('shop_waybill/index'));
        }
        //根据订单内容获取打印数据
        $print_info = $model_waybill->getPrintInfoByOrderInfo($order_info);
		
        //整理打印模板
        $waybill_info['waybill_data'] = fxy_unserialize($waybill_info['waybill_data']);
        foreach ($waybill_info['waybill_data'] as $key => $value) {
            $waybill_info['waybill_data'][$key]['content'] = $print_info[$key];
        }
        //使用商家自定义的偏移尺寸
		$waybill_pixel = 3.8;
        $waybill_info['waybill_pixel_width'] = $waybill_info['waybill_width'] * $waybill_pixel;
        $waybill_info['waybill_pixel_height'] = $waybill_info['waybill_height'] * $waybill_pixel;
        $waybill_info['waybill_pixel_top'] = $waybill_info['waybill_top'] * $waybill_pixel;
        $waybill_info['waybill_pixel_left'] = $waybill_info['waybill_left'] * $waybill_pixel;
		
        $this->assign('waybill_info', $waybill_info);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
}