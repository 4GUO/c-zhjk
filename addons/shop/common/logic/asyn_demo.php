<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class asyn_demo {
    public function test($data) {
        lib\logging::write(var_export($data, true));
        //file_put_contents(__DIR__ . '/asyn_demo_test.txt', $data['i'] . PHP_EOL, FILE_APPEND);
        return true;
    }
    public function add_msg($param) {
        $db = db('chat_room_msg');
        $uid = 1;
		$data = array(
			'room_id' => $param['room_id'],
			'uid' => $uid,
			'msg_type' => $param['msg_type'],
			'content' => serialize($param['content']),
			'add_time' => time(),
		);
		$result = $db->add($data);
		$id = $result['data']['id'];
		$room_id = $param['room_id'];
		$msg_type = $param['msg_type'];
		$content = $param['content'];
		$content['msg']['id'] = $id;
    	$content['msg']['anchor'] = 'msg' . $id;
        if (in_array($msg_type, array('text', 'img', 'voice', 'video', 'redEnvelope', 'system'))) {
    		$last_msg = $content['msg']['content']['old_text'];
    	} else {
    		$last_msg = '新消息';
    	}
    	if ($msg_type == 'system') {
    		$last_msg = $last_msg;
    	} else {
    		$last_msg = 'jackal' . ' ' . $last_msg;
    	}
		#以下代码防止高并发防止$room_id数据行锁等待，可以扔到队列执行
    	$db->table('chat_room')->where(array('room_id' => $room_id))->update(array('last_msg' => $last_msg, 'last_msg_time' => time()));
    	$db->table('chat_room_member')->where(array('room_id' => $room_id, 'uid !=' => $uid, 'last_ack_msg_id' => 0))->update('last_ack_msg_id=' . $id);
    	$db->table('chat_room_member')->where(array('room_id' => $room_id, 'uid !=' => $uid))->update('new_msg_num=new_msg_num+1');
		return true;
    }
}