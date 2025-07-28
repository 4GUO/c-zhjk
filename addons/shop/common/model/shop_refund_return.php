<?php
/**
 * 退款退货
 *
 */
namespace model;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_refund_return extends base\model
{
    protected $tableName = 'shop_refund_return';
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
	public function getInfo($condition = array(), $field = '*'){
		$result = $this->where($condition)->field($field)->find();
		return $result;
	}
	public function add($data){
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array()){
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition = array()){
		$result = $this->where($condition)->delete();
		return $result;
	}
	public function makeRefundSn($order_id)
    {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (double) microtime() * 1000) . sprintf('%03d', (int) $order_id % 1000);
    }
	public function _orderState($refund_info) {
		switch ($refund_info['refund_state']) {
			case 1 :
				$refund_state = '处理中...';
				break;
			
			case 2 :
				$refund_state = '待管理员处理';
				break;
			
			case 3 :
				$refund_state = '已完成';
				break;
			case 4 :
				$refund_state = '已驳回';
				break;
		}
		return $refund_state;
	}
}