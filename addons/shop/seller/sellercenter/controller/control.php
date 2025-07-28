<?php
namespace sellercenter\controller;
use base;
class control extends base\controller {
	protected $config = array();
	protected $store_info = array();
	protected $store_id;
	private $error = '';
	protected $_upload_img_dir = '';
	protected $_upload_img_url = '';
	protected $_upload_file_dir = '';
	protected $_upload_file_url = '';
	protected $_upload_media_dir = '';
	protected $_upload_media_url = '';
	public function _initialize()
	{
		parent::_initialize();
		$this->store_id = seller_login_check();
		if ($this->_controller != 'publics') {
			if (!$this->store_id) {
				header('location:' . _url('publics/login'));
                die;
            }
			$store_info = model('seller')->getInfo(array('id' => $this->store_id));
			if ($store_info) {
				// seller关闭
				if (isset($store_info['state']) && intval($store_info['state']) == 0) {
					$this->error = '您被禁用了！';
				}
			} else {
				$this->error = '用户不存在！';
			}
			if (input('session.is_seller', false) == false) {
				$rsAccount = model('account_store')->getInfo(array('store_id' => $this->store_id, 'account_name' => input('session.sellername', '')));
				if($rsAccount){
					// account关闭
					if (isset($rsAccount['state']) && intval($rsAccount['state']) != 1) {
						$this->error = '您被禁用了！';
					}
				}else{
					$this->error = '用户不存在！';
				}
			}
			//统一附件路径
			$this->_upload_img_dir = UPLOADFILES_PATH . '/seller/' . $this->store_id . '/image/';
			$this->_upload_img_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/seller/' . $this->store_id . '/image/';
			$this->_upload_file_dir = UPLOADFILES_PATH . '/seller/' . $this->store_id . '/file/';
			$this->_upload_file_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/seller/' . $this->store_id . '/file/';
			$this->_upload_media_dir = UPLOADFILES_PATH . '/seller/' . $this->store_id . '/media/';
			$this->_upload_media_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/seller/' . $this->store_id . '/media/';
			$this->assign('title', '商家管理系统');
			$this->assign('menu', input('session.seller_menu', ''));
			// 当前菜单
            $current_menu = $this->_getCurrentMenu(input('session.seller_function_list', ''));
            $this->assign('current_menu', $current_menu);
			$config = model('config')->find();
			$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array());
			$wechat_setting = $wechat_setting ?: array();
			$this->config = array_merge($config, $wechat_setting);
			config($this->config);
			$this->assign('config', $this->config);
			$member_info = model('member')->getInfo(array('uid' => $store_info['member_id']), 'nickname,headimg');
			if ($member_info) {
			    $store_info['nickname'] = $member_info['nickname'];
			    $store_info['headimg'] = $member_info['headimg'];
			}
			$this->store_info = $store_info;
			$this->assign('store_info', $this->store_info);
		}else{
			$this->assign('title', '您需要登录后才可以使用本功能');
		}
		$this->assign('error', $this->error);
	}
	protected function getSellerMenuList($limits = array())
    {
		$seller_menu = array();
		$menu_list = $this->_getMenuList();
		foreach ($menu_list as $key => $value) {
			if($limits){
				foreach ($value['child'] as $child_key => $child_value) {
					if (!in_array($child_value['act'] . '_' . $child_value['op'], $limits)) {
						unset($menu_list[$key]['child'][$child_key]);
					}
				}
			}
			if (count($menu_list[$key]['child']) > 0) {
				$seller_menu[$key] = $menu_list[$key];
			}
		}
        
        $seller_function_list = $this->_getSellerFunctionList($seller_menu);
        return array('seller_menu' => $seller_menu, 'seller_function_list' => $seller_function_list);
    }
	private function _getMenuList()
    {
        include(APP_PATH . '/include/' . 'menu.php');
        return $menu_list;
    }
    private function _getSellerFunctionList($menu_list)
    {
        $format_menu = array();
        foreach ($menu_list as $key => $menu_value) {
            foreach ($menu_value['child'] as $submenu_value) {
                $format_menu[$submenu_value['act'] . '_' . $submenu_value['op']] = array('model' => $key, 'model_name' => $menu_value['name'], 'name' => $submenu_value['name'], 'act' => $submenu_value['act'], 'op' => $submenu_value['op']);
				if(!empty($submenu_value['child'])){
					foreach($submenu_value['child'] as $val){
						$format_menu[$val['act'] . '_' . $val['op']] = array('model' => $key, 'model_name' => $menu_value['name'], 'name' => $val['name'], 'act' => $val['act'], 'op' => $val['op']);
					}
				}
            }
        }
        return $format_menu;
    }
	private function _getCurrentMenu($seller_function_list)
    {
        $current_menu = isset($seller_function_list[$this->_controller . '_' . $this->_action]) ? $seller_function_list[$this->_controller . '_' . $this->_action] : '';
		if (empty($current_menu)) {
			if($this->_controller != 'index'){
				$this->error = '您没有当前操作权限！';
			}else{
				$current_menu = array('model' => 'index', 'model_name' => '首页');
			}
        }
        return $current_menu;
    }
	/**
     * 记录卖家日志
     *
     * @param $content 日志内容
     * @param $state 1成功 0失败
     */
    protected function log($content = '', $state = 1)
    {
        $seller_info = array();
        $seller_info['log_content'] = $content;
        $seller_info['log_time'] = TIMESTAMP;
        $seller_info['store_id'] = $this->store_id;
        $seller_info['log_seller_name'] = input('session.sellername', '');
        $seller_info['log_ip'] = get_client_ip();
        $seller_info['log_url'] = $this->_controller . '&' . $this->_action;
        $seller_info['log_state'] = $state;
        $model_seller_log = model('seller_log');
		$model_seller_log->insert($seller_info);
    }
}
?>