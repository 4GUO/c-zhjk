<?php
namespace logic;
use base;
use lib;
class tpl_message {
	//获取奖励提醒
    public function get_reward($data) {
        $weixin_config = config();
        $access_token = logic('weixin_token')->get_access_token($weixin_config);
		if (empty($weixin_config['wechat_appid']) || empty($weixin_config['wechat_appsecret'])) {
			return false;
		}
		$postdata = array(
		    'first' => array('value' => '奖励收入'),
			'keyword1' => array('value' => $data['detail_bonus']),//收入金额
			'keyword2' => array('value' => $data['type_name']),//收入类型
			'keyword3' => array('value' => date('Y/m/d H:i', time())),//到账时间
			'remark' => array('value' => $data['detail_desc']),
		);
		$url = uni_url('/pages/user/index', array(), true);
        $wechat = new lib\wxSDK\WechatAuth($weixin_config['wechat_appid'], $weixin_config['wechat_appsecret'], $access_token);
		$res = $wechat->messageTplSend($data['openid'], $weixin_config['reward_template_id'], $postdata, $url);
		//lib\logging::write(var_export($res, true));//debug
        return true;
    }
}