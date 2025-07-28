<?php
namespace model;
use base;
class voucher_template extends base\model
{
    protected $tableName = 'voucher_template';
	private $templatestate_arr = array('usable' => array(1, '可用'), 'disabled' => array(2, '不可用'));
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
		if ($result['voucher_t_gettype']) {
			$voucher_gettype_array = model('voucher')->getVoucherGettypeArray();
            foreach ($voucher_gettype_array as $k => $v) {
                if ($result['voucher_t_gettype'] == $v['sign']) {
                    $result['voucher_t_gettype_key'] = $k;
                    $result['voucher_t_gettype_text'] = $v['name'];
                }
            }
        }
        if ($result['voucher_t_state']) {
            foreach ($this->templatestate_arr as $k => $v) {
                if ($result['voucher_t_state'] == $v[0]) {
                    $result['voucher_t_state_text'] = $v[1];
                }
            }
        }
		return $result;
	}
	public function add($data) {
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
    public function getTemplateStateArray() {
        return $this->templatestate_arr;
    }
	/*
     * 获取代金券编码
     */
    public function get_voucher_code($member_id = 0) {
        static $num = 1;
        $sign_arr = array();
        $sign_arr[] = sprintf('%02d', mt_rand(10, 99));
        $sign_arr[] = sprintf('%03d', (double) microtime() * 1000);
        $sign_arr[] = sprintf('%010d', time() - 946656000);
        if ($member_id) {
            $sign_arr[] = sprintf('%03d', (int) $member_id % 1000);
        } else {
            //自增变量
            $tmpnum = 0;
            if ($num > 99) {
                $tmpnum = substr($num, -1, 2);
            } else {
                $tmpnum = $num;
            }
            $sign_arr[] = sprintf('%02d', $tmpnum);
            $sign_arr[] = mt_rand(1, 9);
        }
        $code = implode('', $sign_arr);
        $num += 1;
        return $code;
    }
	/**
     * 生成代金券卡密
     */
    public function create_voucher_pwd($voucher_t_id) {
        if ($voucher_t_id <= 0) {
            return false;
        }
        static $num = 1;
        $sign_arr = array();
        //时间戳
        $time_tmp = uniqid('', true);
        $time_tmp = explode('.', $time_tmp);
        $sign_arr[] = substr($time_tmp[0], -1, 4) . $time_tmp[1];
        //自增变量
        $tmpnum = 0;
        if ($num > 999) {
            $tmpnum = substr($num, -1, 3);
        } else {
            $tmpnum = $num;
        }
        $sign_arr[] = sprintf('%03d', $tmpnum);
        //代金券模板ID
        if ($voucher_t_id > 9999) {
            $voucher_t_id = substr($num, -1, 4);
        }
        $sign_arr[] = sprintf('%04d', $voucher_t_id);
        //随机数
        $sign_arr[] = sprintf('%04d', rand(1, 9999));
        $pwd = implode('', $sign_arr);
        $num += 1;
        return array(md5($pwd), encrypt($pwd));
    }
	/**
     * 获取代金券卡密
     */
    public function get_voucher_pwd($pwd) {
        if (!$pwd) {
            return '';
        }
        $pwd = decrypt($pwd);
		return $pwd;
        /*$pattern = '/^([0-9]{20})\$/i';
        if (preg_match($pattern, $pwd)) {
            return $pwd;
        } else {
            return '';
        }*/
    }
	/**
     * 查询可兑换代金券模板详细信息，包括店铺信息
     */
    public function getCanChangeTemplateInfo($vid, $member_id, $store_id = 0) {
        if ($vid <= 0 || $member_id <= 0) {
            return array('state' => false, 'msg' => '参数错误');
        }
        //查询可用代金券模板
        $where = array();
        $where['voucher_t_id'] = $vid;
        $where['voucher_t_state'] = $this->templatestate_arr['usable'][0];
        $where['voucher_t_end_date >'] = time();
        $template_info = $this->getInfo($where);
        if (empty($template_info) || $template_info['voucher_t_total'] <= $template_info['voucher_t_giveout']) {
            //代金券不存在或者已兑换完
            return array('state' => false, 'msg' => '代金券已兑换完');
        }
        //验证是否为店铺自己
        if ($store_id > 0 && $store_id == $template_info['voucher_t_store_id']) {
            return array('state' => false, 'msg' => '不可以兑换自己店铺的代金券');
        }
        $model_member = model('member');
        $member_info = $model_member->getInfo(array('uid' => $member_id));
        if (empty($member_info)) {
            return array('state' => false, 'msg' => '参数错误');
        }
		$voucher_gettype_array = model('voucher')->getVoucherGettypeArray();
        //验证会员积分是否足够
        if ($template_info['voucher_t_gettype'] == $voucher_gettype_array['points']['sign'] && $template_info['voucher_t_points'] > 0) {
            if (intval($member_info['points']) < intval($template_info['voucher_t_points'])) {
                return array('state' => false, 'msg' => '您的积分不足，暂时不能兑换该代金券');
            }
        }
        //验证会员级别
        if ($member_info['level_id'] < intval($template_info['voucher_t_mgradelimit'])) {
            return array('state' => false, 'msg' => '您的会员级别不够，暂时不能兑换该代金券');
        }
        //查询代金券对应的店铺信息
        $store_info = model('seller')->getInfo(array('id' => $template_info['voucher_t_store_id']));
        if (empty($store_info)) {
            return array('state' => false, 'msg' => '代金券已兑换完');
        }
        //整理代金券信息
        $template_info = array_merge($template_info, $store_info);
        //查询代金券列表
        $where = array();
        $where['voucher_owner_id'] = $member_id;
        $where['voucher_store_id'] = $template_info['voucher_t_store_id'];
        $voucher_list = model('voucher')->getList($where);
        if (!empty($voucher_list['list'])) {
            $voucher_count = 0;
            //在该店铺兑换的代金券数量
            $voucherone_count = 0;
            //该张代金券兑换的数量
            foreach ($voucher_list['list'] as $k => $v) {
                //如果代金券未用且未过期
                if ($v['voucher_state'] == 1 && $v['voucher_end_date'] > time()) {
                    $voucher_count += 1;
                }
                if ($v['voucher_t_id'] == $template_info['voucher_t_id']) {
                    $voucherone_count += 1;
                }
            }
            //买家最多只能拥有同一个店铺尚未消费抵用的店铺代金券最大数量的验证
            if ($voucher_count >= intval(config('promotion_voucher_buyertimes_limit'))) {
                $message = sprintf('您的可用代金券已有%s张,不可再兑换了', config('promotion_voucher_buyertimes_limit'));
                return array('state' => false, 'msg' => $message);
            }
            //同一张代金券最多能兑换的次数
            if (!empty($template_info['voucher_t_eachlimit']) && $voucherone_count >= $template_info['voucher_t_eachlimit']) {
                $message = sprintf('该代金券您已兑换%s次，不可再兑换了', $template_info['voucher_t_eachlimit']);
                return array('state' => false, 'msg' => $message);
            }
        }
        return array('state' => true, 'info' => $template_info);
    }
}