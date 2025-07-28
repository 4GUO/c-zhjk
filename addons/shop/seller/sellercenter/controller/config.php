<?php
namespace sellercenter\controller;
class config extends control {
	private $setting_config;
	public function __construct() {
		parent::_initialize();
		$this->assign('config', $this->store_info);
	}
	//运费设置
	public function shippingOp(){
		if (chksubmit()) {
			$data = array(
				'freight_in' => input('freight_in', 0),
				'freight_in_all' => input('freight_in_all', 0),
				'freight_infree' => input('freight_infree', 0),
			);
			model('seller')->edit(array('id' => $this->store_id), $data);
			if(input('freight_in_all', 0)){
				$goods_array = array(
					'goods_freight' => $data['freight_in']
				);
				model('shop_goods_common')->edit(array('store_id' => $this->store_id), $goods_array);
			}
			output_data(array('msg' => '操作成功', 'url' => _url('config/shipping')));
		}
		$this->display();
	}
	public function shipping_transportOp() {
		$model_transport = model('transport');
		$model_transport_extend = model('transport_extend');
        $result = $model_transport->getList(array('store_id' => $this->store_id), '*', 'id DESC', 10, input('page', 1, 'intval'));
		$this->assign('page', page($result['totalpage'], array('page' => input('page', 1, 'intval')), _url('config/shipping_transport')));
		$list = $result['list'];
        if (!empty($list) && is_array($list)) {
            $transport = array();
            foreach ($list as $v) {
                if (!array_key_exists($v['id'], $transport)) {
                    $transport[$v['id']] = $v['title'];
                }
            }
            $result = $model_transport_extend->getList(array('transport_id' => array_keys($transport)));
			$extend = $result['list'];
            // 整理
            if (!empty($extend)) {
                $tmp_extend = array();
                foreach ($extend as $val) {
                    $tmp_extend[$val['transport_id']]['data'][] = $val;
                    if (isset($val['is_default']) && $val['is_default'] == 1) {
                        $tmp_extend[$val['transport_id']]['price'] = $val['sprice'];
                    }
                }
                $extend = $tmp_extend;
				$this->assign('extend', $extend);
            }
        }
		$this->assign('list', $list);
		$this->display();
	}
	public function shipping_transport_addOp(){
		if(chksubmit()){
			$trans_info = array();
			$trans_info['title'] = input('title', '');
			$trans_info['send_tpl_id'] = 1;
			$trans_info['store_id'] = $this->store_id;
			$trans_info['update_time'] = TIMESTAMP;
			$transport_id = input('transport_id', 0, 'intval');
			$model_transport = model('transport');
			$model_transport_extend = model('transport_extend');
			$model = model();
            $model->beginTransaction();
			if ($transport_id) {
				// 编辑时，删除所有附加表信息
				$model_transport->edit(array('id' => $transport_id), $trans_info);
				$model_transport_extend->del(array('transport_id' => $transport_id));
			} else {
				// 新增
				$transport_id = $model_transport->add($trans_info);
			}
			$trans_list = array();
			$areas = $_POST['areas']['kd'];
			$special = $_POST['special']['kd'];
			//var_dump($areas);exit;
			if (is_array($special)) {
				foreach ($special as $key => $value) {
					$tmp = array();
					if (empty($areas[$key])) {
						continue;
					}
					$areas[$key] = explode('|||', $areas[$key]);
					$tmp['area_id'] = ',' . $areas[$key][0] . ',';
					$tmp['area_name'] = $areas[$key][1];
					$tmp['sprice'] = $value['postage'];
					$tmp['transport_id'] = $transport_id;
					$tmp['transport_title'] = input('title', '');
					// 计算省份ID
					$province = array();
					$tmp1 = explode(',', $areas[$key][0]);
					if (!empty($tmp1) && is_array($tmp1)) {
						$city = model('area')->getCityProvince();
						foreach ($tmp1 as $t) {
							$pid = isset($city[$t]) ? $city[$t] : 0;
							if (!empty($pid) && !in_array($pid, $province)) {
								$province[] = $pid;
							}
						}
					}
					if (count($province) > 0) {
						$tmp['top_area_id'] = ',' . implode(',', $province) . ',';
					} else {
						$tmp['top_area_id'] = '';
					}
					$tmp['store_id'] = $this->store_id;
					$trans_list[] = $tmp;
				}
			}
			$result = $model_transport_extend->insertAll($trans_list);
			if ($result) {
				$model->commit();
				output_data(array('msg' => '操作成功', 'url' => _url('config/shipping_transport')));
			} else {
				$model->rollBack();
				output_error('操作失败');
			}
		}else{
			$areas = model('area')->getAreas();
			$this->assign('areas', $areas);
			$model_transport = model('transport');
			$model_transport_extend = model('transport_extend');
			$transport_id = input('transport_id', 0, 'intval');
			if($transport_id){
				$transport = $model_transport->getInfo(array('id' => $transport_id));
				$extend = $model_transport_extend->getList(array('transport_id' => $transport_id));
				$extend_list = $extend['list'];
				$this->assign('transport', $transport);
				$this->assign('extend', $extend_list);
			}
			$this->display();
		}
	}
	public function shipping_transport_deleteOp(){
		$transport_id = input('transport_id', 0, 'intval');
        $model_transport = model('transport');
        $transport = $model_transport->getInfo(array('id' => $transport_id));
        // 查看是否正在被使用
		if (!is_numeric($transport_id)) {
            output_error('缺少参数');
        }
        $check = model('shop_goods_common')->where(array('transport_id' => $transport_id))->total();
        
        if ($check) {
			output_error('该区域正在被使用，不能删除');
        }
        if ($model_transport->del(array('id' => $transport_id))) {
			output_data(array('msg' => '操作成功', 'url' => _url('config/shipping_transport')));
        } else {
			output_error('删除失败');
        }
	}
}