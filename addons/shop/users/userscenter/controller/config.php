<?php
namespace userscenter\controller;
class config extends control {
	private $setting_config;
	public function __construct() {
		parent::_initialize();

		$result = model('config')->getInfo(array('uniacid' => $this->uniacid));
		if (!empty($result) && is_array($result)) {
			$this->setting_config = $result;
		} else {
			$data = array(
				'uniacid' => $this->uniacid
			);
			model('config')->add($data);
			$result = model('config')->getInfo(array('uniacid' => $this->uniacid));
			$this->setting_config = $result;
			unset($data);
		}
		unset($result);
		$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
		$this->setting_config = array_merge($this->setting_config, $wechat_setting);
		$this->assign('config', $this->setting_config);
	}

	//基本设置
	public function baseOp(){
		if(chksubmit()){
			$data = array(
				'name' => input('name', ''),
				'login_logo' => input('login_logo', ''),
				'kuaidi100_customer' => input('kuaidi100_customer', ''),
				'kuaidi100_key' => input('kuaidi100_key', ''),
				'perfect_information' => input('perfect_information', 1, 'intval'),
				'apploadurl' => input('apploadurl', ''),
				'xieyi_content' => input('xieyi_content', ''),
				'tihuo_freight' => input('tihuo_freight', 0, 'floatval'),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/base')));
		}
		$this->display();
	}
	//短信设置
	public function smsOp(){
		if (IS_API) {
			$data = array(
				'mobile_host_type' => input('mobile_host_type', 1, 'intval'),
				'mobile_username' => input('mobile_username', ''),
				'mobile_pwd' => input('mobile_pwd', ''),
				'mobile_key' => input('mobile_key', ''),
				'sms_status' => input('sms_status', 1, 'intval'),
				'mobile_accessKeyId' => input('mobile_accessKeyId', ''),
				'mobile_accessKeySecret' => input('mobile_accessKeySecret', ''),
				'mobile_templateCode' => input('mobile_templateCode', ''),
				'mobile_sign_name' => input('mobile_sign_name', ''),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/sms')));
		}
		$this->display();
	}
	//分销设置
	public function distributeOp(){
		if (chksubmit()) {
			$data = array(
				'member_inviter' => input('member_inviter', 0, 'intval'),
				'distributor_open_goods' => input('distributor_open_goods', 0, 'intval'),
				'bonus_name_goods' => input('bonus_name_goods', '佣金'),
				'distributor_level_goods' => input('distributor_level_goods', 1, 'intval'),
				'distributor_self_goods' => input('distributor_self_goods', 0, 'intval'),
				'tixian_day_start' => input('tixian_day_start', 0, 'intval'),
				'tixian_day_end' => input('tixian_day_end', 0, 'intval'),
				'tixian_tip' => input('tixian_tip', ''),
				'dis_cometype' => input('dis_cometype', 1, 'intval'),
				'dis_goods_ids' => input('goods_ids', ''),
				'dis_come_money' => input('dis_come_money', 0),
				'yeji_fenhong_bili' => input('yeji_fenhong_bili', 0, 'floatval'),
				'baodan_reward_bili' => input('baodan_reward_bili', 0),
				'fenhong_reward_bili' => input('fenhong_reward_bili', 0),
				'linshou_fenhong_inviter_num' => input('linshou_fenhong_inviter_num', 0, 'intval'),
				'linshou_fenhong_level_id' => input('linshou_fenhong_level_id', 0, 'intval'),
                "fgjdtj" => input('fgjdtj', 0, 'intval'),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/distribute')));
		}
		//所有商品
		$goods_list = model('shop_goods_common')->getList();
		$this->assign('goods_list', $goods_list['list']);
		//参与商品
		$goods_ids = trim($this->setting_config['dis_goods_ids'], ',');
		$this->assign('goods_ids', $goods_ids);
		$goods_ids = explode(',', $goods_ids);
		$goods_list_return = model('shop_goods_common')->getList(array('goods_commonid' => $goods_ids));
		$this->assign('goods_list_return', $goods_list_return['list']);
		//会员级别
		$level_temp = model('vip_level')->getList(array('uniacid' => $this->uniacid));
		$this->assign('member_levels', $level_temp['list']);
		$this->display();
	}
	//公排设置
	public function dis_publicOp() {
		if (chksubmit()) {
			$data = array(
				'public_open' => input('public_open', 0, 'intval'),
				'public_bonus_level' => input('public_bonus_level', 1, 'intval'),
				'public_multi' => input('public_multi', 0, 'intval'),
				'public_out_level' => input('public_out_level', 1, 'intval'),
				'public_cometype' => input('public_cometype', 1, 'intval'),
				'public_goods_ids' => input('goods_ids', ''),
				'public_come_money' => input('public_come_money', 0),
				'public_out_open' => input('public_out_open', 0),
			);
			if ($this->setting_config['public_status'] == 0) {
				$data['public_times'] = input('public_times', 2, 'intval');
			}
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/dis_public')));
		}
		/*排位递增形式*/
		$times = array(
			2 => '二二复制',
			3 => '三三复制',
			4 => '四四复制',
			5 => '五五复制',
			6 => '六六复制',
			7 => '七七复制',
			8 => '八八复制',
			9 => '九九复制'
		);
		$this->assign('times', $times);

		//所有商品
		$goods_list = model('shop_goods_common')->getList();
		$this->assign('goods_list', $goods_list['list']);
		//参与商品
		$goods_ids = trim($this->setting_config['public_goods_ids'], ',');
		$this->assign('goods_ids', $goods_ids);
		$goods_ids = explode(',', $goods_ids);
		$goods_list_return = model('shop_goods_common')->getList(array('goods_commonid' => $goods_ids));
		$this->assign('goods_list_return', $goods_list_return['list']);
		$this->display();
	}
	public function dis_public_commissionOp() {
		if (chksubmit()) {
			$data = array(
				'public_commission' => empty(input('public_commission', '')) ? '' : serialize(input('public_commission', '')),
				'public_inviter' => empty(input('public_inviter', 0)) ? 0 : number_format(input('public_inviter', 0), 2, '.', ''),
				'public_parent' => empty(input('public_parent', 0)) ? 0 : number_format(input('public_parent', 0), 2, '.', ''),
				'public_thankful' => empty(input('public_thankful', 0)) ? 0 : number_format(input('public_thankful', 0), 2, '.', '')
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/dis_public_commission')));
		}
		$public_commission = !empty($this->setting_config['public_commission']) ? fxy_unserialize($this->setting_config['public_commission']) : array();
		$this->assign('public_commission', $public_commission);
		$this->display();
	}
	//运费设置
	public function shippingOp(){
		if (chksubmit()) {
			$freight_out = input('freight_out/a', array());
			foreach($freight_out as $k_p=>$v_p){
				if(empty($v_p) || !is_numeric($v_p)){
					$v_p = 0;
				} else {
					$v_p = priceFormat($v_p);
				}

				$freight_out[$k_p] = $v_p;
			}
			$data = array(
				'freight_in' => input('freight_in', 0),
				'freight_in_all' => input('freight_in_all', 0),
				'freight_infree' => input('freight_infree', 0),
				'freight_out' => serialize($freight_out)
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			if(input('freight_in_all', 0)){
				$goods_array = array(
					'goods_freight' => $data['freight_in']
				);
				model('shop_goods_common')->edit(array('uniacid' => $this->uniacid), $goods_array);
				model('shop_goods')->edit(array('uniacid' => $this->uniacid), $goods_array);
			}
			output_data(array('msg' => '操作成功', 'url' => users_url('config/shipping')));
		} else {
			$freight_out = $this->config['freight_out'] ? fxy_unserialize($this->config['freight_out']) : array();
			$this->assign('freight_out', $freight_out);
			//会员级别
			$level_temp = model('vip_level')->getList(array('uniacid' => $this->uniacid));
			$this->assign('member_levels', $level_temp['list']);
		}
		$this->display();
	}
	public function shipping_transportOp(){
		$model_transport = model('transport');
		$model_transport_extend = model('transport_extend');
        $result = $model_transport->getList(array('uniacid' => $this->uniacid), '*', 'id DESC', 10, input('page', 1, 'intval'));
		$this->assign('page', page($result['totalpage'], array('page' => input('page', 1, 'intval')), users_url('config/shipping_transport')));
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
			$trans_info['uniacid'] = $this->uniacid;
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
					$trans_list[] = $tmp;
				}
			}
			$result = $model_transport_extend->insertAll($trans_list);
			if ($result) {
				$model->commit();
				output_data(array('msg' => '操作成功', 'url' => users_url('config/shipping_transport')));
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
        $goods_info = model('shop_goods')->where(array('transport_id' => $transport_id))->field('goods_id')->find();

        if ($goods_info) {
			output_error('该区域正在被使用，不能删除');
        }
        if ($model_transport->del(array('id' => $transport_id))) {
			output_data(array('msg' => '操作成功', 'url' => users_url('config/shipping_transport')));
        } else {
			output_error('删除失败');
        }
	}
	//支付设置
	public function pay_indexOp(){
		$model_mb_payment = model('mb_payment');
        $mb_payment_list = $model_mb_payment->select();
        $this->assign('list', $mb_payment_list);
		$this->display();
	}
	public function pay_editOp(){
		if (chksubmit()) {
			$payment_id = input('payment_id', 0, 'intval');
			$model_mb_payment = model('mb_payment');
			$mb_payment_info = $model_mb_payment->where(array('payment_id' => $payment_id))->find();
			if ($mb_payment_info['payment_code'] == 'wxapp') {
				$payment_config = array(
					'appid' => input('appid', ''),
					'appsecret' => input('appsecret', ''),
					'mchid' => input('mchid', ''),
					'signkey' => input('signkey', ''),
				);
			} else if ($mb_payment_info['payment_code'] == 'wxpay_jsapi') {
				$payment_config = array(
					'appId' => input('appId', ''),
					'appSecret' => input('appSecret', ''),
					'partnerId' => input('partnerId', ''),
					'apiKey' => input('apiKey', ''),
				);
			} else if ($mb_payment_info['payment_code'] == 'wxpay_h5') {
				$payment_config = array(
					'appId' => input('appId', ''),
					'partnerId' => input('partnerId', ''),
					'apiKey' => input('apiKey', ''),
				);
			} else if ($mb_payment_info['payment_code'] == 'alipay') {
				$payment_config = array(
					'appid' => input('appid', ''),
					'private_key' => input('private_key', '', 'trim'),
					'public_key' => input('public_key', '', 'trim')
				);
			}
			$model_mb_payment->where(array('payment_id' => $payment_id))->update(array('payment_config' => serialize($payment_config), 'payment_state' => input('payment_state', 0, 'intval')));
			output_data(array('msg' => '操作成功', 'url' => users_url('config/pay_edit', array('payment_id' => $payment_id))));
		} else {
			$payment_id = input('payment_id', 0, 'intval');
			$model_mb_payment = model('mb_payment');
			$mb_payment_info = $model_mb_payment->where(array('payment_id' => $payment_id))->find();
			$this->assign('mb_payment_info', $mb_payment_info);
			$this->assign('payment_config', fxy_unserialize($mb_payment_info['payment_config']));
			$this->display();
		}
	}
	//客服设置
	public function kefuOp(){
		if(chksubmit()){
			$data = array(
				'telphone' => input('telphone', ''),
				'kf_open' => input('kf_open', 0, 'intval'),
				'kf_ico' => input('image_path', ''),
				'kf_ercode' => input('kf_ercode', ''),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/kefu')));
		}
		$this->display();
	}
	public function index_moduleOp(){
		$list = model('index_module')->where(array('uniacid' => $this->uniacid))->select();
		$this->assign('list', $list);
		$this->display();
	}
	public function module_addOp(){
		if(chksubmit()){
			$data = array(
				'name' => input('name', ''),
				'link' => input('link', ''),
				'thumb' => input('thumb', ''),
				'status' => input('status', 0),
				'm_sort' => input('m_sort', 0),
			);
			model('index_module')->insert($data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/index_module')));
		}
		$this->display();
	}
	public function module_editOp(){
		if(chksubmit()){
			$id = input('id', 0, 'intval');
			$data = array(
				'name' => input('name', ''),
				'link' => input('link', ''),
				'thumb' => input('thumb', ''),
				'status' => input('status', 0),
				'm_sort' => input('m_sort', 0),
			);
			model('index_module')->where(array('id' => $id))->update($data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/index_module')));
		}
		$id = input('id', 0, 'intval');
		$info = model('index_module')->where(array('id' => $id))->find();
		$this->assign('info', $info);
		$this->display();
	}
	public function module_delOp(){
		$model_class = model('index_module');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['id'] = $id_array;
		$where['uniacid'] = $this->uniacid;

		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('config/index_module')));
        } else {
			output_error('删除失败！');
        }
	}
	public function sys_listOp() {
		//商品分类
		$result = model('shop_goods_class')->getList(array('gc_parent_id' => 0));
		$class_list = array();
		foreach($result['list'] as $k => $v) {
			$v['link'] = uni_url('/pages/goods/goods_list', array('gc_id' => $v['gc_id']));
			$class_list[] = $v;
		}
		unset($result);
		$this->assign('class_list', $class_list);
		$this->view->_layout_file = 'null_layout';
		$this->display();
	}
	public function integralOp() {
		if (chksubmit()) {
			$data = array(
				'points_reg' => input('points_reg', 0),
				'points_login' => input('points_login', 0),
				'points_comments' => input('points_comments', 0),
				'points_signin' => input('points_signin', 0),
				'points_invite' => input('points_invite', 0),
				'points_orderrate' => input('points_orderrate', 1),
				'points_ordermax' => input('points_ordermax', 0, 'intval'),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/integral')));
		}
		$this->display();
	}
	public function offlineOp() {
		if(chksubmit()) {
			$data = array(
				'bank_name' => input('bank_name', ''),
				'bank_address' => input('bank_address', ''),
				'bank_username' => input('bank_username', ''),
				'bank_no' => input('bank_no', ''),
				'offline_type' => input('offline_type', 1),
				'zhifubao_account' => input('zhifubao_account', ''),
				'zhifubao_name' => input('zhifubao_name', ''),
				'zhifubao_ercode' => input('zhifubao_ercode', ''),
				'weixin_ercode' => input('weixin_ercode', ''),
			);
			model('config')->edit(array('uniacid' => $this->uniacid), $data);
			output_data(array('msg' => '操作成功', 'url' => users_url('config/offline')));
		}
		$this->display();
	}
}
