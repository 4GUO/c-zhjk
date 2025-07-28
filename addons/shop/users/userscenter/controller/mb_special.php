<?php
namespace userscenter\controller;
use lib;
class mb_special extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function special_listOp() {
	    $model_mb_special = model('mb_special');
		$where = array();
		$keyword = input('get.keyword', '');
        if ($keyword) {
            $where['special_desc'] = '%' . trim($keyword) . '%';
        }
        $list = $model_mb_special->getList($where, '*', 'special_id DESC', 10, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'keyword' => $keyword), users_url('mb_special/special_list')));
		$this->assign('list', $list['list']);
		$this->display();
	}
	public function special_saveOp() {
	    $model_mb_special = model('mb_special');
		if (chksubmit()) {
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('special_desc', ''), 'require' => 'true', 'message' => '标题不能为空'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$get_special_id = input('id', 0, 'intval');
			$common_array = array();
			$common_array['special_desc'] = input('special_desc', '');
			if ($get_special_id) {
				$special_id = $get_special_id;
				$model_mb_special->where(array('special_id' => $special_id))->update($common_array);
			} else {
				$special_id = $model_mb_special->insert($common_array); // 保存数据
			}
			if ($special_id) {
				output_data(array('msg' => '操作成功', 'url' => users_url('mb_special/special_list')));
			} else {
				output_error('操作失败！');
			}
		} else {
			$special_id = input('id', 0, 'intval');
			if($special_id){
				$info = $model_mb_special->where(array('special_id' => $special_id))->find();
				$this->assign('info', $info);
			}
			$this->display();
		}
	}
	public function special_editOp() {
		$model_mb_special = model('mb_special');
		$special_id = input('id', 0, 'intval') ?: input('special_id', 0, 'intval');
        $special_item_list = $model_mb_special->getMbSpecialItemListByID($special_id);
        $this->assign('list', $special_item_list);
        $this->assign('module_list', $model_mb_special->getMbSpecialModuleList());
        $this->assign('special_id', $special_id);
		$this->display('index_edit');
	}
	/**
     * 删除专题
     */
    public function special_delOp() {
        $model_mb_special = model('mb_special');
        $special_id = input('id', 0, 'intval');
        $result = $model_mb_special->delMbSpecialByID($special_id);
        if ($result) {
            $this->log('删除手机专题' . '[ID:' . $special_id . ']', 1);
            output_data(array('msg' => '操作成功', 'url' => users_url('mb_special/special_list')));
        } else {
            $this->log('删除手机专题' . '[ID:' . $special_id . ']', 0);
            output_error('操作失败！');
        }
    }
	public function index_editOp() {
		$model_mb_special = model('mb_special');
        $special_item_list = $model_mb_special->getMbSpecialItemListByID($model_mb_special::INDEX_SPECIAL_ID);
        $this->assign('list', $special_item_list);
        $this->assign('module_list', $model_mb_special->getMbSpecialModuleList());
        $this->assign('special_id', $model_mb_special::INDEX_SPECIAL_ID);
		$this->display();
	}
	/**
     * 专题项目添加
     */
    public function special_item_addOp() {
        $model_mb_special = model('mb_special');
        $param = array();
        $param['special_id'] = input('special_id', 0, 'intval');
        $param['item_type'] = input('item_type', '');
        //广告只能添加一个
        if ($param['item_type'] == 'adv_list') {
            $result = $model_mb_special->isMbSpecialItemExist($param);
            if ($result) {
                echo json_encode(array('error' => '广告条板块只能添加一个'));
                die;
            }
        }
        //推荐只能添加一个
        if ($param['item_type'] == 'goods1') {
            $result = $model_mb_special->isMbSpecialItemExist($param);
            if ($result) {
                echo json_encode(array('error' => '限时板块只能添加一个'));
                die;
            }
        }
        //团购只能添加一个
        if ($param['item_type'] == 'goods2') {
            $result = $model_mb_special->isMbSpecialItemExist($param);
            if ($result) {
                echo json_encode(array('error' => '团购板块只能添加一个'));
                die;
            }
        }
        //end
        $item_info = $model_mb_special->addMbSpecialItem($param);
        if ($item_info) {
            echo json_encode($item_info);
            die;
        } else {
            echo json_encode(array('error' => '添加失败'));
            die;
        }
    }
	/**
     * 专题项目删除
     */
    public function special_item_delOp() {
        $model_mb_special = model('mb_special');
        $condition = array();
        $condition['item_id'] = input('item_id', 0, 'intval');
		$special_id = input('special_id', 0, 'intval');
        $result = $model_mb_special->delMbSpecialItem($condition, $special_id);
        if ($result) {
            echo json_encode(array('message' => '删除成功'));
            die;
        } else {
            echo json_encode(array('error' => '删除失败'));
            die;
        }
    }
    /**
     * 专题项目编辑
     */
    public function special_item_editOp() {
        $model_mb_special = model('mb_special');
        $theitemid = input('item_id', 0, 'intval');
        $item_info = $model_mb_special->getMbSpecialItemInfoByID($theitemid);
        $this->assign('item_info', $item_info);
        $this->display();
    }
    /**
     * 专题项目保存
     */
    public function special_item_saveOp() {
        $model_mb_special = model('mb_special');
		$item_data = input('item_data/a', array());	
		if (empty($item_data)) {
			web_error('内容不能为空');
		}
		$item_id = input('item_id', 0, 'intval');
		$special_id = input('special_id', 0, 'intval');
        $result = $model_mb_special->editMbSpecialItemByID(array('item_data' => $item_data), $item_id, $special_id);
        if ($result) {
            if ($special_id == $model_mb_special::INDEX_SPECIAL_ID) {
                web_success('保存成功', _url('mb_special/index_edit'));
            } else {
                web_success('保存成功', _url('mb_special/special_edit', array('special_id' => $special_id)));
            }
        } else {
            web_success('保存成功', '');
        }
    }
	/**
     * 更新项目排序
     */
    public function update_item_sortOp() {
        $item_id_string =input('item_id_string', '');
        $special_id = input('special_id', 0, 'intval');
        if (!empty($item_id_string)) {
            $model_mb_special = model('mb_special');
            $item_id_array = explode(',', $item_id_string);
            $index = 0;
            foreach ($item_id_array as $item_id) {
                $result = $model_mb_special->editMbSpecialItemByID(array('item_sort' => $index), $item_id, $special_id);
                $index++;
            }
        }
        $data = array();
        $data['message'] = '操作成功';
        echo json_encode($data);
    }
    /**
     * 更新项目启用状态
     */
    public function update_item_usableOp() {
        $model_mb_special = model('mb_special');
		$usable = input('usable', '');
		$item_id = input('item_id', 0, 'intval');
		$special_id = input('special_id', 0, 'intval');
        $result = $model_mb_special->editMbSpecialItemUsableByID($usable, $item_id, $special_id);
        $data = array();
        if ($result) {
            $data['message'] = '操作成功';
        } else {
            $data['error'] = '操作失败';
        }
        echo json_encode($data);
    }
	/**
     * 商品列表
     */
    public function goods_listOp() {
		$model_goods_common = model('shop_goods_common');
		$model_goods = model('shop_goods');
        $keyw = input('keyword', '');
        $condition = array();
		$condition['goods_state'] = 1;
		if ($keyw) {
			$condition['goods_name'] = '%' . $keyw . '%';
		}
		
		$result = $model_goods_common->getList($condition, 'goods_commonid,goods_image,goods_name', 'goods_commonid DESC', 20, input('page', 1, 'intval'));
		$goods_common_list = array();
		foreach ($result['list'] as $k => $v) {
			$goods_common_list[$v['goods_commonid']] = $v;
		}
		$totalpage = $result['totalpage'];
		$hasmore = $result['hasmore'];
		unset($result);
		$result = $model_goods->getList(array('goods_commonid' => array_keys($goods_common_list)), 'goods_id,goods_commonid,goods_marketprice,goods_price', 'goods_id asc');
		$goods_list = array();
		foreach ($result['list'] as $k => $v) {
			if (isset($goods_list[$v['goods_commonid']])) {
				continue;
			}
			$common_info = $goods_common_list[$v['goods_commonid']];
			$goods_list[$v['goods_commonid']] = array_merge($common_info, $v);
		}
		unset($result);
		$this->assign('page', page($totalpage, array('page' => input('page', 1, 'intval'), 'keyword' => $keyw), users_url('mb_special/goods_list')));
		$this->assign('goods_list', $goods_list);
		$this->view->_layout_file = 'null_layout';
		$this->display();
    }
}