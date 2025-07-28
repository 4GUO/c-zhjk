<?php
/**
 * 取验证码hash值
 *
 * @param
 * @return string 字符串类型的返回结果
 */
function getUrlhash($act = '', $op = '') {
    $act = $act ? : base\dispatcherUrl::getController();
    $op = $op ? : base\dispatcherUrl::getAction();
    return substr(md5(SITE_URL . $act . $op) , 0, 8);
}
/**
 * 产生验证码
 *
 * @param string $myhash 哈希数
 * @return string
 */
function makeSeccode() {
    $seccode = fxy_random(6, 1);
    $seccodeunits = '';
    $s = sprintf('%04s', base_convert($seccode, 10, 23));
    $seccodeunits = 'ABCEFGHJKMPRTVXY2346789';
    if ($seccodeunits) {
        $seccode = '';
        for ($i = 0; $i < 4; $i++) {
            $unit = ord($s[$i]);
            $seccode.= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
        }
    }
    return $seccode;
}
/**
 * 设置cookie
 *
 * @param string $name cookie 的名称
 * @param string $value cookie 的值
 * @param int $expire cookie 有效周期
 * @param string $path cookie 的服务器路径 默认为 /
 * @param string $domain cookie 的域名
 * @param string $secure 是否通过安全的 HTTPS 连接来传输 cookie,默认为false
 */
function setMyCookie($name, $value, $expire = '3600', $path = '', $domain = '', $secure = false) {
    if (empty($path)) $path = '/';
    if (empty($domain)) $domain = $_SERVER['HTTP_HOST'];
    $name = defined('COOKIE_PRE') ? COOKIE_PRE . $name : strtoupper(substr(md5(MD5_KEY) , 0, 4)) . '_' . $name;
    $expire = intval($expire) ? intval($expire) : (intval(SESSION_EXPIRE) ? intval(SESSION_EXPIRE) : 3600);
    $result = setcookie($name, $value, time() + $expire, $path, $domain, $secure);
    $_COOKIE[$name] = $value;
}
/**
 * 取得COOKIE的值
 *
 * @param string $name
 * @return unknown
 */
function getMyCookie($name = '') {
    $name = defined('COOKIE_PRE') ? COOKIE_PRE . $name : strtoupper(substr(md5(MD5_KEY) , 0, 4)) . '_' . $name;
    return input('cookie.' . $name, '');
}
/**
 * 验证验证码
 *
 * @param string $myhash 哈希数
 * @param string $value 待验证值
 * @return boolean
 */
function checkSeccode($myhash, $value, $seccode_key) {
    // 注释掉验证码验证，直接返回true
    return true;
    
    /*
    list($checkvalue, $checktime, $checkidhash) = explode('\t', decrypt($seccode_key, MD5_KEY));
    $return = $checkvalue == strtoupper($value) && $checkidhash == $myhash;
    return $return;
    */
}
/**
 * 取得随机数
 *
 * @param int $length 生成随机数的长度
 * @param int $numeric 是否只产生数字随机数 1是0否
 * @return string
 */
function fxy_random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime() . BASE_PATH) , 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash.= $seed[mt_rand(0, $max)];
    }
    return $hash;
}
/**
 * 检测FORM是否提交
 * @param  $check_token 是否验证token
 * @param  $check_captcha 是否验证验证码
 * @param  $return_type 'alert','num'
 * @return boolean
 */
