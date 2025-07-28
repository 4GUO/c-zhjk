<?php
namespace model;
use base;
class account extends base\model
{
    protected $tableName = 'fxy_account';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null){
		if($page && $get_p){
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
		}else{
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
		}
		return array('list' => $list, 'totalpage' => $totalpage);
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
}