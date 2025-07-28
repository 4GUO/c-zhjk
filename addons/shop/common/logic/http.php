<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class http {
	public function abort($code, $msg = '') {
	    send_http_status($code);
	    $_GET['tip_msg'] = $msg;
	    //执行控制器
	    if ($code == 404) {
	        $page_404 = new \shop\controller\emptyc();
	        $page_404->not_foundOp();
	    }
	    die();
	}
}