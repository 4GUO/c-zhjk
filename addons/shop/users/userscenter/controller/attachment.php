<?php
namespace userscenter\controller;
class attachment extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		$list_temp = model('album_class')->getList(array('store_id' => 0));
		$this->assign('list', $list_temp['list']);
		$this->display();
	}
	public function addOp() {
		$model_class = model('album_class');
		if (chksubmit()) {
			$class_array = array();
            $class_array['aclass_name'] = input('post.aclass_name', '');
            $class_array['store_id'] = 0;
            $class_array['aclass_sort'] = input('post.aclass_sort', 9999, 'intval');
            $state = $model_class->insert($class_array);
            if ($state) {
				output_data(array('msg' => '添加成功', 'url' => users_url('attachment/index')));
            } else {
				output_error('添加失败！');
            }
		} else {
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function editOp() {
		$model_class = model('album_class');
		if (chksubmit()) {
			$class_array = array();
            $class_array['aclass_name'] = input('post.aclass_name', '');
            $class_array['aclass_sort'] = input('post.aclass_sort', 9999, 'intval');
			
            $state = $model_class->where(array('aclass_id' => input('post.id', 0, 'intval')))->update($class_array);
            if ($state) {
                output_data(array('msg' => '编辑成功', 'url' => users_url('attachment/index')));
            } else {
				output_error('编辑失败！');
            }
		} else {
			$aclass_id = input('get.id', 0, 'intval');
			$class_info = model('album_class')->where(array('aclass_id' => $aclass_id))->find();
			$this->assign('class_info', $class_info);
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('album_class');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['aclass_id'] = $id_array;
		$where['store_id'] = 0;
		
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('attachment/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function configOp(){
		if(chksubmit()){
			$data = array(
			    'attachment_open' => input('attachment_open', 0, 'intval'),
				'attachment_host_type' => input('attachment_host_type', 2, 'intval'),
				'alioss' => serialize(input('alioss', '')),
				'qiniu' => serialize(input('qiniu', '')),
				'cos' => serialize(input('cos', '')),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('attachment/config')));
		}
		$result = model('config')->getInfo(array('uniacid' => $this->uniacid));
		$this->assign('alioss', fxy_unserialize($result['alioss']));
		$this->assign('qiniu', fxy_unserialize($result['qiniu']));
		$this->assign('cos', fxy_unserialize($result['cos']));
		$this->display();
	}
}