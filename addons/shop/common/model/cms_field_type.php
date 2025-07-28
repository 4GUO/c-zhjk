<?php
namespace model;
use base;
class cms_field_type extends base\model
{
    protected $tableName = 'cms_field_type';
    public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
		if ($page && $get_p) {
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		} else {
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
}