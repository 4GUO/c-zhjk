<?php
declare (strict_types = 1);
namespace base;
defined('SAFE_CONST') or exit('Access Invalid!');
class route {
    /**
     * 兼容PATH_INFO获取
     * @var array
     */
    protected static $pathinfoFetch = ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL', 'REQUEST_URI'];
    private static $var_module = 'g';
    private static $var_controller = 'c';
    private static $var_action = 'a';
    // 路由检测
    public static function check(string $path_info = '') {
        //去除?参数部分，支持伪静态url后面跟?参数
        $info = parse_url($path_info);
        $path_info = isset($info['path']) ? $info['path'] : '';
        // URL后缀
        $ext = strtolower(pathinfo($path_info, PATHINFO_EXTENSION));
        //去掉后缀
        $regx = preg_replace('/\.' . $ext . '$/i', '', $path_info);
        // URL映射定义（静态路由）
        $maps = config('url_map_rules');
        if (isset($maps[$regx])) {
            $var = self::parseRoute($maps[$regx]);
            $_GET = input('get.');
            $_GET = array_merge($_GET, $var);
            return true;
        }
        // 动态路由处理
        $result = model('cms_url_route_rules')->getList(array(), '*', 'sort_desc DESC');
        $routes = array();
        foreach ($result['list'] as $v) {
            $one_params = htmlspecialchars_decode($v['route']);
            $two_params = $v['vars'] ? json_decode(htmlspecialchars_decode($v['vars']), true) : [];
            $three_params = [];
            if ($v['suffix']) {
                $three_params['ext'] = $v['suffix'];
            }
            if ($v['method']) {
                $three_params['method'] = $v['method'];
            }
            $v['rule'] = htmlspecialchars_decode($v['rule']);
            $routes[$v['rule']] = array($one_params, $two_params, $three_params);
        }
        if (!empty($routes)) {
            foreach ($routes as $rule => $route) {
                if (is_numeric($rule)) {
                    // 支持 array('rule','adddress',...) 定义路由
                    $rule = array_shift($route);
                }
                if (is_array($route) && isset($route[2])) {
                    // 路由参数
                    $options = $route[2];
                    if (isset($options['ext']) && $ext != $options['ext']) {
                        // URL后缀检测
                        continue;
                    }
					if (empty($options['ext']) && $ext) {
						continue;
					}
                    if (isset($options['method']) && self::method() != strtoupper($options['method'])) {
                        // 请求类型检测
                        continue;
                    }
                    // 自定义检测
                    if (!empty($options['callback']) && is_callable($options['callback'])) {
                        if(false === call_user_func($options['callback'])) {
                            continue;
                        }
                    }
                }
                if(0 === strpos($rule, '/') && $rule !== '/' && preg_match($rule, $regx, $matches)) { // 正则路由
                    if ($route instanceof \Closure) {
                        // 执行闭包
                        $result = self::invokeRegx($route, $matches);
                        // 如果返回布尔值 则继续执行
                        return is_bool($result) ? $result : exit;
                    } else {
                        return self::parseRegex($matches, $route, $regx);
                    }
                } else { // 规则路由
                    $len1 = substr_count($regx, '/');
                    $len2 = substr_count($rule, '/');
                    if ($len1 >= $len2 || strpos($rule, '[')) {
                        if ('$' == substr($rule, -1, 1)) {// 完整匹配
                            if ($len1 != $len2) {
                                continue;
                            } else {
                                $rule = substr($rule, 0, -1);
                            }
                        }
                        $match = self::checkUrlMatch($regx, $rule);
                        
                        if (false !== $match) {
                            if ($route instanceof \Closure) {
                                // 执行闭包
                                $result = self::invokeRule($route, $match);
                                // 如果返回布尔值 则继续执行
                                return is_bool($result) ? $result : exit;
                            } else {
                                return self::parseRule($rule, $route, $regx);
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
    // 检测URL和规则路由是否匹配
    private static function checkUrlMatch($regx, $rule) {
        $m1 = explode('/', $regx);
        $m2 = explode('/', $rule);
        $var = array();
        foreach ($m2 as $key => $val) {
            if (0 === strpos($val, '[:')) {
                $val = substr($val, 1, -1);
            }
                
            if (':' == substr($val, 0, 1)) {// 动态变量
                if ($pos = strpos($val, '|')) {
                    // 使用函数过滤
                    $val = substr($val, 1, $pos-1);
                }
                if (strpos($val, '\\')) {
                    $type = substr($val, -1);
                    if('d' == $type) {
                        if(isset($m1[$key]) && !is_numeric($m1[$key]))
                            return false;
                    }
                    $name = substr($val, 1, -2);
                } elseif ($pos = strpos($val, '^')) {
                    $array = explode('-', substr(strstr($val, '^'), 1));
                    if(in_array($m1[$key], $array)) {
                        return false;
                    }
                    $name = substr($val, 1, $pos - 1);
                } else {
                    $name = substr($val, 1);
                }
                $var[$name] = isset($m1[$key]) ? $m1[$key] : '';
            } elseif (0 !== strcasecmp($val, $m1[$key])) {
                return false;
            }
        }
        // 成功匹配后返回URL中的动态变量数组
        return $var;
    }
    // 解析规范的路由地址
    // 地址格式 [控制器/操作?]参数1=值1&参数2=值2...
    private static function parseRoute($url) {
        $var = array();
        if (false !== strpos($url, '?')) { // [控制器/操作?]参数1=值1&参数2=值2...
            $info = parse_url($url);
            $path = explode('/', $info['path']);
            parse_str($info['query'], $var);
        } elseif (strpos($url, '/')) { // [控制器/操作]
            $path = explode('/', $url);
        } else { // 参数1=值1&参数2=值2...
            parse_str($url, $var);
        }
        if (isset($path)) {
            $var[self::$var_action] = array_pop($path);
            if(!empty($path)) {
                $var[self::$var_controller] = array_pop($path);
            }
            if(!empty($path)) {
                $var[self::$var_module] = array_pop($path);
            }
        }
        return $var;
    }
    // 解析规则路由
    // '路由规则'=>'[控制器/操作]?额外参数1=值1&额外参数2=值2...'
    // '路由规则'=>array('[控制器/操作]','额外参数1=值1&额外参数2=值2...')
    // '路由规则'=>'外部地址'
    // '路由规则'=>array('外部地址','重定向代码')
    // 路由规则中 :开头 表示动态变量
    // 外部地址中可以用动态变量 采用 :1 :2 的方式
    // 'news/:month/:day/:id'=>array('News/read?cate=1','status=1'),
    // 'new/:id'=>array('/new.php?id=:1',301), 重定向
    private static function parseRule($rule, $route, $regx) {
        // 获取路由地址规则
        $url = is_array($route) ? $route[0] : $route;
        // 获取URL地址中的参数
        $paths = explode('/', $regx);
        // 解析路由规则
        $matches = array();
        $rule = explode('/', $rule);
        foreach ($rule as $item) {
            $fun = '';
            if (0 === strpos($item, '[:')) {
                $item   =   substr($item, 1, -1);
            }
            if (0 === strpos($item, ':')) { // 动态变量获取
                if ($pos = strpos($item, '|')) { 
                    // 支持函数过滤
                    $fun = substr($item, $pos + 1);
                    $item = substr($item, 0, $pos);
                }
                if ($pos = strpos($item, '^')) {
                    $var = substr($item, 1, $pos - 1);
                } elseif (strpos($item, '\\')) {
                    $var = substr($item, 1, -2);
                } else {
                    $var = substr($item, 1);
                }
                $matches[$var] = !empty($fun) ? $fun(array_shift($paths)) : array_shift($paths);
            } else { // 过滤URL中的静态变量
                array_shift($paths);
            }
        }

        if (0 === strpos($url, '/') || 0 === strpos($url, 'http')) { // 路由重定向跳转
            if (strpos($url, ':')) { // 传递动态参数
                $values = array_values($matches);
                $url = preg_replace_callback('/:(\d+)/', function($match) use($values) { return $values[$match[1] - 1]; }, $url);
            }
            header('Location: ' . $url, true, (is_array($route) && isset($route[1])) ? $route[1] : 301);
            exit;
        } else {
            // 解析路由地址
            $var = self::parseRoute($url);
            // 解析路由地址里面的动态参数
            $values = array_values($matches);
            foreach ($var as $key => $val) {
                if(0 === strpos($val, ':')) {
                    $var[$key] = $values[substr($val, 1) - 1];
                }
            }
            $var = array_merge($matches, $var);
            // 解析剩余的URL参数
            if(!empty($paths)) {
                preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var) { $var[strtolower($match[1])] = strip_tags($match[2]);}, implode('/', $paths));
            }
            // 解析路由自动传入参数
            if(is_array($route) && isset($route[1])) {
                if (is_array($route[1])) {
                    $params = $route[1];
                } else {
                    parse_str($route[1], $params);
                }
                $var = array_merge($var, $params);
            }
            $_GET = input('get.');
            $_GET = array_merge($_GET, $var);
        }
        return true;
    }

    // 解析正则路由
    // '路由正则'=>'[控制器/操作]?参数1=值1&参数2=值2...'
    // '路由正则'=>array('[控制器/操作]?参数1=值1&参数2=值2...','额外参数1=值1&额外参数2=值2...')
    // '路由正则'=>'外部地址'
    // '路由正则'=>array('外部地址','重定向代码')
    // 参数值和外部地址中可以用动态变量 采用 :1 :2 的方式
    // '/new\/(\d+)\/(\d+)/'=>array('News/read?id=:1&page=:2&cate=1','status=1'),
    // '/new\/(\d+)/'=>array('/new.php?id=:1&page=:2&status=1','301'), 重定向
    private static function parseRegex($matches, $route, $regx) {
        // 获取路由地址规则
        $url = is_array($route) ? $route[0] : $route;
        $url = preg_replace_callback('/:(\d+)/', function($match) use($matches){return $matches[$match[1]];}, $url); 
        
        if (0 === strpos($url, '/') || 0 === strpos($url, 'http')) { // 路由重定向跳转
            header('Location: ' . $url, true, (is_array($route) && isset($route[1])) ? $route[1] : 301);
            exit;
        } else {
            // 解析路由地址
            $var = self::parseRoute($url);
            // 处理函数
            foreach($var as $key => $val) {
                if (strpos($val, '|')) {
                    list($val, $fun) = explode('|', $val);
                    $var[$key] = $fun($val);
                }
            }
            // 解析剩余的URL参数
            $regx = substr_replace($regx, '', 0, strlen($matches[0]));
            if ($regx) {
                preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var) {
                    $var[strtolower($match[1])] = strip_tags($match[2]);
                }, $regx);
            }
            // 解析路由自动传入参数
            if (is_array($route) && isset($route[1])) {
                if (is_array($route[1])) {
                    $params = $route[1];
                } else {
                    parse_str($route[1], $params);
                }
                $var = array_merge($var, $params);
            }
            $_GET = input('get.');
            $_GET = array_merge($_GET, $var);
        }
        return true;
    }

    // 执行正则匹配下的闭包方法 支持参数调用
    static private function invokeRegx($closure, $var = array()) {
        $reflect = new \ReflectionFunction($closure);
        $params = $reflect->getParameters();
        $args = array();
        array_shift($var);
        foreach ($params as $param) {
            if (!empty($var)) {
                $args[] = array_shift($var);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }
        return $reflect->invokeArgs($args);
    }

    // 执行规则匹配下的闭包方法 支持参数调用
    static private function invokeRule($closure, $var = array()) {
        $reflect = new \ReflectionFunction($closure);
        $params = $reflect->getParameters();
        $args = array();
        foreach ($params as $param) {
            $name = $param->getName();
            if (isset($var[$name])) {
                $args[] = $var[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            }
        }
        return $reflect->invokeArgs($args);
    }
    
    ///////////////////////////////////////Url类////////////////////////////////////////////////
    public static function buildUrl(string $url = '', array $vars = [], $suffix = false, $domain = false) {
        $depr = '/';
        if (false === strpos($url, '://') && 0 !== strpos($url, '/')) {
            $info = parse_url($url);
            $url  = !empty($info['path']) ? $info['path'] : '';
            if (isset($info['fragment'])) {
                // 解析锚点
                $anchor = $info['fragment'];

                if (false !== strpos($anchor, '?')) {
                    // 解析参数
                    [$anchor, $info['query']] = explode('?', $anchor, 2);
                }

                if (false !== strpos($anchor, '@')) {
                    // 解析域名
                    [$anchor, $domain] = explode('@', $anchor, 2);
                }
            } elseif (strpos($url, '@') && false === strpos($url, '\\')) {
                // 解析域名
                [$url, $domain] = explode('@', $url, 2);
            }
        }
        if ($url) {
            //$url中query部分唯一用途是实现多个路由到一个（控制/方法）
            $query_path = '';
			if (isset($info['query'])) {
				parse_str($info['query'], $query);
				ksort($query);
				foreach ($query as $kk => $vv) {
					$query_path .= $kk . '/' . $vv . '/';
				}
				$query_path = $query_path ? '/' . trim($query_path, '/') : '';
			}
			$checkName = $url .= $query_path;
            $checkDomain = $domain && is_string($domain) ? $domain : null;
            $rule = self::getRuleName($checkName, $checkDomain);
        }
        
        if (!empty($rule) && $match = self::getRuleUrl($rule, $vars, $domain)) {
            // 匹配路由命名标识
            $url = $match[0];
            if ($domain && !empty($match[1])) {
                $domain = $match[1];
            }

            if (!is_null($match[2])) {
                $suffix = $match[2];
            }
        } else {
            // 路由标识不存在 直接解析获取：模块/控制器/操作
            $url = self::parseUrl($url);
        }
		
        $file = self::baseFile();
        if ($file && 0 !== strpos(self::url(), $file)) {
            $file = str_replace('\\', '/', dirname($file));
        }
		$url = rtrim($file, '/') . '/' . $url;
		
		if (strpos($file, '.php') === false) {
			$url = ltrim($url, '/');
		}
        // URL后缀
        if ('/' == substr($url, -1) || '' == $url) {
            $suffix = '';
        } else {
            $suffix = (empty($suffix) || 0 === strpos($suffix, '.')) ? (string) $suffix : '.' . $suffix;
        }
        // 锚点
        $anchor = !empty($anchor) ? '#' . $anchor : '';
        // 参数组装
        if (!empty($vars)) {
            // 添加参数
            foreach ($vars as $var => $val) {
                $val = (string) $val;
                if ('' !== $val) {
                    $url .= $depr . $var . $depr . urlencode($val);
                }
            }
            $url .= $suffix . $anchor;
        } else {
            $url .= $suffix . $anchor;
        }
        // 域名组装
        if ($domain) {
            $rootDomain = self::rootDomain();
            if (true === $domain) {
                $domain = self::host();
            } elseif (false === strpos($domain, '.') && 0 !== strpos($domain, $rootDomain)) {
                $domain .= '.' . $rootDomain;
            }
            if (false !== strpos($domain, '://')) {
                $scheme = '';
            } else {
                $scheme = self::scheme() . '://';
            }
            $domain = $scheme . $domain;
        } else if ($domain === false) {
            $domain = '';
        }
        // URL组装
        return rtrim($domain, '/') . '/' . ltrim($url, '/');
    }
    static protected function getRuleName(string $name = null, string $domain = null, string $method = '*'): array {
		$result = model('cms_url_route_rules')->getList(array(), '*', 'sort_desc DESC');
        $rules = array();
        foreach ($result['list'] as $v) {
            $v['rule'] = htmlspecialchars_decode($v['rule']);
            if ('$' == substr($v['rule'], -1, 1)) {
                // 是否完整匹配
                $v['rule'] = substr($v['rule'], 0, -1);
            }
            
            $item = array();
            //支持正则路由
            if ($v['rule_type'] == 1) {
                $item['rule'] = $v['rule'];
            } else {
                if (false !== strpos($v['rule'], ':')) {
                    $item['rule'] = preg_replace(['/\[\:(\w+)(\\\\d)*\]/', '/\:(\w+)(\\\\d)*/'], ['<\1?>', '<\1>'], $v['rule']);
                } else {
                    $item['rule'] = $v['rule'];
                }
            }
            $item['domain'] = !empty($v['domain']) ? $v['domain'] : self::host(true);
            $item['suffix'] = !empty($v['suffix']) ? $v['suffix'] : '';
            $item['method'] = !empty($v['method']) ? $v['method'] : '*';
            //兼容正则
            $item['rule_type'] = !empty($v['rule_type']) ? $v['rule_type'] : 0;
            $item['route'] = !empty($v['route']) ? htmlspecialchars_decode($v['route']) : '';
            // 去除route中的?后面的参数部分
            if (false !== strpos($v['route'], '?')) { // 兼容 [控制器/操作?]参数1=值1&参数2=值2...
                $info = parse_url($v['route']);
                $v['route'] = $info['path'];
            }
            $vars = $v['vars'] ? json_decode(htmlspecialchars_decode($v['vars']), true) : [];
			$vars_path = '';
			if ($vars) {
				ksort($vars);
				foreach ($vars as $kk => $vv) {
					$vars_path .= $kk . '/' . $vv . '/';
				}
				$vars_path = $vars_path ? '/' . trim($vars_path, '/') : '';
			}
			$v['route'] .= $vars_path;
            $rules[$v['route']][] = $item;
        }
        $return = [];
        if (isset($rules[$name])) {
            if (is_null($domain)) {
                $return = $rules[$name];
            } else {
                foreach ($rules[$name] as $item) {
                    $itemDomain = $item['domain'];
                    $itemMethod = $item['method'];
                    if (($itemDomain == $domain || '*' == $itemDomain) && ('*' == $itemMethod || '*' == $method || $method == $itemMethod)) {
                        $return[] = $item;
                    }
                }
            }
        }
        return $return;
    }
    /**
     * 匹配路由地址
     * @access protected
     * @param  array $rule 路由规则
     * @param  array $vars 路由变量
     * @param  mixed $allowDomain 允许域名
     * @return array
     */
    static protected function getRuleUrl(array $rule, array &$vars = [], $allowDomain = ''): array {
        if (is_string($allowDomain) && false === strpos($allowDomain, '.')) {
            $allowDomain .= '.' . self::rootDomain();
        }
        $port = self::port();
        foreach ($rule as $item) {
            $url = $item['rule'];
            //支持正则
            if ($item['rule_type'] == 1) {
                $route = $item['route'];
                if (false !== strpos($route, '?')) {
                    $info = parse_url($route);
                    parse_str($info['query'], $query);
                    $route_vars = array();
                    //$route_vars：获取route对应的参数值
                    foreach ($query as $k => $v) {
                        //动态参数才会参与置换
                        if (false !== strpos($route, ':')) {
                            if (isset($vars[$k])) {
                                $route_vars[] = $vars[$k];
                                unset($vars[$k]);
                            }
                        }
                    }
					
                    //$url_str：去除rule中多余的干扰字符
                    $url_str = $item['rule'];
					$url_str = trim($url_str, '/');
					$url_str = ltrim($url_str, '^');
					$url_str = rtrim($url_str, '$');
                    //$regx：组装正则表达式
                    $regx = '/';
                    foreach ($route_vars as $k => $v) {
                        $regx .= '.+(\(.+\))';
                    }
                    $regx .= '/';
                    //$matches：置换出rule字符串中的()单元
                    preg_match($regx, $url_str, $matches);
                    unset($matches[0]);
					$matches = array_values($matches);
					foreach ($route_vars as $kk => $vv) {
					    $vv = $vv ?: 0;
						$url_str = substr_replace($url_str, (string) $vv, strpos($url_str, $matches[$kk]), strlen($matches[$kk]));
					}
                    $url = str_replace('\\', '', $url_str);
                }
            } else {
                $pattern = self::parseVar($url);
            }
            $domain = $item['domain'];
            $suffix = $item['suffix'];
            
            if ('*' == $domain) {
                $domain = is_string($allowDomain) ? $allowDomain : self::host(true);
            }
            
            if (is_string($allowDomain) && $domain != $allowDomain) {
                continue;
            }
            if ($port && !in_array($port, [80, 443])) {
                $domain .= ':' . $port;
            }
            if (empty($pattern)) {
                return [rtrim($url, '?-'), $domain, $suffix];
            }
            
            $keys = [];
            foreach ($pattern as $key => $val) {
                if (isset($vars[$key])) {
                    $url = str_replace(['[:' . $key . ']', '<' . $key . '?>', ':' . $key, '<' . $key . '>'], urlencode((string) $vars[$key]), $url);
                    
                    $keys[] = $key;
                    $url = str_replace(['/?', '-?'], ['/', '-'], $url);
                    $result = [rtrim($url, '?-'), $domain, $suffix];
                } elseif (2 == $val) {
                    $url = str_replace(['/[:' . $key . ']', '[:' . $key . ']', '<' . $key . '?>'], '', $url);
                    $url = str_replace(['/?', '-?'], ['/', '-'], $url);
                    $result = [rtrim($url, '?-'), $domain, $suffix];
                } else {
                    $result = null;
                    $keys = [];
                    break;
                }
            }

            $vars = array_diff_key($vars, array_flip($keys));

            if (isset($result)) {
                return $result;
            }
        }

        return [];
    }
    /**
     * 分析路由规则中的变量
     * @access protected
     * @param  string $rule 路由规则
     * @return array
     */
    static protected function parseVar(string $rule): array {
        // 提取路由规则中的变量
        $var = [];
        if (preg_match_all('/<\w+\??>/', $rule, $matches)) {
            foreach ($matches[0] as $name) {
                $optional = false;

                if (strpos($name, '?')) {
                    $name = substr($name, 1, -2);
                    $optional = true;
                } else {
                    $name = substr($name, 1, -1);
                }

                $var[$name] = $optional ? 2 : 1;
            }
        }

        return $var;
    }
    /**
     * 直接解析URL地址
     * @access protected
     * @param  string      $url URL
     * @return string
     */
    static protected function parseUrl(string $url): string {
        if (0 === strpos($url, '/')) {
            // 直接作为路由地址解析
            $url = substr($url, 1);
        } elseif ('' === $url) {
            $url = dispatcherUrl::getController() . '/' . dispatcherUrl::getAction();
        } else {
			$path = explode('/', trim($url, '/'));
			if (count($path) != 3) {
				$module = '';
				$pathinfo = self::pathinfo();
				$pathinfo = explode('/', trim($pathinfo, '/'));
				if (config('module_map_rules')) {
					$module_map_rules = config('module_map_rules');
					if (!empty($pathinfo[0]) && isset($module_map_rules[$pathinfo[0]])) {
						$module = $pathinfo[0] . '/';
					}
				}
				$controller = dispatcherUrl::getController();
				$action = array_pop($path);
				$controller = empty($path) ? $controller : array_pop($path);
				$url = $module . $controller . '/' . $action;
			}
        }
        return $url;
    }
    /**
     * 检查URL是否已经定义过路由
     * @access protected
     * @param  array $route 路由信息
     * @return bool
     */
    public static function hasDefinedRoute(array $route): bool {
        [$controller, $action] = $route;
        if (!$controller && !$action) {
            $name = null;
        } else {
            // 检查地址是否被定义过路由
            $name = strtolower($controller . '/' . $action);
        }
        $host = self::host(true);
        $method = self::method();
        if (self::getRuleName($name, $host, $method)) {
            return true;
        }

        return false;
    }
    
    ///////////////////////////////////////Request类////////////////////////////////////////////////
    /**
     * 获取当前执行的文件 SCRIPT_NAME
     * @access public
     * @param  bool $complete 是否包含完整域名
     * @return string
     */
    public static function baseFile(bool $complete = false): string {
        $url = '';
        if (!IS_CLI) {
            $script_name = basename(input('server.SCRIPT_FILENAME'));
            if (basename(input('server.SCRIPT_NAME')) === $script_name) {
                $url = input('server.SCRIPT_NAME');
            } elseif (basename(input('server.PHP_SELF')) === $script_name) {
                $url = input('server.PHP_SELF');
            } elseif (basename(input('server.ORIG_SCRIPT_NAME')) === $script_name) {
                $url = input('server.ORIG_SCRIPT_NAME');
            } elseif (($pos = strpos(input('server.PHP_SELF'), '/' . $script_name)) !== false) {
                $url = substr(input('server.SCRIPT_NAME'), 0, $pos) . '/' . $script_name;
            } elseif (input('server.DOCUMENT_ROOT') && strpos(input('server.SCRIPT_FILENAME'), input('server.DOCUMENT_ROOT')) === 0) {
                $url = str_replace('\\', '/', str_replace(input('server.DOCUMENT_ROOT'), '', input('server.SCRIPT_FILENAME')));
            }
        }
        return $complete ? self::domain() . $url : $url;
    }
    /**
     * 获取当前完整URL 包括QUERY_STRING
     * @access public
     * @param  bool $complete 是否包含完整域名
     * @return string
     */
    public static function url(bool $complete = false): string {
        if (input('server.HTTP_X_REWRITE_URL')) {
            $url = input('server.HTTP_X_REWRITE_URL');
        } elseif (input('server.REQUEST_URI')) {
            $url = input('server.REQUEST_URI');
        } elseif (input('server.ORIG_PATH_INFO')) {
            $url = input('server.ORIG_PATH_INFO') . (!empty(input('server.QUERY_STRING')) ? '?' . input('server.QUERY_STRING') : '');
        } elseif (isset($_SERVER['argv'][1])) {
            $url = $_SERVER['argv'][1];
        } else {
            $url = '';
        }

        return $complete ? self::domain() . $url : $url;
    }
    /**
     * 当前请求URL地址中的port参数
     * @access public
     * @return int
     */
    public static function port(): int {
        return (int) (input('server.HTTP_X_FORWARDED_PORT') ?: input('server.SERVER_PORT', ''));
    }
    /**
     * 获取当前包含协议的域名
     * @access public
     * @param  bool $port 是否需要去除端口号
     * @return string
     */
    public static function domain(bool $port = false): string {
        return self::scheme() . '://' . self::host($port);
    }
    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public static function scheme(): string {
        return is_ssl() ? 'https' : 'http';
    }
    /**
     * 当前请求URL地址中的query参数
     * @access public
     * @return string
     */
    public static function query(): string {
        return input('server.QUERY_STRING', '');
    }
    /**
     * 当前请求的host
     * @access public
     * @param bool $strict  true 仅仅获取HOST
     * @return string
     */
    public static function host(bool $strict = false): string {
        $host = strval(input('server.HTTP_X_FORWARDED_HOST') ?: input('server.HTTP_HOST'));
        return true === $strict && strpos($host, ':') ? strstr($host, ':', true) : $host;
    }
    /**
     * 获取当前根域名
     * @access public
     * @return string
     */
    public static function rootDomain(): string {
        $item  = explode('.', self::host());
        $count = count($item);
        $root  = $count > 1 ? $item[$count - 2] . '.' . $item[$count - 1] : $item[0];
        return $root;
    }
    /**
     * 当前的请求类型
     * @access public
     * @param  bool $origin 是否获取原始请求类型
     * @return string
     */
    public static function method(bool $origin = false): string {
        if ($origin) {
            // 获取原始请求类型
            return input('server.REQUEST_METHOD') ?: 'GET';
        } else {
            if (input('server.HTTP_X_HTTP_METHOD_OVERRIDE')) {
                $return = strtoupper(input('server.HTTP_X_HTTP_METHOD_OVERRIDE'));
            } else {
                $return = input('server.REQUEST_METHOD') ?: 'GET';
            }
        }
        return $return;
    }
    /**
     * 获取URL访问根地址
     * @access public
     * @param  bool $complete 是否包含完整域名
     * @return string
     */
    public static function root(bool $complete = false): string {
        $file = self::baseFile();
        if ($file && 0 !== strpos(self::url(), $file)) {
            $file = str_replace('\\', '/', dirname($file));
        }
        $root = rtrim($file, '/');
        return $complete ? self::domain() . $root : $root;
    }
    /**
     * 获取URL访问根目录
     * @access public
     * @return string
     */
    public static function rootUrl(): string {
        $base = self::root();
        $root = strpos($base, '.') ? ltrim(dirname($base), DIRECTORY_SEPARATOR) : $base;

        if ('' != $root) {
            $root = '/' . ltrim($root, '/');
        }
        return $root;
    }
    /**
     * 获取当前请求URL的pathinfo信息（含URL后缀）
     * @access public
     * @return string
     */
    public static function pathinfo(): string {
        if (input('server.PATH_INFO')) {
            $pathinfo = input('server.PATH_INFO');
        } elseif (false !== strpos(PHP_SAPI, 'cli')) {
            $pathinfo = strpos(input('server.REQUEST_URI'), '?') ? strstr(input('server.REQUEST_URI'), '?', true) : input('server.REQUEST_URI');
        }
        // 分析PATHINFO信息
        if (!isset($pathinfo)) {
            foreach (self::$pathinfoFetch as $type) {
                if (input('server.' . $type)) {
                    $pathinfo = (0 === strpos(input('server.' . $type), input('server.SCRIPT_NAME'))) ?
                    substr(input('server.' . $type), strlen(input('server.SCRIPT_NAME'))) : input('server.' . $type);
                    break;
                }
            }
        }
        if (!empty($pathinfo)) {
            unset($_GET[$pathinfo], $_REQUEST[$pathinfo]);
        }
        $return = empty($pathinfo) || '/' == $pathinfo ? '' : ltrim($pathinfo, '/');
        return $return;
    }
    /**
     * 当前URL的访问后缀
     * @access public
     * @return string
     */
    public static function ext(): string {
        return pathinfo(self::pathinfo(), PATHINFO_EXTENSION);
    }
}