<?php
namespace userscenter\controller;
class swiper extends control {
	private $module_list = array();
	public function __construct() {
		parent::_initialize();
		$this->module_list = array(
			'index' => '首页'
		);
		$this->assign('module_list', $this->module_list);
	}
	public function indexOp(){
		$where = array();
        $where['store_id'] = 0;
		$module = input('module', '');
        if ($module) {
            $where['module'] = $module;
        }
		$list_temp = model('swiper')->getList($where);
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp(){
		$model = model('swiper');
		if(chksubmit()){
			$array = array();
			$array['store_id'] = 0;
            $array['image'] = input('post.image', '');
            $array['href'] = input('post.href', '');
			$array['module'] = input('post.module', '');
			$array['title'] = input('post.title', '');
            $array['sort'] = input('post.sort', 9999, 'intval');
            $state = $model->insert($array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('swiper/index')));
            } else {
				output_error('添加失败！');
            }
		}else{
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp(){
		$model = model('swiper');
		if(chksubmit()){
			$array = array();
            $array['image'] = input('post.image', '');
            $array['href'] = input('post.href', '');
			$array['module'] = input('post.module', '');
			$array['title'] = input('post.title', '');
            $array['sort'] = input('post.sort', 9999, 'intval');
            $state = $model->where(array('id' => input('post.id', 0, 'intval')))->update($array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('swiper/index')));
            } else {
				output_error('编辑失败！');
            }
		}else{
			$id = input('get.id', 0, 'intval');
			$info = $model->where(array('id' => $id))->find();
			$this->assign('info', $info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model = model('swiper');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['store_id'] = 0;
		$state = $model->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('swiper/index')));
        } else {
			output_error('删除失败！');
        }
    }
}
?>