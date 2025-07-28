<?php
namespace model;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_vr_order extends base\model
{
    protected $tableName = 'fxy_shop_vr_order';
    /**
     * 取单条订单信息
     *
     * @param unknown_type $condition
     * @return unknown
     */
    public function getInfo($condition = array(), $fields = '*', $order = '', $group = '')
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
		//组装商品列表
		$goods = model('shop_goods')->getInfo(array('goods_id' => $order_info['goods_id']), 'goods_commonid');
		$order_goods_list[] = array(
			'order_sn' => $order_info['order_sn'],
			'uid' => $order_info['buyer_id'],
			'goods_commonid' => $goods['goods_commonid'],
			'goods_id' => $order_info['goods_id'],
			'goods_name' => $order_info['goods_name'],
			'goods_num' => $order_info['goods_num'],
			'goods_pay_price' => $order_info['goods_price'],
			'goods_price' => $order_info['goods_price'],
			'goods_image' => $order_info['goods_image'],
			'gc_id' => $order_info['gc_id'],
			'goods_costprice' => $order_info['goods_costprice'],
		);
		$order_info['extend_order_goods'] = $order_goods_list;
        return $order_info;
    }
    
    
    /**
     * 取得订单列表(所有)
     * @param unknown $condition
     * @param string $page
     * @param string $field
     * @param string $order
     * @param string $limit
     * @return Ambigous <multitype:boolean Ambigous <string, mixed> , unknown>
     */
    public function getList($condition, $field = '*', $order = 'order_id desc', $page = null, $get_p = null)
    {
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
		$goods_ids = array();
        foreach ($list as $order) {
            if (isset($order['order_state'])) {
                $order['state_desc'] = $this->_orderState($order);
            }
            if (isset($order['payment_code'])) {
                $order['payment_name'] = orderPaymentName($order['payment_code']);
            }
            $order_list[$order['order_sn']] = $order;
			$goods_ids[] = $order['goods_id'];
        }
        if (empty($order_list)) {
            $order_list = $list;
        }
		//组装商品信息
		$goods_tmp = model('shop_goods')->getList(array('goods_id' => $goods_ids), 'goods_id,goods_commonid');
		$goods_list = array();
		foreach ($goods_tmp['list'] as $v) {
			$goods_list[$v['goods_id']] = $v;
		}
		$order_goods_list = array();
		foreach ($order_list as $order_info) {
			$goods = $goods_list[$order_info['goods_id']];
			$order_goods_list[] = array(
				'order_sn' => $order_info['order_sn'],
				'uid' => $order_info['buyer_id'],
				'goods_commonid' => $goods['goods_commonid'],
				'goods_id' => $order_info['goods_id'],
				'goods_name' => $order_info['goods_name'],
				'goods_num' => $order_info['goods_num'],
				'goods_pay_price' => $order_info['goods_price'],
				'goods_price' => $order_info['goods_price'],
				'goods_image' => $order_info['goods_image'],
				'gc_id' => $order_info['gc_id'],
				'goods_costprice' => $order_info['goods_costprice'],
			);
		}
        foreach ($order_goods_list as $key => $value) {
			$order_list[$value['order_sn']]['extend_order_goods'][] = $value;
		}
		return array('list' => $order_list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
    }
	/**
     * 取得订单状态文字输出形式
     *
     * @param array $order_info 订单数组
     * @return string $order_state 描述输出
     */
    private function _orderState($order_info)
    {
        switch ($order_info['order_state']) {
            case ORDER_STATE_CANCEL:
                $order_state = '已取消';
                break;
            case ORDER_STATE_NEW:
                $order_state = '待付款';
                break;
            case ORDER_STATE_PAY:
                $order_state = '已支付';
                break;
            case ORDER_STATE_SUCCESS:
                $order_state = '已完成';
                break;
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
     * @param string $operate
     * @param array $order_info
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
            //商家取消订单
            case 'store_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
            //平台取消订单
            case 'system_cancel':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
            //平台收款
            case 'system_receive_pay':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
			//订单完成
            case 'receive':
                $state = $order_info['order_state'] == ORDER_STATE_PAY;
                break;
            //支付
            case 'payment':
                $state = $order_info['order_state'] == ORDER_STATE_NEW;
                break;
            //评价
            case 'evaluation':
                $state = $order_info['evaluation_state'] == 0 && $order_info['use_state'];
                break;
            //买家退款
            case 'refund':
                $state = false;
                $code_list = empty($order_info['code_list']) ? array() : $order_info['code_list'];
                //没有使用的兑换码列表
                if (!empty($code_list) && is_array($code_list)) {
                    if ($order_info['vr_indate'] > TIMESTAMP) {
                        //有效期内的能退款
                        $state = true;
                    }
                    if ($order_info['vr_invalid_refund'] == 1 && $order_info['vr_indate'] + 60 * 60 * 24 * CODE_INVALID_REFUND > TIMESTAMP) {
                        //兑换码过期后可退款
                        $state = true;
                    }
                }
                break;
            //分享
            case 'share':
                $state = true;
                break;
            case 'buyer_del':
                $state = $order_info['order_state'] == ORDER_STATE_CANCEL && $order_info['is_del'] == 0;
                break;
        }
        return $state;
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
            //解冻预存款
            $pd_amount = floatval($order_info['pd_amount']);
            if ($pd_amount > 0) {
                $data_pd = array();
				$data_pd['uniacid'] = 1;
                $data_pd['uid'] = $order_info['buyer_id'];
                $data_pd['member_name'] = $order_info['buyer_name'];
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
			logic('yewu')->update_goods_other_state($order_sn, ORDER_STATE_CANCEL);
			logic('yewu')->update_goods_commission_state($order_sn, ORDER_STATE_CANCEL);
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
            $this->commit();
            return callback(true, '操作成功');
        } catch (\Exception $e) {
            $this->rollback();
            return callback(false, '操作失败');
        }
    }
	/**
     * 完成订单
     * @param int $order_id
     * @return array
     */
    public function changeOrderStateSuccess($order_id)
    {
        $condition = array();
        $condition['vr_state'] = 0;
        $condition['refund_lock'] = array(0, 1);
        $condition['order_id'] = $order_id;
        $condition['vr_indate >'] = TIMESTAMP;
        $order_code_info = $this->getOrderCodeInfo($condition);
        if (empty($order_code_info)) {
			try {
				$this->beginTransaction();
				$update = $this->edit(array('order_id' => $order_id), array('order_state' => ORDER_STATE_SUCCESS, 'finnshed_time' => TIMESTAMP, 'use_state' => 1));
				if (!$update) {
					return callback(false, '保存失败');
				}
				$order_info = $this->getInfo(array('order_id' => $order_id));
				$member_id = $order_info['buyer_id'];
				$order_sn = $order_info['order_sn'];
				
				logic('store_bill')->send_supply_commission($order_info);
				model('points_log')->savePointsLog('vr_order', array('pl_memberid' => $order_info['buyer_id'], 'pl_membername' => $order_info['buyer_name'], 'orderprice' => $order_info['order_amount'], 'order_sn' => $order_info['order_sn']), true);
				//logic('yewu')->add_distributer($member_id);//购买成为分销商
				//logic('yewu')->upgrade_level_deal($member_id);
				
				//logic('public_yewu')->deal_public($member_id);
				logic('yewu')->update_goods_other_state($order_sn, ORDER_STATE_SUCCESS);
				logic('yewu')->update_goods_commission_state($order_sn, ORDER_STATE_SUCCESS);
				$this->commit();
				return callback(true, '操作成功');
			} catch (\Exception $e) {
				$this->rollback();
				return callback(false, '操作失败');
			}
        }
        return callback(false, '操作失败');
    }
	/**
	订单统计
	*/
	public function getTongjiIndex($condition = array(), $field = '*', $group = ''){
		$list = $this->where($condition)->field($field)->group($group)->select();
		return array('list' => $list);
	}
	/**
     * 取得兑换码信息
     * @param unknown $condition
     * @param string $fields
     */
    public function getOrderCodeInfo($condition = array(), $fields = '*')
    {
        return model('vr_order_code')->field($fields)->where($condition)->find();
    }
	/**
     * 更新兑换码
     * @param unknown $data
     * @param unknown $condition
     */
    public function editOrderCode($data, $condition)
    {
        return model('vr_order_code')->where($condition)->update($data);
    }
	/**
     * 取得兑换码列表
     * @param unknown $condition
     * @param string $fields
     */
    public function getOrderCodeList($condition = array(), $fields = '*')
    {
        $result = model('vr_order_code')->getList($condition, $fields);
		$code_list = $result['list'];
        //进一步处理
        if (!empty($code_list)) {
            $i = 0;
            foreach ($code_list as $k => $v) {
                if ($v['vr_state'] == 1) {
                    $content = '已使用，使用时间 ' . date('Y-m-d', $v['vr_usetime']);
                } else {
                    if ($v['vr_state'] == 0) {
                        if ($v['vr_indate'] < TIMESTAMP) {
                            $content = '已过期，过期时间 ' . date('Y-m-d', $v['vr_indate']);
                        } else {
                            $content = '未使用，有效期至 ' . date('Y-m-d', $v['vr_indate']);
                        }
                    }
                }
                if ($v['refund_lock'] == 1) {
                    $content = '退款审核中';
                } else {
                    if ($v['refund_lock'] == 2) {
                        $content = '退款已完成';
                    }
                }
                $code_list[$k]['vr_code_desc'] = $content;
                if ($v['vr_state'] == 0) {
                    $i++;
                }
            }
            $code_list[0]['vr_code_valid_count'] = $i;
        }
        return $code_list;
    }
	/**
     * 生成兑换码
     * @param array $order_info
     * @return int 返回 insert_id
     */
    public function addOrderCode($order_info)
    {
        $vrc_num = model('vr_order_code')->where(array('order_id' => $order_info['order_id']))->total();
        if (!empty($vrc_num) && intval($vrc_num) >= intval($order_info['goods_num'])) {
            return false;
        }
        if (empty($order_info)) {
            return false;
        }
        //均摊后每个兑换码支付金额
        $each_pay_price = number_format($order_info['order_amount'] / $order_info['goods_num'], 2);
        //取得店铺兑换码前缀
        $virtual_code_perfix = rand(100, 999);
        //生成兑换码
        $code_list = $this->_makeVrCode($virtual_code_perfix, $order_info['store_id'], $order_info['buyer_id'], $order_info['goods_num']);
        for ($i = 0; $i < $order_info['goods_num']; $i++) {
            $order_code[$i]['order_id'] = $order_info['order_id'];
            $order_code[$i]['store_id'] = $order_info['store_id'];
            $order_code[$i]['buyer_id'] = $order_info['buyer_id'];
            $order_code[$i]['vr_code'] = $code_list[$i];
            $order_code[$i]['pay_price'] = $each_pay_price;
            $order_code[$i]['vr_indate'] = $order_info['vr_indate'];
            $order_code[$i]['vr_invalid_refund'] = $order_info['vr_invalid_refund'];
        }
        //将因舍出小数部分出现的差值补到最后一个商品的实际成交价中
        //$diff_amount = $order_info['order_amount'] - $each_pay_price * $order_info['goods_num'];
        //$order_code[$i-1]['pay_price'] += $diff_amount;
        return model('vr_order_code')->insertAll($order_code);
    }
	/**
     * 生成兑换码
     * 长度 =3位 + 4位 + 2位 + 3位  + 1位 + 5位随机  = 18位
     * @param string $perfix 前缀
     * @param int $store_id
     * @param int $member_id
     * @param unknown $num
     * @return multitype:string
     */
    private function _makeVrCode($perfix, $store_id, $member_id, $num)
    {
        $perfix .= sprintf('%04d', (int) $store_id * $member_id % 10000) . sprintf('%02d', (int) $member_id % 100) . sprintf('%03d', (double) microtime() * 1000);
        $code_list = array();
        for ($i = 0; $i < $num; $i++) {
            $code_list[$i] = $perfix . sprintf('%01d', (int) $i % 10) . random(5, 1);
        }
        return $code_list;
    }
}