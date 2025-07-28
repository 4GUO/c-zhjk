<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_cart extends base\model
{
    protected $tableName = 'fxy_shop_cart';
    /**
     * 购物车商品总金额
     */
    private $cart_all_price = 0;
    /**
     * 购物车商品总数
     */
    private $cart_goods_num = 0;
    /**
     * 取属性值魔术方法
     *
     * @param string $name
     */
    public function __get($name)
    {
        return $this->{$name};
    }
    /**
     * 检查购物车内商品是否存在
     *
     * @param
     */
    public function checkCart($condition = array())
    {
        return $this->where($condition)->find();
    }
    /**
     * 会员购物车内商品数
     *
     * @param int $memberId
     * @return int
     */
    public function countCartByMemberId($memberId)
    {
        return (int) $this->where(array('uid' => (int) $memberId))->count();
    }
    /**
     * 取得 单条购物车信息
     * @param unknown $condition
     * @param string $field
     */
    public function getInfo($condition = array(), $field = '*')
    {
        return $this->field($field)->where($condition)->find();
    }
    /**
     * 添加数据库购物车
     *
     * @param unknown_type $goods_info
     * @param unknown_type $quantity
     * @return unknown
     */
    public function add($goods_info, $quantity)
    {
        //验证购物车商品是否已经存在
        $condition = array();
        $condition['goods_id'] = $goods_info['goods_id'];
        $condition['uid'] = $goods_info['uid'];
        if (isset($goods_info['bl_id'])) {
            $condition['bl_id'] = $goods_info['bl_id'];
        } else {
            $condition['bl_id'] = 0;
        }
        $check_cart = $this->checkCart($condition);
        if (!empty($check_cart)) {
            return true;
        }
        $array = array();
        $array['uid'] = $goods_info['uid'];
        $array['store_id'] = $goods_info['store_id'];
        $array['goods_id'] = $goods_info['goods_id'];
        $array['goods_name'] = $goods_info['goods_name'];
        $array['goods_price'] = $goods_info['goods_price'];
        $array['goods_num'] = $quantity;
        $array['goods_image'] = $goods_info['goods_image'];
        $array['store_name'] = $goods_info['store_name'];
        $array['bl_id'] = isset($goods_info['bl_id']) ? $goods_info['bl_id'] : 0;
		//更改购物车总商品数和总金额
        $this->getCartNum(array('uid' => $goods_info['uid']));
        return $this->insert($array);
    }
    /**
     * 更新购物车
     *
     * @param	array	$param 商品信息
     */
    public function edit($data, $condition)
    {
        $result = $this->where($condition)->update($data);
        if ($result) {
            $this->getCartNum(array('uid' => isset($condition['uid']) ? $condition['uid'] : 0));
        }
        return $result;
    }
    /**
     * 购物车列表
     *
     * @param unknown_type $condition
     * @param int $limit
     */
    public function getList($condition = array(), $limit = '')
    {
        $cart_list = $this->where($condition)->limit($limit)->select();
        $cart_list = is_array($cart_list) ? $cart_list : array();
        //顺便设置购物车商品数和总金额
        $this->cart_goods_num = count($cart_list);
        $cart_all_price = 0;
        if (is_array($cart_list)) {
            foreach ($cart_list as $val) {
                $cart_all_price += $val['goods_price'] * intval($val['goods_num']);
            }
        }
        $this->cart_all_price = priceFormat($cart_all_price);
        return !is_array($cart_list) ? array() : $cart_list;
    }
    /**
     * 删除购物车商品
     *
     * @param unknown_type $condition
     */
    public function del($condition = array())
    {
        $result = $this->where($condition)->delete();
        //重新计算购物车商品数和总金额
        if ($result) {
            $this->getCartNum(array('uid' => $condition['uid']));
        }
        return $result;
    }
    /**
     * 清空购物车
     *
     * @param string $type 存储类型 db,cookie
     * @param unknown_type $condition
     */
    public function clearCart($condition = array())
    {
        
    }
    /**
     * 计算购物车总商品数和总金额
     * @param array $condition 只有登录后操作购物车表时才会用到该参数
     */
    public function getCartNum($condition = array())
    {
        $cart_all_price = 0;
        $cart_goods = $this->getList($condition);
        $this->cart_goods_num = count($cart_goods);
        if (!empty($cart_goods) && is_array($cart_goods)) {
            foreach ($cart_goods as $val) {
                $cart_all_price += $val['goods_price'] * $val['goods_num'];
			}
        }
        return $this->cart_goods_num;
    }
}