<?php
/** 
 * 令牌调用方式
 * 输出：直接在模板上调用getToken
 * 验证：在验证位置调用checkToken
 *  
 **/
namespace lib;
class security{
	/**
	 * 取得令牌内容
	 * 自动输出html 隐藏域
	 *
	 * @param 
	 * @return void 字符串形式的返回结果
	 */
	public static function getToken(){
		$token = encrypt(TIMESTAMP, md5(MD5_KEY));
		echo '<input type=\'hidden\' name=\'formhash\' value=\''. $token .'\' />';
	}
	public static function getTokenValue(){
        return encrypt(TIMESTAMP, md5(MD5_KEY));
	}

	/**
	 * 判断令牌是否正确
	 * 
	 * @param 
	 * @return bool 布尔类型的返回结果
	 */
	public static function checkToken(){
		// 注释掉Token验证，直接返回true
		return true;
		
		/*
		$data = decrypt(input('post.formhash', ''), md5(MD5_KEY));
		return $data && (TIMESTAMP - $data < 5400);
		*/
	}
}