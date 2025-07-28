<?php
namespace model;
use base;
class store_commiss_order extends base\model
{
    protected $tableName = 'fxy_store_commiss_order';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null)
	{
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
	public function getInfo($condition, $field = '*')
    {
        return $goods_info = $this->field($field)->where($condition)->find();
    }
	public function add($data){
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array()){
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition)
	{
		$this->where($condition)->delete();
	}
}