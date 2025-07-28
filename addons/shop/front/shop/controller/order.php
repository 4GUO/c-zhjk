<?php
namespace shop\controller;
use lib;
class order extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_order = model('shop_order');
		if(IS_API){
			$this->title = '我的订单';
			$status = input('status', '0', 'intval');
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$where['is_del'] = 0;
			if($status){
				switch ($status) {
					case 1:
						$where['order_state'] = ORDER_STATE_NEW;
						break;
					case 2:
						$where['order_state'] = ORDER_STATE_PAY;
						$where['lock_state'] = 0;
						break;
					case 3:
						$where['order_state'] = ORDER_STATE_SEND;
						break; 
					case 4:
						$where['evaluation_state'] = 0;
						$where['order_state'] = ORDER_STATE_SUCCESS;
						break; 
				}
			}
			$goods_name = input('goods_name', '');
			if($goods_name){
				$where['goods_name'] = $goods_name;
			}
			$where['lock_state'] = 0;
			$list = $model_order->getList($where, '*', 'order_id desc', 20, input('page', 1, 'intval'), array('order_common', 'order_goods'));
			$group_order_list = array();
			if(!empty($list['list'])){
				foreach ($list['list'] as $value) {
					//显示取消订单
					$value['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $value);
					//显示收货
					$value['if_receive'] = $model_order->getOrderOperateState('receive', $value);
					//显示物流跟踪
					$value['if_deliver'] = $model_order->getOrderOperateState('deliver', $value);
					//显示评价按钮
					$value['if_evaluation'] = $model_order->getOrderOperateState('evaluation', $value);
					//显示退款按钮
					$value['if_refund_cancel'] = $model_order->getOrderOperateState('refund_cancel', $value);
					//显示删除按钮
					$value['if_del'] = $model_order->getOrderOperateState('buyer_del', $value);
					$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
					$value['shipping_time'] = date('Y-m-d H:i:s', $value['extend_order_common']['shipping_time']);
					$value['finnshed_time'] = date('Y-m-d H:i:s', $value['finnshed_time']);
					$group_order_list[$value['pay_sn']]['order_list'][] = $value;
					//如果有在线支付且未付款的订单则显示合并付款链接
					if ($value['order_state'] == ORDER_STATE_NEW) {
						if (!isset($group_order_list[$value['pay_sn']]['pay_amount'])) {
							$group_order_list[$value['pay_sn']]['pay_amount'] = 0;
						}
						$group_order_list[$value['pay_sn']]['pay_amount'] += $value['order_amount'] - $value['pd_amount'];
					}
					$group_order_list[$value['pay_sn']]['add_time'] = $value['add_time'];
					$group_order_list[$value['pay_sn']]['pay_sn'] = $value['pay_sn'];
				}
			}
			$return = array(
				'title' => $this->title,
				'group_order_list' => array_values($group_order_list),
				'totalpage' => $list['totalpage'],
				'hasmore' => $list['hasmore'],
			);
			output_data($return);
		}
	}
	public function order_infoOp(){
		$model_order = model('shop_order');
		if(IS_API){
			$order_id = input('order_id', 0, 'intval');
			$this->title = '订单详情';
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['order_id'] = $order_id;
			$order_info = $model_order->getInfo($where, array('order_common', 'order_goods'));
			if(!$order_info){
				output_error('订单不存在');
			}
			//显示取消订单
			$order_info['if_cancel'] = $model_order->getOrderOperateState('buyer_cancel', $order_info);
			//显示收货
			$order_info['if_receive'] = $model_order->getOrderOperateState('receive', $order_info);
			//显示物流跟踪
			$order_info['if_deliver'] = $model_order->getOrderOperateState('deliver', $order_info);
			//显示评价按钮
			$order_info['if_evaluation'] = $model_order->getOrderOperateState('evaluation', $order_info);
			$order_info['add_time'] = date('Y-m-d H:i:s', $order_info['add_time']);
			$order_info['payment_time'] = empty($order_info['payment_time']) ? '' : date('Y-m-d H:i:s', $order_info['payment_time']);
			$order_info['send_time'] = empty($order_info['send_time']) ? '' : date('Y-m-d H:i:s', $order_info['send_time']);
			$order_info['finnshed_time'] = empty($order_info['finnshed_time']) ? '' : date('Y-m-d H:i:s', $order_info['finnshed_time']);
			$return = array(
				'title' => $this->title,
				'order_info' => $order_info,
			);
			output_data($return);
		}
	}
	public function order_cancelOp(){
		$model_order = model('shop_order');
		if(IS_API){
			$order_id = input('order_id', 0, 'intval');
			$condition = array();
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			$if_allow = $model_order->getOrderOperateState('buyer_cancel', $order_info);
			if (!$if_allow) {
				output_error('无权操作');
			} else {
				$result = $model_order->changeOrderStateCancel($order_info, 'buyer', $this->member_info['uid'], '其它原因');
				if (!$result['state']) {
					output_error($result['msg']);
				} else {
					output_data('1');
				}
			}
		}
	}
	public function order_delOp(){
		$model_order = model('shop_order');
		if(IS_API){
			$order_id = input('order_id', 0, 'intval');
			$condition = array();
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			$if_allow = $model_order->getOrderOperateState('buyer_del', $order_info);
			if (!$if_allow) {
				output_error('无权操作');
			} else {
				$result = $model_order->changeOrderStateDel($order_info, 'buyer', $this->member_info['uid'], '其它原因');
				if (!$result['state']) {
					output_error($result['msg']);
				} else {
					output_data('1');
				}
			}
		}
	}
	/**
     * 订单确认收货
     */
    public function order_receiveOp()
    {
        $model_order = model('shop_order');
        $order_id = input('order_id', 0, 'intval');
        $condition = array();
        $condition['order_id'] = $order_id;
        $condition['uid'] = $this->member_info['uid'];
        $order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
        $if_allow = $model_order->getOrderOperateState('receive', $order_info);
        if (!$if_allow) {
            output_error('无权操作');
        } else {
			$model = model();
			$model->beginTransaction();
			$result = $model_order->changeOrderStateReceive($order_info, 'buyer', $this->member_info['uid'], '签收了货物');
			if (!$result['state']) {
				 $model->rollback();
				output_error($result['msg']);
			} else {
				$model->commit();
				output_data('1');
			}
		}
    }
	/**
     * 物流跟踪
     */
    public function search_deliverOp()
    {
		if(IS_API){
			$order_id = input('order_id', 0, 'intval');
			if ($order_id <= 0) {
				output_error('参数错误');
			}
			$model_order = model('shop_order');
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			if (empty($order_info)) {
				output_error('订单不存在');
			}
			$shipping_express_id = $order_info['extend_order_common']['shipping_express_id'];
			$express = model('express')->getInfo(array('id' => $shipping_express_id));
			$e_code = $express['e_code'];
			$e_name = $express['e_name'];
			$shipping_code = $order_info['shipping_code'];
			$deliver_info = $this->_get_express($e_code, $shipping_code);
			output_data(array('title' => '物流信息', 'express_name' => $e_name, 'shipping_code' => $shipping_code, 'deliver_info' => array_reverse($deliver_info)));
		}
    }
	/**
     * 从第三方取快递信息
     *
     */
    public function _get_express($e_code, $shipping_code)
    {
        $content = logic('deliver')->kuaidi100(config(), array('e_code' => $e_code, 'shipping_code' => $shipping_code));
		if (empty($content)) {
			output_error('系统未配置物流信息');
		}
		if(!is_array($content)){
			$content = json_decode(htmlspecialchars_decode($content), true);
		}
        if (empty($content['status'])) {
            output_error($content['message']);
        }
        $content['data'] = array_reverse($content['data']);
        $output = array();
        if (is_array($content['data'])) {
            foreach ($content['data'] as $k => $v) {
                if ($v['time'] == '') {
                    continue;
                }
                $output[] = $v['time'] . '  ' . $v['context'];
            }
        }
        if (empty($output)) {
            exit(json_encode(false));
        }
        return $output;
    }
	public function evaluation_pageOp() {
		if(IS_API){
			$model_order = model('shop_order');
			$order_id = input('order_id', 0, 'intval');
			$condition = array();
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			$if_evaluation = $model_order->getOrderOperateState('evaluation', $order_info);
			if (!$if_evaluation) {
				output_error('已经评论过了');
			}
			output_data(array('title' => '商品评价', 'order_info' => $order_info));
		}
	}
	public function s_up_img_updateOp(){
		if(IS_API){
			$order_id = input('get.order_id', 0, 'intval') ? input('get.order_id', 0, 'intval') : input('post.order_id', 0, 'intval');
			$file_name = input('get.name', '') ? input('get.name', '') : input('post.name', '');
			if (!empty($_FILES[$file_name]['name'])) {
				$upload = new lib\uploadfile();
				$upload->set('default_dir', front_upload_img_dir($this->member_info['uid']) . '/evaluation/' . $upload->getSysSetPath());
				$upload->set('max_size', config('image_max_filesize'));
				$upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
				//$upload->set('thumb_width', '640');
				//$upload->set('thumb_height', '250');
				$upload->set('fprefix', $order_id);
				$result = $upload->upfile($file_name);
				if (!$result) {
					output_error($upload->error);
				} else {
					$data['src'] = front_upload_img_url($this->member_info['uid']) . '/evaluation/' . $upload->getSysSetPath() . $upload->file_name;
					list($width, $height) = fxy_getimagesize($data['src']);
					$data['width'] = $width;
					$data['height'] = $height;
					output_data($data);
				}
			} else {
				output_error('缺少参数');
			}
		}
	}
	public function evaluation_submitOp(){
		if(IS_API){
			$model_order = model('shop_order');
			$order_id = input('order_id', '');
			$goods_list = json_decode(htmlspecialchars_decode(input('goods_list', '')), true);
			//lib\logging::write(var_export($goods_list, true));
			$where = array();
			$where['uid'] = $this->member_info['uid'];
			$where['order_id'] = $order_id;
			$order_info = $model_order->getInfo($where, array('order_common', 'order_goods'));
			if(!$order_info){
				output_error('订单不存在');
			}
			$model_evaluation = model('shop_evaluate_goods');
			$check = $model_evaluation->where(array('geval_orderid' => $order_id, 'geval_frommemberid' => $this->member_info['uid']))->total();
			if($check){
				output_error('请勿重复评论');
			}
			$goods_ids = array();
			foreach ($goods_list as $v) {
				$goods_ids[] = $v['goods_id'];
			}
			$result = model('shop_goods')->getList(array('goods_id' => $goods_ids), 'goods_id,goods_commonid');
			foreach ($result['list'] as $v) {
				$goods_commonids[$v['goods_id']] = $v['goods_commonid'];
			}
			$evaluation_data = array();
			$i = 0;
			foreach ($goods_list as $goods_info) {
				$evaluation_data[$i]['geval_orderid'] = $order_id;
				$evaluation_data[$i]['geval_orderno'] = $order_info['order_sn'];
				$evaluation_data[$i]['geval_goodsid'] = $goods_commonids[$goods_info['goods_id']];
				$evaluation_data[$i]['geval_goodsname'] = $goods_info['goods_name'];
				$evaluation_data[$i]['geval_goodsprice'] = $goods_info['goods_price'];
				$evaluation_data[$i]['geval_goodsimage'] = $goods_info['goods_image'];
				$evaluation_data[$i]['geval_scores'] = $goods_info['flag'];
				$evaluation_data[$i]['geval_content'] = !empty($goods_info['message']) ? $goods_info['message'] : '此用户没有填写评论';
				$evaluation_data[$i]['geval_isanonymous'] = intval($goods_info['isanonymous']);
				$evaluation_data[$i]['store_id'] = $order_info['store_id'];
				$evaluation_data[$i]['geval_addtime'] = TIMESTAMP;
				$evaluation_data[$i]['geval_frommemberid'] = $this->member_info['uid'];
				$evaluation_data[$i]['geval_frommembername'] = $this->member_info['nickname'];
				$evaluation_data[$i]['geval_frommemberheadimg'] = !empty($this->member_info['tag']['headimgurl']) ? $this->member_info['tag']['headimgurl'] : (!empty($this->member_info['headimgurl']) ? $this->member_info['headimgurl'] : '');
				$evaluation_data[$i]['geval_state'] = 1;
				$evaluation_data[$i]['geval_image'] = serialize($goods_info['image_list']);
				$evaluation_data[$i]['is_spike'] = 0;
				$evaluation_data[$i]['is_points'] = 0;
				$i++;
			}
			$flag = $model_evaluation->insertAll($evaluation_data);
			if ($flag) {
				model('points_log')->savePointsLog('points_comments', array('pl_memberid' => $this->member_info['uid'], 'pl_membername' => $this->member_info['nickname']), true);
				//商品口碑计算
				$total_scores_info = $model_evaluation->getInfo(array('geval_state' => 1), 'SUM(geval_scores) as total_scores');
				$model_goods = model('shop_goods_common');
				foreach($goods_list as $k => $v){
					$goods_info = $model_goods->getInfo(array('goods_commonid' => $goods_commonids[$v['goods_id']]), 'goods_evaluation_total');
					$goods_evaluation = $total_scores_info['total_scores'] / ($goods_info['goods_evaluation_total'] + $v['flag']);
					$model_goods->where(array('goods_commonid' => $goods_commonids[$v['goods_id']]))->update('goods_evaluation_total=goods_evaluation_total+' . $v['flag'] . ',goods_evaluation=' . $goods_evaluation);
				}
				$model_order->edit($where, array('evaluation_state' => 1, 'evaluation_time' => TIMESTAMP));
				output_data('1');
			}else{
				output_error('评论失败');
			}
		}
	}
	public function refund_pageOp() {
		if(IS_API){
			$model_order = model('shop_order');
			$order_id = input('order_id', 0, 'intval');
			$condition = array();
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			$if_evaluation = $model_order->getOrderOperateState('refund_cancel', $order_info);
			if (!$if_evaluation) {
				output_error('订单退款中...');
			}
			$order_info['refund_amount'] = floatval($order_info['order_amount']) - floatval($order_info['shipping_fee']);
			output_data(array('title' => '商品退款', 'order_info' => $order_info, 'telphone' => $this->config['telphone']));
		}
	}
	public function refund_submitOp(){
		if(IS_API){
			$model_order = model('shop_order');
			$buyer_message = input('message', '');
			$order_id = input('order_id', 0, 'intval');
			$condition = array();
			$condition['order_id'] = $order_id;
			$condition['uid'] = $this->member_info['uid'];
			$order_info = $model_order->getInfo($condition, array('order_common', 'order_goods'));
			$if_evaluation = $model_order->getOrderOperateState('refund_cancel', $order_info);
			if (!$if_evaluation) {
				output_error('订单退款中...');
			}
			$shop_refund_return = model('shop_refund_return');
			$refund_array = array();
			$refund_array['order_id'] = $order_id;
			$refund_array['order_sn'] = $order_info['order_sn'];
			$refund_array['refund_sn'] = $shop_refund_return->makeRefundSn($order_id);
			$refund_array['store_id'] = $order_info['store_id'];
			$refund_array['buyer_id'] = $order_info['uid'];
			$refund_array['buyer_name'] = $order_info['member_name'];
			$refund_array['refund_amount'] = floatval($order_info['order_amount']) - floatval($order_info['shipping_fee']);
			$refund_array['refund_type'] = 1;
			$refund_array['seller_state'] = 1;
			$refund_array['refund_state'] = 1;
			$refund_array['add_time'] = TIMESTAMP;
			$refund_array['buyer_message'] = $buyer_message;
			$refund_array['is_spike'] = 0;
			$refund_array['is_points'] = 0;
			$refund_id = $shop_refund_return->add($refund_array);
			if($refund_id){
				$model_order->edit(array('order_id' => $order_id), array('lock_state' => 1));
				output_data('1');
			}
		}
	}
	public function refund_listOp(){
		if(IS_API){
			$this->title = '退款记录';
			$model_refund = model('shop_refund_return');
			$where = array();
			$where['buyer_id'] = $this->member_info['uid'];
			$where['is_spike'] = 0;
			$where['is_points'] = 0;
			$list = $model_refund->getList($where, '*', 'refund_id desc', 20, input('page', 1, 'intval'));
			$refund_list = array();
			if(!empty($list['list'])){
				foreach ($list['list'] as $value) {
					$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
					$value['seller_time'] = date('Y-m-d H:i:s', $value['seller_time']);
					$value['state_desc'] = $model_refund->_orderState($value);
					$refund_list[$value['order_sn']] = $value;
				}
				//取商品列表
				$order_goods_list = model('shop_order')->getOrderGoodsList(array('order_sn' => array_keys($refund_list)));
				if (!empty($order_goods_list)) {
					foreach ($order_goods_list as $key => $value) {
						$refund_list[$value['order_sn']]['extend_order_goods'][] = $value;
					}
				} else {
					$refund_list[$value['order_sn']]['extend_order_goods'] = array();
				}
			}
			$return = array(
				'title' => $this->title,
				'list' => array_values($refund_list),
				'totalpage' => $list['totalpage'],
				'hasmore' => $list['hasmore'],
			);
			unset($list);
			output_data($return);
		}
	}
}