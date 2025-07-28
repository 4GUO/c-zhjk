<?php
/**
 * 图片空间操作
 **/
namespace userscenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class album extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function pic_list_viewOp()
    {
        $where = array();
        $where['store_id'] = 0;
		$aclass_id = input('get.id', 0, 'intval');
        if ($aclass_id) {
            $where['aclass_id'] = $aclass_id;
            $cinfo = model('album_class')->getInfo($where);
            $this->assign('class_name', $cinfo['aclass_name']);
        }
        $class_list = model('album_class')->getList(array('store_id' => 0));
        $this->assign('class_list', $class_list['list']);
		$this->view->_layout_file = 'null_layout';
		$this->display('album_manger');
    }
	public function pic_list_ajaxOp()
	{
		if(IS_API){
			$model_album = model('album');
			$where['store_id'] = 0;
			$aclass_id = input('get.gc_id', 0, 'intval');
			if ($aclass_id) {
				$where['aclass_id'] = $aclass_id;
			}
			$pic_list = $model_album->getList($where, '*', 'apic_id DESC', 18, input('get.page', 1, 'intval'));
			output_data(array('list' => $pic_list['list'], 'totalpage' => $pic_list['totalpage'], 'page_html' => page($pic_list['totalpage'], array('page' => input('get.page', 1, 'intval')), users_url('album/pic_list_ajax'), true)));
		}
	}
	/**
     * 上传图片
     */
    public function image_uploadOp()
    {
        // 上传图片
        $upload = new \lib\uploadfile();
		//var_dump($this->_upload_img_dir . $upload->getSysSetPath());exit;
        $upload->set('default_dir', $this->_upload_img_dir . $upload->getSysSetPath());
        $upload->set('max_size', config('image_max_filesize'));
        $upload->set('thumb_width', 800);
        $upload->set('thumb_height', 800);
        $upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
		$file_name = input('name', 'file');
        $result = $upload->upfile($file_name);
        if (!$result) {
			output_error($upload->error);
        }
		$fullname = $this->_upload_img_dir . $upload->getSysSetPath() . $upload->file_name;
		if (config('attachment_open')) {
		    $attachment_type = config('attachment_host_type');
    		if ($attachment_type == 2) {
    			save_image_to_qiniu($fullname, $this->_upload_img_url . $upload->getSysSetPath() . $upload->file_name);
    		}
		}
        // 取得图像大小
        list($width, $height, $type, $attr) = getimagesize($fullname);
        // 存入相册
        $image = explode('.', $_FILES[$file_name]['name']);
        $insert_array = array();
        $insert_array['apic_name'] = $image['0'];
        $insert_array['apic_tag'] = '';
        $insert_array['aclass_id'] = input('aclass_id', 0, 'intval');
        $insert_array['apic_cover'] = $this->_upload_img_url . $upload->getSysSetPath() . $upload->file_name;
        $insert_array['apic_size'] = intval($_FILES[$file_name]['size']);
        $insert_array['apic_spec'] = $width . 'x' . $height;
        $insert_array['upload_time'] = time();
        $insert_array['store_id'] = 0;
        model('album')->add($insert_array);
        $data = array();
        $data['file_url'] = $this->_upload_img_url . $upload->getSysSetPath() . $upload->file_name;
        output_data($data);
    }
	public function del_picOp()
	{
		$upload = new \lib\uploadfile();
		$apic_id = input('apic_id', 0, 'intval');
		$album_pic_info = model('album')->getInfo(array('apic_id' => $apic_id));
		if ($album_pic_info) {
			model('album')->del(array('store_id' => 0, 'apic_id' => $apic_id));
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $album_pic_info['apic_cover']) && is_file($_SERVER['DOCUMENT_ROOT'] . $album_pic_info['apic_cover'])){
				//unlink($_SERVER['DOCUMENT_ROOT'] . $album_pic_info['apic_cover']);
			}
			output_data('1');
		} else {
			output_error('图片不存在');
		}
	}
	public function movetoOp()
	{
		$where['apic_id'] = input('apic_id', 0, 'intval');
        $array = array();
        $array['aclass_id'] = input('aclass_id', 0, 'intval');
        model('album')->edit($where, $array);
        output_data('1');
	}
}