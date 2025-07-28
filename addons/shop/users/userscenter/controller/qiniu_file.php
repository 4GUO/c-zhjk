<?php
/**
 * 图片空间操作
 **/
namespace userscenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class qiniu_file extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function upload_fileOp()
    {
        // 上传图片
        $upload = new \lib\uploadfile();
        $upload->set('default_dir', $this->_upload_file_dir . $upload->getSysSetPath());
        $upload->set('max_size', config('file_max_filesize'));
        $upload->set('allow_type', array('mp4'));
		$file_name = input('name', 'file');
        $result = $upload->upfile($file_name);
        if (!$result) {
			output_error($upload->error);
        }
		$file_url = $this->_upload_file_url . $upload->getSysSetPath() . $upload->file_name;
		$fullname = $this->_upload_file_dir . $upload->getSysSetPath() . $upload->file_name;
		if (config('attachment_open')) {
    		$attachment_type = config('attachment_host_type');
    		if ($attachment_type == 2) {
    			save_image_to_qiniu($fullname, $file_url);
    		}
		}
        // 存入
		/*$file = explode('.', $_FILES[$file_name]['name']);
        $insert_array = array();
		$insert_array['uniacid'] = $this->store_id;
        $insert_array['file_name'] = $file['0'];
        $insert_array['file_url'] = $file_url;
        $insert_array['aclass_id'] = input('aclass_id', 0, 'intval');
        $insert_array['add_time'] = time();
        model('qiniu_file')->add($insert_array);*/
        $data = array();
        $data['file_url'] = $file_url;
        output_data($data);
    }
}