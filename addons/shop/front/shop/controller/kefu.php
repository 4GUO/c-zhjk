<?php
namespace shop\controller;
class kefu extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if (IS_API) {
			$member_list = array();
			$model_chat = model('kefu_msg');
			$member_id = $this->member_info['uid'];
			$member_name = $this->member_info['nickname'];
			$n = input('n', 0, 'intval');
			if ($n < 1) {
				$n = 50;
			}
			$recent = input('recent', 0, 'intval');
			if ($recent != 1) {
				$member_list = $model_chat->getFriendList(array('friend_frommid' => $member_id), $n, $member_list);
			}
			$add_time = date('Y-m-d');
			$add_time30 = strtotime($add_time) - 60 * 60 * 24 * 30;
			$member_list = $model_chat->getRecentList(array('f_id' => $member_id, 'add_time >=' => $add_time30), 10, $member_list);
			$member_list = $model_chat->getRecentFromList(array('t_id' => $member_id, 'add_time >=' => $add_time30), 10, $member_list);
			$node_info = array();
			$node_info['node_chat'] = config('node_chat');
			$node_info['node_site_url'] = NODE_URL;
			$return = array(
				'title' => '消息列表',
				'node_info' => $node_info,
				'list' => $member_list
			);
			output_data($return);
		}
	}
	/**
     * 删除最近联系人消息
     *
     */
    public function del_msgOp() {
        $model_chat = model('kefu_msg');
        $member_id = $this->member_info['uid'];
        $t_id = input('t_id', 0, 'intval');
        $condition = array();
        $condition['f_id'] = $member_id;
        $condition['t_id'] = $t_id;
        $model_chat->del($condition);
        $condition = array();
        $condition['t_id'] = $member_id;
        $condition['f_id'] = $t_id;
        $model_chat->del($condition);
        output_data(1);
    }
	public function infoOp() {
		if (IS_API) {
			$return = array(
				'title' => '客服消息',
			);
			output_data($return);
		}
	}
	/**
     * node连接参数
     */
    public function get_node_infoOp() {
        $output_data = array('node_chat' => config('node_chat'), 'node_site_url' => NODE_URL, 'resource_site_url' => STATIC_URL . '/chat');
        $member_id = $this->member_info['uid'];
        $output_data['member_info'] = $this->member_info;
		$u_id = input('u_id', 0, 'intval');
        if ($u_id > 0) {
            $store_info = model('seller')->getInfo(array('member_id' => $u_id));
            $output_data['user_info'] = $store_info;
        }
        $goods_id = input('chat_goods_id', 0, 'intval');
        if ($goods_id > 0) {
            $goods = model('shop_goods')->getInfo(array('goods_id' => $goods_id));
            $output_data['chat_goods'] = $goods;
        }
        output_data($output_data);
    }
	/**
     * 发消息
     *
     */
    public function send_msgOp() {
        $store = array();
        $member_id = $this->member_info['uid'];
        $member_name = $this->member_info['nickname'];
        $t_id = input('t_id', 0, 'intval');
        $t_name = input('t_name', '', 'trim');
        $store = model('seller')->getInfo(array('member_id' => $t_id));
        if ($t_name != $store['name']) {
            output_error('接收消息商家信息错误');
        }
        $msg = array();
        $msg['f_id'] = $member_id;
        $msg['f_name'] = $member_name;
        $msg['t_id'] = $t_id;
        $msg['t_name'] = $t_name;
        $msg['t_msg'] = input('t_msg', '', 'trim');
        if ($msg['t_msg'] != '') {
            $chat_msg = model('kefu_msg')->addMsg($msg);
        }
        if ($chat_msg['m_id']) {
            $goods_id = input('chat_goods_id', 0, 'intval');
            if ($goods_id > 0) {
                $goods = model('shop_goods')->getInfo(array('goods_id' => $goods_id));
                $chat_msg['chat_goods'] = $goods;
            }
            output_data(array('msg' => $chat_msg));
        } else {
            output_error('发送失败，请稍后重新发送');
        }
    }
	/**
     * 聊天记录查询
     *
     */
    public function get_chat_logOp() {
        $member_id = $this->member_info['uid'];
        $t_id = input('t_id', 0, 'intval');
        $add_time_to = date('Y-m-d');
        $time_from = array();
        $time_from['7'] = strtotime($add_time_to) - 60 * 60 * 24 * 7;
        $time_from['15'] = strtotime($add_time_to) - 60 * 60 * 24 * 15;
        $time_from['30'] = strtotime($add_time_to) - 60 * 60 * 24 * 30;
        $key = input('t', 0);
        if (!empty($key) && array_key_exists($key, $time_from)) {
            $model_chat = model('kefu_msg_log');
            $list = array();
            $condition1['add_time >='] = $time_from[$key];
			$condition1['f_id'] = $member_id;
			$condition1['t_id'] = $t_id;
			$condition2['f_id'] = $t_id;
			$condition2['t_id'] = $member_id;
			
			$result = $model_chat->getList($condition1, $condition2, '*', 'm_id DESC', 100, input('curpage', 1, 'intval'));
			$list = $result['list'];
			$totalpage = $result['totalpage'];
			$hasmore = $result['hasmore'];
			unset($result);
			$lists = array();
			foreach ($list as $k => $v) {
				$v['time'] = date('Y-m-d H:i:s', $v['add_time']);
                $lists[] = $v;
			}
			$return = array(
				'list' => $lists,
				'totalpage' => $totalpage,
				'hasmore' => $hasmore,
			);
			output_data($return);
        }
    }
}