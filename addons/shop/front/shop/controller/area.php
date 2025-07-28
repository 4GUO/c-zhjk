<?php
/**
 * 地区
 *
 */
namespace shop\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class area extends home
{
    public function __construct()
    {
        parent::_initialize();
    }
    public function indexOp()
    {
        $this->area_listOp();
    }
    /**
     * 地区列表
     */
    public function area_listOp()
    {
        $area_id = input('area_id', 0, 'intval');
        $model_area = model('area');
        $condition = array();
        if ($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getList($condition, 'area_id,area_name');
        output_data(array('area_list' => $area_list['list']));
    }
	/**
     * 地区列表
     */
    public function area_list_newOp()
    {
        $area_id = input('area_id', 0, 'intval');
        $model_area = model('area');
        $condition = array();
        if ($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getList($condition, 'area_id,area_name');
		$return = array();
		foreach ($area_list['list'] as $k => $v) {
			$return[] = array(
				'label' => $v['area_name'],
				'value' => $v['area_id'],
			);
		}	
        output_data(array('area_list' => $return));
    }
	public function area_dataOp() {
		$model_area = model('area');
		$result = $model_area->getList(array(), 'area_id,area_parent_id,area_name');
		$list = recursionTree($result['list'], 'area_id', 'area_parent_id');
		$return = array(
			'list' => $list,
		);
		output_data($return);
	}
}