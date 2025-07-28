<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class deal_url {
    public function split_url($url) {
        $result = array();
        $arr_url = explode('?', $url);
        //获取页面参数
        if (empty($arr_url[1])) {
            $result['requesturi'] = array();
        } else {
            $param = explode('&', $arr_url[1]);
            foreach ($param as $v) {
                $arr = explode('=', $v);
                $result['requesturi'][$arr[0]] = empty($arr[1]) ? '' : $arr[1];
            }
        }
        return $result;
    }
    public function connect_url($ownerid, $url) {
        $result = array();
        $arr_url = explode('?', $url);
        //获取页面参数
        if (empty($arr_url[1])) {
            return $url . '?oid=' . $ownerid;
        } else {
            $new_url = $arr_url[0] . '?oid=' . $ownerid;
            $param = explode('&', $arr_url[1]);
            foreach ($param as $v) {
                $arr = explode('=', $v);
                if ($arr[0] != 'oid') {
                    $new_url.= '&' . $v;
                }
            }
        }
        return $new_url;
    }
}