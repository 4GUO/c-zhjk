<?php
namespace model;
use base;
class voucher extends base\model
{
    protected $tableName = 'voucher';
	const VOUCHER_STATE_UNUSED = 1;
    const VOUCHER_STATE_USED = 2;
    const VOUCHER_STATE_EXPIRE = 3;
    private $voucher_state_array = array(self::VOUCHER_STATE_UNUSED => '未使用', self::VOUCHER_STATE_USED => '已使用', self::VOUCHER_STATE_EXPIRE => '已过期');
	const VOUCHER_GETTYPE_DEFAULT = 'points';
	private $voucher_gettype_array = array('points' => array('sign' => 1, 'name' => '积分兑换'), 'pwd' => array('sign' => 2, 'name' => '卡密兑换'), 'free' => array('sign' => 3, 'name' => '免费领取'));
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
     * 返回代金券状态数组
     * @return array
     */
    public function getVoucherStateArray() {
        return $this->voucher_state_array;
    }
    /**
     * 返回代金券领取方式数组
     * @return array
     */
    public function getVoucherGettypeArray() {
        return $this->voucher_gettype_array;
    }
	public function exchangeVoucher($template_info, $member_id, $member_name = '') {
        if (intval($member_id) <= 0 || empty($template_info)) {
            return array('state' => false, 'msg' => '参数错误');
        }
        //查询会员信息
        if (!$member_name) {
            $member_info = model('member')->getInfo(array('uid' => $member_id), 'truename');
            if (empty($template_info)) {
                return array('state' => false, 'msg' => '参数错误');
            }
            $member_name = $member_info['truename'];
        }
        //添加代金券信息
        $insert_arr = array();
        $insert_arr['voucher_code'] = model('voucher_template')->get_voucher_code($member_id);
        $insert_arr['voucher_t_id'] = $template_info['voucher_t_id'];
        $insert_arr['voucher_title'] = $template_info['voucher_t_title'];
        $insert_arr['voucher_desc'] = $template_info['voucher_t_desc'];
        $insert_arr['voucher_start_date'] = time();
        $insert_arr['voucher_end_date'] = $template_info['voucher_t_end_date'];
        $insert_arr['voucher_price'] = $template_info['voucher_t_price'];
        $insert_arr['voucher_limit'] = $template_info['voucher_t_limit'];
        $insert_arr['voucher_store_id'] = $template_info['voucher_t_store_id'];
		$insert_arr['voucher_t_customimg'] = $template_info['voucher_t_customimg'];
        $insert_arr['voucher_state'] = 1;
        $insert_arr['voucher_active_date'] = time();
        $insert_arr['voucher_owner_id'] = $member_id;
        $insert_arr['voucher_owner_name'] = $member_name;
        $result = $this->insert($insert_arr);
        if (!$result) {
            return array('state' => false, 'msg' => '领取失败');
        }
        //扣除会员积分
        if ($template_info['voucher_t_points'] > 0 && $template_info['voucher_t_gettype'] == $this->voucher_gettype_array['points']['sign']) {
            $member_info = model('member')->getInfo(array('uid' => $member_id), 'points');
			if ($member_info['points'] < $template_info['voucher_t_points']) {
				return array('state' => false, 'msg' => '积分不足');
			}
			$points_arr['pl_memberid'] = $member_id;
            $points_arr['pl_membername'] = $member_name;
            $points_arr['pl_points'] = -$template_info['voucher_t_points'];
            $points_arr['point_ordersn'] = $insert_arr['voucher_code'];
            $points_arr['pl_desc'] = '兑换代金券' . $insert_arr['voucher_code'] . '消耗';
            $result = model('points_log')->savePointsLog('other', $points_arr, true);
            if (!$result) {
                return array('state' => false, 'msg' => '兑换失败');
            }
        }
        if ($result) {
            //代金券模板的兑换数增加
            $result = model('voucher_template')->edit(array('voucher_t_id' => $template_info['voucher_t_id']), 'voucher_t_giveout=voucher_t_giveout+1');
            if (!$result) {
                return array('state' => false, 'msg' => '领取失败');
            }
            return array('state' => true, 'msg' => '领取成功');
        } else {
            return array('state' => false, 'msg' => '领取失败');
        }
    }
	/**
     * 更新过期代金券状态
     */
    public function checkVoucherExpire($member_id) {
        $condition = array();
        $condition['voucher_owner_id'] = $member_id;
        $condition['voucher_state'] = self::VOUCHER_STATE_UNUSED;
        $condition['voucher_end_date <'] = TIMESTAMP;
        $this->where($condition)->update(array('voucher_state' => self::VOUCHER_STATE_EXPIRE));
    }
}