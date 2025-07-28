<?php
namespace lib\wxSDK;

class WechatCrypt{
    /**
     * 加密KEY
     * @var string
     */
    private $cyptKey = '';

    /**
     * 公众平台APPID
     * @var string
     */
    private $appId = '';

    /**
     * 构造方法，初始化加密KEY
     * @param string $key   加密KEY
     * @param string $appid 微信APP_KEY
     */
    public function __construct($key, $appid){
        if($key && $appid){
            $this->appId   = $appid;
            $this->cyptKey = base64_decode($key . '=');
        } else {
            throw new \Exception('缺少参数 APP_ID 和加密KEY!');
        }
    }

    /**
     * 对明文进行加密
     * @param  string $text  需要加密的字符串
     * @return string        密文字符串
     */
    public function encrypt($text) {
		$key = $this->cyptKey;
		try {
			//填充到明文之前的随机字符
			$random = self::getRandomStr(16);

			//网络字节序
			$size = pack('N', strlen($text));

			//生成被加密字符串
			$text = $random . $size . $text . $this->appId;
		
			$iv = substr($key, 0, 16);
			$block_size = 32;
            $text_length = strlen($text);
            $amount_to_pad = $block_size - ($text_length % $block_size);
            if ($amount_to_pad == 0) {
                $amount_to_pad = $block_size;
            }
            $pad_chr = chr($amount_to_pad);
            $tmp = '';
            for ($index = 0; $index < $amount_to_pad; $index++) {
                $tmp .= $pad_chr;
            }
            $text = $text . $tmp;
            $encrypted = openssl_encrypt($text, 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
            return base64_encode($encrypted);
		} catch(\Exception $e) {
            //print $e;
            \lib\logging::write(var_export($e, true));
            return null;
        }
    }

    /**
     * 对密文进行解密
     * @param  string $encrypted 密文
     * @return string          明文
     */
    public function decrypt($encrypted) {
		$key = $this->cyptKey;
		$appid = $this->appId;
		try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $iv = substr($key, 0, 16);
            $decrypted = openssl_decrypt($ciphertext_dec, 'AES-256-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        } catch(\Exception $e) {
            \lib\logging::write(var_export($e, true));
            return null;
        }
		try {
            //去除补位字符
            $result = self::PKCS7Encode($decrypted, 32);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16) return '';
            $content = substr($result, 16, strlen($result));
            $len_list = unpack('N', substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
            $from_appid = substr($from_appid, 0, strlen($appid));
        } catch(\Exception $e) {
            //print $e;
            \lib\logging::write(var_export($e, true));
            return null;
        }
		if ($from_appid != $appid) {
            return null;
        }
        return $xml_content;
    }

    /**
     * PKCS7填充字符
     * @param string  $text 被填充字符
     * @param integer $size Block长度
     */
    private static function PKCS7Encode($text, $size){
		$block_size = $size;
        $text_length = strlen($text);
        //计算需要填充的位数
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr($amount_to_pad);
        $tmp = '';
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp.= $pad_chr;
        }
        return $text . $tmp;
    }

    /**
     * 删除PKCS7填充的字符
     * @param string  $text 已填充的字符
     * @param integer $size Block长度
     */
    private static function PKCS7Decode($text, $size){
        //获取补位字符
        $pad_str = ord(substr($text, -1));

        if ($pad_str < 1 || $pad_str > $size) {
            return '';
        } else {
            return substr($text, 0, strlen($text) - $pad_str);
        }
    }

    /**
     * 生成指定长度的字符串
     * @param  integer $len 字符串长度
     * @return string       生成的字符串
     */
    private static function getRandomStr($len){
        static $pol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

        $str = '';
        $max = strlen($pol) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $pol[mt_rand(0, $max)];
        }

        return $str;
    }
}