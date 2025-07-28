<?php
namespace userscenter\controller;
class article_class extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$list_temp = model('article_class')->getList(array('uniacid' => $this->uniacid));
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp(){
		$model_class = model('article_class');
		if(chksubmit()){
			$class_array = array();
            $class_array['ac_name'] = input('post.ac_name', '');
            $class_array['ac_parent_id'] = 0;
            $class_array['ac_state'] = input('post.ac_state', 0, 'intval');
            $class_array['uniacid'] = $this->uniacid;
            $class_array['ac_sort'] = input('post.ac_sort', 9999, 'intval');
            $state = $model_class->insert($class_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('article_class/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model_class = model('article_class');
		if(chksubmit()){
			$class_array = array();
            $class_array['ac_name'] = input('post.ac_name', '');
            $class_array['ac_parent_id'] = 0;
            $class_array['ac_state'] = input('post.ac_state', 0, 'intval');
            $class_array['ac_sort'] = input('post.ac_sort', 9999, 'intval');
			
            $state = $model_class->where(array('ac_id' => input('post.id', 0, 'intval')))->update($class_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('article_class/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$ac_id = input('get.id', 0, 'intval');
			$class_info = model('article_class')->where(array('ac_id' => $ac_id))->find();
			$this->assign('class_info', $class_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('article_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['ac_id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		
		$article_info = model('article')->where($where)->find();
		if(!empty($article_info['id'])){
			output_error('该分类下有文章，不能删除');
		}
		
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('article_class/index')));
        } else {
			output_error('删除失败！');
        }
    }
}