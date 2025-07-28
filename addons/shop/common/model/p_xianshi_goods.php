<?php
namespace model;
use base;
class p_xianshi_goods extends base\model
{
    protected $tableName = 'p_xianshi_goods';
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
	public function getInfo($condition = array(), $field = '*') {
		$result = $this->where($condition)->field($field)->find();
		$result = $this->getXianshiGoodsExtendInfo($result);
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
     * 读取限时折扣商品列表
     * @param array $condition 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @param int $limit 个数限制
     * @return array 限时折扣商品列表
     *
     */
    public function getGoodsExtendList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
        $result = $this->getList($condition, $field, $order, $page, $get_p);
		$xianshi_goods_list = $result['list'];
        if (!empty($xianshi_goods_list)) {
            for ($i = 0, $j = count($xianshi_goods_list); $i < $j; $i++) {
                $xianshi_goods_list[$i] = $this->getXianshiGoodsExtendInfo($xianshi_goods_list[$i]);
            }
        }
        return $xianshi_goods_list;
    }
	/**
     * 获取限时折扣商品扩展信息
     * @param array $xianshi_info
     * @return array 扩展限时折扣信息
     *
     */
    public function getXianshiGoodsExtendInfo($xianshi_info) {
		if (!$xianshi_info) {
			return array();
		}
        $xianshi_info['goods_url'] = 'javascript:;';
        $xianshi_info['image_url'] = $xianshi_info['goods_image'];
        $xianshi_info['xianshi_price'] = priceFormat($xianshi_info['xianshi_price']);
        $xianshi_info['xianshi_discount'] = number_format($xianshi_info['xianshi_price'] / $xianshi_info['goods_price'] * 10, 1) . '折';
		$xianshi_info['xianshi_title'] = !empty($xianshi_info['xianshi_title']) ? $xianshi_info['xianshi_title'] : '限时折扣';
        return $xianshi_info;
    }
}