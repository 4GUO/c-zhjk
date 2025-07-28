<?php
/**
 * 验证码
 */
namespace shop\controller;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class seccode extends home
{
    public function __construct()
    {
    }
    /**
     * 产生干扰码
     *
     */
    public function makecodekeyOp()
    {
		if(IS_API){
			$seccode = makeSeccode();
			$seccode_key = encrypt(strtoupper($seccode) . '\t' . (time()) . '\t' . input('myhash', ''), MD5_KEY);
			output_data(array('seccode_key' => $seccode_key, 'seccode' => $seccode));
		}
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
}