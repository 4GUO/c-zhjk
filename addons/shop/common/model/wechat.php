<?php
/**
 * 微信配置设置
 *
 */
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class wechat extends base\model
{
    protected $tableName = 'weixin_wechat';
    /**
     * 获取某条信息
	 * @param string $table 表名
     * @param array $condition
     * @return array
     */
    public function getInfoOne($table, $condition, $field = '*') {
        return $this->table($table)->field($field)->where($condition)->find();
    }
	
	/**
	 * 查询信息列表
     *
	 * @param string $table 表名
	 * @param array $condition 查询条件
	 * @param int $page 分页数
	 * @param string $order 排序
	 * @param string $field 字段
	 * @param string $limit 取多少条
     * @return array
	 */
	
	public function getInfoList($table, $condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
		if($page && $get_p){
			$total = $this->table($table)->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->table($table)->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
		}else{
			$totalpage = 0;
			$list = $this->table($table)->where($condition)->field($field)->order($order)->select();
		}
		return array('list' => $list, 'totalpage' => $totalpage);
    }

    /**
     * 更新某条信息
	 * @param string $table 表名
     * @param array $condition
     * @return int
     */
    public function editInfo($table, $update, $condition = array()){
        return $this->table($table)->where($condition)->update($update);
    }
	
	/*
	 * 添加某条信息
     * @param string $table 表名
	 * @param array $param 数据信息
	 * @return bool
	 */	
	public function addInfo($table, $param){
        return $this->table($table)->insert($param);
    }
	
	/*
	 * 删除某条信息
     * @param string $table 表名
	 * @param array $param 数据信息
	 * @return bool
	 */	
	public function delInfo($table, $condition) {
        return $this->table($table)->where($condition)->delete();
    }
	
	/*
	 * 批量添加新数据
     * @param string $table 表名
	 * @param array $param 数据信息
	 * @return bool
	 */	
	public function addAll($table, $param){
        return $this->table($table)->insertAll($param);
    }

}
