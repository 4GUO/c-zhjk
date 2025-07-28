<?php
namespace base;
defined('SAFE_CONST') or exit('Access Invalid!');
class controller {
	/**
     * 视图实例对象
     * @var view
     * @access protected
     */    
    protected $view = null;
	protected $_module = '';
	protected $_controller = '';
    protected $_action = '';
    protected function _initialize() {
		$this->view = base::instance('base\view');
		$this->_module = dispatcherUrl::getModule();
        $this->_controller = dispatcherUrl::getController();
        $this->_action = substr(dispatcherUrl::getAction(), 0, -2);
		$this->assign('__PUBLIC__', APP_PATH . '/publics');
    }
	//实现控制器模板相互调用
	public function __set($property, $value) {
	    $this->$property = $value;
	}
	protected function assign($name, $value = '') {
        $this->view->assign($name, $value);
    }
	protected function display($templateFile = '', $templateDir = '', $auto_base_dir = true) {
		$view_path = APP_PATH . '/' . $this->_module . '/view';
		if($this->view->_layout_file){
			$this->view->_layout_file = $view_path . '/layout/' . $this->view->_layout_file . '.php';
		}else{
			$this->view->_layout_file = $view_path . '/layout/' . $this->_module . '.php';
		}
		if ($auto_base_dir) {//自动识别模板路径
			$this->view->_tpl_dir = $view_path . '/' . $this->_controller;
			if ($templateDir) {
				$this->view->_tpl_dir .= '/' . $templateDir;
			}
		} else {
			$this->view->_tpl_dir = $templateDir;
		}
		if(!$templateFile) {
			$templateFile = $this->_action;
		}
		$templateFile .= '.php';
		$this->assign('tpl_file', $this->view->_tpl_dir . '/' .$templateFile);
        $this->view->display($templateFile);
    }
	/**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method, $args) {
        send_http_status('404');
    }
}