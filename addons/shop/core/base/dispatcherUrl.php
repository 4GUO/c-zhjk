<?php
namespace base;
defined('SAFE_CONST') or die('Access Invalid!');
/**
 * 完成URL解析、路由和调度
 */
class dispatcherUrl {
    private static $var_module = 'g';
    private static $var_controller = 'c';
    private static $var_action = 'a';
    private static $moduleName;
    private static $controllerName;
    private static $actionName;
	public function __construct() {
		
	}
    public static function route() {
        $_SERVER['PATH_INFO'] = route::pathinfo();
        $pathinfo = array();
        if (!empty($_SERVER['PATH_INFO'])) {
            if (!config('url_router_on') || !route::check($_SERVER['PATH_INFO'])) {
                $pathinfo = explode('/', trim(self::safe_filter($_SERVER['PATH_INFO']), '/'));
            }
        } else {
			if (!config('url_router_on') || !route::check()) {
			    $pathinfo = array();
    			$pathinfo[0] = !empty($_GET[self::$var_module]) ? self::safe_filter($_GET[self::$var_module]) : '';
    			$pathinfo[1] = !empty($_GET[self::$var_controller]) ? self::safe_filter($_GET[self::$var_controller]) : '';
    			$pathinfo[2] = !empty($_GET[self::$var_action]) ? self::safe_filter($_GET[self::$var_action]) : '';
			}
        }
        //模块地址映射
		if (config('module_map_rules')) {
			$module_map_rules = config('module_map_rules');
			if (!empty($pathinfo[0]) && isset($module_map_rules[$pathinfo[0]])) {
				defined('BIND_MODULE') or define('BIND_MODULE', $module_map_rules[$pathinfo[0]]['BIND_MODULE']);
				defined('APP_PATH') or define ('APP_PATH', BASE_PATH . '/addons/' . ADDONS_NAME . '/' . $module_map_rules[$pathinfo[0]]['APP_PATH']);
				array_shift($pathinfo);
			} else {
				//直接访问域名定位到第一个模块
				$default_module = array_shift($module_map_rules);
				defined('BIND_MODULE') or define('BIND_MODULE', $default_module['BIND_MODULE']);
				defined('APP_PATH') or define ('APP_PATH', BASE_PATH . '/addons/' . ADDONS_NAME . '/' . $default_module['APP_PATH']);
			}
		}
        // 检测路由，源地址失效
        $route = [$pathinfo[0] ?? '', $pathinfo[1] ?? ''];
        if (route::hasDefinedRoute($route)) {
            send_http_status('404');
			exit('404 Invalid Request。');
        }
        self::path_info_bootstrap($pathinfo);
    }
	private static function path_info_bootstrap($pathinfo) {
		self::$moduleName = defined('BIND_MODULE') ? BIND_MODULE : (!empty($pathinfo[0]) ? self::safe_filter($pathinfo[0]) : '');
		if (!defined('BIND_MODULE') && $pathinfo) {
			array_shift($pathinfo);
		}
		if (!empty($_GET[self::$var_module])) {
		    self::$moduleName = self::safe_filter($_GET[self::$var_module]);
		    unset($_GET[self::$var_module]);
		}
		self::$controllerName = defined('BIND_CONTROLLER') ? BIND_CONTROLLER : (!empty($pathinfo[0]) ? self::safe_filter($pathinfo[0]) : 'index');
		if (!defined('BIND_CONTROLLER') && $pathinfo) {
			array_shift($pathinfo);
		}
		if (!empty($_GET[self::$var_controller])) {
		    self::$controllerName = self::safe_filter($_GET[self::$var_controller]);
		    unset($_GET[self::$var_controller]);
		}
		self::$actionName = defined('BIND_ACTION') ? BIND_ACTION : (!empty($pathinfo[0]) ? self::safe_filter($pathinfo[0]) : 'index');
		if (strpos(self::$actionName, '.') !== false) {
			self::$actionName = explode('.', self::$actionName)[0];
		}
		if (!defined('BIND_ACTION') && $pathinfo) {
			array_shift($pathinfo);
		}
		if (!empty($_GET[self::$var_action])) {
		    self::$actionName = self::safe_filter($_GET[self::$var_action]);
		    unset($_GET[self::$var_action]);
		}
		for ($i = 0; $i < count($pathinfo); $i+= 2) {
			$value = isset($pathinfo[$i + 1]) ? self::safe_filter($pathinfo[$i + 1]) : '';
			if (strpos($value, '.') !== false) {
				$value = explode('.', $value)[0];
			}
			$_GET[$pathinfo[$i]] = $value;
		}
	}
    public static function safe_filter($value) {
        $value = strip_tags($value);
        if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
            $value .= ' ';
        }
		//todo
		$filter_param = ['<', '>', '"', '\'', '%3C', '%3E', '%22', '%27'];
		$value = str_ireplace($filter_param, '', $value);
        return $value;
    }
    public static function dispatch() {
        if (!preg_match('/^[A-Za-z](\/|\w)*$/', self::getController())) { // 安全检测
            $controller = false;
        } else {
            //创建控制器实例 使用命名空间
            $class = self::getModule() . '\\controller';
            $array = explode('/', self::getController());
            foreach ($array as $name) {
                $class.= '\\' . $name;
            }
            if (class_exists($class)) {
                $controller = new $class();
            } else {
                $controller = false;
            }
        }
		if ($controller == false) {
			send_http_status('404');
			exit('404 Bad Request。');
		}
        $action = self::getAction();
        try {
            //执行当前操作
            $method = new \ReflectionMethod($controller, $action);
            if ($method->isPublic() && !$method->isStatic()) {
                $class = new \ReflectionClass($controller);
                // 前置操作
                if ($class->hasMethod('_before_' . $action)) {
                    $before = $class->getMethod('_before_' . $action);
                    if ($before->isPublic()) {
                        $before->invoke($controller);
                    }
                }
                $method->invoke($controller);
                // 后置操作
                if ($class->hasMethod('_after_' . $action)) {
                    $after = $class->getMethod('_after_' . $action);
                    if ($after->isPublic()) {
                        $after->invoke($controller);
                    }
                }
            } else {
                // 操作方法不是Public 抛出异常
                throw new \ReflectionException();
            }
        }
        catch(\ReflectionException $e) {
            // 方法调用发生异常后 引导到__call方法处理
            $method = new \ReflectionMethod($controller, '__call');
            $method->invokeArgs($controller, array(
                $action,
                ''
            ));
        }
        return;
    }
    public static function getModule() {
        $module = self::$moduleName;
        return strtolower($module);
    }
    public static function getController() {
        $controller = self::$controllerName;
        return strtolower($controller);
    }
    public static function getAction() {
        $action = self::$actionName . 'Op';
        return strtolower($action);
    }
	public static function getVar_module() {
        return self::$var_module;
    }
    public static function getVar_controller() {
        return self::$var_controller;
    }
    public static function getVar_action() {
        return self::$var_action;
    }
}