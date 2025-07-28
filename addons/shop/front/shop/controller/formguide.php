<?php
namespace shop\controller;
use base;
use lib;
class formguide extends home {
    //当前表单ID
    public $formid;
    protected $tableName;
    //模型信息
    protected $modelInfo = array();
    //配置
    protected $setting = array();
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = [];
	public function __construct() {
		parent::_initialize();
		$this->formid = input('get.formid', 0, 'intval') ?: input('post.formid', 0, 'intval');
        //模型
        $this->modelInfo = model('cms_model')->where(array('id' => $this->formid, 'status' => 1))->find();
        if (empty($this->modelInfo)) {
            output_error('该表单不存在或者已经关闭！');
        }
        //配置
        $this->modelInfo['setting'] = $this->setting = fxy_unserialize($this->modelInfo['setting']);
        
        $this->assign('modelInfo', $this->modelInfo);
	}
	public function indexOp() {
	    $fieldList = model('cms_model')->getFieldList($this->formid);
        $this->assign('fieldList', $fieldList);
	    $myhash = getUrlhash();
		base\token::$config['token_name'] = 'formtoken';
		list($token_name, $token_key, $token_value) = base\token::getToken();
		$this->assign('myhash', $myhash);
		$this->assign('token_name', $token_name);
		$this->assign('token_value', $token_key . '_' . $token_value);
		$this->display();
	}
	public function doOp() {
		if (IS_API) {
		    //验证权限
            $this->competence();
			$data = input('post.');
			try {
                $this->addFormguideData($this->formid, $data['modelField']);
            } catch (\Exception $ex) {
                output_error($ex->getMessage());
            }
			if ($this->setting['interval']) {
                setcookie('formguide_' . $this->formid, 1, $this->setting['interval'], '/');
            }
			$return  = array(
				
			);
			output_data($return);
		}
	}
	//验证提交权限
    protected function competence() {
        //提交间隔
        if ($this->setting['interval']) {
            $formguide = input('cookie.formguide_' . $this->formid);
            if ($formguide) {
                output_error('操作过快，请歇息后再次提交！');
            }
        }
        //是否允许同一IP多次提交
        if ((int) $this->setting['allowmultisubmit'] == 0) {
            $ip = get_client_ip();
            $count = model()->table($this->tableName)->where(array('ip' => $ip))->total();
            if ($count) {
                output_error('你已经提交过了！');
            }
        }
        if ($this->setting['isverify']) {
            $captcha = input('captcha', '');
			$codekey = input('seccode_key', '');
			$myhash = input('myhash', '');
            // 验证码
            if (!preg_match('/^\\w{4}$/', $captcha) || !checkSeccode($myhash, $captcha, $codekey)) {
				output_error('验证码错误');
			}
        }
    }
    //添加模型内容
    protected function addFormguideData($formid, $data, $dataExt = []) {
        //完整表名获取
        $tablename = model('cms_model_field')->getModelTableName($formid);
        if (!model('cms_model_field')->table_exists($tablename)) {
            throw new \Exception('数据表不存在！');
        }
        $data['uid'] = $this->getMemberIdIfExists();
        $data['username'] = '游客';
        //处理数据+验证
        $dataAll = model('cms_model')->dealModelPostData($formid, $data, $dataExt);
        list($data, $dataExt) = $dataAll;
        $data['inputtime'] = time();
        $data['ip'] = get_client_ip();
        $data['status'] = 2;
        base\token::$config['token_name'] = 'formtoken';
		if (!base\token::checkToken()) {
			output_error('非法请求，刷新页面重试');
		}
        try {
            //主表
            $id = model()->table($tablename)->insert($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
    public function s_up_img_updataOp() {
        $formid = input('get.formid', 0, 'intval') ?: input('post.formid', 0, 'intval');
		if (!$formid) {
		    output_error('缺少参数');
		}
		$upload = new lib\uploadfile();
		$upload->set('default_dir', front_upload_img_dir($formid) . 'evaluation/' . $upload->getSysSetPath());
		$upload->set('max_size', config('image_max_filesize'));
		$upload->set('allow_type', array('gif', 'jpg', 'jpeg', 'png'));
		//$upload->set('thumb_width', '640');
		//$upload->set('thumb_height', '250');
		$upload->set('fprefix', $formid);
		$result = $upload->upfile('file');
		if (!$result) {
			output_error($upload->error);
		} else {
			$data['src'] = front_upload_img_url($formid) . 'evaluation/' . $upload->getSysSetPath() . $upload->file_name;
			list($width, $height) = fxy_getimagesize($data['src']);
			$data['width'] = $width;
			$data['height'] = $height;
			output_data($data);
		}
	}
}