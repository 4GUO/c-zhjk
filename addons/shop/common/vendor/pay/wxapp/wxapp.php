<?php
/**
 * 微信支付接口类
 */
defined('SAFE_CONST') or exit('Access Invalid!');

/**
 * @todo TEST 传递的URL参数是否冲突
 * @todo 后续接收通知
 * @todo 后续页面显示 以及异步结果提示
 */
class wxapp{
	/**
	统一下单接口
	$pay_param包含order_sn, subject, order_amount, appid, appsecret, mchid, signkey
	*/
	public function submit($pay_param){
		$noncestr = md5(rand());
        $param = array();
        $param['appid'] = $pay_param['appid'];
        $param['mch_id'] = $pay_param['mchid'];
        $param['nonce_str'] = $noncestr;
        $param['body'] = $pay_param['subject'];
        $param['out_trade_no'] = $pay_param['mchid'].date('YmdHis').rand(1000, 9999);
        $param['total_fee'] = $pay_param['order_amount'] * 100;
		$param['attach'] = $pay_param['order_sn'] . '_' . $pay_param['order_type'];		
        $param['spbill_create_ip'] = get_server_ip();
        $param['notify_url'] = front_url('payment/notify', array('payment_code' => 'wxapp'), false, true);
        $param['trade_type'] = 'JSAPI';
		$param['openid'] = $pay_param['openid'];
        $sign = $this->_get_wx_pay_sign($param, $pay_param['signkey']);		
        $param['sign'] = $sign;
        $post_data = '<xml>';
        foreach ($param as $key => $value) {
            $post_data .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $post_data .= '</xml>';
        $prepay_result = $this->http_postXml('https://api.mch.weixin.qq.com/pay/unifiedorder', $post_data);
        $array = $this->xmltoarray($prepay_result);		
		$prepay_result = array();
		foreach ($array as $key => $value) {
            $prepay_result[$key] = (string)$value;
        }
		
        if ($prepay_result['return_code'] != 'SUCCESS') {
            return array('error' => $prepay_result['return_msg']);
        }
		
		if ($prepay_result['result_code'] != 'SUCCESS') {
            return array('error' => $prepay_result['err_code_des']);
        }
		
        $data = array();
		$data['appId'] = $pay_param['appid'];
        $data['nonceStr'] = $noncestr;
        $data['package'] = 'prepay_id=' . (string)$prepay_result['prepay_id'];
        $data['timeStamp'] = (string)TIMESTAMP;
		$data['signType'] = 'MD5';	
        $sign = $this->_get_wx_pay_sign($data, $pay_param['signkey']);
        $data['paySign'] = $sign;
		$data['paycode'] = 'wxapp';
        return $data;
	}
	
	private function _get_wx_pay_sign($param, $signkey){
		$string = '';
        ksort($param);
        foreach ($param as $key => $val) {
            $string .= $key . '=' . $val . '&';
        }
        $string .= 'key=' . $signkey;
        return strtoupper(md5($string));
    }
	
	public function http_postXml($url, $postdata){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$response = curl_exec($ch);
		if (!$response) {
			throw new Exception('CURL Error: ' . curl_errno($ch));
		}
		curl_close($ch);
		return $response;
	}

    /**
     * 获取notify信息
     */
    public function getNotifyInfo($payment_config) {
        $result = $this->_verify3($payment_config);

        if ($result) {
            return array(
                //商户订单号
                'out_trade_no' => $result['out_trade_no'],
                //微信支付交易号
                'trade_no' => $result['transaction_id'],
				'total_fee' => $result['total_fee'],
            );
        }

        return false;
    }

    /**
     * 验证返回信息(v3)
     */
    private function _verify3($payment_config) {
        if(empty($payment_config)) {
            return false;
        }
		
        $xml = file_get_contents('php://input');
		
        $array = $this->xmltoarray($xml);
        $param = array();
        foreach ($array as $key => $value) {
            $param[$key] = (string)$value;
        }
		
        ksort($param);
        $hash_temp = '';
        foreach ($param as $key => $value) {
            if($key != 'sign') {
                $hash_temp .= $key . '=' . $value . '&';
            }
        }

        $hash_temp .= 'key' . '=' . $payment_config['signkey'];

        $hash = strtoupper(md5($hash_temp));

        if($hash == $param['sign']) {
            return array(
                'out_trade_no' => $param['attach'],
                'transaction_id' => $param['transaction_id'],
				'total_fee' => $param['total_fee'],
            );
        } else {
            return false;
        }
    }
	
	/**
	提现发放
	$record_info  openid，amount，appid，mchid，signkey，apiclient_cert，apiclient_key
	*/
	public function Compay($pay_param){
		$noncestr = md5(rand());
		$tradeno = $pay_param['mchid'].date('YmdHis').rand(1000, 9999);
        $param = array();
        $param['mch_appid'] = $pay_param['appid'];
        $param['mchid'] = $pay_param['mchid'];
        $param['nonce_str'] = $noncestr;
        $param['partner_trade_no'] = $tradeno;
		$param['openid'] = $pay_param['openid'];
		$param['check_name'] = 'NO_CHECK';
        $param['amount'] = $pay_param['amount'] * 100;
		$param['desc'] = '提成发放';
        $param['spbill_create_ip'] = get_server_ip();		
		$sign = $this->_get_wx_pay_sign($param, $pay_param['signkey']);	
        $param['sign'] = $sign;
        $post_data = '<xml>';
        foreach ($param as $key => $value) {
            $post_data .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $post_data .= '</xml>';
		
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		$ch = curl_init();   	
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $_SERVER['DOCUMENT_ROOT'] . $pay_param['apiclient_cert']);    	
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$_SERVER['DOCUMENT_ROOT'] . $pay_param['apiclient_key']);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		$response = curl_exec($ch);
		if (!$response) {
			return array('error' => 'curl 错误');
		}
		curl_close($ch);
        $array = $this->xmltoarray($response);
		
		$prepay_result = array();
		foreach ($array as $key => $value) {
            $prepay_result[$key] = (string)$value;
        }
		
        if ($prepay_result['return_code'] != 'SUCCESS') {
            return array('error' => $prepay_result['return_msg']);
        }
		
		
		if($prepay_result['result_code'] != 'SUCCESS'){
			return array('error' => $prepay_result['err_code_des']);
		}
		
		$data = array(
			'error' => '',
			'outtradeno' => $prepay_result['partner_trade_no'],
			'tradeno' => $prepay_result['payment_no'],
			'tradetime' => time()
		);
		return $data;
	}
	