function chksubmit($check_token = false, $check_captcha = false) {
    $submit = input('form_submit', '');
    if ($submit != 'ok') return false;
    if ($check_token && !base\token::checkToken()) {
        return -11;
    }
    if ($check_captcha) {
        if (!checkSeccode(input('myhash', '') , input('captcha', '') , input('seccode_key', ''))) {
            return -12;
        }
    }
    return true;
}
function page($totalpage, $param = array() , $base_url = '', $is_ajax = false) {
    if (empty($param['page'])) {
        return false;
    }
    $fuhao = ((strpos($base_url, '?') !== false) ? '&' : '?');
    $get_p = $param['page'];
    //省略
    $maxpageno = 7;
    if ($totalpage > $maxpageno) {
        $half = floor(($maxpageno - 4) / 2);
        $half_start = $get_p - $half + 1;
        if ($maxpageno % 2 !== 0) {
            --$half_start;
        }
        $half_end = $get_p + $half;
    }
    if (($totalpage - $get_p) < ($maxpageno - 3)) {
        $half_start = $totalpage - $maxpageno + 3;
        unset($half_end);
    }
    if ($get_p <= ($maxpageno - 3)) {
        $half_end = $maxpageno - 2;
        unset($half_start);
    }
    //lib\logging::write(var_export($half_start, true));
    //lib\logging::write(var_export($half_end, true));
    $html = '<div class=\'quotes_page\'>';
    for ($i = 1; $i <= $totalpage; $i++) {
        if ($i == 1) {
            if ($get_p <= 1) {
                $html.= '<span class=\'disabled first\'> < </span>';
            } else {
                $param['page'] = $get_p - 1;
                $param_str = http_build_query($param);
                if ($is_ajax) {
                    $html.= '<a class=\'first select_page\' base_url=\'' . $base_url . '\' param_str=\'' . $param_str . '\' href=\'javascript:;\'> < </a>';
                } else {
                    $html.= '<a class=\'first\' href=\'' . $base_url . $fuhao . $param_str . '\'> < </a>';
                }
            }
        }
        if ($get_p != $i) {
            if (isset($half_start) && $i < $half_start && $i > 1) {
                if ($i == 2) $html.= '<span class=\'more\'>...</span>';
                continue;
            }
            if (isset($half_end) && $i > $half_end && $i < $totalpage) {
                if ($i == ($half_end + 1)) $html.= '<span class=\'more\'>...</span>';
                continue;
            }
            $param['page'] = $i;
            $param_str = http_build_query($param);
            if ($is_ajax) {
                $html.= '<a class=\'select_page\' base_url=\'' . $base_url . '\' param_str=\'' . $param_str . '\' href=\'javascript:;\'> ' . $i . ' </a>';
            } else {
                $html.= '<a href=\'' . $base_url . $fuhao . $param_str . '\'>' . $i . '</a>';
            }
        } else {
            $html.= '<span class=\'current\'>' . $i . '</span>';
        }
        if ($i == $totalpage) {
            if ($get_p >= $totalpage) {
                $html.= '<span class=\'disabled last\'> > </span>';
            } else {
                $param['page'] = $get_p + 1;
                $param_str = http_build_query($param);
                if ($is_ajax) {
                    $html.= '<a class=\'last select_page\' base_url=\'' . $base_url . '\' param_str=\'' . $param_str . '\' href=\'javascript:;\'> > </a>';
                } else {
                    $html.= '<a class=\'last\' href=\'' . $base_url . $fuhao . $param_str . '\'> > </a>';
                }
            }
        }
    }
    $html.= '</div>';
    return $html;
}
function fxy_unserialize($str = '') {
    if (empty($str)) {
        return '';
    }
    return unserialize(html_entity_decode(htmlspecialchars_decode($str)));
}
function fxy_json_decode($str = '') {
    if (empty($str)) {
        return '';
    }
    return json_decode(html_entity_decode(htmlspecialchars_decode($str)), true);
}
function users_url($segment, $params = array(), $suffix = 'html', $domain = false)
{
	$url = _url($segment, $params, $suffix, $domain);
	return $url;
}
function users_login_check() {
    $uniacid = input('session.uniacid', 0, 'intval');
    if (empty($uniacid)) {
        return 0;
    }
    return $uniacid;
}
function seller_login_check() {
    $store_id = input('session.store_id', 0, 'intval');
    if (empty($store_id)) {
        return 0;
    }
    return $store_id;
}
function front_url($segment, $params = array() , $suffix = false, $domain = false) {
	$url = _url($segment, $params, $suffix, $domain);
	return $url;
}
function api_url($segment, $params = array() , $suffix = false, $domain = false) {
    $url = _url($segment, $params, $suffix, $domain);
	return $url;
}
function uni_url($segment, $params = array(), $domain = false, $mode = 'history') {
	$paramurl = http_build_query($params , '' , '&');
	$uni_url = trim($segment, '?') . '?' . $paramurl;
	if ($domain) {
		if ($mode == 'hash') {
			$url = SITE_URL . '/wap/#/' . trim($uni_url, '/');
		} else {
			$url = SITE_URL . '/wap/' . trim($uni_url, '/');
		}
	} else {
		$url = $uni_url;
	}
	
	return $url;
}
/**
 * 编辑器内容
 *
 * @param int $id 编辑器id名称，与name同名
 * @param string $value 编辑器内容
 * @param string $width 宽 带px
 * @param string $height 高 带px
 * @param string $style 样式内容
 * @param string $upload_state 上传状态，默认是开启
 */
