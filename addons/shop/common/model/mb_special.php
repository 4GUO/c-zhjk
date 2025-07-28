<?php
namespace model;
use base;
defined('SAFE_CONST') or exit('Access Invalid!');
class mb_special extends base\model
{
    protected $tableName = 'mb_special';
	//专题项目不可用状态
    const SPECIAL_ITEM_UNUSABLE = 0;
    //专题项目可用状态
    const SPECIAL_ITEM_USABLE = 1;
	//首页特殊专题编号
    const INDEX_SPECIAL_ID = 0;
    public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
		if ($page && $get_p) {
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		} else {
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
	/*
     * 删除专题
     * @param int $special_id
     * @return bool
     *
     */
    public function delMbSpecialByID($special_id)
    {
        $special_id = intval($special_id);
        if ($special_id <= 0) {
            return false;
        }
        $condition = array();
        $condition['special_id'] = $special_id;
        $this->delMbSpecialItem($condition, $special_id);
        return $this->table('mb_special')->where($condition)->delete();
    }
    /**
     * 专题项目列表（用于后台编辑显示所有项目）
     * @param int $special_id
     *
     */
    public function getMbSpecialItemListByID($special_id)
    {
        $condition = array();
        $condition['special_id'] = $special_id;
        return $this->_getMbSpecialItemList($condition);
    }
	/**
     * 获取专题模块类型列表
     * @return array
     *
     */
    public function getMbSpecialModuleList()
    {
        $module_list = array();
        $module_list['adv_list'] = array('name' => 'adv_list', 'desc' => '广告条版块');
        $module_list['home1'] = array('name' => 'home1', 'desc' => '模型版块布局A');
        $module_list['home2'] = array('name' => 'home2', 'desc' => '模型版块布局B');
        $module_list['home3'] = array('name' => 'home3', 'desc' => '模型版块布局C');
        $module_list['home4'] = array('name' => 'home4', 'desc' => '模型版块布局D');
        $module_list['goods'] = array('name' => 'goods', 'desc' => '商品版块');
        if (empty($_GET['special_id'])) {
            //$module_list['goods1'] = array('name' => 'goods1', 'desc' => '限时商品');
            //$module_list['goods2'] = array('name' => 'goods2', 'desc' => '团购商品');
        }
        return $module_list;
    }
	/**
     * 检查专题项目是否存在
     * @param array $condition
     *
     */
    public function isMbSpecialItemExist($condition)
    {
        $item_list = $this->table('mb_special_item')->where($condition)->select();
        if ($item_list) {
            return true;
        } else {
            return false;
        }
    }
	/*
     * 增加专题项目
     * @param array $param
     * @return array $item_info
     *
     */
    public function addMbSpecialItem($param)
    {
        $param['item_usable'] = self::SPECIAL_ITEM_UNUSABLE;
        $param['item_sort'] = 255;
        $result = $this->table('mb_special_item')->insert($param);
        //删除缓存
        if ($result) {
            //删除缓存
            $this->_delMbSpecialHtml($param['special_id']);
            $param['item_id'] = $result;
            return $param;
        } else {
            return false;
        }
    }
	/*
     * 删除
     * @param array $condition
     * @return bool
     *
     */
    public function delMbSpecialItem($condition, $special_id)
    {
        //删除缓存
        $this->_delMbSpecialHtml($special_id);
        return $this->table('mb_special_item')->where($condition)->delete();
    }
	/**
     * 获取项目详细信息
     * @param int $item_id
     *
     */
    public function getMbSpecialItemInfoByID($item_id)
    {
        $item_id = intval($item_id);
        if ($item_id <= 0) {
            return false;
        }
        $condition = array();
        $condition['item_id'] = $item_id;
        $item_info = $this->table('mb_special_item')->where($condition)->find();
        $item_info['item_data'] = $this->_initMbSpecialItemData($item_info['item_data'], $item_info['item_type']);
        return $item_info;
    }
	 /**
     * 编辑专题项目
     * @param array $update
     * @param int $item_id
     * @param int $special_id
     * @return bool
     *
     */
    public function editMbSpecialItemByID($update, $item_id, $special_id)
    {
        if (isset($update['item_data'])) {
            $update['item_data'] = serialize($update['item_data']);
        }
        $condition = array();
        $condition['item_id'] = $item_id;
        //删除缓存
        $this->_delMbSpecialHtml($special_id);
        return $this->table('mb_special_item')->where($condition)->update($update);
    }
	/**
     * 获取专题URL地址
     * @param int $special_id
     *
     */
    public function getMbSpecialHtmlUrl($special_id)
    {
        return DATA_URL . '/special_html/' . md5('special' . $special_id) . '.html';
    }
	/**
     * 获取专题静态文件路径
     * @param int $special_id
     *
     */
    public function getMbSpecialHtmlPath($special_id)
    {
        return DATA_PATH . '/special_html/' . md5('special' . $special_id) . '.html';
    }
	/**
     * 编辑专题项目启用状态
     * @param string usable-启用/unsable-不启用
     * @param int $item_id
     * @param int $special_id
     *
     */
    public function editMbSpecialItemUsableByID($usable, $item_id, $special_id)
    {
        $update = array();
        if ($usable == 'usable') {
            $update['item_usable'] = self::SPECIAL_ITEM_USABLE;
        } else {
            $update['item_usable'] = self::SPECIAL_ITEM_UNUSABLE;
        }
        return $this->editMbSpecialItemByID($update, $item_id, $special_id);
    }
	/**
     * 首页专题
     */
    public function getMbSpecialIndex($level_id = 0)
    {
        return $this->getMbSpecialItemUsableListByID(self::INDEX_SPECIAL_ID, $level_id);
    }
	/**
     * 专题可用项目列表（用于前台显示仅显示可用项目）
     * @param int $special_id
     *
     */
    public function getMbSpecialItemUsableListByID($special_id, $level_id = 0)
    {
        $condition = array();
        $condition['special_id'] = $special_id;
        $condition['item_usable'] = self::SPECIAL_ITEM_USABLE;
        $item_list = $this->_getMbSpecialItemList($condition);
        if (!empty($item_list)) {
            $new_item_list = array();
            foreach ($item_list as $value) {
				if ($value['item_type'] == 'adv_list' && !empty($value['item_data']['item'])) {
					foreach($value['item_data']['item'] as $k => $v) {
						$value['item_data']['item'][$k]['image'] = tomedia($v['image']);
						if ($v['type'] == 'url') {
							$value['item_data']['item'][$k]['url'] = $v['data'];
						}
						unset($value['item_data']['item'][$k]['type'], $value['item_data']['item'][$k]['data']);
					}
					$value['item_data']['item'] = array_values($value['item_data']['item']);
				}
				if ($value['item_type'] == 'home1') {
					$value['item_data']['image'] = tomedia($value['item_data']['image']);
					if ($value['item_data']['type'] == 'url') {
						$value['item_data']['url'] = $value['item_data']['data'];
					}
					unset($value['item_data']['type'], $value['item_data']['data']);
					$value['item_data']['model_type'] = 1;
				}
				if ($value['item_type'] == 'home2') {
					$value['item_data']['rectangle1_image'] = tomedia($value['item_data']['rectangle1_image']);
					if ($value['item_data']['rectangle1_type'] == 'url') {
						$value['item_data']['rectangle1_url'] = $value['item_data']['rectangle1_data'];
					}
					unset($value['item_data']['rectangle1_type'], $value['item_data']['rectangle1_data']);
					$value['item_data']['rectangle2_image'] = tomedia($value['item_data']['rectangle2_image']);
					if ($value['item_data']['rectangle2_type'] == 'url') {
						$value['item_data']['rectangle2_url'] = $value['item_data']['rectangle2_data'];
					}
					unset($value['item_data']['rectangle2_type'], $value['item_data']['rectangle2_data']);
					$value['item_data']['square_image'] = tomedia($value['item_data']['square_image']);
					if ($value['item_data']['square_type'] == 'url') {
						$value['item_data']['square_url'] = $value['item_data']['square_data'];
					}
					unset($value['item_data']['square_type'], $value['item_data']['square_data']);
					$value['item_data']['model_type'] = 2;
				}
				if ($value['item_type'] == 'home3' && !empty($value['item_data']['item'])) {
					foreach($value['item_data']['item'] as $k => $v) {
						$value['item_data']['item'][$k]['image'] = tomedia($v['image']);
						if ($v['type'] == 'url') {
							$value['item_data']['item'][$k]['url'] = $v['data'];
						}
						unset($value['item_data']['item'][$k]['type'], $value['item_data']['item'][$k]['data']);
					}
					$value['item_data']['item'] = array_values($value['item_data']['item']);
					$value['item_data']['model_type'] = 3;
				}
				if ($value['item_type'] == 'home4') {
					$value['item_data']['rectangle1_image'] = tomedia($value['item_data']['rectangle1_image']);
					if ($value['item_data']['rectangle1_type'] == 'url') {
						$value['item_data']['rectangle1_url'] = $value['item_data']['rectangle1_data'];
					}
					unset($value['item_data']['rectangle1_type'], $value['item_data']['rectangle1_data']);
					$value['item_data']['rectangle2_image'] = tomedia($value['item_data']['rectangle2_image']);
					if ($value['item_data']['rectangle2_type'] == 'url') {
						$value['item_data']['rectangle2_url'] = $value['item_data']['rectangle2_data'];
					}
					unset($value['item_data']['rectangle2_type'], $value['item_data']['rectangle2_data']);
					$value['item_data']['square_image'] = tomedia($value['item_data']['square_image']);
					if ($value['item_data']['square_type'] == 'url') {
						$value['item_data']['square_url'] = $value['item_data']['square_data'];
					}
					unset($value['item_data']['square_type'], $value['item_data']['square_data']);
					$value['item_data']['model_type'] = 4;
				}
				if ($value['item_type'] == 'goods' && !empty($value['item_data']['item'])) {
					$goods_ids = array();
				    foreach ($value['item_data']['item'] as $k => $v) {
				        $goods_ids[] = $v['goods_id'];
				    }
					if ($goods_ids) {
				        $rs = model('shop_goods')->field('goods_id,goods_commonid,goods_price_vip')->where(array('goods_id' => $goods_ids))->select();
				        $goods_arr = array();
						$goods_commonids = array();
				        foreach ($rs as $k => $v) {
				            $goods_arr[$v['goods_id']] = $v;
							$goods_commonids[$v['goods_id']] = $v['goods_commonid'];
				        }
				        unset($rs);
				        $rs = model('shop_goods_common')->field('goods_commonid,goods_commission,goods_sort')->where(array('goods_commonid' => array_values($goods_commonids)))->select();
				        $goods_common_arr = array();
				        foreach ($rs as $k => $v) {
				            $goods_common_arr[$v['goods_commonid']] = $v;
				        }
				        unset($rs);
				        $shop_goods_list = array();
				        foreach ($goods_commonids as $k => $v) {
							$goods_common_info = $goods_common_arr[$v];
							$goods_info = $goods_arr[$k];
				            $shop_goods_list[$k] = array_merge($goods_common_info, $goods_info);
				        }
				    }
					foreach($value['item_data']['item'] as $k => $v) {
						$goods_info = isset($shop_goods_list[$v['goods_id']])? $shop_goods_list[$v['goods_id']] : array();
						$v['goods_price_vip'] = isset($goods_info['goods_price_vip']) ? $goods_info['goods_price_vip'] : '';
						$v = logic('shop_goods')->get_goods_price($v, $level_id);
						$value['item_data']['item'][$k]['goods_price'] = $v['goods_price'];
						
						$commission_money = 0;
						$goods_commission = !empty($goods_info['goods_commission'])? fxy_unserialize($goods_info['goods_commission']) : array();
						if (isset($goods_commission[$level_id]) && !empty($goods_commission[$level_id][0])) {
					        $commission_money = $v['goods_price'] * $goods_commission[$level_id][0] * 0.01;
					    }
						$value['item_data']['item'][$k]['commission_money'] = priceFormat($commission_money);
						$value['item_data']['item'][$k]['goods_image'] = tomedia($v['goods_image']);
						$value['item_data']['item'][$k]['goods_sort'] = $goods_info['goods_sort'];
					}
					$value['item_data']['item'] = fxy_array_sort($value['item_data']['item'], 'goods_sort');
					$value['item_data']['model_type'] = 5;
				}
                $new_item_list[] = array($value['item_type'] => $value['item_data']);
            }
            $item_list = $new_item_list;
        }
        return $item_list;
    }
	/**
     * 清理缓存
     */
    private function _delMbSpecialHtml($special_id)
    {
        //删除静态文件
        $html_path = $this->getMbSpecialHtmlPath($special_id);
        if (is_file($html_path)) {
            unlink($html_path);
        }
    }
	/**
     * 查询专题项目列表
     */
    private function _getMbSpecialItemList($condition, $order = 'item_sort asc')
    {
        $item_list = $this->table('mb_special_item')->where($condition)->order($order)->select();
        foreach ($item_list as $key => $value) {
            $item_list[$key]['item_data'] = $this->_initMbSpecialItemData($value['item_data'], $value['item_type']);
            if ($value['item_usable'] == self::SPECIAL_ITEM_USABLE) {
                $item_list[$key]['usable_class'] = 'usable';
                $item_list[$key]['usable_text'] = '禁用';
            } else {
                $item_list[$key]['usable_class'] = 'unusable';
                $item_list[$key]['usable_text'] = '启用';
            }
        }
        return $item_list;
    }
	/**
     * 整理项目内容
     *
     */
    private function _initMbSpecialItemData($item_data, $item_type)
    {
        if (!empty($item_data)) {
            $item_data = fxy_unserialize($item_data);
            if ($item_type == 'goods' || $item_type == 'goods1' || $item_type == 'goods2') {
                $item_data = $this->_initMbSpecialItemGoodsData($item_data, $item_type);
            }
        } else {
            $item_data = $this->_initMbSpecialItemNullData($item_type);
        }
        return $item_data;
    }
	/**
     * 处理goods类型内容
     */
    private function _initMbSpecialItemGoodsData($item_data, $item_type)
    {
        $goods_ids = array();
        if (!empty($item_data['item'])) {
            foreach ($item_data['item'] as $value) {
                $goods_ids[] = $value;
            }
            //查询商品信息
            $condition['goods_id'] = $goods_ids;
            $model_goods = model('shop_goods');
            $result = $model_goods->getList($condition, 'goods_id,goods_commonid,goods_price,goods_image');
			$goods_list = $result['list'];
            $goods_list = array_under_reset($goods_list, 'goods_id');
            //整理商品数据
            $new_goods_list = array();
			$goods_commonids = array();
            foreach ($item_data['item'] as $value) {
                if (!empty($goods_list[$value])) {
                    $new_goods_list[] = $goods_list[$value];
					$goods_commonids[] = $goods_list[$value]['goods_commonid'];
                }
            }
			$result = model('shop_goods_common')->getList(array('goods_commonid' => $goods_commonids), 'goods_commonid,goods_name');
			$goods_common_list = array_under_reset($result['list'], 'goods_commonid');
			foreach ($new_goods_list as $k => $v) {
				$common_info = $goods_common_list[$v['goods_commonid']];
				$v = array_merge($common_info, $v);
				$new_goods_list[$k] = $v;
			}
            $item_data['item'] = $new_goods_list;
        }
        return $item_data;
    }
	/**
     * 初始化空项目内容
     */
    private function _initMbSpecialItemNullData($item_type)
    {
        $item_data = array();
        switch ($item_type) {
            case 'home1':
                $item_data = array('title' => '', 'image' => '', 'type' => '', 'data' => '');
                break;
            case 'home2':
            case 'home4':
                $item_data = array('title' => '', 'square_image' => '', 'square_type' => '', 'square_data' => '', 'rectangle1_image' => '', 'rectangle1_type' => '', 'rectangle1_data' => '', 'rectangle2_image' => '', 'rectangle2_type' => '', 'rectangle2_data' => '');
                break;
            default:
        }
        return $item_data;
    }
}