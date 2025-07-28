<?php
namespace api\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class wxapp extends control
{
	public function __construct() {
		parent::_initialize();
	}
	public function login_infoOp() {
        $code = input('code', '', 'trim');
		$appid = config('wxappid');
		$appsecret = config('wxappsecret');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
		$open_str = $this->httpGet($url);
        $data = json_decode($open_str, true);
		if (!empty($data['errcode'])) {
			output_error($data['errmsg']);
		}
        $expires_time = time() + (isset($data['expires_in']) ? $data['expires_in'] : 0);
		$openid = isset($data['unionid']) ? $data['unionid'] : $data['openid'];
		$token = md5($openid);//用于login_do获取缓存信息
        $token_data = array();
        $token_data['wepro_openid_' . $token] = $data['openid'];
        $token_data['wepro_expires_time_' . $token] = $expires_time;
        $token_data['wepro_session_key_' . $token] = $data['session_key'];
        $token_data['wepro_unionid_' . $token] = isset($data['unionid']) ? $data['unionid'] : '';
        vkcache('wepro_' . $token, $token_data);
        $return = array(
            'token' => $token,
            'openid' => $data['openid']
        );
        output_data($return);
    }
	public function login_doOp() {
        $model_fans = model('fans');
        $token = input('wxtoken', '', 'trim');
        $nickName = input('nickName', '', 'trim');
        $avatarUrl = input('avatarUrl', '', 'trim');
        $oid = input('oid', 0, 'intval');
        $caceh_data = vkcache('wepro_' . $token);
        $openid = $caceh_data['wepro_openid_' . $token];
        $expires_time = $caceh_data['wepro_expires_time_' . $token];
        $session_key = $caceh_data['wepro_session_key_' . $token];
        $unionid = $caceh_data['wepro_unionid_' . $token];
        $orign_nickname = $nickName;
        $nickName = filterEmoji($nickName);
        $nickName = trim($nickName);
        if (empty($openid)) {
            output_error('信息失败');
        }
		$openid = !empty($unionid) ? $unionid : $openid;
        $fans_info = $model_fans->where(array('openid' => $openid))->find();
        if (!empty($fans_info)) {
			$model_fans->edit(array('fanid' => $fans_info['fanid']), array('session_key' => $session_key, 'expires_time' => $expires_time, 'nickname' => $nickName, 'headimg' => $avatarUrl));
			$return = array(
				'fanid' => $fans_info['fanid'],
			);
			if (!empty($fans_info['uid'])) {
			    model('member')->edit(array('uid' => $fans_info['uid']), array('nickname' => $nickName, 'headimg' => $avatarUrl));
			    $member_info = model('member')->getInfo(array('uid' => $fans_info['uid']));
			    $key = model('member')->get_token($member_info['uid'], $member_info['nickname'], $openid, 'wxapp', $this->uniacid);
    			$return['key'] = $key;
    			$return['member_id'] = $member_info['uid'];
			}
			output_data($return);
        } else {
            $response = array(
				'oid' => $oid,
				'nickname' => $nickName,
				'openid' => $openid,
				'headimgurl' => $avatarUrl,
			);
			$result = $model_fans->register($response, 'wap', $this->uniacid);
			if (!$result['state']) {
				output_error($result['msg']);
			} else {
				$fanid = $result['data']['fanid'];
			}
			if (!empty($fanid)) {
				$model_fans->edit(array('fanid' => $fanid), array('session_key' => $session_key, 'expires_time' => $expires_time));
				$return = array(
					'fanid' => $fanid,
				);
				output_data($return);
			}
			output_error('注册失败');
        }
    }
	//手机号注册账号
	public function get_phone_numberOp() {
		$model_member = model('member');
        $iv = input('iv', '', 'trim');
        $encryptedData = input('encryptedData', '', 'trim');
		$fanid = input('get.fanid', 0, 'intval') ? input('get.fanid', 0, 'intval') : input('post.fanid', 0, 'intval');
		$fans_info = model('fans')->where(array('fanid' => $fanid))->find();
        $res = $this->decryptData($encryptedData, $iv, $fans_info['session_key']);
        $phoneNumber = $res->phoneNumber;
        if ($phoneNumber) {
			$member_info = $model_member->getInfo(array('mobile' => $phoneNumber, 'uniacid' => $this->uniacid));
			if ($member_info) {
				$update_member_data = array(
					'weixin_unionid' => !empty($fans_info['unionid']) ? $fans_info['unionid'] : $fans_info['openid'],
					'openid' => $fans_info['openid'],
					'headimg' => $fans_info['headimg'],
					'nickname' => $fans_info['nickname'],
				);
			    $model_member->where(array('uid' => $member_info['uid']))->update($update_member_data);
				$update_fans_data = array(
					'uid' => $member_info['uid'],
				);
				model('fans')->where(array('fanid' => $fanid))->update($update_fans_data);
				$token = $model_member->get_token($member_info['uid'], $fans_info['nickname'], $fans_info['openid'], $this->client_type, $this->uniacid);
				$logindata = array('key' => $token, 'member_id' => $member_info['uid']);
				output_data($logindata);
			}
            $response = array(
    			'password' => '',
    			'mobile' => $phoneNumber,
    			'oid' => $fans_info['inviter_id'],
    			'fanid' => $fans_info['fanid'],
    		);
    		$result = $model_member->register($response, 'wxapp', $this->uniacid);
    		if (!$result['state']) {
    			output_error($result['msg']);
    		} else {
    			$uid = $result['data']['uid'];
    		}
			$token = $model_member->get_token($uid, $fans_info['nickname'], $fans_info['openid'], $this->client_type, $this->uniacid);
			$logindata = array('key' => $token, 'member_id' => $uid);
			output_data($logindata);
        } else {
			output_error('获取失败[' . $res . ']，请手动输入手机号');
        }
    }
	// 小程序解密
    private function decryptData($encryptedData, $iv, $session_key) {
		$wepro_appid = config('wxappid');
        if (strlen($session_key) != 24) {
            return -41001;
        }
        $aesKey = base64_decode($session_key);
        if (strlen($iv) != 24) {
            return -41002;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, 'AES-128-CBC', $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return -41003;
        }
        if ($dataObj->watermark->appid != $wepro_appid) {
            return -41003;
        }
        return $dataObj;
    }
}