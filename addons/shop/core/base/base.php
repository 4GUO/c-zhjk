<?php
/**
 * JDPHP框架 引导类
 *
 **/
namespace base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
final class base {
	const CPURL = '';
    // 实例化对象
    private static $_instance = array();
    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start() {
        // 设定错误和异常处理
        register_shutdown_function([__CLASS__, 'fatalError']);
        set_error_handler([__CLASS__, 'appError'], E_ALL|E_STRICT);
        set_exception_handler([__CLASS__, 'appException']);
		if (!IS_CLI) {
			self::_cpurl();
		}
        dispatcherUrl::route();
        dispatcherUrl::dispatch();
    }
    /**
     * 取得对象实例 支持调用类的静态方法
     * @param string $class 对象类名
     * @param string $method 类的静态方法名
     * @return object
     */
    public static function instance($class, $method = '') {
        $identify = $class . $method;
        if (!isset(self::$_instance[$identify])) {
            if (class_exists($class)) {
                $o = new $class();
                if (!empty($method) && method_exists($o, $method)) self::$_instance[$identify] = call_user_func(array(&$o,
                    $method
                ));
                else self::$_instance[$identify] = $o;
            } else {
                self::halt('class error: ' . $class . ' is not exists!');
            }
        }
        return self::$_instance[$identify];
    }
	/**
     * 合法性验证
     *
     */
    private static function _cpurl() {
        // 注释掉域名检测功能，允许所有域名访问
        return;
        
        /*
        if (self::CPURL == '') return;
        if ($_SERVER['HTTP_HOST'] == 'localhost') return;
        if ($_SERVER['HTTP_HOST'] == '127.0.0.1') return;
        if (strpos(self::CPURL, '||') !== false) {
            $a = explode('||', self::CPURL);
            foreach ($a as $v) {
                $d = strtolower(stristr($_SERVER['HTTP_HOST'], $v));
                if ($d == strtolower($v)) {
                    return;
                } else {
                    continue;
                }
            }
            exit();
        } else {
            $d = strtolower(stristr($_SERVER['HTTP_HOST'], self::CPURL));
            if ($d != strtolower(self::CPURL)) {
                exit();
            }
        }
        */
    }
    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    public static function appException($e) {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
        // 发送500信息
        //header('HTTP/1.1 500 Internal Server Error');
        //header('Status:500 Internal Server Error');
        self::halt($error);
    }
    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                if (ob_get_level()) {
					ob_end_clean();
				}
                $errorStr = $errstr . ' ' . $errfile . ' 第 ' . $errline . '行.';
                self::halt($errorStr);
                break;

            default:
                $errorStr = '[' . $errno . ']' . $errstr . ' ' . $errfile . ' 第 ' . $errline . ' 行.';
                self::halt($errorStr);
                break;
        }
    }
    // 致命错误捕获
    public static function fatalError() {
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
					if (ob_get_level()) {
						ob_end_clean();
					}
                    self::halt($e);
                    break;
            }
        }
    }
    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    public static function halt($error) {
        $e = array();
        //调试模式下输出错误信息
        if (!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['line'] = $trace[0]['line'];
            ob_start();
            debug_print_backtrace();
            $e['trace'] = ob_get_clean();
        } else {
            $e = $error;
        }
		lib\logging::write(var_export($e['message'], true));
		if (config('debug')) {
		    if (IS_CLI) {
                exit(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'FILE: ' . $e['file'] . '(' . $e['line'] . ')' . PHP_EOL . $e['trace'] . PHP_EOL);
            } else {
                // 包含异常页面模板
    			$exceptionFile = FRAMEWORK_PATH . '/views/' . 'exception.php';
    			include $exceptionFile;
    			exit;  
            }
			
		}
    }
}