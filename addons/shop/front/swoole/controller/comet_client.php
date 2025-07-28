<?php
namespace swoole\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class comet_client extends control {
	/**
	 * host-IP，请求IP
	*/
	private $_host = '127.0.0.1';
	public function __construct() {
        parent::_initialize();
    }
    public function send_to_uidOp() {
		$data['type'] = input('type', '', 'trim');
		$data['room_id'] = input('room_id', 0, 'intval');
		$data['uid'] = input('uid', 0, 'intval');
		$msg = input('msg', '', 'trim');
		$data['msg'] = json_decode($msg, true);
		$data['broadcast_type'] = 'send_to_uid';
		//$data必须为json格式
		$post_data['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->https_post('http://' . $this->_host . ':' . $this->_websocket_port, $post_data);
    }
    public function send_to_groupOp() {
		$data['type'] = input('type', '', 'trim');
		$data['room_id'] = input('room_id', 0, 'intval');
		$data['uid'] = input('uid', 0, 'intval');
		$msg = input('msg', '', 'trim');
		$data['msg'] = json_decode($msg, true);
		$data['broadcast_type'] = 'send_to_group';
		//$data必须为json格式
		$post_data['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->https_post('http://' . $this->_host . ':' . $this->_websocket_port, $post_data);
    }
    public function send_to_allOp() {
		$data['type'] = input('type', '', 'trim');
		$data['room_id'] = input('room_id', 0, 'intval');
		$data['uid'] = input('uid', 0, 'intval');
		$msg = input('msg', '', 'trim');
		$data['msg'] = json_decode($msg, true);
		$data['broadcast_type'] = 'send_to_all';
		//$data必须为json格式
		$post_data['data'] = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->https_post('http://' . $this->_host . ':' . $this->_websocket_port, $post_data);
    }
}