	/**
	提现发放
	$record_info  amount，trade_no，appid，mchid，signkey，apiclient_cert，apiclient_key
	*/
	public function Refundpay($pay_param){
		$noncestr = md5(rand());
		$tradeno = $pay_param['mchid'].date('YmdHis').rand(1000, 9999);
        $param = array();
        $param['appid'] = $pay_param['appid'];
        $param['mch_id'] = $pay_param['mchid'];
        $param['nonce_str'] = $noncestr;
        $param['transaction_id'] = $pay_param['trade_no'];
		$param['out_refund_no'] = $tradeno;		
        $param['total_fee'] = $pay_param['amount'] * 100;
		$param['refund_fee'] = $pay_param['amount'] * 100;	
		$sign = $this->_get_wx_pay_sign($param, $pay_param['signkey']);	
        $param['sign'] = $sign;
        $post_data = '<xml>';
        foreach ($param as $key => $value) {
            $post_data .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        $post_data .= '</xml>';
		
		$url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
		$ch = curl_init();   	
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $_SERVER['DOCUMENT_ROOT'] . $pay_param['apiclient_cert']);    	
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$_SERVER['DOCUMENT_ROOT'] . $pay_param['apiclient_key']);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
		$response = curl_exec($ch);
		if (!$response) {
			return array('error' => 'curl 错误');
		}
		curl_close($ch);
        $array = $this->xmltoarray($response);
		if (!is_array($array)) {
		    return array('error' => 'simplexml_load_string解析出错');
		}
		$prepay_result = array();
		foreach ($array as $key => $value) {
            $prepay_result[$key] = (string)$value;
        }
		
        if ($prepay_result['return_code'] != 'SUCCESS') {
            return array('error' => $prepay_result['return_msg']);
        }
		
		
		if($prepay_result['result_code'] != 'SUCCESS'){
			return array('error' => $prepay_result['err_code_des']);
		}
		
		$data = array(
			'error' => '',
			'refund_id' => $prepay_result['refund_id']
		);
		return $data;
	}
	
	public function xmltoarray($xml){
		libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
	} 
}
