<?php
namespace lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class chat {
    public static function getChatHtml($seller_info) {
        $web_html = '';
		$avatar = $seller_info['logo'];
		$member_id = $seller_info['member_id'];
		$member_name = isset($seller_info['nickname']) ? $seller_info['nickname'] : $seller_info['name'];
		$store_id = $seller_info['id'];
		$store_name = $seller_info['name'];
		$css_url = STATIC_URL . '/chat';
		$app_url = SITE_URL;
		$node_url = NODE_URL;
		$web_html = <<<EOT
			<link href='{$css_url}/css/chat.css' rel='stylesheet' type='text/css'>
			<div style='clear: both;'></div>
			<div id='web_chat_dialog' style='display: none;float:right;'>
			</div>
			<a id='chat_login' href='javascript:void(0)' style='display: none;'></a>
			<script type='text/javascript'>
				var APP_URL = '{$app_url}';
				var STATIC_URL = '{$css_url}';
				var connect_url = '{$node_url}';
				var user = {};
				user['u_id'] = '{$member_id}';
				user['u_name'] = '{$member_name}';
				user['s_id'] = '{$store_id}';
				user['s_name'] = '{$store_name}';
				user['avatar'] = '{$avatar}';
			</script>
EOT;
		$web_html.= '<link href=\'' . $css_url . '/lib/perfect-scrollbar.min.css\' rel=\'stylesheet\' type=\'text/css\'>';
		$web_html.= '<script type=\'text/javascript\' src=\'' . $css_url . '/lib/perfect-scrollbar.min.js\'></script>';
		$web_html.= '<script type=\'text/javascript\' src=\'' . $css_url . '/lib/jquery.mousewheel.js\'></script>';
		$web_html.= '<script type=\'text/javascript\' src=\'' . $css_url . '/lib/jquery.charCount.js\' charset=\'utf-8\'></script>';
		$web_html.= '<script type=\'text/javascript\' src=\'' . $css_url . '/lib/jquery.smilies.js\' charset=\'utf-8\'></script>';
		$seller_id = $seller_info['id'];
		$seller_name = $seller_info['name'];
		$seller_is_admin = 1;
		$web_html.= '<script type=\'text/javascript\' src=\'' . $css_url . '/js/store.js\' charset=\'utf-8\'></script>';
		$seller_smt_limits = '';
		if (!empty(input('session.seller_smt_limits')) && is_array(input('session.seller_smt_limits'))) {
			$seller_smt_limits = implode(',', input('session.seller_smt_limits'));
		}
		$web_html.= <<<EOT
			<script type='text/javascript'>
				user['seller_id'] = '{$seller_id}';
				user['seller_name'] = '{$seller_name}';
				user['seller_is_admin'] = '{$seller_is_admin}';
				var smt_limits = '{$seller_smt_limits}';
			</script>
EOT;
        return $web_html;
    }
}