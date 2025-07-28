<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class pd_recharge extends base\model
{
    protected $tableName = 'pd_recharge';
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
}