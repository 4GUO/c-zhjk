<?php
namespace base;
use lib;
class cache
{
    /**
     * @var array 缓存的实例
     */
    public static $instance = [];

    /**
     * @var int 缓存读取次数
     */
    public static $readTimes = 0;

    /**
     * @var int 缓存写入次数
     */
    public static $writeTimes = 0;

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 连接缓存驱动
     * @access public
     * @param  array       $options 配置数组
     * @param  bool|string $name    缓存连接标识，true强制重新连接
     * @return Driver
     */
    public static function connect(array $options = [], $name = false)
    {
		$options = empty($options) ? config('cache.default') : $options;
        $type = !empty($options['type']) ? $options['type'] : 'file';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
			$class = false === strpos($type, '\\') ? '\\cache\\driver\\' . strtolower($type) : $type;
			
            if (true === $name) {
                return new $class($options);
            }

            self::$instance[$name] = new $class($options);
        }
        self::$handler = self::$instance[$name];
        return self::$handler;
    }
    /**
     * 切换缓存类型
     * @access public
     * @param  string $name 缓存标识
     * @return Driver
     */
    public static function store($name = '')
    {
        if ('' !== $name) {
            return self::connect(config('cache.' . $name), strtolower($name));
        }

        return self::_init();
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public static function has($name)
    {
        self::$readTimes++;

        return self::_init()->has($name);
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name    缓存标识
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public static function get($name, $default = false)
    {
        self::$readTimes++;

        return self::_init()->get($name, $default);
    }

    /**
     * 写入缓存
     * @access public
     * @param  string   $name   缓存标识
     * @param  mixed    $value  存储数据
     * @param  int|null $expire 有效时间 0为永久
     * @return boolean
     */
    public static function set($name, $value, $expire = null)
    {
        self::$writeTimes++;

        return self::_init()->set($name, $value, $expire);
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int    $step 步长
     * @return false|int
     */
    public static function inc($name, $step = 1)
    {
        self::$writeTimes++;

        return self::_init()->inc($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string $name 缓存变量名
     * @param  int    $step 步长
     * @return false|int
     */
    public static function dec($name, $step = 1)
    {
        self::$writeTimes++;

        return self::_init()->dec($name, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存标识
     * @return boolean
     */
    public static function rm($name)
    {
        self::$writeTimes++;

        return self::_init()->rm($name);
    }

    /**
     * 清除缓存
     * @access public
     * @param  string $tag 标签名
     * @return boolean
     */
    public static function clear($tag = null)
    {
        self::$writeTimes++;

        return self::_init()->clear($tag);
    }

    /**
     * 读取缓存并删除
     * @access public
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public static function pull($name)
    {
        self::$readTimes++;
        self::$writeTimes++;

        return self::_init()->pull($name);
    }

    /**
     * 如果不存在则写入缓存
     * @access public
     * @param  string $name   缓存变量名
     * @param  mixed  $value  存储数据
     * @param  int    $expire 有效时间 0为永久
     * @return mixed
     */
    public static function remember($name, $value, $expire = null)
    {
        self::$readTimes++;

        return self::_init()->remember($name, $value, $expire);
    }

    /**
     * 缓存标签
     * @access public
     * @param  string       $name    标签名
     * @param  string|array $keys    缓存标识
     * @param  bool         $overlay 是否覆盖
     * @return Driver
     */
    public static function tag($name, $keys = null, $overlay = false)
    {
        return self::_init()->tag($name, $keys, $overlay);
    }

	/**
     * 自动初始化缓存
     * @access private
     * @return Driver
     */
    private static function _init()
    {
        if (is_null(self::$handler)) {
            self::$handler = self::connect();
        }
        return self::$handler;
    }
}