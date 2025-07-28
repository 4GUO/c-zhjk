<?php
namespace userscenter\controller;
use lib;
class poster extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list_temp = model()->table('fxy_weishang_poster')->where(array('uniacid' => $this->uniacid))->select();
		$this->assign('list', $list_temp);
		$this->display();
	}
	public function addOp(){
		if(chksubmit()){
			$obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('cert_image', ''), 'require' => 'true', 'message' => '请上传图片'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$img_info = fxy_getimagesize(input('cert_image', ''));
			$cert_info = array(
				'uniacid' => $this->uniacid,
				'cert_name' => input('cert_name', ''),
				'cert_image' => input('cert_image', ''),
				'cert_width' => $img_info[0],
				'cert_height' => $img_info[1],
				'cert_data' => '',
				'cert_usable' => input('cert_usable', 0, 'intval'),
				'cert_top' => 0,
				'cert_left' => 0,
				'cert_font_family' => 'simhei',
				'cert_font_color' => '#000000',
				'cert_font_size' => '14',
				'cert_sort' => input('cert_sort', 0, 'intval'),
				'cert_ico' => input('cert_ico', ''),
				'cert_type' => input('cert_type', 4, 'intval'),
			);
            $state = model()->table('fxy_weishang_poster')->insert($cert_info);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('poster/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		if(chksubmit()){
			$obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('cert_image', ''), 'require' => 'true', 'message' => '请上传图片'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$img_info = fxy_getimagesize(input('cert_image', ''));
			$cert_info = array(
				'uniacid' => $this->uniacid,
				'cert_name' => input('cert_name', ''),
				'cert_image' => input('cert_image', ''),
				'cert_width' => $img_info[0],
				'cert_height' => $img_info[1],
				'cert_usable' => input('cert_usable', 0, 'intval'),
				'cert_top' => 0,
				'cert_left' => 0,
				'cert_font_family' => 'simhei',
				'cert_font_color' => '#000000',
				'cert_font_size' => '14',
				'cert_sort' => input('cert_sort', 0, 'intval'),
				'cert_ico' => input('cert_ico', ''),
				'cert_type' => input('cert_type', 4, 'intval'),
			);
            $state = model()->table('fxy_weishang_poster')->where(array('id' => input('id', 0, 'intval')))->update($cert_info);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('poster/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$id = input('id', 0, 'intval');
			$info = model()->table('fxy_weishang_poster')->where(array('id' => $id))->find();
			$this->assign('info', $info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$state = model()->table('fxy_weishang_poster')->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('poster/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function designOp(){
		if(chksubmit()){
			$waybill_data = input('waybill_data/a', array());
			$cert_info = array(
				'cert_data' => serialize($waybill_data)
			);
            $state = model()->table('fxy_weishang_poster')->where(array('id' => input('id', 0, 'intval')))->update($cert_info);
            del_dir_or_file(UPLOADFILES_PATH . '/poster/poster' . input('cert_type', 4, 'intval') . '/');
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('poster/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$id = input('id', 0, 'intval');
			
			$cert_info = model()->table('fxy_weishang_poster')->where(array('id' => $id, 'uniacid' => $this->uniacid))->find();
			if(empty($cert_info['id'])){
				web_error('信息不存在');
			}
			if(empty($cert_info['id'])){
				web_error('请先设置背景图', users_url('poster/edit', array('id' => $id)));
			}
			
			$info_data = fxy_unserialize($cert_info['cert_data']);
			//项目列表
			list($item_list, $info_data) = $this->getItemList($cert_info['cert_type'], $info_data);
			$this->assign('info_data', $info_data);
			$this->assign('item_list', $item_list);
			$this->assign('cert_info', $cert_info);
			$this->display();
		}
	}
	private function getItemList($cert_type, $info_data){
		if($cert_type == 1) {//商学院
			$item_list = array(
				'nickname' => array('item_text' => '微信昵称'),
				'headimg' => array('item_text' => '微信图像'),
				'ercode' => array('item_text' => '二维码'),
				'thumb' => array('item_text' => '课程缩略图'),
				'chart_title' => array('item_text' => '课程标题'), 
				'chart_desc' => array('item_text' => '课程简介'), 
			);
		} else if ($cert_type == 4) {
			$item_list = array(
				'nickname' => array('item_text' => '微信昵称'),
				'headimg' => array('item_text' => '微信图像'),
				'ercode' => array('item_text' => '二维码'),
			);
		}
		if (!empty($info_data)) {
			foreach ($info_data as $key => $value) {
				$info_data[$key]['item_text'] = $item_list[$key]['item_text'];
			}
		}
		foreach ($item_list as $key => $value) {
			$item_list[$key]['check'] = !empty($info_data[$key]['check']) ? 'checked' : '';
			$item_list[$key]['width'] = !empty($info_data[$key]['width']) ? $info_data[$key]['width'] : '0';
			$item_list[$key]['height'] = !empty($info_data[$key]['height']) ? $info_data[$key]['height'] : '0';
			$item_list[$key]['top'] = !empty($info_data[$key]['top']) ? $info_data[$key]['top'] : '0';
			$item_list[$key]['left'] = !empty($info_data[$key]['left']) ? $info_data[$key]['left'] : '0';
		}
		return array($item_list, $info_data);
	}
}