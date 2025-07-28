<?php
defined('SAFE_CONST') or exit('Access Invalid!');
class wxpay_h5
{
    public function submit($param)
    {
        $data = array();
        $data['appid'] = $param['appId'];
        $data['mch_id'] = $param['partnerId'];
        $data['nonce_str'] = md5(uniqid(mt_rand(), true));
        $data['body'] = $param['order_sn'];
        $data['attach'] = $param['order_type'];//订单类型
        $data['out_trade_no'] = $param['order_sn'];
        $data['total_fee'] = intval($param['order_amount'] * 100);
        $data['spbill_create_ip'] = $this->get_server_ip();
        $data['notify_url'] = $param['notify_url'];
        $data['trade_type'] = 'MWEB';
		$data['scene_info'] = json_encode(array('h5_info' => array('type' => 'Wap', 'wap_url' => $param['site_url'], 'wap_name' => '订单支付')));
        $sign = $this->sign($data, $param['apiKey']);
        $data['sign'] = $sign;
        $result = $this->postXml('https://api.mch.weixin.qq.com/pay/unifiedorder', $data);
        if ($result['return_code'] != 'SUCCESS') {
            return $result['return_msg'];
        }
        if ($result['result_code'] != 'SUCCESS') {
            return '[' . $result['err_code'] . ']' . $result['err_code_des'];
        }
		$result_sign = $result['sign'];
		unset($result['sign']);
		$sign = $this->sign($result, $param['apiKey']);
		if($sign == $result_sign){
			$result['mweb_url'] = $result['mweb_url'] . '&redirect_url=' . urlencode($param['payment_result_url']);
			
			return '<!DOCTYPE html PUBLIC \'-//W3C//DTD XHTML 1.0 Transitional//EN\' \'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\'>
				<html>
				<head>
					<meta http-equiv=\'Content-Type\' content=\'text/html; charset=utf-8\'>
					<meta http-equiv=\'Refresh\' content=\'0; url=' . $result['mweb_url'] . '\' /> 
					<title>正在为你跳转支付</title>
				</head>
				<body>
				</body>
				</html>';
		} else {
			return 'error sign！';
		}
    }
	
	public function getNotifyInfo($payment_config)
    {
        try {
            $data = $this->onNotify($payment_config);
            $resultXml = $this->arrayToXml(array(
                'return_code' => 'SUCCESS',
            ));
			return array('out_trade_no' => $data['out_trade_no'] . '_' . $data['attach'], 'trade_no' => $data['transaction_id']);
        } catch (Exception $ex) {
            $data = null;
            $resultXml = $this->arrayToXml(array(
                'return_code' => 'FAIL',
                'return_msg' => $ex->getMessage(),
            ));
			return false;
        }
		
    }

    private function onNotify($payment_config)
    {
        $d = $this->xmlToArray(file_get_contents('php://input'));

        if (empty($d)) {
            throw new Exception(__METHOD__);
        }

        if ($d['return_code'] != 'SUCCESS') {
            throw new Exception($d['return_msg']);
        }

        if ($d['result_code'] != 'SUCCESS') {
            throw new Exception('[' . $d['err_code'] . ']' . $d['err_code_des']);
        }

        if (!$this->verify($d, $payment_config)) {
            throw new Exception('Invalid signature');
        }

        return $d;
    }

    private function verify(array $d, $payment_config)
    {
        if (empty($d['sign'])) {
            return false;
        }

        $sign = $d['sign'];
        unset($d['sign']);

        return $sign == $this->sign($d, $payment_config['apiKey']);
    }
	
	private function sign($data, $apikey){
        ksort($data);

        $a = array();
        foreach ($data as $k => $v) {
            if ((string) $v === '') {
                continue;
            }
            $a[] = $k . '=' . $v;
        }

        $a = implode('&', $a);
        $a .= '&key=' . $apikey;

        return strtoupper(md5($a));
    }
	
	private function get_server_ip(){
        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $cip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $cip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else if(!empty($_SERVER['REMOTE_ADDR']))
        {
            $cip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $cip = '';
        }
        preg_match('/[\d\.]{7,15}/', $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
	}
	
	private function postXml($url, array $data){
        // pack xml
        $xml = $this->arrayToXml($data);

        // curl post
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if (!$response) {
            throw new Exception('CURL Error: ' . curl_errno($ch));
        }
        curl_close($ch);

        // unpack xml
        return $this->xmlToArray($response);
    }

    private function arrayToXml(array $data){
        $xml = '<xml>';
        foreach ($data as $k => $v) {
            if (is_numeric($v)) {
                $xml .= '<' . $k . '>' . $v . '</' . $k . '>';
            } else {
                $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
            }
        }
        $xml .= '</xml>';
        return $xml;
    }

    private function xmlToArray($xml){
		libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}