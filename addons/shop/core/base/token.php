<?php
namespace base;
use lib;
class token
{
	// 默认配置
    public static $config = [
        'token_on'    => true,
        'token_name'  => 'formhash',
        'token_type'  => 'md5',
        'token_reset' => true,
    ];
	/**
     * 获得token
     * @return array
     */
    public static function getToken()
    {
        $tokenName = self::$config['token_name'];
        $tokenType = self::$config['token_type'];
        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = [];
        }
        // 标识当前页面唯一性
        $tokenKey = md5($_SERVER['REQUEST_URI']);
        if (isset($_SESSION[$tokenName][$tokenKey])) {
            // 相同页面不重复生成session
            $tokenValue = $_SESSION[$tokenName][$tokenKey];
        } else {
            $tokenValue = is_callable($tokenType) ? $tokenType(microtime(true)) : md5(microtime(true));
            $_SESSION[$tokenName][$tokenKey] = $tokenValue;
        }

        return [$tokenName, $tokenKey, $tokenValue];
    }
	
	/**
     * 验证表单令牌
     * @param array $params
     * @access protected
     * @return bool
     */
    public static function checkToken($params = [])
    {
        if (self::$config['token_on']) {
            isset($params['rule']) || $params['rule'] = self::$config['token_name'];// 验证规则
            isset($params['data']) || $params['data'] = empty($params['data']) ? input('param.', []) : $params['data'];// 数据
            // 令牌数据无效
            if (!isset($params['data'][$params['rule']]) || !isset($_SESSION[$params['rule']])) {
                return false;
            }
            // 令牌验证
            list($key, $value) = explode('_', $params['data'][$params['rule']]);
			unset($params['data']);
            if (isset($_SESSION[$params['rule']][$key]) && $value && $_SESSION[$params['rule']][$key] === $value) {
                // 防止重复提交
                unset($_SESSION[$params['rule']][$key]); // 验证完成销毁session

                return true;
            }
            // 开启TOKEN重置
            if(self::$config['token_reset'])
                unset($_SESSION[$params['rule']][$key]);

            return false;
        }

        return true;
    }
}