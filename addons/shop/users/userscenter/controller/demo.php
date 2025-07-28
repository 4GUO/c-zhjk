<?php
namespace userscenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class demo extends \base\controller
{
    public function __construct()
    {
        parent::_initialize();
		
    }
    public function indexOp() {
        die();
		//var_dump(intval('ZH004'));
		$uid = 15;
		logic('shop_queue')->tihuoquan_to_fenhongquan(array('uid' => $uid));
	}
}