function showEditor($id, $value = '', $width = '100%', $height = '300px', $style = 'visibility:hidden;', $media_open = 'false', $type = 'all') {
    //是否开启多媒体
    $media = '';
    if ($media_open == 'true') {
        $media = ', \'flash\', \'media\'';
    }
    switch ($type) {
        case 'basic':
            $items = '[\'source\', \'|\', \'fullscreen\', \'undo\', \'redo\', \'cut\', \'copy\', \'paste\', \'|\', \'about\']';
            break;

        case 'simple':
            $items = '[\'source\', \'|\', \'fullscreen\', \'undo\', \'redo\', \'cut\', \'copy\', \'paste\', \'|\', \'fontname\', \'fontsize\', \'forecolor\', \'hilitecolor\', \'bold\', \'italic\', \'underline\', \'removeformat\', \'justifyleft\', \'justifycenter\', \'justifyright\', \'insertorderedlist\', \'insertunorderedlist\', \'|\', \'emoticons\', \'link\', \'|\', \'about\']';
            break;

        default:
            $items = '[\'source\', \'|\', \'fullscreen\', \'preview\', \'undo\', \'redo\', \'print\', \'cut\', \'copy\', \'paste\', \'plainpaste\', \'wordpaste\', \'code\', \'|\', \'justifyleft\', \'justifycenter\', \'justifyright\', \'justifyfull\', \'insertorderedlist\', \'insertunorderedlist\', \'indent\', \'outdent\', \'subscript\', \'superscript\', \'|\', \'selectall\', \'clearhtml\', \'quickformat\', \'|\', \'formatblock\', \'fontname\', \'fontsize\', \'|\', \'forecolor\', \'hilitecolor\', \'bold\', \'italic\', \'underline\', \'strikethrough\', \'lineheight\', \'removeformat\', \'|\'' . $media . ', \'table\', \'hr\', \'emoticons\', \'link\', \'unlink\', \'|\', \'about\']';
            break;
    }
    //Flash、视频、文件的本地上传都可开启
    echo '<textarea id=\'' . $id . '\' name=\'' . $id . '\' style=\'width:' . $width . ';height:' . $height . ';' . $style . '\'>' . $value . '</textarea>';
    echo '
	<script src=\'' . STATIC_URL . '/js/kindeditor/kindeditor-all.js\' charset=\'utf-8\'></script>
	<script src=\'' . STATIC_URL . '/js/kindeditor/lang/zh-CN.js\' charset=\'utf-8\'></script>
	<script>
		var ' . $id . ';
		KindEditor.ready(function(K) {
			' . $id . ' = K.create(\'textarea[name=' . $id . ']\', {
							items : ' . $items . ',
							cssPath : \'' . STATIC_URL . '/js/kindeditor/themes/default/default.css\',
							allowImageUpload : false,
							allowFlashUpload : ' . $media_open . ',
							allowMediaUpload : ' . $media_open . ',
							allowFileManager : ' . $media_open . ',
							syncType:\'form\',
							afterCreate : function() {
								var self = this;
								self.sync();
							},
							afterChange : function() {
								var self = this;
								self.sync();
							},
							afterBlur : function() {
								var self = this;
								self.sync();
							}
			});
			' . $id . '.appendHtml = function(id, val) {
				this.html(this.html() + val);
				if (this.isCreated) {
					var cmd = this.cmd;
					cmd.range.selectNodeContents(cmd.doc.body).collapse(false);
					cmd.select();
				}
				return this;
			}
		});
	</script>
	';
    return true;
}
function getBaseDomain($url = '') {
    if (!$url) {
        return $url;
    }
    //列举域名中固定元素
    $state_domain = array(
        'al',
        'dz',
        'af',
        'ar',
        'ae',
        'aw',
        'om',
        'az',
        'eg',
        'et',
        'ie',
        'ee',
        'ad',
        'ao',
        'ai',
        'ag',
        'at',
        'au',
        'mo',
        'bb',
        'pg',
        'bs',
        'pk',
        'py',
        'ps',
        'bh',
        'pa',
        'br',
        'by',
        'bm',
        'bg',
        'mp',
        'bj',
        'be',
        'is',
        'pr',
        'ba',
        'pl',
        'bo',
        'bz',
        'bw',
        'bt',
        'bf',
        'bi',
        'bv',
        'kp',
        'gq',
        'dk',
        'de',
        'tl',
        'tp',
        'tg',
        'dm',
        'do',
        'ru',
        'ec',
        'er',
        'fr',
        'fo',
        'pf',
        'gf',
        'tf',
        'va',
        'ph',
        'fj',
        'fi',
        'cv',
        'fk',
        'gm',
        'cg',
        'cd',
        'co',
        'cr',
        'gg',
        'gd',
        'gl',
        'ge',
        'cu',
        'gp',
        'gu',
        'gy',
        'kz',
        'ht',
        'kr',
        'nl',
        'an',
        'hm',
        'hn',
        'ki',
        'dj',
        'kg',
        'gn',
        'gw',
        'ca',
        'gh',
        'ga',
        'kh',
        'cz',
        'zw',
        'cm',
        'qa',
        'ky',
        'km',
        'ci',
        'kw',
        'cc',
        'hr',
        'ke',
        'ck',
        'lv',
        'ls',
        'la',
        'lb',
        'lt',
        'lr',
        'ly',
        'li',
        're',
        'lu',
        'rw',
        'ro',
        'mg',
        'im',
        'mv',
        'mt',
        'mw',
        'my',
        'ml',
        'mk',
        'mh',
        'mq',
        'yt',
        'mu',
        'mr',
        'us',
        'um',
        'as',
        'vi',
        'mn',
        'ms',
        'bd',
        'pe',
        'fm',
        'mm',
        'md',
        'ma',
        'mc',
        'mz',
        'mx',
        'nr',
        'np',
        'ni',
        'ne',
        'ng',
        'nu',
        'no',
        'nf',
        'na',
        'za',
        'aq',
        'gs',
        'eu',
        'pw',
        'pn',
        'pt',
        'jp',
        'se',
        'ch',
        'sv',
        'ws',
        'yu',
        'sl',
        'sn',
        'cy',
        'sc',
        'sa',
        'cx',
        'st',
        'sh',
        'kn',
        'lc',
        'sm',
        'pm',
        'vc',
        'lk',
        'sk',
        'si',
        'sj',
        'sz',
        'sd',
        'sr',
        'sb',
        'so',
        'tj',
        'tw',
        'th',
        'tz',
        'to',
        'tc',
        'tt',
        'tn',
        'tv',
        'tr',
        'tm',
        'tk',
        'wf',
        'vu',
        'gt',
        've',
        'bn',
        'ug',
        'ua',
        'uy',
        'uz',
        'es',
        'eh',
        'gr',
        'hk',
        'sg',
        'nc',
        'nz',
        'hu',
        'sy',
        'jm',
        'am',
        'ac',
        'ye',
        'iq',
        'ir',
        'il',
        'it',
        'in',
        'id',
        'uk',
        'vg',
        'io',
        'jo',
        'vn',
        'zm',
        'je',
        'td',
        'gi',
        'cl',
        'cf',
        'cn',
        'yr',
        'com',
        'arpa',
        'edu',
        'gov',
        'int',
        'mil',
        'net',
        'org',
        'biz',
        'info',
        'pro',
        'name',
        'museum',
        'coop',
        'aero',
        'xxx',
        'idv',
        'me',
        'mobi',
        'asia',
        'ax',
        'bl',
        'bq',
        'cat',
        'cw',
        'gb',
        'jobs',
        'mf',
        'rs',
        'su',
        'sx',
        'tel',
        'travel'
    );
    $preg_match = '/^http|^https/is';
    if (!preg_match($preg_match, $url)) {
        $url = (is_ssl() ? 'https://' : 'http://') . $url;
    }
    $res = array();
    $res['domain'] = null;
    $res['host'] = null;
    $url_parse = parse_url(strtolower($url));
    $urlarr = explode('.', $url_parse['host']);
    $count = count($urlarr);
    if ($count <= 2) {
        //当域名直接根形式不存在host部分直接输出
        $res['domain'] = $url_parse['host'];
    } elseif ($count > 2) {
        $last = array_pop($urlarr);
        $last_1 = array_pop($urlarr);
        $last_2 = array_pop($urlarr);
        $res['domain'] = $last_1 . '.' . $last;
        $res['host'] = $last_2;
        if (in_array($last, $state_domain)) {
            $res['domain'] = $last_1 . '.' . $last;
            $res['host'] = implode('.', $urlarr);
        }
        if (in_array($last_1, $state_domain)) {
            $res['domain'] = $last_2 . '.' . $last_1 . '.' . $last;
            $res['host'] = implode('.', $urlarr);
        }
        //print_r(get_defined_vars());die;
        
    }
    return $res;
}
/**
 * 获取签名 将链接地址的所有参数按字母排序后拼接加上token进行md5
 * @param $para 需要拼接的数组
 * return Sign
 */
