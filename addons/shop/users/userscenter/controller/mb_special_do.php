<?php
namespace userscenter\controller;
class mb_special_do extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function image_publishOp(){
		$this->assign('item_type', input('item_type', ''));
		if (input('image', '')) {
			$image = decrypt(input('image', ''));
		} else {
			$image = '';
		}
		$this->assign('image', $image);
		$this->assign('type', input('type', ''));
		if (input('data', '')) {
			$data = decrypt(input('data', ''));
		} else {
			$data = '';
		}
		$this->assign('data', $data);
		$this->assign('size', input('size', ''));
		$this->assign('imgbox', input('imgbox', ''));
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
}