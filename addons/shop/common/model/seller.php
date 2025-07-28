<?php
namespace model;
use base;
class seller extends base\model
{
    protected $tableName = 'seller';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null, $having = ''){
		if($page && $get_p){
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->having($having)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		}else{
			$totalpage = 0;
			$list = $this->where($condition)->having($having)->field($field)->order($order)->select();
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
	/**
	 * 根据经纬度获取位置信息
     *
     */
    public function get_gps_seller_list($condition, $latitude, $longitude, $fields = '*', $page = null, $get_p = null, $limit_mile = 3, $all = 0) {
		$having = array();
		$order = '';
		if (empty($all)) {
			if (isset($limit_mile) && !empty($limit_mile) && floatval($limit_mile > 0)) {
				$having['distance <='] = floatval($limit_mile) * 1000;
			}
		}
		if ($latitude && $longitude) {
			$field = 'ROUND(6378.138*2*ASIN(SQRT(POW(SIN((' . $latitude . '*PI()/180-lat*PI()/180)/2),2)+COS(' . $latitude . '*PI()/180)*COS(lat*PI()/180)*POW(SIN((' . $longitude . '*PI()/180-lon*PI()/180)/2),2)))*1000) AS distance';
			$fields = $fields . ',' . $field;
			$order = 'ORDER BY distance ASC';
		}
		$result = $this->getList($condition, $fields, $order, $page, $get_p, $having);
		return $result;
    }
}