function getSign($para, $md5_key) {
    $para_filter = array();
	foreach ($para as $key => $val) {
		if ($key == 'sign' || $key === 'oid' || $val === '') continue;
        else $para_filter[$key] = $para[$key];
	}
    ksort($para_filter);
    reset($para_filter);
    $arg_str = '';
	
	foreach ($para_filter as $key => $val) {
	    if (is_array($val)) {
	        continue;
	    }
	    $val = htmlspecialchars_decode($val);
        $arg_str.= $key . '=' . $val . '&';
    }
    //去掉最后一个&字符
    $arg_str = substr($arg_str, 0, strlen($arg_str) - 1);
    //如果存在转义字符，那么去掉转义
    $magic_quotes_gpc = ini_set('magic_quotes_runtime', 0) ? true : false;
    if ($magic_quotes_gpc) {
        $arg_str = stripslashes($arg_str);
    }
    $prestr = $arg_str . $md5_key;
    //\lib\logging::write(var_export($prestr, true));
    //\lib\logging::write(var_export(md5($prestr), true));
    return md5($prestr);
}
function front_upload_img_dir($uid) {
    return UPLOADFILES_PATH . '/front/image/' . $uid . '/';
}
function front_upload_img_url($uid) {
    return UPLOADFILES_URL . '/front/image/' . $uid . '/';
}
function front_upload_file_dir($uid) {
    return UPLOADFILES_PATH . '/front/file/' . $uid . '/';
}
function front_upload_file_url($uid) {
    return UPLOADFILES_URL . '/front/file/' . $uid . '/';
}
function front_upload_media_dir($uid) {
    return UPLOADFILES_PATH . '/front/media/' . $uid . '/';
}
function front_upload_media_url($uid) {
    return UPLOADFILES_URL . '/front/media/' . $uid . '/';
}
function cc_format($name) {
    $temp_array = array();
    for ($i = 0; $i < strlen($name); $i++) {
        $ascii_code = ord($name[$i]);
        if ($ascii_code >= 65 && $ascii_code <= 90) {
            if ($i == 0) {
                $temp_array[] = chr($ascii_code + 32);
            } else {
                $temp_array[] = '-' . chr($ascii_code + 32);
            }
        } else {
            $temp_array[] = $name[$i];
        }
    }
    return implode('', $temp_array);
}
function getNext(&$array, $curr_key) {
    $next = array();
    reset($array);
    do {
        $tmp_key = key($array);
        $res = next($array);
    } while (($tmp_key != $curr_key) && $res);
    if ($res) {
        $next = key($array);
    }
    return $next;
}
/**
 * 取得订单支付类型文字输出形式
 */
