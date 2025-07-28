<?php
namespace api\controller;
use core;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class tippage extends control {
    public function __construct() {
        parent::_initialize();
    }
    public function errorOp() {
		$msg = input('msg', '');
		$this->assign('msg', $msg);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
}