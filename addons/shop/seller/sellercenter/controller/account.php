<?php
/**
 * 账号管理
 **/
namespace sellercenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class account extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function indexOp()
    {
        $model_account = model('account_store');
        $condition = array('store_id' => $this->store_id, 'group_id > ' => 0);
        $list = $model_account->getList($condition);
		$this->assign('list', $list['list']);
        $model_group = model('account_group_store');
        $group_array = $model_group->getList(array('store_id' => $this->store_id));
        $group_list = array_under_reset($group_array['list'], 'group_id');
		$this->assign('group_list', $group_list);
        $this->display();
    }
    public function addOp(){
		$model = model('account_store');
		if(chksubmit()){
			if(!input('account_name', '')){
				output_error('登陆账号不能为空');
			}
			if(!input('group_id', 0, 'intval')){
				output_error('请选择用户组');
			}
			if(!input('password', '')){
				output_error('请填写登陆密码');
			}
			if($this->_is_seller_name_exist(input('account_name', ''))){
				output_error('登陆账号已存在');
			}
			if($this->_is_account_name_exist(input('account_name', ''))){
				output_error('登陆账号已存在');
			}
			$array = array();
			$array['store_id'] = $this->store_id;
            $array['account_name'] = input('account_name', '');
            $array['group_id'] = input('group_id', 0, 'intval');
			$array['last_login_time'] = TIMESTAMP;
			$array['state'] = input('state', 0, 'intval');
			$array['salt'] = fxy_random(8);
			$array['password'] = md5(input('password', '') . $array['salt']);
            $state = $model->insert($array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => _url('account/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$model_group = model('account_group_store');
			$group_array = $model_group->getList(array('store_id' => $this->store_id));
			$this->assign('group_list', $group_array['list']);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model = model('account_store');
		if(chksubmit()){
			if(!input('group_id', 0, 'intval')){
				output_error('请选择用户组');
			}
			if($this->_is_seller_name_exist(input('account_name', ''))){
				output_error('登陆账号已存在');
			}
			if($this->_is_account_name_exist(input('account_name', ''))){
				output_error('登陆账号已存在');
			}
			$id = input('id', 0, 'intval');
			$info = $model->where(array('account_id' => $id))->find();
			$array = array();
            $array['group_id'] = input('group_id', 0, 'intval');
			$array['state'] = input('state', 0, 'intval');
			if(input('password', '')){
				$array['password'] = md5(input('password', '') . $info['salt']);
			}
            $state = $model->where(array('account_id' => $id))->update($array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => _url('account/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$id = input('id', 0, 'intval');
			$info = $model->getInfo(array('account_id' => $id));
			$this->assign('info', $info);
			$model_group = model('account_group_store');
			$group_array = $model_group->getList(array('store_id' => $this->store_id));
			$this->assign('group_list', $group_array['list']);
			
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model = model('account_store');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['account_id'] = $id_array;
		$where['store_id'] = $this->store_id;
		$state = $model->where($where)->delete();
        if ($state) {
            output_data(array('url' => _url('account/index')));
        } else {
			output_error('删除失败！');
        }
    }
    private function _is_seller_name_exist($username)
    {
        $condition = array();
        $condition['name'] = $username;
        $model = model('seller');
        return $model->getInfo($condition);
    }
	private function _is_account_name_exist($account_name)
    {
        $condition = array();
		$condition['store_id'] = $this->store_id;
        $condition['account_name'] = $account_name;
        $model = model('account_store');
        return $model->getInfo($condition);
    }
}