function orderPaymentName($payment_code) {
    return str_replace(array(
        'offline',
        'online',
        'ali_native',
        'alipay',
        'tenpay',
        'chinabank',
        'predeposit',
        'wxpay',
        'wx_jsapi',
        'wx_saoma'
    ) , array(
        '线下付款',
        '在线付款',
        '支付宝移动支付',
        '支付宝',
        '财付通',
        '网银在线',
        '站内余额支付',
        '微信支付[客户端]',
        '微信支付[jsapi]',
        '微信支付[扫码]'
    ) , $payment_code);
}
function get_wxapp_access_token($uniacid = '', $appid = '', $secret = '') {
    $access_token = base\cache::store('file')->get($uniacid . 'access_token');
    if ($access_token) {
        return $access_token;
    }
    $access_token_info = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret);
    $access_token_info = json_decode($access_token_info, true);
    $access_token = $access_token_info['access_token'];
    base\cache::store('file')->remember($uniacid . 'access_token', $access_token, 6900);
    return $access_token;
}
function send_wxapp_tpl_msg($template_id, $touser, $data, $form_id, $emphasis_keyword, $page_url = '', $uniacid = '', $appid = '', $secret = '') {
    $post_data = array(
        'touser' => $touser,
        'template_id' => $template_id,
        'page' => $page_url,
        'form_id' => $form_id,
        'data' => $data,
        'color' => '#ccc',
        'emphasis_keyword' => $emphasis_keyword
    );
    $access_token = get_wxapp_access_token($uniacid, $appid, $secret);
    $curl = new \lib\curl();
    $send_result = $curl->curl_post('https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=' . $access_token, $post_data);
    return $send_result;
}
/**
 * PHP 非递归实现查询该目录下所有文件
 * @param unknown $dir
 * @param array $exts 读取包含指定后缀的文件
 * @return multitype:|multitype:string
 */
