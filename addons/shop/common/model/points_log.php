<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class points_log extends base\model
{
    protected $tableName = 'points_log';
    public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null){
		if($page && $get_p){
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		}else{
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
    public function getInfo($condition, $order = '')
    {
        $info = $this->where($condition)->order($order)->find();
        return $info;
    }
    public function add($param)
    {
        return $this->insert($param);
    }
    public function edit($condition, $update)
    {
        return $this->where($condition)->update($update);
    }
    public function del($condition)
    {
        return $this->where($condition)->delete();
    }
	function savePointsLog($stage, $insertarr, $if_repeat = true)
    {
        if (!$insertarr['pl_memberid']) {
            return false;
        }
        //记录原因文字
        switch ($stage) {
            case 'points_reg':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '注册会员';
                }
                $insertarr['pl_points'] = intval(config('points_reg'));
                break;
            case 'points_login':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '会员登录';
                }
                $insertarr['pl_points'] = intval(config('points_login'));
                break;
            case 'points_comments':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '评论商品';
                }
                $insertarr['pl_points'] = intval(config('points_comments'));
                break;
            case 'order':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '订单' . $insertarr['order_sn'] . '购物消费';
                }
                $insertarr['pl_points'] = 0;
                if (!empty($insertarr['orderprice']) && !empty(config('points_orderrate'))) {
                    $insertarr['pl_points'] = intval(strval($insertarr['orderprice'] * config('points_orderrate')));
                    if (!empty(config('points_ordermax')) && $insertarr['pl_points'] > intval(config('points_ordermax'))) {
                        $insertarr['pl_points'] = intval(config('points_ordermax'));
                    }
                }
                //订单添加赠送积分列
                $obj_order = model('shop_order_common');
                $obj_order->edit('order_pointscount=order_pointscount+' . $insertarr['pl_points'], array('order_sn' => $insertarr['order_sn']));
                break;
			case 'vr_order':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '订单' . $insertarr['order_sn'] . '购物消费';
                }
                $insertarr['pl_points'] = 0;
                if (!empty($insertarr['orderprice']) && !empty(config('points_orderrate'))) {
                    $insertarr['pl_points'] = intval(strval($insertarr['orderprice'] * config('points_orderrate')));
                    if ($insertarr['pl_points'] > intval(config('points_ordermax'))) {
                        $insertarr['pl_points'] = intval(config('points_ordermax'));
                    }
                }
                break;
            case 'system':
                break;
            case 'pointorder':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '兑换礼品信息' . $insertarr['point_ordersn'] . '消耗积分';
                }
                break;
            case 'points_signin':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '签到得到积分';
                }
                break;
            case 'points_invite':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '邀请新会员[' . $insertarr['invited'] . ']注册';
                }
                $insertarr['pl_points'] = intval(config('points_invite'));
                break;
			case 'points_renwu':
                if (empty($insertarr['pl_desc'])) {
                    $insertarr['pl_desc'] = '完成任务';
                }
                $insertarr['pl_points'] = intval(config('points_renwu'));
                break;
            case 'other':
                break;
        }
        $save_sign = true;
        if ($if_repeat == false) {
            //检测是否有相关信息存在，如果没有，入库
            $condition['pl_memberid'] = $insertarr['pl_memberid'];
            $condition['pl_stage'] = $stage;
            $log_array = $this->getInfo($condition);
            if (!empty($log_array)) {
                $save_sign = false;
            }
        }
        if ($save_sign == false) {
            return true;
        }
        //新增日志
        $value_array = array();
        $value_array['pl_memberid'] = $insertarr['pl_memberid'];
        $value_array['pl_membername'] = $insertarr['pl_membername'];
        if (!empty($insertarr['pl_adminid'])) {
            $value_array['pl_adminid'] = $insertarr['pl_adminid'];
        }
        if (!empty($insertarr['pl_adminname'])) {
            $value_array['pl_adminname'] = $insertarr['pl_adminname'];
        }
        $value_array['pl_points'] = $insertarr['pl_points'];
        $value_array['pl_addtime'] = time();
        $value_array['pl_desc'] = $insertarr['pl_desc'];
        $value_array['pl_stage'] = $stage;
        $value_array['adddate'] = strtotime(date('Ymd'));
		//var_dump($value_array);exit;
        $result = false;
        if (!empty($value_array['pl_points'])) {
            $result = $this->add($value_array);
        }
        if ($result) {
            //更新member内容
            $obj_member = model('member');
            $obj_member->edit(array('uid' => $insertarr['pl_memberid']), 'points=points+' . $insertarr['pl_points']);
            return true;
        } else {
            return false;
        }
    }
}