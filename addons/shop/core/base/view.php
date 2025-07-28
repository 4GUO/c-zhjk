<?php
namespace base;
defined('SAFE_CONST') or exit('Access Invalid!');
class view {
    private $_output_value = array();
    public $_tpl_dir = '';
    public $_layout_file = '';
    public $caching = false;
    public $cache_dir = 'cache';
    public $cache_time = 3600;
    public function assign($output, $input = '') {
        $this->_output_value[$output] = $input;
    }
    public function display($page_name) {
        if (!empty($this->_tpl_dir)) {
            $_tpl_dir = $this->_tpl_dir . '/';
        } else {
            $_tpl_dir = '';
        }
        $output = $this->_output_value;
        if (!empty($this->_layout_file)) {
            $tpl_file = $this->_layout_file;
        } else {
            $tpl_file = $_tpl_dir . $page_name;
        }
        if (file_exists($tpl_file)) {
            if ($this->caching) {
                $cache_name = $_SERVER['HTTP_HOST'] . (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '');
                $cache_file = $this->cache_dir . '/' . md5($cache_name) . '.html';
                if (!file_exists($cache_file) || filemtime($cache_file) < filemtime($tpl_file)) {
                    if (time() > filemtime($cache_file) + $this->cache_time) {
                        ob_start();
                        ob_implicit_flush(false);
                        include ($tpl_file);
                        $_content = ob_get_clean();
                        if (!preg_match('/Status.*[345]{1}\d{2}/i', implode(' ', headers_list())) && !preg_match('/(-[a-z0-9]{2}){3,}/i', $cache_file)) {
                            if (FALSE === file_put_contents($cache_file, $_content)) {
                                base::halt('Cache files generates an error!');
                            }
                        } else {
                            base::halt('Prohibit storage, file abnormal!');
                        }
                    }
                }
                include ($cache_file);
            } else {
                include ($tpl_file);
            }
        } else {
            base::halt('view error:' . $tpl_file . ' is not exists');
        }
    }
}