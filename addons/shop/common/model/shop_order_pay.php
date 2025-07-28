<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_order_pay extends base\model
{
    protected $tableName = 'fxy_shop_order_pay';
    public function getInfo($condition = array(), $field = '*')
    {
        return $this->field($field)->where($condition)->find();
    }
    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function edit($data, $condition)
    {
        return $this->where($condition)->update($data);
    }
	/**
     * 插入订单扩展表信息
     * @param array $data
     * @return int 返回 insert_id
     */
    public function add($data)
    {
        return $this->insert($data);
    }
	/**
     * 取得订单扩展表列表
     * @param unknown $condition
     * @param string $fields
     * @param string $limit
     */
    public function getList($condition = array(), $fields = '*', $order = '', $limit = null)
    {
        return $this->field($fields)->where($condition)->order($order)->limit($limit)->select();
    }
}