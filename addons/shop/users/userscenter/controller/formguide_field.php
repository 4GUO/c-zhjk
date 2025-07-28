<?php
/**
 * 自定义表单
 **/
namespace userscenter\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class formguide_field extends control {
    public function __construct() {
        parent::_initialize();
        //允许使用的字段列表
        $this->banfie = array('text', 'checkbox', 'textarea', 'radio', 'select', 'image', 'number', 'Ueditor');
    }
    public function indexOp() {
        $modelid = input('modelid', 0, 'intval');
        $info = model('cms_model')->where(array('id' => $modelid))->find();
        $model = model('cms_model_field');
		$where = array();
		$where['modelid'] = $modelid;
		$keyword = input('get.keyword', '');
        if ($keyword) {
            $where['title'] = '%' . trim($keyword) . '%';
        }
        $list = $model->getList($where, '*', 'listorder ASC', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval'), 'keyword' => $keyword, 'modelid' => $modelid), _url('formguide_field/index')));
		$this->assign('list', $list['list']);
		$this->display();
    }
    public function publishOp() {
        if (chksubmit()) {
            $setting = input('setting', array());
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('title', ''), 'require' => 'true', 'message' => '字段别名不能为空'), array('input' => input('name', ''), 'require' => 'true', 'message' => '字段名不能为空'), array('input' => input('type', ''), 'require' => 'true', 'message' => '请选择字段类型'), array('input' => $setting['define'], 'require' => 'true', 'message' => '字段定义不能为空'));
            $error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
            if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]*$/', input('name', ''))) {
                output_error('字段名必须由字母、数字组成，并且仅能字母开头');
            }
            $modelid = input('modelid', 0, 'intval');
            if (!$modelid) {
                output_error('参数错误');
            }
            $id = input('id', 0, 'intval');
            $data = array(
                'modelid' => $modelid,
                'title' => input('title', '', 'trim'),
                'name' => input('name', '', 'trim'),
                'remark' => input('remark', '', 'trim'),
                'type' => input('type', '', 'trim'),
                'pattern' => input('pattern', '', 'trim'),
                'errortips' => input('errortips', '', 'trim'),
                'ifsystem' => input('ifsystem', 0, 'intval'),
                'ifrequire' => input('ifrequire', 0, 'intval'),
                'isadd' => input('isadd', 0, 'intval'),
                'isindex' => input('isindex', 0, 'intval'),
                'ifonly' => input('ifonly', 0, 'intval'),
                'setting' => input('setting', array()),
                'status' => input('status', 1, 'intval'),
                'listorder' => input('listorder', 0, 'intval'),
            );
            try {
                if ($id) {
                    $data['update_time'] = time();
                    $id = model('cms_model_field')->editField($data, $id);
    			} else {
    			    $data['create_time'] = time();
    				$id = model('cms_model_field')->addField($data);
    			}
            } catch (\Exception $e) {
                output_error($e->getMessage());
            }
            if ($id) {
				output_data(array('msg' => '操作成功', 'url' => _url('formguide_field/index', array('modelid' => $modelid))));
			} else {
				output_error('操作失败！');
			}
        } else {
            $modelid = input('modelid', 0, 'intval');
            $id = input('id', 0, 'intval');
			if ($id) {
				$info = model('cms_model_field')->where(array('id' => $id))->find();
				$info['setting'] = fxy_unserialize($info['setting']);
				$this->assign('info', $info);
			}
			$fieldType = model('cms_field_type')->where(array('name' => $this->banfie))->order('listorder')->field('name,title,default_define,ifoption,ifstring')->select();
			$fieldType = array_under_reset($fieldType, 'name');
			$this->assign('fieldType', $fieldType);
            $modelInfo = model('cms_model')->where(array('id' => $modelid))->find();
            $this->assign('modelInfo', $modelInfo);
            $fasttype = array(
                array('value' => 'varchar(255) NOT NULL', 'label' => '255个字符串以内'),
                array('value' => 'int(7) NOT NULL', 'label' => '10位以内纯数字'),
                array('value' => 'tinyint(2) NOT NULL', 'label' => '2位以内纯数字'),
                array('value' => 'text NOT NULL', 'label' => '常用文本文档'),
                array('value' => 'decimal(10,2) unsigned NOT NULL', 'label' => '价格'),
                array('value' => 'mediumtext NOT NULL', 'label' => '巨型文本文档'),
            );
            $this->assign('fasttype', $fasttype);
            $this->display();
        }
    }
    public function delOp() {
		$model = model('cms_model');
		$modelid = input('modelid', 0, 'intval');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
				continue;
			}
			try {
                model('cms_model_field')->deleteField($val);
            } catch (\Exception $e) {
                output_error($e->getMessage());
            }
		}
		output_data(array('url' => _url('formguide_field/index', array('modelid' => $modelid))));
    }
}