function scanfiles($dir, $exts = array('php')) {
    if (!is_dir($dir)) return array();
    // 兼容各操作系统
    $dir = rtrim(str_replace('\\', '/', $dir) , '/') . '/';
    // 栈，默认值为传入的目录
    $dirs = array(
        $dir
    );
    // 放置所有文件的容器
    $result = array();
    do {
        // 弹栈
        $dir = array_pop($dirs);
        // 扫描该目录
        $tmp = scandir($dir);
        foreach ($tmp as $f) {
            // 过滤. ..
            if ($f == '.' || $f == '..') continue;
            // 组合当前绝对路径
            $path = $dir . $f;
            // 如果是目录，压栈。
            if (is_dir($path)) {
                array_push($dirs, $path . '/');
            } else if (is_file($path)) { // 如果是文件，放入容器中
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array($ext, $exts)) {
                    $result[] = $path;
                }
            }
        }
    }
    while ($dirs); // 直到栈中没有目录
    return $result;
} 
function randcode($length, $pid, $id) {
    $english = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'L',
        'M',
        'N',
        'P',
        'Q',
        'R',
        'T',
        'Y',
        'Z'
    );
    $pid = intval($pid);
    $yushu = $pid % count($english);
    if (strlen($id) < $length) {
        $id = $english[$yushu] . $id;
    }
    if (strlen($id) >= $length) {
        return $id;
    }
    $length = $length - strlen($id);
    $chars = '123456789';
    $temchars = '';
    for ($i = 0; $i < $length; $i++) {
        $temchars.= $chars[mt_rand(0, strlen($chars) - 1) ];
    }
    return $temchars . $id;
}
function fxy_cutstr($string, $length, $havedot = false, $charset = 'utf8') {
    if (strtolower($charset) == 'gbk') {
        $charset = 'gbk';
    } else {
        $charset = 'utf8';
    }
    if (fxy_istrlen($string, $charset) <= $length) {
        return $string;
    }
    if (function_exists('mb_strcut')) {
        $string = mb_substr($string, 0, $length, $charset);
    } else {
        $pre = '{%';
        $end = '%}';
        $string = str_replace(array(
            '&amp;',
            '&quot;',
            '&lt;',
            '&gt;'
        ) , array(
            $pre . '&' . $end,
            $pre . '"' . $end,
            $pre . '<' . $end,
            $pre . '>' . $end
        ) , $string);
        $strcut = '';
        $strlen = strlen($string);
        if ($charset == 'utf8') {
            $n = $tn = $noc = 0;
            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n+= 2;
                    $noc++;
                } elseif (224 <= $t && $t <= 239) {
                    $tn = 3;
                    $n+= 3;
                    $noc++;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n+= 4;
                    $noc++;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n+= 5;
                    $noc++;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n+= 6;
                    $noc++;
                } else {
                    $n++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n-= $tn;
            }
            $strcut = substr($string, 0, $n);
        } else {
            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t > 127) {
                    $tn = 2;
                    $n+= 2;
                    $noc++;
                } else {
                    $tn = 1;
                    $n++;
                    $noc++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n-= $tn;
            }
            $strcut = substr($string, 0, $n);
        }
        $string = str_replace(array(
            $pre . '&' . $end,
            $pre . '"' . $end,
            $pre . '<' . $end,
            $pre . '>' . $end
        ) , array(
            '&amp;',
            '&quot;',
            '&lt;',
            '&gt;'
        ) , $strcut);
    }
    if ($havedot) {
        $string = $string . '...';
    }
    return $string;
}
function fxy_istrlen($string, $charset = 'utf8') {
    if (strtolower($charset) == 'gbk') {
        $charset = 'gbk';
    } else {
        $charset = 'utf8';
    }
    if (function_exists('mb_strlen')) {
        return mb_strlen($string, $charset);
    } else {
        $n = $noc = 0;
        $strlen = strlen($string);
        if ($charset == 'utf8') {
            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $n+= 2;
                    $noc++;
                } elseif (224 <= $t && $t <= 239) {
                    $n+= 3;
                    $noc++;
                } elseif (240 <= $t && $t <= 247) {
                    $n+= 4;
                    $noc++;
                } elseif (248 <= $t && $t <= 251) {
                    $n+= 5;
                    $noc++;
                } elseif ($t == 252 || $t == 253) {
                    $n+= 6;
                    $noc++;
                } else {
                    $n++;
                }
            }
        } else {
            while ($n < $strlen) {
                $t = ord($string[$n]);
                if ($t > 127) {
                    $n+= 2;
                    $noc++;
                } else {
                    $n++;
                    $noc++;
                }
            }
        }
        return $noc;
    }
}
/**
 * 价格格式化
 *
 * @param int	$price
 * @return string	$price_format
 */
