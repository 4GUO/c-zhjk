<?php
/**
 * 微信自动授权
 *
 */
namespace api\controller;
use core;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class getauthinfo extends control {
    public function __construct() {
		parent::_initialize();
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $wechat_issue = config('wechat_isuse');
        if (empty($wechat_issue)) {
            header('Location:' . input('get.ref', ''));
            exit;
        }
        if (strpos($agent, 'MicroMessenger') && $this->_controller == 'getauthinfo') {
            if (empty(config('wechat_appid')) || empty(config('wechat_appsecret'))) {
				header('Location:' . api_url('api/tippage/error', array('msg' => '微信模块配置信息不全', 'i' => $this->uniacid)));
                exit;
            }
        }
    }
    public function loginOp() {
		$ownerid = input('oid', 0, 'intval');
		$this->code = input('code', '');
        if (empty($this->code)) {
			output_error('授权失败');
		}
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . config('wechat_appid') . '&secret=' . config('wechat_appsecret') . '&code=' . $this->code . '&grant_type=authorization_code';
		$res = json_decode($this->httpGet($url) , true);
		//lib\logging::write(var_export($res, true));
		if (isset($res['errcode'])) {
			$msg = '错误：' . $res['errcode'] . '[' . $res['errmsg'] . ']';
			output_error($msg);
		}
		$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . config('wechat_appid') . '&grant_type=refresh_token&refresh_token=' . $res['refresh_token'];
		$access_token = json_decode($this->httpGet($access_token_url) , true);
		if (isset($access_token['errcode'])) {
			$msg = '错误：' . $access_token['errcode'] . '[' . $access_token['errmsg'] . ']';
			output_error($msg);
		}
		$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token['access_token'] . '&openid=' . $access_token['openid'] . '&lang=zh_CN';
		$user_info = json_decode(file_get_contents($user_info_url), true);
		if (isset($user_info['errcode'])) {
			$msg = '错误：' . $user_info['errcode'] . '[' . $user_info['errmsg'] . ']';
			output_error($msg);
		}
		$user_info['unionid'] = !empty($user_info['unionid']) ? $user_info['unionid'] : $user_info['openid'];
		//lib\logging::write(var_export($user_info, true));
		$model_fans = model('fans');
		$fans_info = $model_fans->getInfo(array('unionid' => $user_info['unionid']));
		
		if (!empty($fans_info)) {//已成粉丝访问
			$user_info['nickname'] = filterEmoji($user_info['nickname']);
			if (!empty($user_info['nickname']) && $user_info['nickname'] != $fans_info['nickname']) {
				$model_fans->edit(array('unionid' => $user_info['unionid']) , array('nickname' => $user_info['nickname']));
			}
			if (empty($fans_info['headimg'])) {
				$headimgurl = $user_info['headimgurl'];
                if (!empty($headimgurl)) {
					$model_fans->edit(array('unionid' => $user_info['unionid']) , array('headimg' => $headimgurl));
				}
				//弥补扫码关注公众号后的提醒
				if ($fans_info['inviter_id'] > 0) {
					$access_token = logic('weixin_token')->get_access_token(config());
					$flag = logic('weixin_message')->addmember($access_token, config(), config(), $user_info['nickname'], $fans_info['inviter_id'], 'ercode');
				}
			}
			$return = array(
			    'fanid' => $fans_info['fanid'],
			);
			if (!empty($fans_info['uid'])) {
			    $model_member = model('member');
			    $member_info = $model_member->getInfo(array('uid' => $fans_info['uid']));
			    $member_update = array();
			    if (!empty($user_info['nickname']) && $user_info['nickname'] != $member_info['nickname']) {
    				$member_update['nickname'] = $user_info['nickname'];
    			}
    			if (empty($member_info['headimg'])) {
    			    logic('poster')->get_headimg(str_replace('https://', 'http://', $user_info['headimgurl']), UPLOADFILES_PATH . '/headimg/' . $member_info['uid'] . '.jpg');
    			    $member_update['headimg'] = $user_info['headimgurl'];
    			}
    			if ($member_update) {
    			    $model_member->edit(array('uid' => $member_info['uid']) , $member_update);
    			}
			    $token = $model_member->get_token($member_info['uid'], $member_info['nickname'], $user_info['unionid'], 'wap', $this->uniacid);
			    $return['key'] = $token;
			    $return['member_id'] = $member_info['uid'];
			}
			output_data($return);exit();
		} else {//未成粉丝访问
			$user_info['inviter_id'] = $ownerid;
			if ($fanid = $this->register($user_info, 'wap')) {
				$fans_info = $model_fans->getInfo(array('fanid' => $fanid));
				if ($fans_info['inviter_id'] > 0) {
					$access_token = logic('weixin_token')->get_access_token(config());
					$flag = logic('weixin_message')->addmember($access_token, config(), config(), $fans_info['nickname'], $fans_info['inviter_id'], 'link');
				}
				output_data(array('fanid' => $fans_info['fanid']));exit();
			}
		}
    }
}