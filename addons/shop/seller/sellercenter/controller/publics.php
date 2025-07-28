<?php
namespace sellercenter\controller;
use lib;
class publics extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function loginOp(){
		if (IS_API) {
			\base\token::$config['token_name'] = 'sellerlogintoken';
			$result = chksubmit(true, false);
			if ($result) {
				if ($result === -11) {
					output_error('TOKEN失效，刷新页面重试');
				} else if ($result === -12) {
					output_error('验证码不正确');
				} else if(lib\process::islock('seller')) {
					output_error('您的操作过于频繁，请稍后再试');
				} else {
					$Account = input('post.Account', '');
					$Pass = input('post.Password', '');
					if (!$Account) {
						output_error('用户名不能为空');
					} else if(!$Pass) {
						output_error('密码不能为空');
					} else {
						$account = model('account_store')->getInfo(array('account_name' => $Account));
						if ($account) {
							$seller['id'] = $account['store_id'];
							$seller['name'] = $account['account_name'];
							$seller['login_slat'] = $account['salt'];
							$seller['login_password'] = $account['password'];
							$group = model('account_group_store')->getInfo(array('group_id' => $account['group_id']));
							$limits = explode(',', $group['limits']);
							$_SESSION['account_id'] = $account['account_id'];
							$_SESSION['is_seller'] = false;
						} else {
							$seller = model('seller')->getInfo(array('login_name' => $Account));
							$limits = array();
							$_SESSION['is_seller'] = true;
						}
						if ($seller) {
							if ($seller['login_password'] == md5($Pass . $seller['login_slat'])) {
								 // 卖家菜单
								$_SESSION['store_id'] = $seller['id'];
								$_SESSION['sellername'] = $seller['name'];
								$seller_menu = $this->getSellerMenuList($limits);
								$_SESSION['seller_menu'] = $seller_menu['seller_menu'];
								$_SESSION['seller_function_list'] = $seller_menu['seller_function_list'];
								$this->store_id = $seller['id'];
								$this->log('登录成功');
								lib\process::clear('seller');
								output_data('1');
							} else {
								lib\process::addprocess('seller');
								output_error('您输入的密码错误！');
							}
						} else {
							lib\process::addprocess('seller');
							output_error('您输入的账号不存在！');
						}
					}
				}
			}
		} else {
			\base\token::$config['token_name'] = 'sellerlogintoken';
			list($token_name, $token_key, $token_value) = \base\token::getToken();
			$this->assign('token_name', $token_name);
			$this->assign('token_value', $token_key . '_' . $token_value);
			$this->view->_layout_file = 'null_layout';
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
		unset($_SESSION['store_id'], $_SESSION['sellername']);
		header('location:' . _url('publics/login'));
		exit;
	}
}
?>