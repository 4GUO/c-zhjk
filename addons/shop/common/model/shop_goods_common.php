<?php
namespace model;
use base;
class shop_goods_common extends base\model
{
    protected $tableName = 'fxy_shop_goods_common';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null, $group = '') {
		if(!empty($condition['goods_name'])){
			$condition2 = array_merge($condition, array('goods_jingle' => $condition['goods_name']));
			unset($condition2['goods_name']);
		}
		if($page && $get_p){
			if(!empty($condition['goods_name'])){
				$total = $this->where($condition, $condition2)->total();
			}else{
				$total = $this->where($condition)->total();
			}
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			if(!empty($condition['goods_name'])){
				if ($group) {
					$list = $this->where($condition, $condition2)->field($field)->order($order)->limit($limitpage, $page)->group($group)->select();
				} else {
					$list = $this->where($condition, $condition2)->field($field)->order($order)->limit($limitpage, $page)->select();
				}
			}else{
				if ($group) {
					$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->group($group)->select();
				} else {
					$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
				}
			}
			$hasmore = $total > $get_p * $page ? true : false;
		}else{
			$totalpage = 0;
			if(!empty($condition['goods_name'])){
				if ($group) {
					$list = $this->where($condition, $condition2)->field($field)->order($order)->group($group)->select();
				} else {
					$list = $this->where($condition, $condition2)->field($field)->order($order)->select();
				}
				
			}else{
				if ($group) {
					$list = $this->where($condition)->field($field)->order($order)->group($group)->select();
				} else {
					$list = $this->where($condition)->field($field)->order($order)->select();
				}
			}
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
	public function getInfo($condition, $field = '*')
    {
        return $common_info = $this->field($field)->where($condition)->find();
    }
	public function getInfoByID($goods_commonid)
    {
		$common_info = $this->where(array('goods_commonid' => $goods_commonid))->find();
        return $common_info;
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
		return $this->where($condition)->delete();
	}
}