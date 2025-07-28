<?php
namespace swoole\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class index extends control {
    /**
	 * 聊天室成员
	*/
	private $_rooms = [];
	/**
	 * 成员uid绑定
	*/
	private $_uids = [];
	/**
	 * WS的全局启动实例
	*/
	public $_ws;
	/**
	 * host-IP，0.0.0.0表示允许接收所有请求
	*/
	private $_host = '0.0.0.0';
	public function __construct() {
        parent::_initialize();
        //实例化websocket服务器对象并存储在我们的_ws属性上，达到单例的设计
        $this->_ws = new \Swoole\WebSocket\Server($this->_host, $this->_websocket_port);
        //设置允许访问静态文件
        $this->_ws->set([
            'document_root' => '/www/wwwroot/app.qianchawang.cn/public/static', #这里传入静态文件的目录
            'enable_static_handler' => true, #允许访问静态文件
            'daemonize' => 1,               # 是否作为守护进程
            'log_file' => '/www/server/data/swoole/swoole.log',   # 指定日志文件路径
            'log_level' => '1',             # 设置 swoole_server 错误日志打印的等级
            'heartbeat_check_interval' => 10, # 设置心跳检测间隔
            'heartbeat_idle_time' => 20    # 设置某个连接允许的最大闲置时间
        ]);
        //worker进程启动事件
        $this->_ws->on('workerstart', [$this, 'onWorkerStart']);
        //监听websocker服务开始事件
        $this->_ws->on('start', [$this, 'onStart']);
        //监听连接事件
        $this->_ws->on('open', [$this, 'onOpen']);
        //监听接收消息事件
        $this->_ws->on('message', [$this, 'onMessage']);
        //监听外部请求推送事件 HTTP长链接
        $this->_ws->on('request', [$this, 'onRequest']);
        //监听关闭事件
        $this->_ws->on('close', [$this, 'onClose']);
        //开启服务
        $this->_ws->start();
    }
    /**
     * 此事件在 Worker 进程 / Task 进程 启动时发生，这里创建的对象可以在进程生命周期内使用。
     * 初始化IO链接等相关工作
     * @param $server
     */
    public function onWorkerStart(\Swoole\Server $server, $worker_id) {
        $cache = base\cache::connect();
        $server->cache = $cache;
        //初始化进程成员变量
        $this->_server = $server;
    }
    /**
     * 启动后在主进程（master）的主线程回调此函数
     * 启动websocker服务成功回调函数
     * @param $server
     */
    public function onStart(\Swoole\Server $server) {
        echo 'Websocket Server is started at ws://' . $this->_host . ':' . $this->_websocket_port . PHP_EOL;
    }
    /**
     * 当 WebSocket 客户端与服务器建立连接并完成握手后会回调此函数。
     * @param $server 
     * @param $request  是一个 HTTP 请求对象，包含了客户端发来的握手请求信息
     */
    public function onOpen(\Swoole\WebSocket\Server $server, \Swoole\Http\Request $request) {
        //echo 'server: handshake success with fd=' . $request->fd . PHP_EOL;
        $data = array(
            'type' => 'handshake',
        );
        $this->sendToClient($request->fd, $this->json($data));
    }
    /**
     * 接收到信息的回调函数
     * @param $server
     * @param $frame 是 Swoole\WebSocket\Frame 对象，包含了客户端发来的数据帧信息
     */
    public function onMessage(\Swoole\WebSocket\Server $server, \Swoole\WebSocket\Frame $frame) {
        echo 'receive from ' . $frame->fd . ':' . $frame->data . ',opcode:' . $frame->opcode . ',fin:' . $frame->finish . PHP_EOL;
        $data = json_decode($frame->data, true);
        $type = isset($data['type']) ? $data['type'] : '';
        if ($type == 'connect') {
            //客户端握手绑定uid
            $uid = $data['uid'];
            $this->bindUid($frame->fd, $uid);
        } else if ($type == 'joinGroup') {
            $room_id = 'room_' . $data['room_id'];
            $this->joinGroup($frame->fd, $room_id);
            $this->sendToClient($frame->fd, $this->json($data));
        } else if (strpos($type, 'room_msg') !== false) {
            //群聊广播
            $room_id = 'room_' . $data['room_id'];
            $data['online_num'] = $this->getClientCountByGroup($room_id);
            $this->sendToGroup($room_id, $this->json($data), [$frame->fd]);
        } else {
            $this->sendToClient($frame->fd, $this->json($data));
        }
    }
    /**
     * WebSocket 服务器除了提供 WebSocket 功能之外，实际上也可以处理 HTTP 长连接。只需要增加onRequest事件监听即可实现 Comet 方案 HTTP 长轮询。
     * @param $request HTTP请求信息对象，包含header、get、post、cookie等相关信息
     * @param $response HTTP响应对象，支持cookie、header、status等HTTP操作
     */
    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
        $this->_server = $this->_ws;
        $data = $request->post ?? array();
        if (!empty($data['data'])) {
            $data = json_decode($data['data'], true);
        }
        if (isset($data['broadcast_type'])) {
            if ($data['broadcast_type'] == 'send_to_uid') {
                # 单发
                if (!empty($data['uid'])) {
                    if ($this->isUidOnline($data['uid'])) {
            		    $this->sendToUid($data['uid'], $this->json($data));
            			# 返回消息
            			$this->endRequest('200', $data, $request, $response);
            		} else {
            			# 返回消息
            			$this->endRequest('400', array('msg' => '客户已下线'), $request, $response);
            		}
                } else {
                    # 返回消息
            		$this->endRequest('400', array('msg' => '客户参数缺失'), $request, $response);
                }
            } else if ($data['broadcast_type'] == 'send_to_group') {
                # 群发
                if (!empty($data['room_id'])) {
                    $this->sendToGroup($data['room_id'], $this->json($data));
                } else {
                    # 返回消息
            		$this->endRequest('400', array('msg' => '房间参数缺失'), $request, $response);
                }
            } else if ($data['broadcast_type'] == 'send_to_all') {
                # 广播
                $this->sendToAll($this->json($data));
            }
        }
        # 返回消息
    	$this->endRequest('500', array('msg' => '请指定广播类型'), $request, $response);
    }
    /**
     * TCP 客户端连接关闭后，在 Worker 进程中回调此函数。
     * @param $server
     * @param $fd
     * @param $reactorId 主动 close 关闭时为负数，只有在 PHP 代码中主动调用 close 方法被视为主动关闭；心跳检测是由心跳检测线程通知关闭的，关闭时 onClose 的 $reactorId 参数不为 -1
     */
    public function onClose(\Swoole\Server $server, $fd, $reactorId) {
        if ($reactorId < 0) {
            echo $fd . ' closed by Server' . PHP_EOL; 
        } else {
            echo 'reactor线程[' . $reactorId . '] ' . $fd . ' closed by Client' . PHP_EOL;
        }
        //退出群聊
        $this->leaveGroup($fd);
        //解除uid绑定
        $this->unBindUid($fd);
        //定时清除
        $this->clear_cache_timer();
    }
    /**
	 * request事件返回值
	*/
	private function endRequest($code, $data, $request, $response) {
	    # 使用 Chrome 浏览器访问服务器，会产生额外的一次请求，/favicon.ico，可以在代码中响应 404 错误。
	    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $json = [
			'code' => $code,
			'data' => $data,
		];
		# 输出响应
		$return = $this->json($json);
		$response->end($return);
	}
	private function clear_cache_timer() {
	    $obj = $this;
	    //三分钟执行一次
	    \Swoole\Timer::tick(180 * 1000, function ($timer_id) use (&$obj) {
	        if (count($obj->_server->connections) <= 0) {
	            if (isset($obj->_rooms)) {
                    unset($obj->_rooms); 
                }
                if (isset($obj->_uids)) {
                    unset($obj->_uids); 
                }
	            $obj->vkcache('_rooms', null);
	            $obj->vkcache('_uids', null);
	            \Swoole\Timer::clear($timer_id);
	        }
	        
	    });
	}
	/**
	 * 数组转json
	 * @param array $array 数组
	 * @return json
	*/
	private function json($array) {
		return json_encode($array, JSON_UNESCAPED_UNICODE);
	}
	private function joinGroup($client, $room_id) {
	    $this->_rooms = $this->vkcache('_rooms') ? fxy_unserialize($this->vkcache('_rooms')) : [];
	    $client_ids = isset($this->_rooms[$room_id]) ? $this->_rooms[$room_id] : array();
	    if ($client_ids) {
	        $client_ids = array_flip($client_ids);
	    }
	    //防止重复加入
	    if (!isset($client_ids[$client]) && $this->isOnline($client)) {
	        $this->_rooms[$room_id][] = $client;
	        $this->vkcache('_rooms', serialize($this->_rooms));
	    }
	}
	/*离开房间*/
	private function leaveGroup($client, $room_id = 0) {
	    $this->_rooms = $this->vkcache('_rooms') ? fxy_unserialize($this->vkcache('_rooms')) : [];
	    if ($room_id) {
	        $client_ids = isset($this->_rooms[$room_id]) ? $this->_rooms[$room_id] : [];
	        foreach ($client_ids as $k => $client_id) {
                //附带清理房间其他不在线得人，减少循环
                if ($client == $client_id || !$this->isOnline($client_id)) {
                    if (isset($this->_rooms[$room_id][$k])) {
                        unset($this->_rooms[$room_id][$k]); 
                    }
                }
            }
	    } else {
            foreach ($this->_rooms as $gorup => $client_ids) {
                foreach ($client_ids as $k => $client_id) {
                    //附带清理房间其他不在线得人，减少循环
                    if ($client == $client_id || !$this->isOnline($client_id)) {
                        if (isset($this->_rooms[$gorup][$k])) {
                           unset($this->_rooms[$gorup][$k]); 
                        }
                    }
                }
            }
	    }
	    $this->vkcache('_rooms', serialize($this->_rooms));
	}
	/*解散房间*/
	private function unGroup($room_id) {
	    $this->_rooms = $this->vkcache('_rooms') ? fxy_unserialize($this->vkcache('_rooms')) : [];
	    if (isset($this->_rooms[$room_id])) {
	        unset($this->_rooms[$room_id]);
	        $this->vkcache('_rooms', serialize($this->_rooms));
	    }
	}
	private function bindUid($client, $uid) {
	    //干掉无效绑定
	    $this->_uids = $this->vkcache('_uids') ? fxy_unserialize($this->vkcache('_uids')) : [];
	    foreach ($this->_uids as $k => $v) {
	        if (!$this->isOnline($k)) {
                unset($this->_uids[$k]);
            }
	    }
	    //支持多端绑定同一个uid
	    $this->_uids[$client] = $uid;
	    $this->vkcache('_uids', serialize($this->_uids));
	}
	private function unBindUid($client) {
	    $this->_uids = $this->vkcache('_uids') ? fxy_unserialize($this->vkcache('_uids')) : [];
	    if (isset($this->_uids[$client])) {
	        unset($this->_uids[$client]);
	        $this->vkcache('_uids', serialize($this->_uids));
	    }
	}
	private function isUidOnline($uid) {
	    $this->_uids = $this->vkcache('_uids') ? fxy_unserialize($this->vkcache('_uids')) : [];
	    if ($this->_uids) {
	        $return = false;
	        foreach ($this->_uids as $k => $v) {
    	        if ($this->isOnline($k)) {
                    $return = true;
                    break;
                }
    	    }
    	    return $return;
	    } else {
	        return false;
	    }
	}
	/*获取uid绑定得client列表*/
	private function getClientListByUid($uid) {
	    $this->_uids = $this->vkcache('_uids') ? fxy_unserialize($this->vkcache('_uids')) : [];
	    $return = [];
	    foreach ($this->_uids as $k => $v) {
	        if ($v == $uid) {
	            $return[] = $k;
	        }
        }
        return $return;
	}
	private function sendToGroup($room_id, $data, $without_client_list = []) {
	    echo 'sendToGroup' . PHP_EOL;
	    $this->_rooms = $this->vkcache('_rooms') ? fxy_unserialize($this->vkcache('_rooms')) : [];
	    $client_ids = isset($this->_rooms[$room_id]) ? $this->_rooms[$room_id] : array();
	    foreach ($client_ids as $k => $client) {
	        echo 'ROOM_CLIENT：' . $client . PHP_EOL;
            if ($without_client_list && in_array($client, $without_client_list)) {
                continue;
            }
            $this->sendToClient($client, $data);
        }
	}
	private function sendToClient($client, $data) {
	    if (empty($data)) {
	        return false;
	    }
	    # 需要先判断是否是正确的websocket连接，否则有可能会push失败
	    if ($this->_server->isEstablished($client)) {
	        $this->_server->push($client, $data);
	    }
	    return true;
	}
	private function sendToUid($uid, $data, $without_client_list = []) {
	    $this->_uids = $this->vkcache('_uids') ? fxy_unserialize($this->vkcache('_uids')) : [];
	    foreach ($this->_server->connections as $fd) {//遍历TCP连接迭代器，拿到每个在线的客户端id
            if ($without_client_list && in_array($fd, $without_client_list)) {
                continue;
            }
            if (isset($this->_uids[$fd]) && $this->_uids[$fd] == $uid) {
               $this->sendToClient($fd, $data);
            }
        }
        return true;
	}
	private function getClientCountByGroup($room_id) {
	    $this->_rooms = $this->vkcache('_rooms') ? fxy_unserialize($this->vkcache('_rooms')) : [];
	    return isset($this->_rooms[$room_id]) ? count($this->_rooms[$room_id]) : 0;
	}
	private function isOnline($client) {
	    if ($this->_server->isEstablished($client)) {
            return true;
        } else {
            return false;
        }
	}
	private function sendToAll($data, $without_client_list = []) {
	    echo 'sendToAll' . PHP_EOL;
	    #connections连接迭代器依赖pcre库，未安装pcre库无法使用此功能
        foreach ($this->_server->connections as $fd) {//遍历TCP连接迭代器，拿到每个在线的客户端id
            if ($without_client_list && in_array($fd, $without_client_list)) {
                continue;
            }
            echo $fd . PHP_EOL;
            $this->sendToClient($fd, $data);
        }
	}
}