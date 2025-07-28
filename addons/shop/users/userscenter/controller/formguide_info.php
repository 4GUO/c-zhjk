<?php
/**
 * 自定义表单
 **/
namespace userscenter\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class formguide_info extends control {
    public function __construct() {
        parent::_initialize();
    }
    public function indexOp() {
        $modelid = input('modelid', 0, 'intval');
        $tableName  = model('cms_model_field')->getModelTableName($modelid);
        $model = model()->table($tableName);
        $result = model('cms_model_field')->where(array('modelid' => $modelid, 'status' => 1))->order('listorder DESC')->select();
        $fieldList = array();
        foreach ($result as $k => $value) {
            $value['setting'] = fxy_unserialize($value['setting']);
            $value['options'] = $value['setting']['options'] ?? '';
            if ($value['options'] != '') {
                $value['options'] = model('cms_model')->parse_attr($value['options']);
            }
            $fieldList[] = $value;
        }
        unset($result);
        $this->assign('fieldList', $fieldList);
        //搜索
        $where = $url_arr = array();
        $get = input('get.', array());
        foreach ($fieldList as $v) {
            foreach ($get as $kk => $vv) {
                if ($v['name'] == $kk) {
                    if ($vv !== '') {
                        if ($v['type'] == 'number' || ($v['type'] == 'select' && $vv != -1)) {
                            $where[$v['name']] = $vv;
                        }
                        if ($v['type'] == 'text') {
                            $where[$v['name']] = '%' . $vv . '%';
                        }
                        $url_arr[$v['name']] = $vv;
                    }
                }
            }
        }
        $query_start_date = input('query_start_date', '');
		$query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date. '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date. '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['inputtime >='] = $start_unixtime;
            $url_arr['query_start_date'] = $query_start_date;
        }
		if ($end_unixtime > 0) {
			$where['inputtime <='] = $end_unixtime;
			$url_arr['query_end_date'] = $query_end_date;
        }
        $status = input('status', -1);
		if ($status != -1) {
			$where['status'] = $status;
			$url_arr['status'] = $status;
		}
		$page = 10;
		$get_p = input('page', 1, 'intval');
        $total = $model->where($where)->total();
		$totalpage = ceil($total / $page);//总计页数
		$limitpage = ($get_p - 1) * $page;//每次查询取记录
		$list = $model->where($where)->order('id DESC')->limit($limitpage, $page)->select();
        $page_url = array('page' => input('page', 1, 'intval'), 'modelid' => $modelid);
        $page_url = array_merge($url_arr, $page_url);
        $this->assign('page', page($totalpage, $page_url, _url('formguide_info/index')));
		$this->assign('list', $list);
		
		//组装搜索项
		$fieldArr = model('cms_model')->getFieldList($modelid);
        $this->assign('fieldArr', $fieldArr);
		$this->display();
    }
    public function publishOp() {
        $modelid = input('modelid', 0, 'intval');
        if (chksubmit()) {
            $id = input('id', 0, 'intval');
            $data = array(
                'remark' => input('remark', '', 'trim'),
                'status' => input('status', 1, 'intval'),
            );
            try {
                $tableName  = $tableName  = model('cms_model_field')->getModelTableName($modelid);
                $model = model()->table($tableName);
                $model->where(array('id' => $id))->update($data);
            } catch (\Exception $e) {
                output_error($e->getMessage());
            }
            if ($id) {
				output_data(array('msg' => '操作成功', 'url' => _url('formguide_info/index', array('modelid' => $modelid))));
			} else {
				output_error('操作失败！');
			}
        } else {
            $id = input('id', 0, 'intval');
			if ($id) {
                $tableName = model('cms_model_field')->getModelTableName($modelid);
                $model = model()->table($tableName);
                
				$info = $model->where(array('id' => $id))->find();
				$this->assign('info', $info);
			}
            $this->view->_layout_file = 'null_layout';
			$this->display();
        }
    }
    public function delOp() {
        $modelid = input('modelid', 0, 'intval');
        if (!$modelid) {
            output_error('缺少参数');
        }
        $tableName  = model('cms_model_field')->getModelTableName($modelid);
        $model = model()->table($tableName);

		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$state = $model->where($where)->delete();
        if ($state) {
            output_data(array('url' => _url('formguide_info/index', array('modelid' => $modelid))));
        } else {
			output_error('删除失败！');
        }
    }
}