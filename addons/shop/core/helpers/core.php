<?php
/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * input('id',0); 获取id参数 自动判断get或者post
 * input('post.name','','htmlspecialchars'); 获取$_POST['name']
 * input('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function input($name = '', $default = '', $filter = null, $datas = null, $safe = false) {
    static $_PUT = null;
    if (strpos($name, '/')) { // 指定修饰符
        list($name, $type) = explode('/', $name, 2);
    }
    if (strpos($name, '.')) { // 指定参数来源
        list($method, $name) = explode('.', $name, 2);
    } else { // 默认为自动判断
        $method = 'param';
    }
    switch (strtolower($method)) {
        case 'get':
            $input = & $_GET;
            break;

        case 'post':
            $input = & $_POST;
            break;

        case 'put':
            if (is_null($_PUT)) {
                parse_str(file_get_contents('php://input') , $_PUT);
            }
            $input = $_PUT;
            break;

        case 'param':
			$REQUEST_METHOD = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
            switch ($REQUEST_METHOD) {
                case 'POST':
                    $input = $_POST;
                    break;

                case 'PUT':
                    if (is_null($_PUT)) {
                        parse_str(file_get_contents('php://input') , $_PUT);
                    }
                    $input = $_PUT;
                    break;

                default:
                    $input = $_GET;
            }
            break;

        case 'request':
            $input = & $_REQUEST;
            break;

        case 'session':
            $input = & $_SESSION;
            break;

        case 'cookie':
            $input = & $_COOKIE;
            break;

        case 'server':
            $input = & $_SERVER;
            break;

        case 'data':
            $input = & $datas;
            break;

        default:
            return null;
    }
    if ('' == $name) { // 获取全部变量
        $data = $input;
        $filters = !empty($filter) ? $filter : config('default_filter');
        if ($filters) {
            if (is_string($filters)) {
                $filters = explode(',', $filters);
            }
            foreach ($filters as $filter) {
                $data = array_map_recursive($filter, $data); // 参数过滤

            }
        }
    } elseif (isset($input[$name])) { // 取值操作
        $data = $input[$name];
        $filters = isset($filter) ? $filter : config('default_filter');
        if ($filters) {
            if (is_string($filters)) {
                if (0 === strpos($filters, '/')) {
                    if (1 !== preg_match($filters, (string)$data)) {
                        // 支持正则验证
                        return isset($default) ? $default : null;
                    }
                } else {
                    $filters = explode(',', $filters);
                }
            } elseif (is_int($filters)) {
                $filters = array(
                    $filters
                );
            }
            if (is_array($filters)) {
                foreach ($filters as $filter) {
                    if (function_exists($filter)) {
                        $data = is_array($data) ? array_map_recursive($filter, $data) : $filter($data); // 参数过滤

                    } else {
                        $data = filter_var($data, is_int($filter) ? $filter : filter_id($filter));
                        if (false === $data) {
                            return isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if (!empty($type)) {
            switch (strtolower($type)) {
                case 'a': // 数组
                    $data = (array)$data;
                    break;

                case 'd': // 数字
                    $data = (int)$data;
                    break;

                case 'f': // 浮点
                    $data = (float)$data;
                    break;

                case 'b': // 布尔
                    $data = (boolean)$data;
                    break;

                case 's': // 字符串

                default:
                    $data = (string)$data;
            }
        }
    } else { // 变量默认值
        $data = isset($default) ? $default : null;
    }
    is_array($data) && array_walk_recursive($data, 'safe_filter');
    $data = $safe && is_string($data) ? \base\dispatcherUrl::safe_filter($data) : $data;
    return $data;
}
function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val) ? array_map_recursive($filter, $val) : call_user_func($filter, $val);
    }
    return $result;
}
/**
 * 优化的require_once
 * @param string $filename 文件地址
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists($filename)) {
            //require $filename;
            $_importFiles[$filename] = require $filename;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
function config($name = null, $value = null, $default = null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (strpos($name, '.') === false) {
            $name = strtolower($name);
            if (is_null($value)) return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $c1 = array_shift($name);
        $c2 = array_shift($name);
        if (is_null($value)) return isset($_config[$c1][$c2]) ? $_config[$c1][$c2] : $default;
        $_config[$c1][$c2] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)) {
        $_config = array_merge($_config, array_change_key_case($name, CASE_LOWER));
        return null;
    }
    return null; // 避免非法参数

}
function autoload($class) {
    if (false !== strpos($class, '\\')) {
        $tmpArr = explode('\\', $class);
        $name = array_shift($tmpArr);
        if (in_array($name, array(
            'base',
			'lib'
        )) || is_dir(FRAMEWORK_PATH . '/' . $name)) {
            // 框架目录下面的命名空间自动定位
            $path = FRAMEWORK_PATH;
        } else if(in_array($name, array(
            'model',
			'logic',
			'table'
        )) || is_dir(COMMON_PATH . '/' . $name)) {
			$path = COMMON_PATH;
		} else {
            $path = dirname(APP_PATH . '/' . $name);
        }
        $filename = $path . '/' . str_replace('\\', '/', $class) . '.php';
        if (!require_cache($filename)) {
            return false;
        }
    } else {
		return false;
	}
}
/**
 * 实例化模型类
 * @param string $name 资源地址
 * @return object
 */
function model($name = null, $dbconfig = array()) {
    static $_model = array();
	if (empty($dbconfig)) {
        $dbconfig = config('db');
    }
	$safekey = explode('session', decrypt(config(base64_decode('bWQ1a2V5'))));
	$key = md5($safekey[0] . $name . '.' . serialize($dbconfig));
    if (isset($_model[$key])) return $_model[$key];
	$class_name = '\\model\\' . $name;
	if (class_exists($class_name)) {
		$db = new $class_name($dbconfig);
	} else {
	    if (is_null($name)) {
			$db = new base\model($dbconfig);
		} else {
			$db = new base\model($dbconfig);
			//如果表结构不存在，则获取表结构
			$db->setTable($name);
		}
	}
    $_model[$key] = $db;
    if (!IS_CLI) {
        $d = strtolower(stristr($_SERVER['HTTP_HOST'], $safekey[0]));
    //  if ($d != strtolower($safekey[0])) {
    //      exit();
    //  }
    }
    return $db;
}
/**
 * 实例化模型类
 * @param string $name Model名称
 * @param string $tablePrefix 表前缀
 * @param mixed $connection 数据库连接信息
 * @return Think\Model
 */
function db($name = '', $tablePrefix = '', $connection = array()) {
    if (empty($name)) return new base\db;
    static $_model = array();
    $safekey = explode('session', decrypt(config(base64_decode('bWQ1a2V5'))));
	$key = md5($safekey[0] . $name);
    if (isset($_model[$key])) return $_model[$key];
    $class = '\\table\\' . $name;
    if (class_exists($class)) {
        $model = new $class($name, $tablePrefix, $connection);
    } else {
        $model = new base\db($name, $tablePrefix, $connection);
    }
    $_model[$key] = $model;
    if (!IS_CLI) {
        $d = strtolower(stristr($_SERVER['HTTP_HOST'], $safekey[0]));
    	if ($d != strtolower($safekey[0])) {
    		exit();
    	}
    }
    return $model;
}
/**
 * 行为模型实例
 * @param string $name 资源地址
 * @return object
 */
function logic($name = null) {
    static $_cache = array();
    if (isset($_cache[$name])) return $_cache[$name];
	$class_name = '\\logic\\' . $name;
	if(class_exists($class_name)){
		$logic = new $class_name();
	}else{
		$error = 'Logic Error:  Class ' . $class_name . ' is not exists!';
		throw new Exception($error);
	}
    $_cache[$name] = $logic;
    return $logic;
}
/**
 * URL组装
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]
 * @param array $vars 传入的参数，支持数组
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function _url($url = '', $vars = [], $suffix = 'html', $domain = false) {
    $url = base\route::buildUrl($url, $vars, $suffix, $domain);
    return $url;
}
/**
 * 判断是否SSL协议
 * @return boolean
 */
function is_ssl() {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        return true;
    } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}
/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id   数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xml_encode($data, $root = 'php', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
    if (is_array($attr)) {
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = $key . '=' . $value;
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : ' ' . $attr;
    $xml = '<?xml version=\'1.0\' encoding=\'' . $encoding . '\'?>';
    $xml.= '<' . $root . $attr . '>';
    $xml.= data_to_xml($data, $item, $id);
    $xml.= '</' . $root . '>';
    return $xml;
}
/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function data_to_xml($data, $item = 'item', $id = 'id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = ' ' . $id . '=' . $key;
            $key = $item;
        }
        $xml.= '<' . $key . $attr . '>';
        $xml.= (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml.= '</' . $key . '>';
    }
    return $xml;
}
function xmlToArray($xml){
    return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
}
/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf('%u', ip2long($ip));
    $ip = $long ? array(
        $ip,
        $long
    ) : array(
        '0.0.0.0',
        0
    );
    return $ip[$type];
}
/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}
function safe_filter(&$value) {
    // TODO 其他安全过滤
    // 过滤查询特殊字符
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}
/**
 * 加密函数
 *
 * @param string $txt 需要加密的字符串
 * @param string $key 密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = '') {
	if (empty($txt)) return $txt;
	if (empty($key)) $key = md5(MD5_KEY);
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_*';
	$ikey ='-x6g6ZWm2G9g_vr0Bo*pOq3kRIxsZ6rm';
	$nh1 = rand(0, 64);
	$nh2 = rand(0, 64);
	$nh3 = rand(0, 64);
	$ch1 = $chars[$nh1];
	$ch2 = $chars[$nh2];
	$ch3 = $chars[$nh3];
	$nhnum = $nh1 + $nh2 + $nh3;
	$knum = 0;
	$i = 0;
	while(isset($key[$i])) $knum += ord($key[$i++]);
	$mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum%8, $knum%8 + 16);
	$txt = base64_encode(time() . '_' . $txt);
	$txt = str_replace(array('+','/','='), array('-','_','.'), $txt);
	$tmp = '';
	$j = 0;
	$k = 0;
	$tlen = strlen($txt);
	$klen = strlen($mdKey);
	for ($i=0; $i<$tlen; $i++) {
		$k = $k == $klen ? 0 : $k;
		$j = ($nhnum + strpos($chars, $txt[$i]) + ord($mdKey[$k++]))%64;
		$tmp .= $chars[$j];
	}
	$tmplen = strlen($tmp);
	$tmp = substr_replace($tmp, $ch3, $nh2 % ++$tmplen, 0);
	$tmp = substr_replace($tmp, $ch2, $nh1 % ++$tmplen, 0);
	$tmp = substr_replace($tmp, $ch1, $knum % ++$tmplen, 0);
	return $tmp;
}

/**
 * 解密函数
 *
 * @param string $txt 需要解密的字符串
 * @param string $key 密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0) {
	if (empty($txt)) return $txt;
	if (empty($key)) $key = md5(MD5_KEY);
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_*';
	$ikey = '-x6g6ZWm2G9g_vr0Bo*pOq3kRIxsZ6rm';
	$knum = 0;
	$i = 0;
	$tlen = strlen($txt);
	while(isset($key[$i])) $knum += ord($key[$i++]);
	$ch1 = $txt[$knum % $tlen];
	$nh1 = strpos($chars, $ch1);
	$txt = substr_replace($txt, '', $knum % $tlen--, 1);
	$ch2 = $txt[$nh1 % $tlen];
	$nh2 = strpos($chars, $ch2);
	$txt = substr_replace($txt, '', $nh1 % $tlen--, 1);
	$ch3 = $txt[$nh2 % $tlen];
	$nh3 = strpos($chars, $ch3);
	$txt = substr_replace($txt, '', $nh2 % $tlen--, 1);
	$nhnum = $nh1 + $nh2 + $nh3;
	$mdKey = substr(md5(md5(md5($key . $ch1) . $ch2 . $ikey) . $ch3), $nhnum % 8, $knum % 8 + 16);
	$tmp = '';
	$j = 0;
	$k = 0;
	$tlen = strlen($txt);
	$klen = strlen($mdKey);
	for ($i=0; $i<$tlen; $i++) {
		$k = $k == $klen ? 0 : $k;
		$j = strpos($chars, $txt[$i]) - $nhnum - ord($mdKey[$k++]);
		while ($j<0) $j+=64;
		$tmp .= $chars[$j];
	}
	$tmp = str_replace(array('-','_','.'), array('+','/','='), $tmp);
	$tmp = trim(base64_decode($tmp));

	if (preg_match('/\d{10}_/s', substr($tmp, 0, 11))) {
		if ($ttl > 0 && (time() - substr($tmp, 0, 11) > $ttl)) {
			$tmp = null;
		} else {
			$tmp = substr($tmp, 11);
		}
	}
	return $tmp;
}
/**
 * 将字符部分加密并输出
 * @param unknown $str
 * @param unknown $start 从第几个位置开始加密(从1开始)
 * @param unknown $length 连续加密多少位
 * @return string
 */
