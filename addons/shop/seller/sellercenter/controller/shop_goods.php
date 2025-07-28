<?php
namespace sellercenter\controller;
use lib;
class shop_goods extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_goods_common = model('shop_goods_common');
		$model_goods = model('shop_goods');
		$where = array();
        $where['store_id'] = $this->store_id;
		$goods_state = input('goods_state', 2, 'intval');
		$where['goods_state'] = $goods_state - 1;
		$gc_id = input('gc_id', 0, 'intval');
        if ($gc_id) {
			$gc_ids = array();
			$gc_ids[] = $gc_id;
			$result = model('shop_goods_class')->getList(array('gc_parent_id' => $gc_id, 'gc_state' => 1), 'gc_id');
			foreach($result['list'] as $r){
				$gc_ids[] = $r['gc_id'];
			}
			unset($result);
            $where['gc_id'] = $gc_ids;
        }
		$keyword = input('keyword', '');
		$search_type = input('search_type', 0);
        if ($keyword) {
            switch ($search_type) {
                case 0:
                    $where['goods_name'] = '%' . trim($keyword) . '%';
                    break;
                case 1:
                    $where['goods_serial'] = '%' . trim($keyword) . '%';
                    break;
            }
        }
        $list = $model_goods_common->getList($where, '*', 'goods_commonid DESC', 10, input('page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('page', 1, 'intval'), 'gc_id' => $gc_id, 'search_type' => $search_type, 'keyword' => $keyword, 'goods_state' => $goods_state), _url('shop_goods/index')));
        // 计算库存
        $storage_array = $model_goods->calculateStorage($list['list']);
		$this->assign('storage_array', $storage_array);
        // 商品分类
        $class_list = model('shop_goods_class')->getList(array('gc_state' => 1), '*', 'gc_parent_id asc');
		$gc_list = array();
		$gc_name_arr = array();
		foreach($class_list['list'] as $gc_info){
			if($gc_info['gc_parent_id'] == 0){
				$gc_list[$gc_info['gc_id']] = $gc_info;
				$gc_name_arr[$gc_info['gc_id']] = $gc_info['gc_name'];
			} else {
				$gc_list[$gc_info['gc_parent_id']]['child'][] = $gc_info;
				$gc_name_arr[$gc_info['gc_id']] = $gc_name_arr[$gc_info['gc_parent_id']] . '<br />' . $gc_info['gc_name'];
			}
			
			
		}
		$this->assign('goods_class', $gc_list);
		$this->assign('gc_name_arr', $gc_name_arr);
		$goods_common_list = array();
		foreach ($list['list'] as $k => $v) {
			$goods_common_list[$v['goods_commonid']] = $v;
		}
		$goods_list = array();
		if ($goods_common_list) {
			$result = model('shop_goods')->getList(array('goods_commonid' => array_keys($goods_common_list)), 'goods_id,goods_commonid', 'goods_id asc');
			foreach ($result['list'] as $k => $v) {
				if (isset($goods_list[$v['goods_commonid']])) {
					continue;
				}
				$common_info = $goods_common_list[$v['goods_commonid']];
				$goods_list[$v['goods_commonid']] = array_merge($common_info, $v);
			}
			unset($result);
		}
		$this->assign('list', $goods_list);
		$this->display();
	}
	public function publishOp() {
		$model_goods = model('shop_goods');
		$model_goods_common = model('shop_goods_common');
		if (chksubmit()) {
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('cate_id', 0, 'intval'), 'require' => 'true', 'message' => '请选择分类'),array('input' => input('g_name', ''), 'require' => 'true', 'message' => '产品名称不能为空'), array('input' => input('g_storage', ''), 'require' => 'true', 'message' => '库存不能为空'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$get_common_id = input('commonid', 0, 'intval');
			$goods_class = model('shop_goods_class')->getInfo(array('gc_id' => input('cate_id', 0, 'intval'), 'gc_state' => 1));
			if (!$goods_class) {
				output_error('平台分类不存在或已被禁用');
			}
			$store_class = model('store_goods_class')->getInfo(array('stc_id' => input('stc_id', 0, 'intval'), 'stc_state' => 1));
			$common_array = array();
			$common_array['goods_name'] = input('g_name', '');
			$common_array['goods_jingle'] = input('g_jingle', '');
			$common_array['gc_first_id'] = $goods_class['gc_parent_id'] == 0 ? input('cate_id', 0, 'intval') : $goods_class['gc_parent_id'];
			$common_array['gc_id'] = input('cate_id', 0, 'intval');
			$common_array['gc_name'] = $goods_class['gc_name'];
			$common_array['store_id'] = $this->store_id;
			$common_array['stc_first_id'] = empty($store_class['stc_parent_id']) ? input('stc_id', 0, 'intval') : $store_class['stc_parent_id'];
			$common_array['stc_id'] = input('stc_id', 0, 'intval');
			$common_array['goods_image'] = input('image_path', '');
			if (!empty($this->config['goods_verify'])) {
				$common_array['goods_state'] = 0;
			} else {
				$common_array['goods_state'] = input('g_status', 0, 'intval');
			}
			$common_array['goods_freight'] = input('g_freight', 0) ?: 0;
			if (empty(input('g_marketprice', 0))) {
				output_error('市场价不能为空');
			}
			$common_array['goods_marketprice'] = input('g_marketprice', 0) ?: 0;
			$common_array['goods_discount'] = input('g_discount', 0) ?: 0;
			$common_array['goods_serial'] = input('g_serial', '');
			$common_array['goods_weight'] = input('g_weight', 0) ?: 0;
			
			if (!empty($goods_class['gc_virtual'])) {
				$common_array['is_virtual'] = input('is_gv', 0, 'intval');
				$common_array['virtual_indate'] = input('g_vindate', 0) ? strtotime(input('g_vindate', 0)) : 0;
				$common_array['virtual_limit'] = input('g_vlimit', 0, 'intval') > 10 ? 10 : input('g_vlimit', 0, 'intval');
				$common_array['virtual_invalid_refund'] = input('g_vinvalidrefund', 0, 'intval');
			}
			
			$common_array['is_new'] = input('g_is_new', 0);
			$common_array['goods_commend'] = input('g_commend', 0);
			$common_array['goods_sort'] = input('g_sort', 9999, 'intval');
			$goods_price = 0;
			$price_vip = input('g_price/a', array());
			$price_vip = array_reverse($price_vip, true);
			foreach ($price_vip as $k_p => $v_p) {
				if (empty($v_p) || !is_numeric($v_p)) {
					output_error('请设置有效价格');
				}
				$v_p = priceFormat($v_p);
				$price_vip[$k_p] = $v_p;
				if ($goods_price == 0) {
					$goods_price = $v_p;
				}
			}
			if (empty($price_vip) || $goods_price < 0.01) {
				output_error('请设置有效价格');
			}
			$common_array['goods_price'] = $goods_price;
			//属性规格
			$spec = input('spec/a', array());
			$sp_val = input('sp_val/a', array());
			$sp_name = input('sp_name/a', array());
			$common_array['spec_name'] = !empty($spec) ? serialize($sp_name) : serialize(null);
            $common_array['spec_value'] = !empty($sp_val) ? serialize($sp_val) : serialize(null);
            $common_array['mobile_body'] = input('mobile_body', '');
			$common_array['goods_video'] = input('goods_video', '');
			$common_array['goods_video_poster'] = input('goods_video_poster', '');
			$common_array['buy_xiangou'] = input('buy_xiangou', 0, 'intval');
			if (!empty($spec) && is_array($spec)) {
				$common_array['has_spec'] = 1;
			} else {
				$common_array['has_spec'] = 0;
			}
			try {
				$model = model();
				$model->beginTransaction();
				if ($get_common_id) {
					$common_id = $get_common_id;
					$common_array['goods_edittime'] = TIMESTAMP;
					$model_goods_common->edit(array('store_id' => $this->store_id, 'goods_commonid' => $common_id), $common_array);
				} else {
					$common_array['goods_addtime'] = TIMESTAMP;
					$common_id = $model_goods_common->add($common_array); // 保存数据
				}
			
				//获得分类规格（区分图片）
				$spec_image_colors = array();			
				$spec_info = model('shop_spec')->getInfo(array('gc_id' => ($goods_class['gc_parent_id'] == 0 ? $goods_class['gc_id'] : $goods_class['gc_parent_id']), 'is_image' => 1), 'spec_id');
				if (!empty($spec_info['spec_id'])) {
					$list_temp = model('shop_spec_value')->getList(array('sp_id' => $spec_info['spec_id']));
					if (!empty($list_temp['list'])) {
						foreach($list_temp['list'] as $r) {
							$spec_image_colors[] = $r['sp_value_id'];
						}
					}
					unset($list_temp);
				}			
				unset($spec_info);
				
				if ($common_id) {
					$goods = array();
					$goodsid_array = array();
					$colorid_array = array();
					
					if (!empty($spec) && is_array($spec)) {
						foreach ($spec as $value) {
							if(empty($spec_image_colors)){
								$color_id = 0;
							} else {
								$color_ids = array_intersect($spec_image_colors, array_keys($value['sp_value']));
								$color_ids = array_values($color_ids);
								if(empty($color_ids)){
									$color_id = 0;
								} else {
									$color_id = $color_ids[0];
								}
								
								unset($color_ids);
							}
							$goods = array();
							$goods['goods_commonid'] = $common_id;
							$goods['goods_name'] = $common_array['goods_name'] . ' ' . implode(' ', $value['sp_value']);
							$goods_price = 0;
							$price_vip = empty($value['price']) ? array() : $value['price'];
							$price_vip = array_reverse($price_vip, true);
							foreach($price_vip as $k_p=>$v_p) {
								if (empty($v_p) || !is_numeric($v_p)) {
									throw new \Exception('请设置有效价格');
								}
								$v_p = priceFormat($v_p);
								$price_vip[$k_p] = $v_p;
								if($goods_price == 0){
									$goods_price = $v_p;
								}
							}
							if (empty($price_vip) || $goods_price < 0.01) {
								throw new \Exception('请设置有效价格');
							}
								
							$goods['goods_price'] = $goods_price;
							$goods['goods_price_vip'] = serialize(array_reverse($price_vip, true));
							unset($goods_price);
							unset($price_vip);
							$goods['goods_marketprice'] = empty($value['marketprice']) ? $common_array['goods_marketprice'] : $value['marketprice'];
							$goods['goods_serial'] = $value['sku'];
							$goods['goods_storage'] = isset($value['stock']) ? $value['stock'] : 0;
							$goods['goods_salenum'] = isset($value['salenum']) ? $value['salenum'] : 0;
							$goods['goods_spec'] = serialize($value['sp_value']);					
							$goods['goods_image'] = $common_array['goods_image'];
							$goods['color_id'] = $color_id;
							$value['goods_id'] = empty($value['goods_id']) ? 0 : intval($value['goods_id']);
							$goods_info = $model_goods->getInfo(array('goods_commonid' => $common_id, 'goods_id' => $value['goods_id']), 'goods_id');
							if (!empty($goods_info)) {
								$goods_id = $goods_info['goods_id'];
								$model_goods->edit(array('goods_id' => $goods_id), $goods);
							} else {
								$goods['goods_addtime'] = TIMESTAMP;
								$goods_id = $model_goods->add($goods);	
							}					
							$goodsid_array[] = $goods_id;
							$colorid_array[] = $color_id;
						}
					} else {
						$goods['goods_commonid'] = $common_id;
						$goods['goods_name'] = $common_array['goods_name'];
						$goods_price = 0;
						$price_vip = input('g_price/a', array());
						$price_vip = array_reverse($price_vip, true);
						foreach ($price_vip as $k_p => $v_p) {
							if (empty($v_p) || !is_numeric($v_p)) {
								throw new \Exception('请设置有效价格');
							}
							$v_p = priceFormat($v_p);
							$price_vip[$k_p] = $v_p;
							if ($goods_price == 0) {
								$goods_price = $v_p;
							}
						}
						if (empty($price_vip) || $goods_price < 0.01) {
							throw new \Exception('请设置有效价格');
						}
						$goods['goods_price'] = $goods_price;
						$goods['goods_price_vip'] = serialize(array_reverse($price_vip, true));
						$goods['goods_marketprice'] = $common_array['goods_marketprice'];
						$goods['goods_serial'] = $common_array['goods_serial'];
						$goods['goods_storage'] = input('g_storage', 0, 'intval');
						$goods['goods_salenum'] = input('g_salenum', 0, 'intval');
						$goods['goods_image'] = $common_array['goods_image'];
						$goods['goods_spec'] = '';
						$goods['color_id'] = 0;
						if ($get_common_id) {
							$goods_info = $model_goods->getInfo(array('goods_commonid' => $get_common_id), 'goods_id');
							$goods_id = $goods_info['goods_id'];
							$model_goods->edit(array('goods_id' => $goods_id), $goods);	
						} else {
							$goods_id = $model_goods->add($goods);	
						}
						
						$goodsid_array[] = $goods_id;
						$colorid_array[] = 0;
					}
					
					//清除无用的商品
					if ($get_common_id) {				
						$model_goods->del('goods_id NOT IN(' . implode(',', $goodsid_array) . ') AND goods_commonid=' . $common_id);
						$colorid_array = array_unique($colorid_array);
						model('shop_goods_images')->del('goods_commonid=' . $common_id . ' AND color_id NOT IN(' . implode(',', $colorid_array) . ')');
					}
					
					if ($goods_id) {
						if ($get_common_id) {
							$url = _url('shop_goods/index');
						} else {
							$url = _url('shop_goods/edit_images', array('commonid' => $common_id, 'type' => 'add'));
						}
						$model->commit();
						output_data(array('msg' => '操作成功', 'url' => $url));
					} else {
						throw new \Exception('操作失败！');
					}
				}
			} catch (\Exception $e) {
				$model->rollback();
				output_error($e->getMessage());
			}
		} else {
			//分类
			$class_list = model('shop_goods_class')->getList(array('gc_state' => 1), '*', 'gc_parent_id asc');
			$gc_list = array();
			foreach($class_list['list'] as $gc_info) {
				if ($gc_info['gc_parent_id'] == 0) {
					$gc_list[$gc_info['gc_id']] = $gc_info;
				} else {
					$gc_list[$gc_info['gc_parent_id']]['child'][] = $gc_info;
				}
			}
			$this->assign('class_list', $gc_list);
			//本店分类
			$result = model('store_goods_class')->getList(array('stc_state' => 1, 'store_id' => $this->store_id), '*', 'stc_parent_id asc');
			$store_class_list = array();
			foreach($result['list'] as $gc_info) {
				if ($gc_info['stc_parent_id'] == 0) {
					$store_class_list[$gc_info['stc_id']] = $gc_info;
				} else {
					$store_class_list[$gc_info['stc_parent_id']]['child'][] = $gc_info;
				}
			}
			unset($result);
			$this->assign('store_class_list', $store_class_list);
			$common_id = input('commonid', 0, 'intval');
			if ($common_id) {
				$goodscommon_info = $model_goods_common->getInfoByID($common_id);
				$where = array('goods_commonid' => $common_id);
				$goodscommon_info['g_storage'] = $model_goods->getGoodsSum($where, 'goods_storage');
				$goodscommon_info['g_salenum'] = $model_goods->getGoodsSum($where, 'goods_salenum');
				$spec_name = fxy_unserialize($goodscommon_info['spec_name']);
				if ($spec_name) {
					//带属性
					$goods_info = model('shop_goods')->getInfo(array('goods_commonid' => $common_id), 'goods_price_vip', 'goods_price ASC');
					$goodscommon_info['goods_price_vip'] = $goods_info['goods_price_vip'] ? fxy_unserialize($goods_info['goods_price_vip']) : array();
				} else {
					//不带属性
					$goods_info = model('shop_goods')->getInfo(array('goods_commonid' => $common_id));
					$goodscommon_info['goods_price_vip'] = $goods_info['goods_price_vip'] ? fxy_unserialize($goods_info['goods_price_vip']) : array();
				}
				$this->assign('goods', $goodscommon_info);
				$edit_goods_sign = true;
				$this->assign('goods_freight', $goodscommon_info['goods_freight']);
			} else {
				$edit_goods_sign = false;
				$this->assign('goods_freight', $this->store_info['freight_in']);
			}
			
			$this->assign('config', $this->config);
			$this->assign('edit_goods_sign', $edit_goods_sign);
			
			//会员级别
			$level_ids = array();
			$level_temp = model('vip_level')->getList(array(), '*', 'level_sort DESC');
			foreach($level_temp['list'] as $value){
				$level_ids[] = $value['id'];
			}
			$this->assign('level_ids', $level_ids);
			$this->assign('member_levels', $level_temp['list']);
			//视频
			$upload_ret = input('upload_ret', '');
			if ($upload_ret) {
				$upload_array = json_decode(base64_decode($upload_ret), true);
			} else {
				$upload_array = array();
			}
			$this->assign('upload_array', $upload_array);
			$this->display();
		}
	}
	
	/**
     * 第三步添加颜色图片
     */
    public function edit_imagesOp()
    {
        $common_id = input('commonid', 0, 'intval');
		$type = input('type', '');
        if ($common_id <= 0 || $type == '') {
            output_error(array('msg' => '参数不全', 'url' => _url('shop_goods/index')));
        }
        $model_goods = model('shop_goods');
		$model_goods_images = model('shop_goods_images');
		if(chksubmit()){
			// 保存
			$post_images = input('img/a', array());		
            $insert_array = array();
            foreach ($post_images as $key => $value) {
                foreach ($value as $v) {
                    if ($v['name'] == '') {
                        continue;
                    }
                    // 商品默认主图
                    $update_array = array();
                    // 更新商品主图
                    $update_where = array();
                    $update_array['goods_image'] = $v['name'];
                    $update_where['goods_commonid'] = $common_id;
                    $update_where['color_id'] = $key;
                    if ($v['default'] == 1) {
                        $update_array['goods_image'] = $v['name'];
                        $update_where['goods_commonid'] = $common_id;
                        $update_where['color_id'] = $key;
                        // 更新商品主图
                        $model_goods->edit($update_where, $update_array);
                    }
                    $tmp_insert = array();
                    $tmp_insert['goods_commonid'] = $common_id;
                    $tmp_insert['color_id'] = $key;
                    $tmp_insert['goods_image'] = $v['name'];
                    $tmp_insert['goods_image_sort'] = $v['default'] == 1 ? 0 : intval($v['sort']);
                    $tmp_insert['is_default'] = $v['default'];
                    $insert_array[] = $tmp_insert;
                }
            }
			
			if(empty($insert_array)){
				output_error('操作失败！');
			} else {
				$model_goods_images->del(array('goods_commonid' => $common_id));
				$rs = model('fxy_shop_goods_images')->insertAll($insert_array);
				if($type == 'add'){
					$url = _url('shop_goods/index');
				} else {
					$url = _url('shop_goods/edit_images', array('commonid' => $common_id, 'type' => 'edit'));
				}
				output_data(array('msg' => '操作成功', 'url' => $url));
			}            
		} else {
			$result = $model_goods_images->getList(array('goods_commonid' => $common_id));
			if(!empty($result['list'])){
				if (!empty($result['list'][0]['color_id'])) {
			        $image_list = $this->array_under_reset($result['list'], 'color_id', 2);
			    } else {
			        foreach ($result['list'] as $k => $v) {
			            $image_list[0][$k] = $v;
			        }
			    }
			} else {
				$image_list = array();
			}        
			unset($result);
			
			$img_array = array();
			$result = $model_goods->getList(array('goods_commonid' => $common_id), 'color_id,goods_image', 'color_id asc');
			$img_array = $result['list'];
			unset($result);
			
			// 整理，更具id查询颜色名称
			$colorid_array = array();
			$image_array = array();
			if (!empty($img_array)) {
				foreach ($img_array as $val) {
					if (isset($image_list[$val['color_id']])) {
						$image_array[$val['color_id']] = $image_list[$val['color_id']];
					} else {
						$image_array[$val['color_id']][0]['goods_image'] = $val['goods_image'];
						$image_array[$val['color_id']][0]['is_default'] = 1;
					}
					$colorid_array[] = $val['color_id'];
				}
			}
			
			$this->assign('img', $image_array);
			
			$model_spec = model('shop_spec_value');
			$result = $model_spec->getList(array('sp_value_id' => $colorid_array, 'store_id' => $this->store_id), 'sp_value_id,sp_value_name');
			if (empty($result['list'])) {
				$value_array[] = array('sp_value_id' => '0', 'sp_value_name' => '无属性');
			} else {
				$value_array = $result['list'];
			}
			unset($result);
			
			$this->assign('value_array', $value_array);
			$this->assign('commonid', $common_id);
			$edit_goods_sign = $type == 'add' ? false : true;
			$this->assign('edit_goods_sign', $edit_goods_sign);
			$this->assign('type', $type);
			$this->display();
		}
    }
	
	public function get_goods_list_ajaxOp()
	{
		$common_id = input('commonid', 0, 'intval');
        if (!$common_id) {
            output_error('参数错误');
        }
        $model_goods = model('shop_goods');
		$model_goods_common = model('shop_goods_common');
        $goodscommon = $model_goods_common->getInfoByID($common_id);
        if (empty($goodscommon) || $goodscommon['store_id'] != $this->store_id) {
            output_error('记录不存在');
        }
        $goods_list = $model_goods->getList(array('goods_commonid' => $common_id), 'goods_id,goods_spec,goods_price,goods_serial,goods_storage,goods_image,goods_costprice');
        if (empty($goods_list['list'])) {
            output_error('记录不存在');
        }
		
		$goods_list = $goods_list['list'];
        $spec_name = array_values((array) fxy_unserialize($goodscommon['spec_name']));
		
        foreach ($goods_list as $key => $val) {
            $goods_spec = array_values((array) fxy_unserialize($val['goods_spec']));
            $goods_list[$key]['goods_spec'] = '<div class=\'goods_spec\' style=\'text-align:center\'><em>' . implode(' | ', array_values($goods_spec)). '</em></div>';
        }
		output_data(array('goods_list' => $goods_list));
	}
	public function delOp()
	{
		$model_goods_common = model('shop_goods_common');
		$model_goods = model('shop_goods');
		$id_array = explode(',', input('id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$state = $model_goods_common->del(array('goods_commonid' => $id_array, 'store_id' => $this->store_id));
        if ($state) {
			$where = array();
			$where['goods_commonid'] = $id_array;
			$model_goods->del($where);
			model('shop_goods_taocan_goods')->where(array('goods_commonid' => $id_array))->delete();
            output_data(array('url' => _url('shop_goods/index')));
        } else {
			output_error('删除失败！');
        }
    }
	
	private function array_under_reset($array, $key, $type = 1){
		if (is_array($array)) {
			$tmp = array();
			foreach ($array as $v) {
				if ($type === 1) {
					$tmp[$v[$key]] = $v;
				} elseif ($type === 2) {
					$tmp[$v[$key]][] = $v;
				}
			}
			return $tmp;
		} else {
			return $array;
		}
	}
	public function clear_goods_cardOp(){
		$file = $this->_upload_img_dir . '/goods_card/';
		if (strpos($file, '..') !== false) return false;
		if (is_dir($file)){
			$file_list = array();
			readFileList($file, $file_list);
			if (!empty($file_list)) {
				foreach ($file_list as $v){
					//if (basename($v) != 'index.html' && (false == strpos($v, 'weixin_qrcode_'))) unlink($v);
				}
			}
		}
		web_success('清除成功！', _url('shop_goods/index'));
	}
	public function select_goods_viewOp() {
        $this->view->_layout_file = 'null_layout';
		$this->display();
    }
	public function select_goodsOp() {
        $condition = array();
        $condition['store_id'] = $this->store_id;
		$goods_name = input('goods_name', '');
		if ($goods_name) {
			$condition['goods_name'] = '%' . $goods_name . '%';
		}
		$has_special_goods = input('has_special_goods', 0, 'intval');
		if (empty($has_special_goods)) {
			//去除特殊商品
			$condition['is_virtual'] = 0;
		}
		$result = model('shop_goods_common')->getList($condition, '*', '', 20, input('page', 1, 'intval'));
		$goods_common_list = array();
		foreach ($result['list'] as $k => $v) {
			$goods_common_list[$v['goods_commonid']] = $v;
		}
		$totalpage = $result['totalpage'];
		$hasmore = $result['hasmore'];
		unset($result);
		$result = model('shop_goods')->getList(array('goods_commonid' => array_keys($goods_common_list)));
		$goods_list = array();
		foreach ($result['list'] as $k => $v) {
			if (isset($goods_list[$v['goods_commonid']])) {
				continue;
			}
			$common_info = $goods_common_list[$v['goods_commonid']];
			$goods_list[$v['goods_commonid']] = array_merge($common_info, $v);
		}
		unset($result);
		output_data(array('list' => array_values($goods_list), 'totalpage' => $totalpage, 'page_html' => page($totalpage, array('page' => input('get.page', 1, 'intval')), _url('shop_goods/select_goods'), true)));
    }
}