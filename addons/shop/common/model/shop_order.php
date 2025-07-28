<?php
namespace model;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_order extends base\model
{
    protected $tableName = 'fxy_shop_order';
    /**
     * 取单条订单信息
     *
     * @param unknown_type $condition
     * @param array $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return unknown
     */
    public function getInfo($condition = array(), $extend = array(), $fields = '*', $order = '', $group = '')
    {
        $order_info = $this->field($fields)->where($condition)->group($group)->order($order)->find();
		if (empty($order_info)) {
            return array();
        }
        if (isset($order_info['order_state'])) {
            $order_info['state_desc'] = $this->_orderState($order_info);
        }
        if (isset($order_info['payment_code'])) {
            $order_info['payment_name'] = orderPaymentName($order_info['payment_code']);
        }
        //追加返回订单扩展表信息
        if (in_array('order_common', $extend)) {
            $order_info['extend_order_common'] = $this->getOrderCommonInfo(array('order_sn' => $order_info['order_sn']));
            $order_info['extend_order_common']['reciver_info'] = !empty($order_info['extend_order_common']['reciver_info']) ? fxy_unserialize($order_info['extend_order_common']['reciver_info']) : array();
            $order_info['extend_order_common']['invoice_info'] = !empty($order_info['extend_order_common']['invoice_info']) ? fxy_unserialize($order_info['extend_order_common']['invoice_info']) : array();
			$order_info['extend_order_common']['zengpin_list'] = !empty($order_info['extend_order_common']['zengpin_list']) ? fxy_unserialize($order_info['extend_order_common']['zengpin_list']) : array();
			$order_info['extend_order_common']['promotion_info'] = !empty($order_info['extend_order_common']['promotion_info']) ? htmlspecialchars_decode($order_info['extend_order_common']['promotion_info']) : '';
		}
        //返回买家信息
        if (in_array('member', $extend)) {
            $order_info['extend_member'] = model('member')->getInfo(array('uid' => $order_info['uid']));
        }
        //追加返回商品信息
        if (in_array('order_goods', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_sn' => $order_info['order_sn']));
            $order_info['extend_order_goods'] = $order_goods_list;
        }
        return $order_info;
    }
    
    
    /**
     * 取得订单列表(所有)
     * @param unknown $condition
     * @param string $page
     * @param string $field
     * @param string $order
     * @param string $limit
     * @param unknown $extend 追加返回那些表的信息,如array('order_common','order_goods','store')
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getList($condition, $field = '*', $order = 'order_id desc', $page = null, $get_p = null, $extend = array())
    {
		$order_goods_list_ids = array();
		if (!empty($condition['goods_name'])) {
			$order_goods_list_tmp = model('shop_order_goods')->field('order_sn')->where(array('goods_name' => '%' . $condition['goods_name'] . '%'))->select();
			if($order_goods_list_tmp){
				foreach($order_goods_list_tmp as $k => $v){
					$order_goods_list_ids[] = $v['order_sn'];
				}
			}
			unset($condition['goods_name']);
			if($order_goods_list_ids){
				$condition['order_sn'] = $order_goods_list_ids;
			}else{
				$condition['order_sn'] = '';
			}
		}
		$member_list_ids = array();
		if (!empty($condition['buyer_name'])) {
			$member_tmp = model('member')->field('uid')->where(array('truename' => '%' . $condition['buyer_name'] . '%'),array('nickname' => '%' . $condition['buyer_name'] . '%'))->order('uid desc')->select();
			if($member_tmp){
				foreach($member_tmp as $k => $v){
					$member_list_ids[] = $v['uid'];
				}
			}
			unset($condition['buyer_name']);
			$condition['uid'] = $member_list_ids;
		}
		if ($page && $get_p) {
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list_tmp = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$list = array();
			if($list_tmp){
				foreach($list_tmp as $k => $v){
					$list[$v['order_sn']] = $v;
				}
			}
			$hasmore = $total > $get_p * $page ? true : false;
			unset($list_tmp);
		} else {
			$totalpage = 0;
			$list_tmp = $this->where($condition)->field($field)->order($order)->select();
			$list = array();
			if($list_tmp){
				foreach($list_tmp as $k => $v){
					$list[$v['order_sn']] = $v;
				}
			}
			$hasmore = false;
			unset($list_tmp);
		}
        if (empty($list)) {
            return array('list' => array(), 'totalpage' => 0, 'hasmore' => false);
        }
        $order_list = array();
		
        foreach ($list as $order) {
            if (isset($order['order_state'])) {
                $order['state_desc'] = $this->_orderState($order);
            }
            if (isset($order['payment_code'])) {
                $order['payment_name'] = orderPaymentName($order['payment_code']);
            }
            $order_list[$order['order_sn']] = $order;
        }
        if (empty($order_list)) {
            $order_list = $list;
        }
		
        //追加返回订单扩展表信息
        if (in_array('order_common', $extend)) {
            $order_common_list = $this->getOrderCommonList(array('order_sn' => array_keys($order_list)), '*');
            foreach ($order_common_list as $value) {
                $order_list[$value['order_sn']]['extend_order_common'] = $value;
                $order_list[$value['order_sn']]['extend_order_common']['reciver_info'] = !empty($value['reciver_info']) ? fxy_unserialize($value['reciver_info']) : array();
				$invoice_info = !empty($value['invoice_info']) ? fxy_unserialize($value['invoice_info']) : array();
				if(empty($invoice_info)){
					$invoice_info['type'] = '';
					$invoice_info['title'] = '';
					$invoice_info['content'] = '';
				}
                $order_list[$value['order_sn']]['extend_order_common']['invoice_info'] = $invoice_info;
				$order_list[$value['order_sn']]['extend_order_common']['zengpin_list'] = !empty($order_list[$value['order_sn']]['extend_order_common']['zengpin_list']) ? fxy_unserialize($order_list[$value['order_sn']]['extend_order_common']['zengpin_list']) : array();
				$order_list[$value['order_sn']]['extend_order_common']['promotion_info'] = !empty($order_list[$value['order_sn']]['extend_order_common']['promotion_info']) ? htmlspecialchars_decode($order_list[$value['order_sn']]['extend_order_common']['promotion_info']) : '';
            }
        }
        //追加返回买家信息
        if (in_array('member', $extend)) {
            foreach ($order_list as $order_sn => $order) {
                $order_list[$order_sn]['extend_member'] = model('member')->getInfo(array('uid' => $order['uid']));
            }
        }
        //追加返回商品信息
        if (in_array('order_goods', $extend)) {
            //取商品列表
            $order_goods_list = $this->getOrderGoodsList(array('order_sn' => array_keys($order_list)), '*');
            if (!empty($order_goods_list)) {
                foreach ($order_goods_list as $key => $value) {
                    $order_list[$value['order_sn']]['extend_order_goods'][] = $value;
                }
            } else {
                $order_list[$value['order_sn']]['extend_order_goods'] = array();
            }
        }
		return array('list' => $order_list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
    }
	public function getOrderCommonList($condition = array(), $fields = '*', $order = '', $limit = '')
    {
        return model('shop_order_common')->field($fields)->where($condition)->order($order)->limit($limit)->select();
    }
    public function getOrderGoodsList($condition = array(), $fields = '*', $order = 'id desc', $limit = '')
    {
        return model('shop_order_goods')->field($fields)->where($condition)->limit($limit)->order($order)->select();
    }
	public function getOrderCommonInfo($condition = array(), $field = '*')
    {
        return model('shop_order_common')->field($field)->where($condition)->find();
    }
	public function _orderState($order_info) {
		switch ($order_info ['order_state']) {
			case ORDER_STATE_CANCEL :
				$order_state = '已取消';
				break;
			
			case ORDER_STATE_NEW :
				$order_state = '待付款';
				break;
			
			case ORDER_STATE_PAY :
				$order_state = '待发货';
				break;
			
			case ORDER_STATE_SEND :
				$order_state = '待收货';
				break;
			
			case ORDER_STATE_SUCCESS :
				$order_state = '交易完成';
				break;
		}
		if($order_info['lock_state'] == 1){
			$order_state = '申请退款中...';
		}
		return $order_state;
	}
	
    /**
     * 取得订单数量
     * @param unknown $condition
     */
    public function getCount($condition)
    {
        return $this->where($condition)->total();
    }

    /**
     * 插入订单表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function add($data)
    {
        $insert = $this->insert($data);
        return $insert;
    }
    
    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function edit($condition, $data)
    {
        $update = $this->where($condition)->update($data);
        return $update;
    }
    /**
     * 返回是否允许某些操作
     * @param unknown $operate
     * @param unknown $order_info
     */
    public function getOrderOperateState($operate, $order_info)
    {
        if (!is_array($order_info) || empty($order_info)) {
            return false;
        }
        switch ($operate) {
            //买家取消订单
            case 'buyer_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
            //申请退款
            case 'refund_cancel':
                $state = !intval($order_info['lock_state']) && in_array($order_info['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND));
                break;
            //商家取消订单
            case 'store_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_NEW || $order_info['payment_code'] == 'offline' && in_array($order_info['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND));
                break;
            //平台取消订单
            case 'system_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_NEW || $order_info['payment_code'] == 'offline' && $order_info['order_state'] == ORDER_STATE_PAY;
                break;
            //平台收款
            case 'system_receive_pay':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
            //评价
            case 'evaluation':
                $state = !$order_info['lock_state'] && !$order_info['evaluation_state'] && $order_info['order_state'] == ORDER_STATE_SUCCESS;
                break;
            case 'payment':
                $state = $order_info['order_state'] == ORDER_STATE_NEW && $order_info['payment_code'] == 'online';
                break;
            //调整运费
            case 'modify_price':
                $state = $order_info['order_state'] == ORDER_STATE_NEW || $order_info['payment_code'] == 'offline' && $order_info['order_state'] == ORDER_STATE_PAY;
                $state = floatval($order_info['shipping_fee']) > 0 && $state;
                break;
            //调整商品价格
            case 'spay_price':
                $state = $order_info['order_state'] == ORDER_STATE_NEW || $order_info['payment_code'] == 'offline' && $order_info['order_state'] == ORDER_STATE_PAY;
                $state = floatval($order_info['goods_amount']) > 0 && $state;
                break;
            //发货
            case 'send':
                $state = !$order_info['lock_state'] && $order_info['order_state'] == ORDER_STATE_PAY;
                break;
            //收货
            case 'receive':
                $state = !$order_info['lock_state'] && $order_info['order_state'] == ORDER_STATE_SEND;
                break;
            //快递跟踪
            case 'deliver':
                $state = !empty($order_info['shipping_code']) && in_array($order_info['order_state'], array(ORDER_STATE_SEND, ORDER_STATE_SUCCESS));
                break;
			case 'lock':
                $state = intval($order_info['lock_state']) ? true : false;
                break;
			case 'buyer_del':
                $state = $order_info['order_state'] == ORDER_STATE_CANCEL && $order_info['is_del'] == 0;
                break;
        }
        return $state;
    }
	/**
     * 添加订单日志
     */
    public function addOrderLog($data)
    {
        $data['log_role'] = str_replace(array('buyer', 'seller', 'system', 'admin'), array('买家', '商家', '系统', '管理员'), $data['log_role']);
        $data['log_time'] = TIMESTAMP;
        return model('shop_order_log')->insert($data);
    }
	
	/**
     * 查询订单日志信息
     */
    public function getOrderLogInfo($condition)
    {
        return model('shop_order_log')->where($condition)->find();
    }
	/**
     * 取消订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @return array
     */
    public function changeOrderStateCancel($order_info, $role, $user = '', $msg = '')
    {
        try {
			if(!$order_info){
				return callback(false, '订单不在');
			}
            $this->beginTransaction();
            $order_sn = $order_info['order_sn'];
            if (!empty($order_info['is_spike']) && $order_info['order_state'] == ORDER_STATE_NEW) {
				//库存销量变更
				if(empty($order_info['extend_order_goods'])) {
					$goods_list = $this->getOrderGoodsList(array('order_sn' => $order_sn));
				} else {
					$goods_list = $order_info['extend_order_goods'];
				}
				$kucun_data = array();
				foreach ($goods_list as $v) {
					$kucun_data[] = array(
						'goods_id' => $v['goods_id'],
						'goods_salenum' => '(goods_salenum-' . $v['goods_num'] . ')',
						'goods_storage' => '(goods_storage+' . $v['goods_num'] . ')',
					);
				}
				if ($kucun_data) {
					$sql = batchUpdate('ims_fxy_shop_goods', $kucun_data, 'goods_id', array(), true);
					$result = model()->query($sql, 'update');
					if (!$result) {
						throw new \Exception('库存更新失败');
						lib\logging::write(var_export($sql, true));
					}
				}
			}
            //解冻预存款
            $pd_amount = floatval($order_info['pd_amount']);
            if ($pd_amount > 0) {
                $data_pd = array();
				$data_pd['uniacid'] = 1;
                $data_pd['uid'] = $order_info['uid'];
                $data_pd['member_name'] = $order_info['member_name'];
                $data_pd['amount'] = $pd_amount;
                $data_pd['order_sn'] = $order_sn;
                logic('predeposit')->changePd('order_cancel', $data_pd);
            }
            //更新订单信息
            $update_order = array('order_state' => ORDER_STATE_CANCEL);
			
            $update = $this->edit(array('order_sn' => $order_sn), $update_order);
            if (!$update) {
				return callback(true, '保存失败');
            }
			if (!empty($order_info['extend_order_common'])) {
				$order_common = $order_info['extend_order_common'];
			} else {
				$order_common = model('shop_order_common')->where(array('order_sn' => $order_sn))->find();
			}
			model('voucher')->edit(array('voucher_id' => $order_common['voucher_id']), array('voucher_state' => 1));
			logic('yewu')->update_goods_other_state($order_sn, ORDER_STATE_CANCEL);
			logic('yewu')->update_goods_commission_state($order_sn, ORDER_STATE_CANCEL);
            //添加订单日志
            $data = array();
            $data['order_id'] = $order_info['order_id'];
            $data['log_role'] = $role;
            $data['log_msg'] = '取消了订单';
            $data['log_user'] = $user;
            if ($msg) {
                $data['log_msg'] .= ' ( ' . $msg . ' )';
            }
            $data['log_orderstate'] = ORDER_STATE_CANCEL;
            $this->addOrderLog($data);
            $this->commit();
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            $this->rollback();
            return callback(false, '操作失败');
        }
    }
	/**
     * 删除订单
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @return array
     */
    public function changeOrderStateDel($order_info, $role, $user = '', $msg = '')
    {
        try {
			if(!$order_info){
				return callback(false, '订单不在');
			}
            $this->beginTransaction();
			$order_sn = $order_info['order_sn'];
            $update_order = array('is_del' => 1);
			
            $update = $this->edit(array('order_sn' => $order_sn), $update_order);
            if (!$update) {
				return callback(true, '保存失败');
            }
            //添加订单日志
            $data = array();
            $data['order_id'] = $order_info['order_id'];
            $data['log_role'] = $role;
            $data['log_msg'] = '删除了订单';
            $data['log_user'] = $user;
            if ($msg) {
                $data['log_msg'] .= ' ( ' . $msg . ' )';
            }
            $data['log_orderstate'] = ORDER_STATE_CANCEL;
            $this->addOrderLog($data);
            $this->commit();
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            $this->rollback();
            return callback(false, '操作失败');
        }
    }
	/**
     * 更改运费
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 运费
     * @return array
     */
    public function changeOrderShipPrice($order_info, $role, $user, $price)
    {
        try {
            $order_id = $order_info['order_id'];
            $data = array();
            $data['shipping_fee'] = abs(floatval($price));
            $data['order_amount'] = $order_info['order_amount'] + $data['shipping_fee'] - $order_info['shipping_fee'];
            $update = $this->edit(array('order_id' => $order_id), $data);
            
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了运费' . '( ' . $price . ' )';
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $this->addOrderLog($data);
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            return callback(false, '操作失败');
        }
    }
    /**
     * 更改运费
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param float $price 运费
     * @return array
     */
    public function changeOrderSpayPrice($order_info, $role, $user, $price)
    {
        try {
            $order_id = $order_info['order_id'];
            $data = array();
            $data['goods_amount'] = abs(floatval($price));
            $data['order_amount'] = $order_info['order_amount'] + ($data['goods_amount'] - $order_info['goods_amount']);
			if($data['order_amount'] < 0.01){
				return callback(true, '订单总额不能小于0.01元');
			}
            $update = $this->edit(array('order_id' => $order_id), $data);
            
            //记录订单日志
            $data = array();
            $data['order_id'] = $order_id;
            $data['log_role'] = $role;
            $data['log_user'] = $user;
            $data['log_msg'] = '修改了运费' . '( ' . $price . ' )';
            $data['log_orderstate'] = $order_info['payment_code'] == 'offline' ? ORDER_STATE_PAY : ORDER_STATE_NEW;
            $this->addOrderLog($data);
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            return callback(false, '操作失败');
        }
    }
	/**
     * 发货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @return array
     */
    public function changeOrderSend($order_info, $role, $user = '', $post = array())
    {
        $order_sn = $order_info['order_sn'];
        try {
            $data = array();
            $data['deliver_explain'] = $post['deliver_explain'];
            $data['shipping_express_id'] = intval($post['shipping_express_id']);
            $data['shipping_time'] = TIMESTAMP;
            $condition = array();
            $condition['order_sn'] = $order_sn;
            $update = model('shop_order_common')->edit($data, $condition);
            if (!$update) {
                return callback(false, '操作失败');
            }
            $data = array();
            $data['shipping_code'] = isset($post['shipping_code']) ? $post['shipping_code'] : '';
            $data['order_state'] = ORDER_STATE_SEND;
            $data['delay_time'] = TIMESTAMP;
            $update = $this->edit($condition, $data);
            if (!$update) {
                return callback(false, '操作失败');
            }
			logic('yewu')->update_goods_other_state($order_sn, ORDER_STATE_SEND);
			logic('yewu')->update_goods_commission_state($order_sn, ORDER_STATE_SEND);
        } catch (\Exception $e) {
            return callback(false, $e->getMessage());
        }
        //添加订单日志
        $data = array();
        $data['order_id'] = $order_info['order_id'];
        $data['log_role'] = 'seller';
        $data['log_user'] = $user;
        $data['log_msg'] = '发出了货物 ( 编辑了发货信息 )';
        $data['log_orderstate'] = ORDER_STATE_SEND;
        $this->addOrderLog($data);       
        return callback(true, '操作成功');
    }
	/**
     * 收货
     * @param array $order_info
     * @param string $role 操作角色 buyer、seller、admin、system 分别代表买家、商家、管理员、系统
     * @param string $user 操作人
     * @param string $msg 操作备注
     * @return array
     */
    public function changeOrderStateReceive($order_info, $role = 'buyer', $user = '', $msg = '')
    {
        try {
            $member_id = $order_info['uid'];
            $order_sn = $order_info['order_sn'];
            //更新订单状态
            $update_order = array();
            $update_order['finnshed_time'] = TIMESTAMP;
            $update_order['order_state'] = ORDER_STATE_SUCCESS;
            $update = $this->edit(array('order_sn' => $order_sn), $update_order);
            if (!$update) {
				return callback(false, '保存失败');
            }
            if (empty($order_info['is_points'])) {
                logic('store_bill')->send_supply_commission($order_info);
			    model('points_log')->savePointsLog('order', array('pl_memberid' => $order_info['uid'], 'pl_membername' => $order_info['member_name'], 'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn']), true);
            }
			//logic('yewu')->add_distributer($member_id);//购买成为分销商
			//logic('yewu')->upgrade_level_deal($member_id);
			
			//logic('public_yewu')->deal_public($member_id);
			logic('yewu')->update_goods_other_state($order_sn, ORDER_STATE_SUCCESS);
			logic('yewu')->update_goods_commission_state($order_sn, ORDER_STATE_SUCCESS);
            //添加订单日志
            $data = array();
            $data['order_id'] = $order_info['order_id'];
            $data['log_role'] = $role;
            $data['log_msg'] = '签收了货物';
            $data['log_user'] = $user;
            if ($msg) {
                $data['log_msg'] .= ' ( ' . $msg . ' )';
            }
            $data['log_orderstate'] = ORDER_STATE_SUCCESS;
            $this->addOrderLog($data);
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            return callback(false, $e->getMessage());
        }
    }
	
	/**
	订单统计
	*/
	public function getTongjiIndex($condition = array(), $field = '*', $group = ''){
		$list = $this->where($condition)->field($field)->group($group)->select();
		return array('list' => $list);
	}
}