function encryptShow($str, $start, $length)
{
    $end = $start - 1 + $length;
    $array = str_split($str);
    foreach ($array as $k => $v) {
        if ($k >= $start - 1 && $k < $end) {
            $array[$k] = '*';
        }
    }
    return implode('', $array);
}
//根据数组中指定值排序
function fxy_array_sort($arr, $keys, $type = 'asc')
{
	$keysvalue = $new_array = array();
	foreach ($arr as $k => $v) {
		$keysvalue[$k] = $v[$keys];
	}
	if ($type == 'asc') {
		//对数组进行排序并保持索引关系
		asort($keysvalue);
	} else {
		//对数组进行逆向排序并保持索引关系
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k => $v) {
		$new_array[] = $arr[$k];
	}
	return $new_array;
}
function output_data($datas = array(), $extend_data = array())
{
	if (isset($datas['error'])) {
		$code = 400;
		$msg = $datas['error'];
		unset($datas['error']);
	} else if (is_string($datas)) {
		$code = 200;
		$msg = $datas;
		$datas = array();
	} else {
		$code = 200;
		$msg = '';
	}
	if (!empty($extend_data)) {
		$datas = array_merge($datas, $extend_data);
	}
	$json_data = callback($code, $msg, $datas);
	header('Content-Type:application/json; charset=utf-8');
	if (!empty($_GET['callback'])) {
		echo $_GET['callback'] . '(' . json_encode($json_data, JSON_UNESCAPED_UNICODE) . ')';
		die;
	} else {
	    if (IS_CLI) {
	        echo json_encode($json_data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
	    } else {
	        echo json_encode($json_data, JSON_UNESCAPED_UNICODE);
	    }
		die;
	}
}
function output_error($message = '', $extend_data = array())
{
    $datas = array('error' => $message);
    output_data($datas, $extend_data);
}
function web_error($message, $jumpUrl = '') {
   dispatchJump($message, $jumpUrl, $status = 0);
}

function web_success($message, $jumpUrl = '') {
	dispatchJump($message, $jumpUrl, $status = 1);
}
/**
 * 默认跳转操作 支持错误导向和正确跳转
 * 调用模板显示 默认为框架目录下面的msg_jump.php页面
 * @param string $message 提示信息
 * @param string $jumpUrl 页面跳转地址
 * @param Boolean $status 状态
 * @access protected
 * @return void
 */
function dispatchJump($message, $jumpUrl = '', $status = 1) {
    // 提示标题
	$msgTitle = $status ? '操作成功' : '操作失败';
    if($status) { //发送成功信息
        // 成功操作后默认停留1秒
        if(!isset($waitSecond)) $waitSecond = 1;
        // 默认操作成功自动返回操作前页面
        if(empty($jumpUrl)) $jumpUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		require(FRAMEWORK_PATH . '/views/msg_jump.php');
    }else {
        //发生错误时候默认停留3秒
        if(!isset($waitSecond)) $waitSecond = 3;
        // 默认发生错误的话自动返回上页
        if(empty($jumpUrl)) $jumpUrl = 'javascript:history.back(-1)';
		require(FRAMEWORK_PATH . '/views/msg_jump.php');
        // 中止执行  避免出错后继续执行
    }
    exit;
}
/**
 * 规范数据返回函数
 * @param unknown $state
 * @param unknown $msg
 * @param unknown $data
 * @return multitype:unknown
 */
function callback($state = true, $msg = '', $data = array())
{
    return array('state' => $state, 'msg' => $msg, 'data' => $data);
}
/**
 * 缓存管理
 * @param mixed $name 缓存名称
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function vkcache($name, $value = '', $options = null) {
    static $cache = '';
    if (is_array($options)) {
        // 缓存操作的同时初始化
        $cache = \base\cache::connect($options);
    } else if (empty($cache)) { // 自动初始化
        $cache = \base\cache::connect();
    }
    if ('' === $value) { // 获取缓存
        return $cache->get($name);
    } elseif (is_null($value)) { // 删除缓存
        return $cache->rm($name);
    } else { // 缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : NULL;
        } else {
            $expire = is_numeric($options) ? $options : NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}
/**
 * 返回以原数组某个值为下标的新数据
 *
 * @param array $array
 * @param string $key
 * @param int $type 1一维数组2二维数组
 * @return array
 */
function array_under_reset($array, $key, $type = 1)
{
    if (is_array($array)) {
        $tmp = array();
        foreach ($array as $v) {
            if ($type === 1) {
                $tmp[$v[$key]] = $v;
            } elseif ($type === 2) {
                $tmp[$v[$key]][] = $v;
            }
        }
        return $tmp;
    } else {
        return $array;
    }
}
//二维数组根据指定元素键去重
function second_array_unique_bykey($arr, $key) {
	$tmp_arr = array();
	foreach($arr as $k => $v) {
		if(in_array($v[$key], $tmp_arr)) {   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
			unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值
		} else {
			$tmp_arr[$k] = $v[$key];  //将不同的值放在该数组中保存
		}
	}
	//ksort($arr); //ksort函数对数组进行排序(保留原键值key)  sort为不保留key值
	return $arr;
}
/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function random($length, $numeric = 0)
{
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? str_replace('0', '', $seed) . '012340567890' : $seed . 'zZ' . strtoupper($seed);
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}
/**
 * 批量更新函数
 * @param $data array 待更新的数据，二维数组格式
 * @param array $params array 值相同的条件，键值对应的一维数组
 * @param string $field string 值不同的条件，默认为id
 * @return bool|string
 */
function batchUpdate($table, $data, $field, $params = [], $exe = false)
{
	if (!is_array($data) || !$field || !is_array($params)) {
		return false;
	}
	$updates = _parseUpdate($data, $field, $exe);
	$where = _parseParams($params, $exe);

	// 获取所有键名为$field列的值，值两边加上单引号，保存在$fields数组中
	// array_column()函数需要PHP5.5.0+，如果小于这个版本，可以自己实现，
	// 参考地址：http://php.net/manual/zh/function.array-column.php#118831
	$fields = array_column($data, $field);
	$fields = implode(',', array_map(function($value) {
		return '\'' . $value . '\'';
	}, $fields));
	$sql = sprintf('UPDATE `%s` SET %s WHERE `%s` IN (%s) %s', $table, $updates, $field, $fields, $where);
	return $sql;
}

/**
 * 将二维数组转换成CASE WHEN THEN的批量更新条件
 * @param $data array 二维数组
 * @param $field string 列名
 * @return string sql语句
 */
function _parseUpdate($data, $field, $exe)
{
	$sql = '';
	$keys = array_keys(current($data));
	foreach ($keys as $column) {
		if ($column == $field) {
			continue;
		}
		$sql .= sprintf('`%s` = CASE `%s` ' . PHP_EOL, $column, $field);
		foreach ($data as $line) {
			if (is_numeric($line[$column])) {
				$sql .= sprintf('WHEN \'%s\' THEN \'%d\' ' . PHP_EOL, $line[$field], $line[$column]);
			} else {
				if ($exe) {
					$sql .= sprintf('WHEN \'%s\' THEN %s ' . PHP_EOL, $line[$field], $line[$column]);
				} else {
					$sql .= sprintf('WHEN \'%s\' THEN \'%s\' ' . PHP_EOL, $line[$field], $line[$column]);
				}
			}
		}
		$sql .= 'END,';
	}
	return rtrim($sql, ',');
}

/**
 * 解析where条件
 * @param $params
 * @return array|string
 */
function _parseParams($params, $exe)
{
	$where = [];
	foreach ($params as $key => $value) {
		if ($exe) {
			$where[] = $value;
		} else {
			if (is_numeric($value)) {
				$where[] = sprintf('`%s` = \'%d\'', $key, $value);
			} else {
				$where[] = sprintf('`%s` = \'%s\'', $key, $value);
			}
		}
	}
	return $where ? ' AND ' . implode(' AND ', $where) : '';
}
// 过滤掉emoji表情
function filterEmoji($str)
{
	$str = preg_replace_callback( '/./u',
	function (array $match) {
		return strlen($match[0]) >= 4 ? '' : $match[0];
	},
	$str);
   return $str;
}
/**
 * 异步执行
 * $rule string 路由规则 logic/fun
 * $param array 其它参数
 * 宝塔不要开启强制跳转https
 */
function asynRun($rule, $param, $debug = false, $timeout = 10) {
    $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/asyn.php';
    $urlinfo = parse_url($url);
    $host = $urlinfo['host'];
    $path = $urlinfo['path'];
    $param['rule'] = $rule;
    $param['postsigntime'] = time();
    $param['timeout'] = $timeout;
    $query = isset($param) ? http_build_query($param) : '';
    // 注释掉签名生成
    // $sign = md5($query . config('md5key'));
    // $query .= '&sign=' . $sign;
    $port = 80;
    $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$fp) {
        echo $errstr . '(' . $errno . ')' . PHP_EOL;
        return false;
    }
    # 如果webserver是nginx，需要开启非阻塞模式
    stream_set_blocking($fp, false);
    stream_set_timeout($fp, 3); //设置超时时间（s）
    $header = 'POST ' . $path . ' HTTP/1.1' . PHP_EOL;
    $header .= 'Host:' . $host . PHP_EOL;
    $header .= 'Content-Length: ' . strlen($query) . PHP_EOL;
    $header .= 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL;
    $header .= 'Connection: Close' . PHP_EOL . PHP_EOL;
    $header .= $query;
    $d = fputs($fp, $header);
    # 等待10ms给足够时间把请求转到fastcgi去执行（nginx）
	usleep(1000);
	if ($debug) {
        $html = '';
        while (!feof($fp)) {
            $html .= fgets($fp);
        }
        echo $html;
    }
    fclose($fp);
}
/**
 * 前台生成密码hash值
 * @param $password
 * @return string
 */
function f_hash($password) {
    return md5('@by_' . md5(md5($password) . config('md5key')));
}
/**
 * 获取允许绩效分红的级别ID列表
 * @param int $uniacid 租户ID，默认为1
 * @return array 级别ID数组
 */
function get_yeji_fenhong_level_ids($uniacid = 1) {
    $config = model('config')->getInfo(array('uniacid' => $uniacid), 'yeji_fenhong_level_ids');
    if (empty($config['yeji_fenhong_level_ids'])) {
        return array();
    }
    $ids = explode(',', trim($config['yeji_fenhong_level_ids'], ','));
    return array_filter(array_map('intval', $ids));
}
