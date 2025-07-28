<?php
namespace model;
use base;
class area extends base\model
{
    protected $tableName = 'fxy_area';
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
	public function getTopAreaName($area_id, $area_name = ''){
        $info_parent = $this->getInfo(array('area_id' => $area_id), 'area_name,area_parent_id');
        if ($info_parent) {
            return $this->getTopAreaName($info_parent['area_parent_id'], $info_parent['area_name']) . ' ' . $info_parent['area_name'];
        }
    }
	
	public function getAreaArrayForJson(){
        $data = $this->_getAllArea();
        $arr = array();
        foreach ($data['children'] as $k => $v) {
            foreach ($v as $vv) {
                $arr[$k][] = array($vv, $data['name'][$vv]);
            }
        }
        return $arr;
    }
	public function getAreas(){
        return $this->_getAllArea();
    }
	/**
     * 获取获取市级id对应省级id的数组
     *
     * @return array 键为市级id 值为省级id
     */
    public function getCityProvince()
    {
        $data = $this->_getAllArea();
        $arr = array();
        foreach ($data['parent'] as $k => $v) {
            if ($v && $data['parent'][$v] == 0) {
                $arr[$k] = $v;
            }
        }
        return $arr;
    }
	private function _getAllArea()
    {
        $data = array();
        $result = $this->getList();
		$area_all_array = $result['list'];
        foreach ((array) $area_all_array as $a) {
            $data['name'][$a['area_id']] = $a['area_name'];
            $data['parent'][$a['area_id']] = $a['area_parent_id'];
            $data['children'][$a['area_parent_id']][] = $a['area_id'];
            if ($a['area_deep'] == 1 && $a['area_region']) {
                $data['region'][$a['area_region']][] = $a['area_id'];
            }
        }
        return $data;
    }
}