<?php
namespace model;
use base;
class p_mansong extends base\model
{
    protected $tableName = 'p_mansong';
	const MANSONG_STATE_NORMAL = 1;
    const MANSONG_STATE_CLOSE = 2;
    const MANSONG_STATE_CANCEL = 3;
    private $mansong_state_array = array(0 => '全部', self::MANSONG_STATE_NORMAL => '正常', self::MANSONG_STATE_CLOSE => '已结束', self::MANSONG_STATE_CANCEL => '管理员关闭');
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
	public function getInfo($condition = array(), $field = '*', $order = ''){
		$result = $this->where($condition)->field($field)->order($order)->find();
		$result = $this->getMansongExtendInfo($result);
		return $result;
	}
	public function add($data) {
		$data['state'] = self::MANSONG_STATE_NORMAL;
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array()){
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition = array()) {
		$result = $this->where($condition)->delete();
		return $result;
	}
	/**
     * 获取满即送状态列表
     *
     */
    public function getMansongStateArray() {
        return $this->mansong_state_array;
    }
	/**
     * 获取满即送扩展信息，包括状态文字和是否可编辑状态
     * @param array $mansong_info
     * @return string
     *
     */
    public function getMansongExtendInfo($mansong_info) {
		if (!$mansong_info) {
			return array();
		}
        if ($mansong_info['end_time'] > TIMESTAMP) {
            $mansong_info['mansong_state_text'] = $this->mansong_state_array[$mansong_info['state']];
        } else {
            $mansong_info['mansong_state_text'] = '已结束';
        }
        if ($mansong_info['state'] == self::MANSONG_STATE_NORMAL && $mansong_info['end_time'] > TIMESTAMP) {
            $mansong_info['editable'] = true;
        } else {
            $mansong_info['editable'] = false;
        }
        return $mansong_info;
    }
	/**
     * 获取店铺当前可用满即送活动
     * @param array $store_id 店铺编号 
     * @return array 满即送活动
     *
     */
    public function getMansongInfoByStoreID($store_id) {
        if (intval($store_id) <= 0) {
            return array();
        }
        $condition = array();
		$condition['state'] = self::MANSONG_STATE_NORMAL;
		$condition['store_id'] = $store_id;
		$condition['end_time >'] = TIMESTAMP;
		$mansong_info = $this->getInfo($condition, '*', 'start_time asc');
		
		if (!empty($mansong_info)) {
			$model_mansong_rule = model('p_mansong_rule');
			$mansong_info['rules'] = $model_mansong_rule->getMansongRuleListByID($mansong_info['mansong_id']);
			
			if (empty($mansong_info['rules'])) {
				$mansong_info = array();
				// 如果不存在规则直接返回不记录缓存。
			} else {
				// 规则数组序列化保存
				$mansong_info['rules'] = serialize($mansong_info['rules']);
			}
		}
        if (!empty($mansong_info) && $mansong_info['start_time'] > TIMESTAMP) {
            $mansong_info = array();
        }
        if (!empty($mansong_info)) {
            $mansong_info['rules'] = unserialize($mansong_info['rules']);
        }
		
        return $mansong_info;
    }
	/**
     * 获取订单可用满即送规则
     * @param array $store_id 店铺编号 
     * @param array $order_price 订单金额
     * @return array 满即送规则
     *
     */
    public function getMansongRuleByStoreID($store_id, $order_price) {
        $mansong_info = $this->getMansongInfoByStoreID($store_id);
        if (empty($mansong_info)) {
            return null;
        }
        $rule_info = null;
        foreach ($mansong_info['rules'] as $value) {
            if ($order_price >= $value['price']) {
                $rule_info = $value;
                $rule_info['mansong_name'] = $mansong_info['mansong_name'];
                $rule_info['start_time'] = $mansong_info['start_time'];
                $rule_info['end_time'] = $mansong_info['end_time'];
                break;
            }
        }
        return $rule_info;
    }
	/**
     * 获取店铺新满即送活动开始时间限制
     *
     */
    public function getMansongNewStartTime($store_id) {
        if (empty($store_id)) {
            return null;
        }
        $condition = array();
        $condition['store_id'] = $store_id;
        $condition['state'] = self::MANSONG_STATE_NORMAL;
        $mansong_info = $this->where($condition)->field('end_time')->order('end_time desc')->find();
		if (!empty($mansong_info['end_time'])) {
			return $mansong_info['end_time'];
		} else {
			return 0;
		}
    }
}