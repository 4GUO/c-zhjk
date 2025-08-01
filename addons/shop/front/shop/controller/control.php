<?php
namespace shop\controller;
use base;
use lib;
class control extends base\controller {
	protected $uniacid;
	protected $title = '';
	protected $rsUsers = array();
	protected $config = array();
	protected $fans_info;
	protected $client_type;
	protected $sign = '';
	protected $originarr = array();//允许跨域的域名
	public function _initialize() {
		parent::_initialize();
		$this->originarr = array(
		   'qq.test1.com',
		   'www.test2.com',
		);
		$this->client_type = input('get.client_type', '') ? input('get.client_type', '') : input('post.client_type', '');
		$this->sign = input('get.sign', '') ? input('get.sign', '') : input('post.sign', '');
		if (IS_API) {
			$sign_para = array_merge(input('get.'), input('post.'));
			// 注释掉签名验证
			/*
			if ($this->sign != getSign($sign_para, MD5_KEY)) {
				//output_error('接口签名错误');
			}
			*/
		}

		//lib\logging::write(var_export(array_merge(input('get.'), input('post.')), true));
		$this->uniacid = input('get.i', 1, 'intval') ? input('get.i', 1, 'intval') : input('post.i', 1, 'intval');
		if ($this->uniacid) {
			$this->rsUsers = model('users')->where(array('uniacid' => $this->uniacid))->find();
			$config = model('config')->where(array('uniacid' => $this->uniacid))->find();
			$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
			$this->config = array_merge($config, $wechat_setting);
			config($this->config);

			$fanid = input('get.fanid', 0, 'intval') ? input('get.fanid', 0, 'intval') : input('post.fanid', 0, 'intval');
			//lib\logging::write(var_export($fanid, true));
			if ($fanid) {
    			$this->fans_info = model('fans')->where(array('fanid' => $fanid))->find();
    			if (empty($this->fans_info['uid'])) {
    				if (!in_array($this->_controller, array('publics')) && !in_array($this->_action, array('bind_member_page','bind_member_submit'))) {
    				    output_error('', array('redirect' => '/pages/login/bind_member'));
    				}
    			}
			}
		} else {
			output_error('url缺少必要参数！');
		}
		$this->assign('cur_controller', $this->_controller);
		$this->assign('cur_action', $this->_action);
		$this->assign('config', $this->config);
		$this->assign('title', $this->config['name']);
	}
	protected function getMemberIdIfExists() {
		$key = input('post.key', '') ? input('post.key', '') : input('get.key', '');
		$mb_user_token_info = $this->getMbUserTokenInfoByToken($key);
		if (empty($mb_user_token_info)) {
			return 0;
		}
		return $mb_user_token_info['member_id'];
    }
	protected function get_payment_list() {
		$payment_list = model('mb_payment')->where(array('payment_state' => 1))->select();
		$only_payment_list = array();
		if (!empty($payment_list)) {
			foreach ($payment_list as $k => $value) {
				$payment_list[$k]['payment_config'] = fxy_unserialize($value['payment_config']);
				unset($payment_list[$k]['payment_id']);
				unset($payment_list[$k]['payment_config']);
				if (in_array($value['payment_code'], array('alipay', 'alipay_app'))) {
					$payment_list[$k]['payment_logo'] = '/static/btn_alipay_logo.png';
				}
				if (in_array($value['payment_code'], array('wxpay_jsapi', 'wxpay_h5', 'wxapp', 'wxpay_app'))) {
					$payment_list[$k]['payment_logo'] = '/static/btn_wxpay_logo.png';
				}
				if ($this->client_type == 'wxweb' && in_array($value['payment_code'], array('wxpay_jsapi', 'alipay'))) {
					$only_payment_list[] = $payment_list[$k];
				}
				if ($this->client_type == 'wap' && in_array($value['payment_code'], array('wxpay_h5', 'alipay'))) {
					$only_payment_list[] = $payment_list[$k];
				}
				if ($this->client_type == 'wxapp' && in_array($value['payment_code'], array('wxapp'))) {
					$only_payment_list[] = $payment_list[$k];
				}
				if ($this->client_type == 'app' && in_array($value['payment_code'], array('wxpay_app', 'alipay_app'))) {
					$only_payment_list[] = $payment_list[$k];
				}
			}
		}
		return $only_payment_list;
    }
	/**
	 *  跨域请求
	 */
	protected function setheader() {
		// 获取当前跨域域名
		$url = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
		$url_info = parse_url($url);
		//lib\logging::write(var_export($url_info, true));
		if (in_array($url_info['host'], $this->originarr)) {
		    $origin = $url_info['scheme'] . '://' . $url_info['host'];
			// 允许 $originarr 数组内的 域名跨域访问
			header('Access-Control-Allow-Origin:' . $origin);
			// 响应类型
			header('Access-Control-Allow-Methods:POST,GET');
			// 带 cookie 的跨域访问
			header('Access-Control-Allow-Credentials: true');
			// 响应头设置
			header('Access-Control-Allow-Headers:x-requested-with,Content-Type,X-CSRF-Token');
		}
	}
	/*
	 * 绘图文字分行函数
	 * by COoL
	 * - 输入：
	 * str: 原字符串
	 * fontFamily: 字体
	 * fontSize: 字号
	 * charset: 字符编码
	 * width: 限制每行宽度(px)
	 * - 输出：
	 * 分行后的字符串数组
	 */
	protected function autoLineSplit ($str, $fontFamily, $fontSize, $charset, $width) {
		$result = [];

		$len = (strlen($str) + mb_strlen($str, $charset)) / 2;

		// 计算总占宽
		$dimensions = imagettfbbox($fontSize, 0, $fontFamily, $str);
		$textWidth = abs($dimensions[4] - $dimensions[0]);

		// 计算每个字符的长度
		$singleW = $textWidth / $len;
		// 计算每行最多容纳多少个字符
		$maxCount = floor($width / $singleW);

		while ($len > $maxCount) {
			// 成功取得一行
			$result[] = mb_strimwidth($str, 0, $maxCount, '', $charset);
			// 移除上一行的字符
			$str = str_replace($result[count($result) - 1], '', $str);
			// 重新计算长度
			$len = (strlen($str) + mb_strlen($str, $charset)) / 2;
		}
		// 最后一行在循环结束时执行
		$result[] = $str;

		return $result;
	}
	protected function downloadImageFromWeiXin($url, $postdata, $file_path) {
       	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata, JSON_UNESCAPED_UNICODE));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$file = curl_exec($ch);
		curl_close($ch);
		$flag = true;
        $write = fopen ( $file_path, 'w' );
        if ($write == false) {
            $flag = false;
        }
        if (fwrite ( $write, $file ) == false) {
            $flag = false;
        }
        if (fclose ( $write ) == false) {
            $flag = false;
        }
		return $flag;
	}
	protected function getMbUserTokenInfoByToken($token)
	{
		if (empty($token)) {
            return null;
        }
        return model()->table('mb_user_token')->where(array('token' => $token))->find();
	}
	protected function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }
	//获取收入
	public function get_rewards($where) {
		//定制分销
		$r = model('distribute_detail_other')->getInfo($where, 'SUM(detail_bonus) as money');
		$rewards1 = empty($r['money']) ? 0 : $r['money'];
		//分红
		$r = model('distribute_fenhong_record_detail')->getInfo($where, 'SUM(detail_bonus) as money');
		$rewards2 = empty($r['money']) ? 0 : $r['money'];
		//商品分销奖励
		$r = model('distribute_detail')->getInfo($where, 'SUM(detail_bonus) as money');
		$rewards3 = empty($r['money']) ? 0 : $r['money'];
		//活动奖励
		$r = model('distribute_turntable_record_detail')->getInfo($where, 'SUM(detail_bonus) as money');
		$rewards4 = empty($r['money']) ? 0 : $r['money'];
		$rewards = $rewards1 + $rewards2 + $rewards3 + $rewards4;
        // TODO 见单奖励
		return array('rewards' => $rewards);
	}
	//获取公排收入
	public function get_public_rewards($where) {
		$where['member_id'] = $where['uid'];
		unset($where['uid']);
		$r = model('distributor_gp_detail')->getInfo($where, 'SUM(detail_bonus) as money');
		$rewards = empty($r['money']) ? 0 : $r['money'];
		return array('rewards' => $rewards);
	}
	//公排+分销已提现金额
	public function get_user_use_money($uid) {
		$r = model('withdraw_record')->getInfo(array('uid' => $uid, 'record_status' => array(0, 1)), 'SUM(record_total) as money');
		$withdraw = empty($r['money']) ? 0 : $r['money'];
		return $withdraw;
	}
}
