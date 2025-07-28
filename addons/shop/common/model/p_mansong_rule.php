<?php
namespace model;
use base;
class p_mansong_rule extends base\model
{
    protected $tableName = 'p_mansong_rule';
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
     * 读取满即送规则列表
     * @param array $mansong_id 查询条件
     * @param int $page 分页数
     * @param string $order 排序
     * @param string $field 所需字段
     * @return array 满即送套餐列表
     *
     */
    public function getMansongRuleListByID($mansong_id) {
        $condition = array();
        $condition['mansong_id'] = $mansong_id;
        $mansong_rule_list = $this->where($condition)->order('price desc')->select();
        if (!empty($mansong_rule_list)) {
            for ($i = 0, $j = count($mansong_rule_list); $i < $j; $i++) {
                $goods_id = intval($mansong_rule_list[$i]['goods_id']);
                if (!empty($goods_id)) {
					$goods_common = model('shop_goods_common')->getInfo(array('goods_commonid' => $goods_id), 'goods_name,goods_image');
					$goods = model('shop_goods')->getInfo(array('goods_commonid' => $goods_id), 'goods_id,goods_storage', 'goods_id asc');
					$goods_info = array_merge($goods_common, $goods);
                    if (!empty($goods_info)) {
                        if (empty($mansong_rule_list[$i]['mansong_goods_name'])) {
                            $mansong_rule_list[$i]['mansong_goods_name'] = $goods_info['goods_name'];
                        }
                        $mansong_rule_list[$i]['goods_image'] = tomedia($goods_info['goods_image']);
                        $mansong_rule_list[$i]['goods_image_url'] = tomedia($goods_info['goods_image']);
                        $mansong_rule_list[$i]['goods_storage'] = $goods_info['goods_storage'];
                        $mansong_rule_list[$i]['goods_id'] = $goods_id;
						$mansong_rule_list[$i]['goods_goods_id'] = $goods_info['goods_id'];
                        $mansong_rule_list[$i]['goods_url'] = 'javascript:;';
                    }
                }
            }
        }
        return $mansong_rule_list;
    }
}