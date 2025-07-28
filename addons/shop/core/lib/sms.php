<?php
/**
 * 手机短信类
 */
namespace lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class sms
{
    /*
     * 发送手机短信
     * @param unknown $mobile 手机号
     * @param unknown $content 短信内容
     */
    public function send($mobile, $content)
    {
        $mobile_host_type = config('mobile_host_type');
        if ($mobile_host_type == 1) {
            return $this->mysend_smsbao($mobile, $content);
        }
        if ($mobile_host_type == 2) {
            return $this->mysend_yunpian($mobile, $content);
        }
		if ($mobile_host_type == 3) {
            return $this->mysend_longxiang($mobile, $content);
        }
        if ($mobile_host_type == 4) {
            //开发指南：https://help.aliyun.com/document_detail/55359.html?spm=a2c4g.11186623.2.26.464e57cfuNqIcX
            return $this->mysend_aliyun($mobile, $content);
        }
    }
    /*
    您于{$send_time}绑定手机号，验证码是：{$verify_code}。【{$site_name}】
    0  提交成功
    30：密码错误
    40：账号不存在
    41：余额不足
    42：帐号过期
    43：IP地址限制
    50：内容含有敏感词
    51：手机号码不正确
    http://api.smsbao.com/sms?u=USERNAME&p=PASSWORD&m=PHONE&c=CONTENT
    */
    private function mysend_smsbao($mobile, $content)
    {
        $user_id = urlencode(config('mobile_username'));
        // 这里填写用户名
        $pass = urlencode(config('mobile_pwd'));
        // 这里填登陆密码
        if (!$mobile || !$content || !$user_id || !$pass) {
            return false;
        }
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $mobile = urlencode($mobile);
        //$content = $content . '【我的网站】';
        $content = urlencode($content);
        $pass = md5($pass);
        //MD5加密
        $url = 'http://api.smsbao.com/sms?u=' . $user_id . '&p=' . $pass . '&m=' . $mobile . '&c=' . $content . '';
        $res = file_get_contents($url);
        //return $res;
        $ok = $res == '0';
        if ($ok) {
            return true;
        }
        return false;
    }
    /**
    	 * http://www.yunpian.com/
    * 发送手机短信
    * @param unknown $mobile 手机号
    * @param unknown $content 短信内容
    	  0 	OK 	调用成功，该值为null 	无需处理
    	  1 	请求参数缺失 	补充必须传入的参数 	开发者
    	  2 	请求参数格式错误 	按提示修改参数值的格式 	开发者
    	  3 	账户余额不足 	账户需要充值，请充值后重试 	开发者
    	  4 	关键词屏蔽 	关键词屏蔽，修改关键词后重试 	开发者
    	  5 	未找到对应id的模板 	模板id不存在或者已经删除 	开发者
    	  6 	添加模板失败 	模板有一定的规范，按失败提示修改 	开发者
    	  7 	模板不可用 	审核状态的模板和审核未通过的模板不可用 	开发者
    	  8 	同一手机号30秒内重复提交相同的内容 	请检查是否同一手机号在30秒内重复提交相同的内容 	开发者
    	  9 	同一手机号5分钟内重复提交相同的内容超过3次 	为避免重复发送骚扰用户，同一手机号5分钟内相同内容最多允许发3次 	开发者
    	  10 	手机号黑名单过滤 	手机号在黑名单列表中（你可以把不想发送的手机号添加到黑名单列表） 	开发者
    	  11 	接口不支持GET方式调用 	接口不支持GET方式调用，请按提示或者文档说明的方法调用，一般为POST 	开发者
    	  12 	接口不支持POST方式调用 	接口不支持POST方式调用，请按提示或者文档说明的方法调用，一般为GET 	开发者
    	  13 	营销短信暂停发送 	由于运营商管制，营销短信暂时不能发送 	开发者
    	  14 	解码失败 	请确认内容编码是否设置正确 	开发者
    	  15 	签名不匹配 	短信签名与预设的固定签名不匹配 	开发者
    	  16 	签名格式不正确 	短信内容不能包含多个签名【 】符号 	开发者
    	  17 	24小时内同一手机号发送次数超过限制 	请检查程序是否有异常或者系统是否被恶意攻击 	开发者
    	  -1 	非法的apikey 	apikey不正确或没有授权 	开发者
    	  -2 	API没有权限 	用户没有对应的API权限 	开发者
    	  -3 	IP没有权限 	访问IP不在白名单之内，可在后台"账户设置->IP白名单设置"里添加该IP 	开发者
    	  -4 	访问次数超限 	调整访问频率或者申请更高的调用量 	开发者
    	  -5 	访问频率超限 	短期内访问过于频繁，请降低访问频率 	开发者
    	  -50 未知异常 	系统出现未知的异常情况 	技术支持
    	  -51 系统繁忙 	系统繁忙，请稍后重试 	技术支持
    	  -52 充值失败 	充值时系统出错 	技术支持
    	  -53 提交短信失败 	提交短信时系统出错 	技术支持
    	  -54 记录已存在 	常见于插入键值已存在的记录 	技术支持
    	  -55 记录不存在 	没有找到预期中的数据 	技术支持
    	  -57 用户开通过固定签名功能，但签名未设置 	联系客服或技术支持设置固定签名 	技术支持
    */
    private function mysend_yunpian($mobile, $content)
    {
        $yunpian = 'yunpian';
        $plugin = str_replace('\\', '', str_replace('/', '', str_replace('.', '', $yunpian)));
        if (!empty($plugin)) {
            return smsapi\yunpian_send::send_sms($content, $mobile);
        } else {
            return false;
        }
    }
	private function mysend_longxiang($mobile, $content)
    {
        $user_id = urlencode(config('mobile_username'));// 这里填写用户名
        $pass = urlencode(config('mobile_pwd'));// 这里填登陆密码
		$apiid = urlencode(config('mobile_key'));
        if (!$mobile || !$content || !$user_id || !$pass) {
            return false;
        }
        if (is_array($mobile)) {
            $mobile = implode(',', $mobile);
        }
        $mobile = urlencode($mobile);
        $content = urlencode($content);
        //MD5加密
        $url = 'http://39.108.190.210:8118/msgapi.aspx?action=send&username=' . $user_id . '&password=' . $pass . '&apiid=' . $apiid . '&mobiles=' . $mobile . '&text=' . $content;
        /*$url = 'http://39.108.190.210:8118/msgapi.aspx';
		$postdata = array(
			'action' => 'send',
			'username' => $user_id,
			'password' => $pass,
			'apiid' => $apiid,
			'mobiles' => $mobile,
			'text' => $content
		);
		$res = $this->curl_post($url, $postdata);*/
		$res = file_get_contents($url);
		\lib\logging::write(var_export($mobile, true));
		\lib\logging::write(var_export($res, true));
		$xml = simplexml_load_string($res);
		$result = json_decode(json_encode($xml), TRUE);
        $ok = $result['result'];
        if ($ok) {
            return true;
        }
        return false;
    }
    /**
     * 发送短信
     */
    private function mysend_aliyun($mobile, $content) {
    
        $params = array ();
    
        // *** 需用户填写部分 ***
        // fixme 必填：是否启用https
        $security = false;
    
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = config('mobile_accessKeyId');
        $accessKeySecret = config('mobile_accessKeySecret');
    
        // fixme 必填: 短信接收号码
        $params['PhoneNumbers'] = $mobile;
    
        // fixme 必填: 短信签名，应严格按'签名名称'填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params['SignName'] = config('mobile_sign_name');
    
        // fixme 必填: 短信模板Code，应严格按'模板CODE'填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params['TemplateCode'] = config('mobile_templateCode');
    
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            'code' => '12345',
            'product' => '阿里通信'
        );
    
        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = '12345';
    
        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = '1234567';
    
    
        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params['TemplateParam']) && is_array($params['TemplateParam'])) {
            $params['TemplateParam'] = json_encode($params['TemplateParam'], JSON_UNESCAPED_UNICODE);
        }
    
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new lib\AliyunDySDKLite\SignatureHelper();
    
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            'dysmsapi.aliyuncs.com',
            array_merge($params, array(
                'RegionId' => 'cn-hangzhou',
                'Action' => 'SendSms',
                'Version' => '2017-05-25',
            )),
            $security
        );
    
        return $content;
    }
	private function curl_get($url){
       	$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
		curl_close($ch);
		$encoding = mb_detect_encoding($res, array('ASCII','UTF-8','GB2312','GBK','BIG5'));
		$res = mb_convert_encoding($res, 'utf-8', $encoding);
		$data = json_decode($res, true);
		return $data;
	}
	private function curl_post($url, $postdata){
		$postdata = json_encode($postdata);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $res = curl_exec($ch);
		\lib\logging::write(var_export($res, true));
		curl_close($ch);
		$data = json_decode($res, true);
		return $data;
	}
}