function priceFormat($price) {
    $price_format = is_numeric($price) ? number_format($price, 2, '.', '') : 0;
    return $price_format;
}
function str_cut($string, $length, $dot = '') {
    $string = str_replace(array(
        '&nbsp;',
        '&amp;',
        '&quot;',
        '&#039;',
        '&ldquo;',
        '&rdquo;',
        '&mdash;',
        '&lt;',
        '&gt;',
        '&middot;',
        '&hellip;'
    ) , array(
        ' ',
        '&',
        '"',
        '\'',
        '“',
        '”',
        '—',
        '<',
        '>',
        '·',
        '…'
    ) , $string);
    $strlen = strlen($string);
    if ($strlen <= $length) {
        return $string;
    }
    $maxi = $length - strlen($dot);
    $strcut = '';
    $charset = 'utf-8';
    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < $strlen) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || 32 <= $t && $t <= 126) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n+= 2;
                $noc+= 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n+= 3;
                $noc+= 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n+= 4;
                $noc+= 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n+= 5;
                $noc+= 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n+= 6;
                $noc+= 2;
            } else {
                $n++;
            }
            if ($noc >= $maxi) {
                break;
            }
        }
        if ($noc > $maxi) {
            $n-= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen;
        for ($i = 0; $i < $maxi; $i++) {
            $strcut.= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    $strcut = str_replace(array(
        '&',
        '"',
        '\'',
        '<',
        '>'
    ) , array(
        '&amp;',
        '&quot;',
        '&#039;',
        '&lt;',
        '&gt;'
    ) , $strcut);
    return $strcut . $dot;
}
function get_server_ip() {
    if (isset($_SERVER)) {
        if ($_SERVER['SERVER_ADDR']) {
            $server_ip = $_SERVER['SERVER_ADDR'];
        } else {
            $server_ip = $_SERVER['LOCAL_ADDR'];
        }
    } else {
        $server_ip = getenv('SERVER_ADDR');
    }
    return $server_ip;
}
/**
 * 删除目录或者文件
 * @param  string  $path
 * @param  boolean $is_del_dir
 * @return fixed
 */
function del_dir_or_file($path, $is_del_dir = FALSE) {
    $handle = is_dir($path) ? opendir($path) : false;
    if ($handle) {
        // $path为目录路径
        while (false !== ($item = readdir($handle))) {
            // 除去..目录和.目录
            if ($item != '.' && $item != '..') {
                if (is_dir($path . '/' . $item)) {
                    // 递归删除目录
                    del_dir_or_file($path . '/' . $item, $is_del_dir);
                } else {
                    // 删除文件
					if ($item != 'index.html') {
						unlink($path . '/' . $item);
					}
                }
            }
        }
        closedir($handle);
        if ($is_del_dir) {
            // 删除目录
            return rmdir($path);
        }
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return false;
        }
    }
}
/**
 * 输出聊天信息
 *
 * @return string
 */
