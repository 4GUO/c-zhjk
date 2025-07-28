<?php
/**
 * 业务模块处理
 *
 */
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class deliver{
	public function kuaidi100($config, $express_info){
		if ($config['kuaidi100_customer'] && $config['kuaidi100_key'] && $express_info['e_code'] && $express_info['shipping_code']) {
			$key = $config['kuaidi100_key'];                        //客户授权key
			$customer = $config['kuaidi100_customer'];                   //查询公司编号
			$param = array (
				'com' => $express_info['e_code'],             //快递公司编码
				'num' => $express_info['shipping_code'],     //快递单号
				'phone' => '',                //手机号
				'from' => '',                 //出发地城市
				'to' => '',                   //目的地城市
				'resultv2' => '1'             //开启行政区域解析
			);
			
			//请求参数
			$post_data = array();
			$post_data['customer'] = $customer;
			$post_data['param'] = json_encode($param);
			$sign = md5($post_data['param'] . $key . $post_data['customer']);
			$post_data['sign'] = strtoupper($sign);
			
			$url = 'http://poll.kuaidi100.com/poll/query.do';    //实时查询请求地址
			
			$params = '';
			foreach ($post_data as $k => $v) {
				$params .= $k . '=' . urlencode($v) . '&';              //默认UTF-8编码格式
			}
			$post_data = substr($params, 0, -1);
			//发送post请求
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($ch);
		} else {
			$content = '';
		}		
		return $content;
	}
}