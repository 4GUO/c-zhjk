<?php
namespace model;
use base;
class kefu_msg extends base\model
{
    protected $tableName = 'kefu_msg';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null)
	{
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
	public function getInfo($condition, $field = '*')
    {
        return $goods_info = $this->field($field)->where($condition)->find();
    }
	public function add($data){
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array()){
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition)
	{
		$this->where($condition)->delete();
	}
	public function addMsg($msg)
    {
        $msg['f_ip'] = get_client_ip();
        $msg['r_state'] = 2;
        //state:1--read ,2--unread
        $msg['add_time'] = time();
        $m_id = $this->add($msg);
        if ($m_id > 0) {
            $msg['m_id'] = $m_id;
            unset($msg['r_state']);
            $this->table('kefu_msg_log')->insert($msg);
            $msg['m_id'] = $m_id;
            $msg['add_time'] = date('Y-m-d H:i:s', $msg['add_time']);
            $t_msg = $msg['t_msg'];
            $goods_id = 0;
            $goods_info = array();
            $pattern = '#@(\\d+)@$#';
            preg_match($pattern, $t_msg, $matches);
            $goods_id = isset($matches[1]) ? intval($matches[1]) : 0;
            if ($goods_id >= 1) {
                $goods_info = model('shop_goods')->getInfo(array('goods_id' => $goods_id));
                $goods_id = intval($goods_info['goods_id']);
            }
            $msg['goods_id'] = $goods_id;
            $msg['goods_info'] = $goods_info;
            return $msg;
        } else {
            return 0;
        }
    }
	/**
     * 商家客服
     *
     * @param
     * @return array
     */
    public function getSellerList($condition = array(), $page = 50, $member_list = array())
    {
        $model_seller = model('seller');
        $result = $model_seller->getList($condition, '*', 'id desc', $page, 1);
		$list = $result['list'];
		unset($result);
        if (!empty($list) && is_array($list)) {
            $member_ids = array();
            //会员编号数组
            foreach ($list as $k => $v) {
                $member = array();
                $u_id = $v['member_id'];
                $member_ids[] = $u_id;
                $member['u_id'] = $u_id;
                $member['u_name'] = '';
                $member['seller_id'] = $v['id'];
                $member['seller_name'] = $v['name'];
                $member['avatar'] = $v['logo'];
                $member['seller'] = 1;
                $member_list[$u_id] = $member;
            }
            $model_member = model('member');
            $condition = array();
            $condition['uid'] = $member_ids;
            $result = $model_member->getList($condition);
			$m_list = $result['list'];
            if (!empty($m_list) && is_array($m_list)) {
                foreach ($m_list as $key => $value) {
                    $u_id = $value['uid'];
                    //会员编号
                    $member_list[$u_id]['u_name'] = $value['nickname'];
                }
            }
        }
        return $member_list;
    }
	public function getRecentList($condition = array(), $limit = 5, $member_list = array())
    {
        $list = $this->getMemberRecentList($condition, $limit);
        if (!empty($list) && is_array($list)) {
            foreach ($list as $k => $v) {
                $member = array();
                $u_id = $v['t_id'];
                $member['u_id'] = $u_id;
                $member['u_name'] = $v['t_name'];
                $member['avatar'] = $v['headimg'];
                $member['recent'] = 1;
                $member['time'] = date('Y-m-d H:i:s', $v['addtime']);
                if (empty($member_list[$u_id])) {
                    $member_list[$u_id] = $member;
                } else {
                    $member_list[$u_id]['recent'] = 1;
                    $member_list[$u_id]['time'] = date('Y-m-d H:i:s', $v['addtime']);
                }
            }
        }
        return $member_list;
    }
	/**
     * 收到消息的会员记录
     *
     * @param
     * @return array
     */
    public function getMemberRecentList($condition = array(), $limit = '')
    {
        $list = array();
        $field = 't_id,t_name,max(add_time) as addtime';
		$list = $this->field($field)->group('t_id')->where($condition)->limit($limit)->order('addtime desc')->select();
		$uids = array();
		foreach ($list as $v) {
			$uids[] = $v['t_id'];
		}
		$result = model('member')->getList(array('uid' => $uids), 'uid,headimg');
		$members = array();
		foreach ($result['list'] as $k => $v) {
			$members[$v['uid']] = $v;
		}
		unset($result);
		foreach ($list as $k => $v) {
			$v['headimg'] = $members[$v['t_id']]['headimg'];
			$list[$k] = $v;
		}
        return $list;
    }
	public function getRecentFromList($condition = array(), $limit = 5, $member_list = array())
    {
        $list = $this->getMemberFromList($condition, $limit);
        if (!empty($list) && is_array($list)) {
            foreach ($list as $k => $v) {
                $member = array();
                $u_id = $v['f_id'];
                $member['u_id'] = $u_id;
                $member['u_name'] = $v['f_name'];
                $member['avatar'] = $v['headimg'];
                $member['recent'] = 1;
                $member['time'] = date('Y-m-d H:i:s', $v['addtime']);
                if (empty($member_list[$u_id])) {
                    $member_list[$u_id] = $member;
                } else {
                    $member_list[$u_id]['recent'] = 1;
                    $member_list[$u_id]['time'] = date('Y-m-d H:i:s', $v['addtime']);
                }
            }
        }
        return $member_list;
    }
	/**
     * 发出消息的会员记录
     *
     * @param
     * @return array
     */
    public function getMemberFromList($condition = array(), $limit = '')
    {
        $list = array();
        $field = 'f_id,f_name,max(add_time) as addtime';
		$list = $this->field($field)->group('f_id')->where($condition)->limit($limit)->order('addtime DESC')->select();
		$uids = array();
		foreach ($list as $v) {
			$uids[] = $v['f_id'];
		}
		$result = model('member')->getList(array('uid' => $uids), 'uid,headimg');
		$members = array();
		foreach ($result['list'] as $k => $v) {
			$members[$v['uid']] = $v;
		}
		unset($result);
		foreach ($list as $k => $v) {
			$v['headimg'] = $members[$v['f_id']]['headimg'];
			$list[$k] = $v;
		}
        return $list;
    }
	public function getFriendList($condition = array(), $limit = '', $member_list = array()) {
		$list = model('sns_friend')->where($condition)->limit($limit)->order('friend_addtime DESC')->select();
		$uids = array();
		foreach ($list as $v) {
			$uids[] = $v['u_id'];
		}
		$result = model('member')->getList(array('uid' => $uids), 'uid,headimg');
		$members = array();
		foreach ($result['list'] as $k => $v) {
			$members[$v['uid']] = $v;
		}
		unset($result);
		if(!empty($list) && is_array($list)) {
			foreach($list as $k => $v) {
				$member = array();
				$u_id = $v['friend_tomid'];
				$member['u_id'] = $u_id;
				$member['u_name'] = $v['friend_tomname'];
				$member['avatar'] = $members[$u_id]['headimg'];
				$member['friend'] = 1;
				$member_list[$u_id] = $member;
			}
		}
		return $member_list;
	}
}