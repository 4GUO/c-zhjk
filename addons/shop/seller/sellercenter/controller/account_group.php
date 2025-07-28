<?php
/**
 * 账号组管理
 **/
namespace sellercenter\controller;
defined('SAFE_CONST') or exit('Access Invalid!');
class account_group extends control
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function indexOp()
    {
        $model_account_group = model('account_group_store');
        $list = $model_account_group->getList(array('store_id' => $this->store_id));
        $this->assign('list', $list['list']);
        $this->display();
    }
    public function publishOp()
    {
		$model_account_group = model('account_group_store');
		$group_id = input('id', 0, 'intval');
		if(IS_API){
			if(!input('group_name', '')){
				output_error('请填写组名称');
			}
			if(!input('limits', '')){
				output_error('请选择权限');
			}
			$info = array();
			$info['group_name'] = input('group_name', '');
			$info['limits'] = implode(',', input('limits', ''));
			$info['store_id'] = $this->store_id;
			if (empty($group_id)) {
				$result = $model_account_group->add($info);
				$this->log('添加组成功，组编号' . $result);
				output_data(array('msg' => '操作成功', 'url' => _url('account_group/index')));
			} else {
				$condition = array();
				$condition['group_id'] = $group_id;
				$condition['store_id'] = $this->store_id;
				$model_account_group->edit($condition, $info);
				$this->log('编辑组成功，组编号' . $group_id);
				output_data(array('msg' => '操作成功', 'url' => _url('account_group/index')));
			}
		}else{
			$group_info = $model_account_group->getInfo(array('group_id' => $group_id));
			$this->assign('info', $group_info);
			$this->assign('limits', !empty($group_info['limits']) ? explode(',', $group_info['limits']) : array());
			$this->display();
		}
    }
    public function delOp()
    {
        $group_id = input('id', 0, 'intval');
        if ($group_id > 0) {
            $condition = array();
            $condition['group_id'] = $group_id;
            $condition['store_id'] = $this->store_id;
            $model_seller_group = model('account_group_store');
            $result = $model_seller_group->where($condition)->delete();
            if ($result) {
                $this->log('删除组成功，组编号' . $group_id);
				output_data(array('msg' => '操作成功', 'url' => _url('account_group/index')));
            } else {
                $this->log('删除组失败，组编号' . $group_id);
                output_data(array('msg' => '操作成功', 'url' => _url('account_group/index')));
            }
        } else {
            output_data(array('msg' => '参数错误', 'url' => _url('account_group/index')));
        }
    }
}