<?php
namespace shop\controller;
use lib;
class chat extends member {
	public function __construct() {
		parent::_initialize();
		$room_id = input('room_id', 0, 'intval');
		if ($room_id) {
		    $room_member_info = model('chat_room_member')->getInfo(array('uid' => $this->member_info['uid'], 'room_id' => $room_id));
	        $room_member_info = $room_member_info ?: array();
	        $this->member_info = array_merge($this->member_info, $room_member_info);
		}
		# 引入模型
		$this->db = db('chat_room');
	}
	public function indexOp() {
		if (IS_API) {
			$model_room = model('chat_room');
			$model_room_member = model('chat_room_member');
			$this->title = '聊天';
			$where = array(
			    'uid' => $this->member_info['uid'],
			);
			$result = $model_room_member->getList($where, 'room_id,uid,new_msg_num');
			$room_list = $room_ids = $uids = array();
			foreach($result['list'] as $k => $r){
				$room_list[$r['room_id']]['new_msg_num'] = $r['new_msg_num'];
				$room_ids[] = $r['room_id'];
			}
			unset($result);
			$where = array(
			    'room_id' => $room_ids,
			);
			$result = $model_room_member->getList($where, 'room_id,uid');
			foreach($result['list'] as $k => $r){
				$room_list[$r['room_id']]['uids'][] = $r['uid'];
				$uids[] = $r['uid'];
			}
			unset($result);
			$member_list = model('member')->getList(array('uid' => $uids), 'uid,headimg');
			$member_list = array_under_reset($member_list['list'], 'uid');
			if (!$room_ids) {
			    $room_ids = array(0);
			}
			$where = array(
			   'room_id' => $room_ids,    
			);
			$result = $model_room->getList($where, '*', 'last_msg_time DESC', 20, input('page', 1, 'intval'));
			$list = array();
			foreach ($result['list'] as $k => $v) {
			    $room_uids = $room_list[$v['room_id']]['uids'];
			    if(count($room_uids) > 9) {
			        $room_uids = array_slice($room_uids, 0, 9);
			    }
			    $avatar_list = array();
			    foreach ($room_uids as $kk => $vv) {
			        $avatar_list[$kk]['url'] = !empty($member_list[$vv]['headimg']) ? tomedia($member_list[$vv]['headimg']) : STATIC_URL . '/shop/img/default_user.png';
			    }
			    $item = array(
			        'room_id' => $v['room_id'],
			        'author_name' => $v['room_name'],
			        'published_at' => lib\timer::friend_date($v['last_msg_time']),
			        'note' => htmlspecialchars_decode($v['last_msg']),
			        'text' => $room_list[$v['room_id']]['new_msg_num'],
			        'cover' => $v['cover'] ? tomedia($v['cover']) : STATIC_URL . '/shop/img/default_user.png',
			        'avatar_list' => $avatar_list,
			    );
			    $list[$k] = $item;
			}
			$return = array(
				'title' => $this->title,
				'list' => $list,
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
			);
			output_data($return);
		}
	}
	public function room_infoOp() {
	    if (IS_API) {
	        $room_id = input('room_id', 0, 'intval');
	        $info = model('chat_room')->getInfo(array('room_id' => $room_id));
	        if (!$info) {
	            output_error('聊天室不存在！');
	        }
	        $info_extend = model('chat_room_extend')->getInfo(array('room_id' => $room_id));
	        if ($info_extend) {
	            $info_extend['gonggao'] = fxy_unserialize($info_extend['gonggao']);
	            $info = array_merge($info, $info_extend);
	        }
	        //房主信息
	        $info['homeowner_member_info'] = model('chat_room_member')->getInfo(array('uid' => $info['homeowner_uid'], 'room_id' => $room_id));
	        $info['share_default_logo'] = STATIC_URL . '/shop/img/share_default_logo.png?t=' . time();
	        $member_info = array(
	            'uid' => $this->member_info['uid'],
	            'nickname' => $this->member_info['nickname'],
	            'headimg' => tomedia($this->member_info['headimg']),
	        );
	        $room_member_info = model('chat_room_member')->getInfo(array('uid' => $this->member_info['uid'], 'room_id' => $room_id));
	        $room_member_info = $room_member_info ?: array();
	        $member_info = array_merge($member_info, $room_member_info);
	        $ercode = $this->_get_room_ercode($this->member_info, 8, $room_id);
	        $return = array(
				'title' => $info['room_name'],
				'info' => $info,
				'member_info' => $member_info,
				'ercode' => $ercode,
			);
			output_data($return);
	    }
	}
	public function room_publishOp() {
	    if (IS_API) {
	        $room_id = input('room_id', 0, 'intval');
	        $name = input('name', '', 'trim');
	        if (!$name) {
	            output_error('聊天室名称不能为空！');
	        }
	        $data = array(
	            'room_name' => $name,
	        );
	        if ($room_id) {
	            $where = array(
	                'room_id' => $room_id,
	            );
	            model('chat_room')->edit($where, $data);
	        } else {
	            $data['creator_uid'] = $this->member_info['uid'];
	            $data['homeowner_uid'] = $this->member_info['uid'];
	            $data['status'] = 1;
	            $data['cover'] = $this->member_info['headimg'];
	            $data['need_check'] = 0;
	            $data['password'] = input('password', '', 'trim') ? f_hash(input('password', '', 'trim')) : f_hash('123456');
	            $data['last_msg'] = '成功创建聊天室~';
	            $data['last_msg_time'] = time();
	            $result = $this->db->add($data);
	            if (!$result['state']) {
                    output_error($result['msg']);
                }
                $room_id = $result['data']['room_id'];
                $data_extend = array(
                    'gonggao' => '',
    	        );
    	        $this->db->table('chat_room_extend')->insert($data_extend);
        	    $room_member = array(
        	        'room_id' => $room_id,
        	        'uid' => $this->member_info['uid'],
        	        'nickname' => $this->member_info['nickname'],
        	        'add_time' => time(),
        	    );
    		    $this->db->table('chat_room_member')->insert($room_member);
	        }
	        output_data(array('room_id' => $room_id));
	    }
	}
	public function setting_edit_itemOp() {
	    $room_id = input('room_id', 0, 'intval');
		$k_name = input('k_name', '', 'trim');
		$v_value = input('v_value', '', 'trim');
		$data = array();
		if ($k_name == 'nickname') {
			$v_value = filterEmoji($v_value);
			if (!$v_value) {
                output_error('昵称不能为空');
            }
		}
		$data[$k_name] = $v_value;
		$flag = model('chat_room_member')->edit(array('uid' => $this->member_info['uid'], 'room_id' => $room_id), $data);
		output_data(1);
	}
	public function manger_edit_itemOp() {
	    $room_id = input('room_id', 0, 'intval');
		$k_name = input('k_name', '', 'trim');
		$v_value = input('v_value', '', 'trim');
		$data = array();
		if ($k_name == 'room_name') {
			$v_value = filterEmoji($v_value);
			if (!$v_value) {
                output_error('群名称不能为空');
            }
			$data['truename'] = $v_value;
		} else if ($k_name == 'password') {
			if(!$v_value){
				output_error('密码不能为空');
			}
			$v_value = f_hash($v_value);
		}
		$data[$k_name] = $v_value;
		$flag = model('chat_room')->edit(array('room_id' => $room_id), $data);
		output_data(1);
	}
	public function gonggao_publishOp() {
	    if (IS_API) {
	        $room_id = input('room_id', 0, 'intval');
	        $gonggao = input('gonggao', '', 'trim');
	        if (!$gonggao) {
	            output_error('公告内容不能为空！');
	        }
	        $gonggao_data = array(
	            'headimg' => $this->member_info['headimg'],
                'nickname' => $this->member_info['nickname'],
                'time' => date('Y-m-d H:i:s'),
                'content' => $gonggao,
            );
	        $data = array(
	            'gonggao' => serialize($gonggao_data),
	        );
	        $where = array(
	            'room_id' => $room_id,
	        );
	        model('chat_room_extend')->edit($where, $data);
	        output_data('1');
	    }
	}
	public function member_listOp() {
	    if (IS_API) {
			$model_room_member = model('chat_room_member');
			$room_id = input('room_id', 0, 'intval');
			$where = array(
			    'room_id' => $room_id,
			);
			$result = $model_room_member->getList($where, '*', 'id ASC', 50, input('page', 1, 'intval'));
			$uids = array();
			foreach($result['list'] as $k => $r){
				$uids[] = $r['uid'];
			}
			
			$member_list = model('member')->getList(array('uid' => $uids), 'uid,headimg');
			$member_list = array_under_reset($member_list['list'], 'uid');
			$list = array();
			foreach ($result['list'] as $k => $v) {
			    $v['headimg'] = !empty($member_list[$v['uid']]['headimg']) ? tomedia($member_list[$v['uid']]['headimg']) : STATIC_URL . '/shop/img/default_user.png';
			    $list[] = $v;
			}
			$return = array(
				'list' => $list,
				'totalpage' => $result['totalpage'],
				'hasmore' => $result['hasmore'],
			);
			output_data($return);
		}
	}
	public function add_room_memberOp() {
        if (IS_API) {
            $room_id = input('room_id', 0, 'intval');
            $inviter_id = input('inviter_id', 0, 'intval');
            //防止重复加入
            $check = model('chat_room_member')->field('uid')->where(array('room_id' => $room_id, 'uid' => $this->member_info['uid']))->find();
            if (!empty($check['uid'])) {
                output_error('');
            }
            if ($inviter_id) {
                $check = model('chat_room_member')->field('uid')->where(array('room_id' => $room_id, 'uid' => $inviter_id))->find();
                if (empty($check['uid'])) {
                    output_error('邀请人不在聊天室', array('redirect' => '/pages/chat/index'));
                }
                $inviter_info = model('member')->getInfo(array('uid' => $inviter_id), 'nickname');
            }
            $room_member = array(
                'room_id' => $room_id,
                'uid' => $this->member_info['uid'],
                'nickname' => $this->member_info['nickname'],
                'inviter_id' => $inviter_id,
                'add_time' => time(),
            );
            model('chat_room_member')->add($room_member);
            if (!empty($inviter_info)) {
                $text = $inviter_info['nickname'] . ' 邀请了 ' . $this->member_info['nickname'] . ' 加入聊天室';
            } else {
                $text = $this->member_info['nickname'] . ' 加入聊天室';
            }
            output_data(array('text' => $text));
	    }
	}
	public function del_memberOp() {
	    $room_id = input('room_id', 0, 'intval');
	    $uid = input('uid', 0, 'intval');
	    $room_info = model('chat_room')->getInfo(array('room_id' => $room_id));
	    if ($room_info['creator_uid'] == $uid || $room_info['homeowner_uid'] == $uid) {
	        output_error('不能删除管理员或群主');
	    }
        $where = array(
           'room_id' => $room_id,
           'uid' => $uid,
        );
        model('chat_room_member')->where($where)->delete();
        output_data('1');
	}
	public function close_roomOp() {
	    $room_id = input('room_id', 0, 'intval');
	    $room_info = model('chat_room')->getInfo(array('room_id' => $room_id));
	    if ($room_info['creator_uid'] != $this->member_info['uid']) {
	        output_error('你不是创建者无权操作');
	    }
        $where = array(
           'room_id' => $room_id,
        );
        model('chat_room')->where($where)->update(array('status' => 2));
        output_data('1');
	}
	public function get_indexed_membersOp() {
	    if (IS_API) {
			$model_room_member = model('chat_room_member');
			$room_id = input('room_id', 0, 'intval');
			$where = array(
			    'room_id' => $room_id,
			);
			$search_nickname = input('search_nickname', '', 'trim');
			if ($search_nickname) {
			    $where['nickname'] = '%' . $search_nickname . '%';
			}
			$result = $model_room_member->getList($where, 'uid,nickname');
			$room_members = array();
			foreach($result['list'] as $k => $r){
			    if ($r['uid'] != $this->member_info['uid']) {
			       $room_members[$r['uid']] = $r; 
			    }
			}
			unset($result);
			$result = model('member')->getList(array('uid' => array_keys($room_members)), 'uid,headimg');
			$list = array();
			foreach ($result['list'] as $k => $v) {
			    $v['letter'] = getFirstCharter($room_members[$v['uid']]['nickname']);
			    $v['nickname'] = $room_members[$v['uid']]['nickname'];
			    $v['headimg'] = !empty($v['headimg']) ? tomedia($v['headimg']) : STATIC_URL . '/shop/img/default_user.png';
			    $list[] = $v;
			}
			$new_list = array();
			$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			foreach ($list as $k => $v) {
			    $new_list[$v['letter']]['letter'] = $v['letter'];
			    $new_list[$v['letter']]['data'][] = array(
			        'uid' => $v['uid'],
			        'nickname' => $v['nickname'],
			        'headimg' => $v['headimg']
			    );  
			}
			$member_list = array();
			foreach ($cellName as $letter) {
			    if (isset($new_list[$letter])) {
			        $member_list[] = $new_list[$letter];
			    } else {
			        $member_list[] = array(
			            'letter' => $letter,
			            'data' => array(),
			        );
			    }
			}
			$return = array(
				'list' => $member_list,
			);
			output_data($return);
		}
	}
	private function _get_room_ercode($member_info, $cert_type, $third_party_id) {
	    if ($this->client_type == 'wxapp') {
			//获取二维码
			$weixin_qrcode_path = UPLOADFILES_PATH . '/qrcode/cert_type' . $cert_type . '/wxapp_qrcode_' . $member_info['uid'] . '_' . $third_party_id . '.jpg';
			if (file_exists($weixin_qrcode_path)) {
				$code_img = $weixin_qrcode_path;
			} else {
				$code_img = logic('poster')->create_qrcode_wxapp($member_info['uid'], $cert_type, $third_party_id);
			}
		} else if ($this->client_type == 'wap' || $this->client_type == 'wxweb' || $this->client_type == 'app') {
			$code_img = UPLOADFILES_PATH . '/qrcode/cert_type' . $cert_type . '/weixin_qrcode_' . $member_info['uid'] . '_' . $third_party_id . '.jpg';
			if (!file_exists($code_img)) {
				if ($this->config['wechat_isuse'] && $this->client_type == 'wxweb') {
					$code_img = logic('poster')->create_qrcode_weixin($member_info['uid'], $cert_type, $third_party_id);
				} else {
					$code_img = logic('poster')->create_qrcode_wap($member_info['uid'], $cert_type, $third_party_id);
				}
			}
		}
		return str_replace(UPLOADFILES_PATH, UPLOADFILES_URL, $code_img);
	}
}