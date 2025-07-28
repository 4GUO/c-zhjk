<?php
namespace model;
use base;
class kefu_msg_log extends base\model
{
    protected $tableName = 'kefu_msg_log';
	public function getList($condition1 = array(), $condition2 = array(), $field = '*', $order = '', $page = null, $get_p = null)
	{
		if ($page && $get_p) {
			if (!empty($condition2)) {
				$total = $this->explain(false)->where($condition1, $condition2)->total();
			} else {
				$total = $this->where($condition1)->total();
			}
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			if (!empty($condition2)) {
				$list = $this->explain(false)->where($condition1, $condition2)->field($field)->order($order)->limit($limitpage, $page)->select();
			} else {
				$list = $this->where($condition1)->field($field)->order($order)->limit($limitpage, $page)->select();
			}
			$hasmore = $total > $get_p * $page ? true : false;
		} else {
			$totalpage = 0;
			if (!empty($condition2)) {
				$list = $this->where($condition1, $condition2)->field($field)->order($order)->select();
			} else {
				$list = $this->where($condition1)->field($field)->order($order)->select();
			}
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