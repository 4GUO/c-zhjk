<?php
namespace shop\controller;
use base;
use lib;
class emptyc extends home {
	public function __construct() {
		parent::_initialize();
	}
	//404
	public function not_foundOp() {
	    $SEO = array(
	        'site_title' => config('name'),
	        'title' => '抱歉！内容不存在或已经删除',
	        'keywords' => '抱歉！内容不存在或已经删除',
	        'description' => '抱歉！内容不存在或已经删除',
	    );
	    $this->assign('tip_msg', input('get.tip_msg', '', 'trim'));
	    //定位模板
	    $this->_controller = 'emptyc';
	    $this->_action = 'not_found';
	    $this->assign('SEO', $SEO);
	    $this->view->_layout_file = 'null_layout';
	    $this->display();
	}
}