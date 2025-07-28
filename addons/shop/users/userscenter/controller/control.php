<?php
namespace userscenter\controller;
use base;
class control extends base\controller {
	protected $config = array();
	protected $uniacid;
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
		$this->uniacid = users_login_check();
		if ($this->_controller != 'publics') {
			if (!$this->uniacid) {
				header('location:' . users_url('publics/login'));
                die;
            }
			$rsUsers = model('users')->getInfo(array('uniacid' => $this->uniacid));
			if ($rsUsers) {
				// users关闭
				if (isset($rsUsers['status']) && intval($rsUsers['status']) == 1) {
					$this->error = '您被禁用了！';
				}
			} else {
				$this->error = '用户不存在！';
			}
			if (input('session.is_admin', false) == false) {
				$rsAccount = model('account')->getInfo(array('uniacid' => $this->uniacid, 'account_name' => input('session.username', '')));
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
			$this->_upload_img_dir = UPLOADFILES_PATH . '/users/image/';
			$this->_upload_img_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/users/image/';
			$this->_upload_file_dir = UPLOADFILES_PATH . '/users/file/';
			$this->_upload_file_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/users/file/';
			$this->_upload_media_dir = UPLOADFILES_PATH . '/users/media/';
			$this->_upload_media_url = str_replace(SITE_URL, '', UPLOADFILES_URL) . '/users/media/';
			$config = model('config')->where(array('uniacid' => $this->uniacid))->find();
			$wechat_setting = model('wechat')->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
			$wechat_setting = $wechat_setting ?: array();
			$this->config = array_merge($config, $wechat_setting);
			config($this->config);
			$this->assign('setting', $this->config);
			$this->assign('title', (!empty($this->config['name']) ? $this->config['name'] . '-' : '新零售-') . '管理系统');
			$this->assign('menu', input('session.users_menu', ''));
			// 当前菜单
            $current_menu = $this->_getCurrentMenu(input('session.users_function_list', ''));
            $this->assign('current_menu', $current_menu);
		} else {
			$this->assign('title', '您需要登录后才可以使用本功能');
		}
		$this->assign('error', $this->error);
	}
	protected function getUsersMenuList($limits = array())
    {
		$users_menu = array();
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
				$users_menu[$key] = $menu_list[$key];
			}
		}
        
        $users_function_list = $this->_getUsersFunctionList($users_menu);
        return array('users_menu' => $users_menu, 'users_function_list' => $users_function_list);
    }
	private function _getMenuList()
    {
        include(APP_PATH . '/include/' . 'menu.php');
        return $menu_list;
    }
    private function _getUsersFunctionList($menu_list)
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
	private function _getCurrentMenu($users_function_list)
    {
        $current_menu = isset($users_function_list[$this->_controller . '_' . $this->_action]) ? $users_function_list[$this->_controller . '_' . $this->_action] : '';
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
        $users_info = array();
        $users_info['log_content'] = $content;
        $users_info['log_time'] = TIMESTAMP;
        $users_info['uniacid'] = $this->uniacid;
        $users_info['log_users_name'] = input('session.username', '');
        $users_info['log_ip'] = get_client_ip();
        $users_info['log_url'] = $this->_controller . '&' . $this->_action;
        $users_info['log_state'] = $state;
        $model_users_log = model('users_log');
		$model_users_log->insert($users_info);
    }
}
?>