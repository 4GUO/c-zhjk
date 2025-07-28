<?php
namespace model;
use base;
class shop_goods extends base\model
{
    protected $tableName = 'fxy_shop_goods';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null, $group = ''){
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
	public function getGoodsSum($condition, $field)
    {
        $r = $this->field('SUM(' . $field . ') AS sum')->where($condition)->find();
		return $r['sum'];
    }
	public function getInfo($condition, $field = '*', $order = '')
    {
        return $goods_info = $this->field($field)->where($condition)->order($order)->find();
    }
	/**
     * 计算商品库存
     *
     * @param array $goods_list        	
     * @return array|boolean
     */
    public function calculateStorage($goods_list)
    {
        // 计算库存
        if (!empty($goods_list)) {
            $goodsid_array = array();
			$goodscommonid_array = array();
            foreach ($goods_list as $value) {
                $goodscommonid_array[] = $value['goods_commonid'];
            }
            $goods_storage = $this->getList(array('goods_commonid' => $goodscommonid_array), 'goods_storage,goods_commonid,goods_id,goods_salenum');
			$goods_storage_list = $goods_storage['list'];
            $storage_array = array();
            foreach ($goods_storage_list as $val) {
				if(!isset($storage_array[$val['goods_commonid']]['sum'])){
					$storage_array[$val['goods_commonid']]['sum'] = 0;
				}
                $storage_array[$val['goods_commonid']]['sum'] += $val['goods_storage'];
                $storage_array[$val['goods_commonid']]['goods_id'] = $val['goods_id'];
				if(!isset($storage_array[$val['goods_commonid']]['goods_salenum'])){
					$storage_array[$val['goods_commonid']]['goods_salenum'] = 0;
				}
				$storage_array[$val['goods_commonid']]['goods_salenum'] += $val['goods_salenum'];
            }
            return $storage_array;
        } else {
            return false;
        }
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