<?php
/**
 * 自定义表单
 **/
namespace userscenter\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class formguide extends control {
    public function __construct() {
        parent::_initialize();
    }
    public function indexOp() {
        $model = model('cms_model');
		$where = array();
		$where['is_del'] = 0;
		$keyword = input('get.keyword', '');
        if ($keyword) {
            $where['name'] = '%' . trim($keyword) . '%';
        }
        $list = $model->getList($where, '*', 'id DESC', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval'), 'keyword' => $keyword), users_url('formguide/index')));
		$this->assign('list', $list['list']);
		$this->display();
    }
    public function publishOp() {
        if (chksubmit()) {
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('name', ''), 'require' => 'true', 'message' => '表单标题不能为空'), array('input' => input('tablename', ''), 'require' => 'true', 'message' => '表单键名不能为空'));
            $error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
            if (!preg_match('/^[a-z]+[a-z0-9_]*$/', input('tablename', ''))) {
                output_error('表单键名必须由小写字母、数字组成，并且仅能小写字母开头');
            }
            $id = input('id', 0, 'intval');
            $data = array(
                'name' => input('name', '', 'trim'),
                'tablename' => input('tablename', '', 'trim'),
                'description' => input('description', '', 'trim'),
                'setting' => input('setting', array()),
                'status' => input('status', 1, 'intval'),
            );
            try {
                if ($id) {
                    $data['setting'] = serialize($data['setting']);
                    $data['update_time'] = time();
                    model('cms_model')->where(['id' => (int) $id])->update($data);
    			} else {
    			    $data['create_time'] = time();
    				$id = model('cms_model')->addModelFormguide($data);
    			}
            } catch (\Exception $e) {
                output_error($e->getMessage());
            }
            if ($id) {
				output_data(array('msg' => '操作成功', 'url' => _url('formguide/index')));
			} else {
				output_error('操作失败！');
			}
        } else {
            $id = input('id', 0, 'intval');
			if ($id) {
				$info = model('cms_model')->where(array('id' => $id))->find();
				$info['setting'] = fxy_unserialize($info['setting']);
				$this->assign('info', $info);
			}
            $this->display();
        }
    }
    public function delOp() {
		$model = model('cms_model');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$state = $model->where($where)->update(array('is_del' => 1));
        if ($state) {
            output_data(array('url' => users_url('formguide/index')));
        } else {
			output_error('删除失败！');
        }
    }
}