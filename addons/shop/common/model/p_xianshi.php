<?php
namespace model;
use base;
class p_xianshi extends base\model
{
    protected $tableName = 'p_xianshi';
	const XIANSHI_STATE_NORMAL = 1;
    const XIANSHI_STATE_CLOSE = 2;
    const XIANSHI_STATE_CANCEL = 3;
    private $xianshi_state_array = array(0 => '全部', self::XIANSHI_STATE_NORMAL => '正常', self::XIANSHI_STATE_CLOSE => '已结束', self::XIANSHI_STATE_CANCEL => '管理员关闭');
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
		$result = $this->getXianshiExtendInfo($result);
		return $result;
	}
	public function add($data){
		$data['state'] = self::XIANSHI_STATE_NORMAL;
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
     * 限时折扣状态数组
     *
     */
    public function getXianshiStateArray() {
        return $this->xianshi_state_array;
    }
	/**
     * 获取限时折扣扩展信息，包括状态文字和是否可编辑状态
     * @param array $xianshi_info
     * @return string
     *
     */
    public function getXianshiExtendInfo($xianshi_info) {
        if ($xianshi_info['end_time'] > TIMESTAMP) {
            $xianshi_info['xianshi_state_text'] = $this->xianshi_state_array[$xianshi_info['state']];
        } else {
            $xianshi_info['xianshi_state_text'] = '已结束';
        }
        if ($xianshi_info['state'] == self::XIANSHI_STATE_NORMAL && $xianshi_info['end_time'] > TIMESTAMP) {
            $xianshi_info['editable'] = true;
        } else {
            $xianshi_info['editable'] = false;
        }
        return $xianshi_info;
    }
}