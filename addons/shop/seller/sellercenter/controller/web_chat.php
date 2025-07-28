<?php
namespace sellercenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class web_chat extends control
{
    private $member_info;
    public function __construct()
    {
        parent::_initialize();
		$this->member_info = model('member')->getInfo(array('uid' => $this->store_info['member_id']), 'nickname');
    }
	public function send_msgOp()
    {
        $member_id = $this->store_info['member_id'];
        $member_name = $this->member_info['nickname'];
        $f_id = input('f_id', 0, 'intval');
        $t_id = input('t_id', 0, 'intval');
        $t_name = input('t_name', '', 'trim');
        if ($member_id < 1 || $member_id != $f_id) {
            $this->error('登录超时或者当前账号已退出');
        }
        $msg = array();
        $msg['f_id'] = $f_id;
        $msg['f_name'] = $member_name;
        $msg['t_id'] = $t_id;
        $msg['t_name'] = $t_name;
        $msg['t_msg'] = input('t_msg', '', 'trim');
        if ($msg['t_msg'] != '') {
            $chat_msg = model('kefu_msg')->addMsg($msg);
        }
        if ($chat_msg['m_id']) {
            $this->json($chat_msg);
        } else {
            $this->error(core\language::get('nc_member_chat_add_error'));
        }
    }
    public function get_seller_listOp()
    {
        $member_list = array();
        $model_chat = model('kefu_msg');
        $member_id = $this->store_info['member_id'];
        $member_name = isset($this->member_info['nickname']) ? $this->member_info['nickname'] : $this->store_info['name'];
        $store_id = $this->store_info['id'];
        $f_id = input('f_id', 0, 'intval');
        if ($member_id < 1 || $member_id != $f_id) {
            $this->error('登录超时或者当前账号已退出');
        }
        $n = input('n', 0, 'intval');
        if ($n < 1) {
            $n = 50;
        }
        if (empty(input('session.seller_list'))) {
            $member_list = $model_chat->getSellerList(array('id' => $store_id), $n, $member_list);
			$_SESSION['seller_list'] = $member_list;
        } else {
            $member_list = input('session.seller_list');
        }
        $add_time = date('Y-m-d');
        $add_time30 = strtotime($add_time) - 60 * 60 * 24 * 30;
        $member_list = $model_chat->getRecentList(array('f_id' => $f_id, 'add_time >=' => $add_time30), 10, $member_list);
        $member_list = $model_chat->getRecentFromList(array('t_id' => $f_id, 'add_time >=' => $add_time30), 10, $member_list);
        $this->json($member_list);
    }
	public function get_infoOp()
    {
        if ($this->store_info['member_id'] < 1) {
            $this->error('登录超时或者当前账号已退出');
        }
        $val = '';
        $member = array();
        $types = array('member_id', 'member_name', 'store_id', 'member');
        $key = $_GET['t'];
        $member_id = intval($_GET['u_id']);
        if (trim($key) != '' && in_array($key, $types)) {
            $member = model('member')->getInfo(array('uid' => $member_id), 'uid,nickname,headimg');
			$member = array_merge($this->store_info, $member);
            $this->json($member);
        }
    }
	public function get_chat_logOp()
    {
        $member_id = $this->store_info['member_id'];
        $f_id = input('f_id', 0, 'intval');
        $t_id = input('t_id', 0, 'intval');
        $page = input('page', 1, 'intval');
        if ($member_id < 1 || $member_id != $f_id) {
            $this->error('登录超时或者当前账号已退出');
        }
        if ($page < 1) {
            $page = 20;
        }
        $add_time_to = date('Y-m-d');
        $time_from = array();
        $time_from['7'] = strtotime($add_time_to) - 60 * 60 * 24 * 7;
        $time_from['15'] = strtotime($add_time_to) - 60 * 60 * 24 * 15;
        $time_from['30'] = strtotime($add_time_to) - 60 * 60 * 24 * 30;
        $key = input('t', 0);
        if (!empty($key) && array_key_exists($key, $time_from)) {
			$model_chat = model('kefu_msg_log');
			$chat_log = array();
            $condition1['add_time >='] = $time_from[$key];
			$condition1['f_id'] = $f_id;
			$condition1['t_id'] = $t_id;
			$condition2['f_id'] = $t_id;
			$condition2['t_id'] = $f_id;
			$result = $model_chat->getList($condition1, $condition2, '*', 'm_id DESC', $page, input('curpage', 1, 'intval'));
			$list = array();
			foreach ($result['list'] as $k => $v) {
				$v['time'] = date('Y-m-d H:i:s', $v['add_time']);
                $list[] = $v;
			}
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			$chat_log['list'] = $list;
            $chat_log['total_page'] = $totalpage;
            $this->json($chat_log);
        }
    }
	/**
     * 商品图片和名称
     *
     */
    public function get_goods_infoOp()
    {
        $goods_id = input('goods_id', 0, 'intval');
        $goods = model('shop_goods')->getInfo(array('goods_id' => $goods_id));
		$goods['url'] = 'javascript:;';
        $this->json($goods);
    }
	public function json($json)
    {
        echo $_GET['callback'] . '(' . json_encode($json) . ')';
        exit;
    }
    /**
     * error
     *
     */
    public function error($msg = '')
    {
        $this->json(array('error' => $msg));
    }
	public function get_sessionOp()
    {
        $key = input('key', '');
        $val = '';
        if (!empty(input('session.' . $key))) {
            $val = input('session.' . $key);
        } else {
			$_SESSION[$key] = $this->store_info['member_id'];
		}
        echo $val;
        exit;
    }
}