<?php
/**
 * 文件上传操作
 **/
namespace userscenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class file_upload extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
	/**
     * 上传pem
     */
    public function pem_uploadOp()
    {
		$this->_upload_img_dir = str_replace('/image/', '/file/', $this->_upload_img_dir);
        $upload = new \lib\uploadfile();
        $upload->set('default_dir', $this->_upload_img_dir . $upload->getSysSetPath());
        $upload->set('max_size', config('image_max_filesize'));
        $upload->set('fprefix', $this->uniacid);
        $upload->set('allow_type', array('pem'));
		$file_name = input('name', '');
        $result = $upload->upfile($file_name);
        if (!$result) {
			output_error($upload->error);
        }
		$this->_upload_img_url = str_replace('/image/', '/file/', $this->_upload_img_url);
        $data['file_url'] = $this->_upload_img_url . $upload->getSysSetPath() . $upload->file_name;
        output_data($data);
    }
}