function getChat($seller_info) {
    if (!config('node_chat') || !file_exists(FRAMEWORK_PATH . '/lib/chat.php')) {
        return '';
    }
    return \lib\chat::getChatHtml($seller_info);
}
/**
 七牛云上传方法
 @param file_path 本地文件的绝对路径
 @param new_file_name 上传到七牛上保存的文件名称,一般不要Uploads/image下面的层次
 *
 */
function save_image_to_qiniu($file_path, $new_file_name) {
	require_once COMMON_PATH . '/vendor/qiniu/autoload.php';
	$config = fxy_unserialize(config('qiniu'));
	$accessKey = $config['accesskey'];
	$secretKey = $config['secretkey'];
	$bucket = $config['bucket'];
	$auth = new \Qiniu\Auth($accessKey, $secretKey);
	// 生成上传 Token
	$token = $auth->uploadToken($bucket);
	$filePath = $file_path;
	// 上传到七牛后保存的文件名
	$key = $new_file_name; //'Uploads/2.jpg';
	// 初始化 UploadManager 对象并进行文件的上传。
	$uploadMgr = new \Qiniu\Storage\UploadManager();
	// 调用 UploadManager 的 putFile 方法进行文件的上传。
	list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
	if ($err !== null) {
		return callback(false, '上传失败');
	} else {
		return callback(true, '', array('key' => $ret['key']));
	}
}
function tomedia($image) {
    if (strpos($image, 'http:') !== false || strpos($image, 'https:') !== false) {
        return $image;
    } else {
        $domain = SITE_URL;
        if (config('attachment_open')) {
            $attachment_type = config('attachment_host_type');
            if ($attachment_type == 2) {
    			$config = fxy_unserialize(config('qiniu'));
                $qiniu_url = $config['url'];
                return $qiniu_url . $image;
            } else {
    			return SITE_URL . $image;
    		}
        } else {
            return SITE_URL . $image;
        }
    }
}
function fxy_getimagesize($image) {
	if (!preg_match('/^(http:\/\/|https:\/\/).*$/', $image)) {
		$image = $_SERVER['DOCUMENT_ROOT'] . $image;
	} else {
	    $image = str_replace(SITE_URL, $_SERVER['DOCUMENT_ROOT'], $image);
	}
	$image = iconv('gbk', 'utf-8//IGNORE', $image);
	return getimagesize($image);
}
/**
 * 分支树显示无限分类
 * @param $arr  需要处理的数组
 * @param $key  id名称
 * @param $pkey 父类id名称
 * @param $pid  父id
 * @return $list 返回的数组
 */
function recursionTree($arr = [], $key = 'id', $pkey = 'pid', $pid = 0) {
	$list = array();
	foreach($arr as $val) {
		if ($val[$pkey] == $pid) {
			$tmp = recursionTree($arr, $key, $pkey, $val[$key]);
			if($tmp) {
				$val['child'] = $tmp;
			}
			$list[] = $val;
		}
	}
	return $list;
}
//php获取中文字符拼音首字母 
function getFirstCharter($str) {
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str[0]);
    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str[0]);
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return null;
}
//对数字不足百位进行前置填充零的功能
function padNumber($number) {
    // 将数字转换为字符串
    $numberStr = (string)$number;
    
    // 如果数字长度小于3，则进行前置填充零操作
    if(strlen($numberStr) < 3) {
        $numberStr = str_pad($numberStr, 3, '0', STR_PAD_LEFT);
    }
    
    return $numberStr;
}