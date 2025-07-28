<?php
namespace shop\controller;
use lib;
class chat_msg extends member {
	public function __construct() {
		parent::_initialize();
		$room_id = input('room_id', 0, 'intval');
		if ($room_id) {
		    $room_member_info = model('chat_room_member')->getInfo(array('uid' => $this->member_info['uid'], 'room_id' => $room_id));
	        $room_member_info = $room_member_info ?: array();
	        $this->member_info = array_merge($this->member_info, $room_member_info);
		}
		# 引入模型
		$this->db = db('chat_room_msg');
	}
	public function listOp() {
		if (IS_API) {
			$room_id = input('room_id', 0, 'intval');
			$where = '`room_id`=' . $room_id;
			//$resPartition = $this->db->buildPartitionSql('chat_room_msg', 'id', 'id', $where);
			//$sql = 'select count(1) as total from' . $resPartition['countSql'];
			//$result0 = $this->db->explain(true)->query($sql, 'find');
			//$total = $result0['total'];
			# 新写法 使用计数器
			$result0 = $this->db->table('chat_room_msg_counter')->explain(false)->field('records')->where($where)->find();
			$total = isset($result0['records']) ? $result0['records'] : 0;
			$page = 20;
			$get_p = input('page', 1, 'intval');
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$hasmore = $total > $get_p * $page ? true : false;
			//$sql = 'select id from' . $resPartition['listSql'] . ' order by id DESC limit ' . $limitpage . ',' . $page;
			# 新写法 性能更牛X
			//$get_p = 1201;
			//$limitpage = 1200;
			$resPartition2 = $this->db->buildPartitionSqlNew('chat_room_msg', 'id', 'id', $where, 0, $get_p * $page);
			$sql = 'select id from' . $resPartition2['listSql'] . ' order by id DESC limit ' . $limitpage . ',' . $page;
			
			#获取ids
			$ids = [0];
			$result1 = $this->db->explain(false)->query($sql, 'select');
			
			foreach ($result1 as $k => $v) {
			    $ids[] = $v['id'];
			}
			# 获取完整数据
			$res = $this->db->buildPartitionListSqlById('chat_room_msg', 'id', $ids, 'id,content,add_time');
            $sql = 'select id,content,add_time from' . $res;
            $result = $this->db->explain(false)->query($sql, 'select');
            # 用php实现 order by id desc，节省mysql的排序资源？
            array_multisort(array_column($result, 'id'), SORT_DESC, $result);
			$list = array();
			foreach ($result as $k => $v) {
			    $content = fxy_unserialize($v['content']);
			    $content['msg']['id'] = $v['id'];
		        $content['msg']['anchor'] = 'msg' . $v['id'];
		        $content['msg']['time'] = lib\timer::friend_date($v['add_time']);
			    $list[] = $content;
			}
			$return = array(
				'list' => $list,
				'totalpage' => $totalpage,
				'hasmore' => $hasmore,
			);
			output_data($return);
		}
	}
	public function add_msgOp() {
	    if (IS_API) {
			$room_id = input('room_id', 0, 'intval');
			$msg_type = input('msg_type', '', 'trim');
			$content = input('content', '', 'trim');
			$content = json_decode($content, true);
			$data = array(
    			'room_id' => $room_id,
    			'uid' => $this->member_info['uid'],
    			'msg_type' => $msg_type,
    			'content' => serialize($content),
    			'add_time' => time(),
    		);
    		$result = $this->db->add($data);
    		if (!$result['state']) {
                output_error($result['msg']);
            }
            $id = $result['data']['id'];
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
    		    $last_msg = $this->member_info['nickname'] . ' ' . $last_msg;
    		}
    		#以下代码防止高并发防止$room_id数据行锁等待，可以扔到队列执行
    		$this->db->table('chat_room')->where(array('room_id' => $room_id))->update(array('last_msg' => $last_msg, 'last_msg_time' => time()));
    		$this->db->table('chat_room_member')->where(array('room_id' => $room_id, 'uid !=' => $this->member_info['uid'], 'last_ack_msg_id' => 0))->update('last_ack_msg_id=' . $id);
    		$this->db->table('chat_room_member')->where(array('room_id' => $room_id, 'uid !=' => $this->member_info['uid']))->update('new_msg_num=new_msg_num+1');
    		output_data(array('content' => $content));
	    }
	}
	public function upload_imgOp() {
        // 上传图片
        $upload = new \lib\uploadfile();
		$default_dir = front_upload_img_dir($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
        $upload->set('default_dir', $default_dir);
        $upload->set('max_size', config('image_max_filesize'));
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
		$file = input('name', 'file');
        $result = $upload->upfile($file);
        if (!$result) {
			output_error($upload->error);
        }
		$default_url = front_upload_img_url($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
		$file_name = $upload->file_name;
		$fullname = $default_dir . $file_name;
		//缩略图
		//list($width, $height) = getimagesize($fullname);
		//$resizeImage = new lib\resizeimage();
		//$resizeImage->newImg($fullname, $width, $height, 360, '.', dirname($fullname), true);
		
		$file_url = $default_url . $file_name;
		$attachment_type = config('attachment_host_type');
		if ($attachment_type == 2) {
			save_image_to_qiniu($fullname, $file_url);
		}
		$data['file_url'] = $file_url;
		output_data($data);
	}
	public function upload_videoOp() {
        // 上传
        $upload = new \lib\uploadfile();
		$default_dir = front_upload_media_dir($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
        $upload->set('default_dir', $default_dir);
        $upload->set('max_size', config('file_max_filesize'));
        $upload->set('allow_type', array('mp4'));
		$file = input('name', 'file');
        $result = $upload->upfile($file);
        if (!$result) {
			output_error($upload->error);
        }
		$default_url = front_upload_media_url($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
		$file_name = $upload->file_name;
		$fullname = $default_dir . $file_name;
		//缩略图
		//list($width, $height) = getimagesize($fullname);
		//$resizeImage = new lib\resizeimage();
		//$resizeImage->newImg($fullname, $width, $height, 360, '.', dirname($fullname), true);
		
		$file_url = $default_url . $file_name;
		$attachment_type = config('attachment_host_type');
		if ($attachment_type == 2) {
			save_image_to_qiniu($fullname, $file_url);
		}
		$data['file_url'] = $file_url;
		output_data($data);
	}
	public function upload_voiceOp() {
	    // 上传
        $upload = new \lib\uploadfile();
		$default_dir = front_upload_media_dir($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
        $upload->set('default_dir', $default_dir);
        $upload->set('max_size', config('file_max_filesize'));
        $upload->set('allow_type', array('mp3'));
		$file = input('name', 'file');
        $result = $upload->upfile($file);
        if (!$result) {
			output_error($upload->error);
        }
		$default_url = front_upload_media_url($this->member_info['uid']) . 'chat_msg/' . $upload->getSysSetPath();
		$file_name = $upload->file_name;
		$fullname = $default_dir . $file_name;
		
		$file_url = $default_url . $file_name;
		$attachment_type = config('attachment_host_type');
		if ($attachment_type == 2) {
			save_image_to_qiniu($fullname, $file_url);
		}
		$data['file_url'] = $file_url;
		output_data($data);
	}
	public function msg_delOp() {
	    if (IS_API) {
	        $room_id = input('room_id', 0, 'intval');
	        $room_info = model('chat_room')->getInfo(array('room_id' => $room_id));
			$msg_id = input('msg_id', 0, 'intval');
			$msg_uid = input('msg_uid', 0, 'intval');
			if ($msg_uid != $this->member_info['uid'] && $this->member_info['uid'] != $room_info['creator_uid'] && $this->member_info['uid'] != $room_info['homeowner_uid']) {
			    output_error('无权操作');
			}
			$where = array(
			    'id' => $msg_id,
		    );
		    $content = input('content', '', 'trim');
		    $content = json_decode($content, true);
			$data = array(
			    'content' => serialize($content),
		    );
		    $this->db->edit($where, $data, $msg_id);
		    $msg = $content['msg']['content']['text'];
		    $last_msg = strip_tags($msg);
		    model('chat_room')->edit(array('room_id' => $room_id), array('last_msg' => $last_msg, 'last_msg_time' => time()));
		    output_data('1');
	    }
	}
	public function msg_readOp() {
	    if (IS_API) {
	        $model_room_member = model('chat_room_member');
	        $room_id = input('room_id', 0, 'intval');
			$uid = input('uid', 0, 'intval') ?: $this->member_info['uid'];
			$where = array(
			    'room_id' => $room_id,
			    'uid' => $uid
		    );
		    $model_room_member->where($where)->update(array('last_ack_msg_id' => 0, 'new_msg_num' => 0));
		    output_data('1');
	    }
	}
	//发红包
	public function send_red_envelopeOp() {
	    if (IS_API) {
	        $model_red_envelope = model('chat_red_envelope');
	        $room_id = input('room_id', 0, 'intval');
	        $red_envelope_data = input('red_envelope_data', array(), 'trim');
	        $red_envelope_data = json_decode($red_envelope_data, true);
	        if ($red_envelope_data['type'] == 'luck') {
	            $number = $red_envelope_data['number'];
	            $totalmoney = $money = priceFormat($red_envelope_data['money']);
	            $red_moneys = $this->getHongTwo($money, $number);
	            if (array_sum($red_moneys) != $money) {
	                lib\logging::write(var_export($money, true));
	                lib\logging::write(var_export(array_sum($red_moneys), true));
	                lib\logging::write(var_export($red_moneys, true));
	                output_error('红包信息错误，联系技术处理');
	            }
	            foreach ($red_moneys as $v) {
	                if ($v <= 0) {
	                    lib\logging::write(var_export($red_moneys, true));
	                    output_error('红包信息错误，联系平台补偿');
	                    break;
	                }
	            }
	            $red_envelope_data['red_moneys'] = $red_moneys;
	        } else {
	            $totalmoney = priceFormat($red_envelope_data['number'] * $red_envelope_data['money']);
	        }
	        $red_envelope_data['totalmoney'] = $totalmoney;
	        $data = array(
	            'uid' => $this->member_info['uid'],
	            'room_id' => $room_id,
	            'red_envelope_data' => serialize($red_envelope_data),
	            'add_time' => time(),
	        );
	        $rid = $model_red_envelope->add($data);
		    output_data(array('rid' => $rid));
	    }
	}
	//领红包
	public function receive_red_envelopeOp() {
	    if (IS_API) {
	        $model_red_envelope = model('chat_red_envelope');
	        $rid = input('rid', 0, 'intval');
	        $info = $model_red_envelope->getInfo(array('rid' => $rid));
	        if (!$info) {
	            output_error('红包信息错误');
	        }
	        $red_envelope_data = fxy_unserialize($info['red_envelope_data']);
	        $info['red_envelope_data'] = $red_envelope_data;
	        if (empty($red_envelope_data['uids'])) {
	            $red_envelope_data['uids'] = array();
	        }
	        if (empty($red_envelope_data['receivedList'])) {
	            $red_envelope_data['receivedList'] = array();
	        }
	        if (empty($red_envelope_data['receivedNumber'])) {
	            $red_envelope_data['receivedNumber'] = 0;
	        }
	        if (empty($red_envelope_data['receivedMoney'])) {
	            $red_envelope_data['receivedMoney'] = 0;
	        }
	        if (!empty($red_envelope_data['received_uid']) && $red_envelope_data['received_uid'] != $this->member_info['uid']) {
	            output_error('你无权领取');
	        }
	        if (!in_array($this->member_info['uid'], $red_envelope_data['uids']) && $red_envelope_data['receivedNumber'] < $red_envelope_data['number']) {
	            if ($red_envelope_data['type'] == 'luck') {
	                //抽取红包
	                $red_moneys = $red_envelope_data['red_moneys'];
	                $money = array_pop($red_moneys);
	                $red_envelope_data['red_moneys'] = $red_moneys;
	                //手气王
	                $islucky = true;
    	            foreach ($red_envelope_data['receivedList'] as $k => $v) {
    	                if ($v['money'] > $money) {
    	                    $islucky = false;
    	                    break;
    	                }
    	            }
    	            if ($islucky == true) {
    	                //把其他人全部运气王置成false
    	               foreach ($red_envelope_data['receivedList'] as $k => $v) {
        	                $red_envelope_data['receivedList'][$k]['islucky'] = false;
        	            } 
    	            }
	            } else if ($red_envelope_data['type'] == 'normal') {
	                $islucky = false;
	                $money = priceFormat($red_envelope_data['money']);
	            } else if ($red_envelope_data['type'] == 'exclusive') {
	                $islucky = false;
	                $money = priceFormat($red_envelope_data['money']);
	            }
	            $received = array(
	                'uid' => $this->member_info['uid'],
	                'username' => $this->member_info['nickname'],
	                'face' => $this->member_info['headimg'],
	                'time' => date('Y-m-d H:i:s'),
	                'money' => $money,
	                'islucky' => $islucky,
	            );
	            array_push($red_envelope_data['receivedList'], $received);
	            //领取红包后显示金额
	            $info['red_envelope_data'] = $red_envelope_data;
	            //已经领取
	            $receivedNumber = $red_envelope_data['receivedNumber'] + 1;
	            $red_envelope_data['receivedNumber'] = $receivedNumber;
	            $receivedMoney = $red_envelope_data['receivedMoney'] + $money;
	            $red_envelope_data['receivedMoney'] = priceFormat($receivedMoney);
	            //领取红包后防止重复发送socket消息
	            array_push($red_envelope_data['uids'], $this->member_info['uid']);
	        }
	        $update_data = array(
	            'red_envelope_data' => serialize($red_envelope_data),
	        );
	        $model_red_envelope->edit(array('rid' => $rid), $update_data);
	        $member_info = model('member')->getInfo(array('uid' => $info['uid']));
	        $member_info['headimg'] = $member_info['headimg'] ? tomedia($member_info['headimg']) : STATIC_URL . '/shop/img/default_user.png';
		    $info['member_info'] = $member_info;
		    output_data(array('info' => $info));
	    }
	}
	public function red_envelope_infoOp() {
	    if (IS_API) {
	        $model_red_envelope = model('chat_red_envelope');
	        $rid = input('rid', 0, 'intval');
	        $info = $model_red_envelope->getInfo(array('rid' => $rid));
	        if (!$info) {
	            output_error('红包信息错误');
	        }
	        $red_envelope_data = fxy_unserialize($info['red_envelope_data']);
	        $info['red_envelope_data'] = $red_envelope_data;
	        $member_info = model('member')->getInfo(array('uid' => $info['uid']));
	        $member_info['headimg'] = $member_info['headimg'] ? tomedia($member_info['headimg']) : STATIC_URL . '/shop/img/default_user.png';
		    $info['member_info'] = $member_info;
		    $info['myuid'] = $this->member_info['uid'];
		    output_data(array('info' => $info));
	    }
	}
	# 抢红包方法二   线段切割法
    ##  基本思路：将金额总量 最为一个整体M   N个人来分  则分N-1次   先分开，然后依次拿走
    ##  可能遇到的问题：①随机分出现重复 ②如何尽可能降低时间复杂度和空间复杂度。
     
     
    private function getHongTwo($total = 0 , $num = 0) {
    	$bag = [];
    	$max = 0;
    	for ($i=0; $i < $num - 1; $i++) { 
    		$_bag = $this->is_repeat($bag, $total);
    		if ($_bag > $max) {
    			$max = $_bag;
    		}
    		array_push($bag, $_bag);
    	}
    	sort($bag);
    	$money = [];
    	for ($i=0; $i < count($bag); $i++) { 
    		if ($i == 0) {
    			$_money = priceFormat($bag[$i]);
    		} else {
    			$_money = priceFormat($bag[$i] - $bag[$i - 1]);
    		}
    		array_push($money, $_money);
    	}
    	# 最后一个值(max可以不进行比较，在数组排序后 选择$bag[$num-2])
    	$_quantity = priceFormat($total - $max);
    	array_push($money, $_quantity);
     
    	return $money;
    }
    private function is_repeat($array = [] , $max = 0) {
    	$_bag = $this->randomFloat(1, $max - 1);
    	if (in_array($_bag, $array)) {
    		$_bag = $this->is_repeat($array , $max);
    	}
    	return $_bag;
    }
	//生成带有小数的随机数
	private function randomFloat($min = 0, $max = 1) {
        $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
       	return sprintf('%.2f', $num);  //控制小数后几位
    }
}