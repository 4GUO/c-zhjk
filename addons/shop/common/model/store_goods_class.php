<?php
namespace model;
use base;
class store_goods_class extends base\model
{
    protected $tableName = 'store_goods_class';
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
	/**
     * 取分类列表 用于mobile
     *
     * $param int $storeId 店铺ID
     * @return array 数组类型的返回结果
     */
    public function getStoreGoodsClassPlainList($storeId) {
        $data = array();
        $goods_class_list = (array) $this->getShowTreeList($storeId);
        foreach ($goods_class_list as $v) {
            $data[] = array('id' => $v['stc_id'], 'name' => $v['stc_name'], 'level' => 1, 'pid' => 0);
			if (!empty($v['children'])) {
				foreach ((array) $v['children'] as $vv) {
					$data[] = array('id' => $vv['stc_id'], 'name' => $vv['stc_name'], 'level' => 2, 'pid' => $v['stc_id']);
				}
			}
        }
        return $data;
    }
	/**
     * 取分类列表(前台店铺页左侧用到)
     *
     * $param int $store_id 店铺ID
     * @return array 数组类型的返回结果
     */
    public function getShowTreeList($store_id) {
		$show_class = array();
		$result = $this->getList(array('store_id' => $store_id, 'stc_state' => '1'));
		$class_list = $result['list'];
		if (is_array($class_list) && !empty($class_list)) {
			foreach ($class_list as $val) {
				if ($val['stc_parent_id'] == 0) {
					$show_class[$val['stc_id']] = $val;
				} else {
					if (isset($show_class[$val['stc_parent_id']])) {
						$show_class[$val['stc_parent_id']]['children'][] = $val;
					}
				}
			}
		}
        return $show_class;
    }
}