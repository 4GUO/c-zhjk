<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class shop_order_log extends base\model
{
    protected $tableName = 'fxy_shop_order_log';
    public function getInfo($condition = array(), $fields = '*', $order = '')
    {
        return $this->where($condition)->field($fields)->order($order)->find();
    }
    public function add($data)
    {
        return $this->insert($data);
    }
    /**
     * 更改订单信息
     *
     * @param unknown_type $data
     * @param unknown_type $condition
     */
    public function edit($condition, $data)
    {
        return $this->where($condition)->update($data);
    }
}