<?php
namespace logic;
use base;
use lib;
class weixin_userinfo {
	//根据openid获取微信图像和昵称
    public function subscribe($postdata) {
		$config = $postdata['config'];
        $openid = $postdata['openid'];
        $fanid = $postdata['fanid'];
        $ownerid = $postdata['ownerid'];
		$insert = $postdata['insert'];
        $access_token = logic('weixin_token')->get_access_token($config);
        if (!empty($access_token) && $fanid) {
            //获取用户信息
            $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $openid . '&lang=zh_CN';
            $curl = new lib\curl();
            $weixin_info = $curl->curl_get($url);
            lib\logging::write(var_export($weixin_info, true));
            if (empty($weixin_info['errcode'])) {
                $model_fans = model('fans');
				if ($weixin_info['headimgurl']) {
					$fans_data['headimg'] = $headimgurl;
				}
				$weixin_info['nickname'] = filterEmoji($weixin_info['nickname']);
                if ($weixin_info['nickname']) {
                    $fans_data['nickname'] = $weixin_info['nickname'];
                }
                if (!empty($fans_data)) {
					$fans_data['updatetime'] = TIMESTAMP;
					$fans_data['nickname'] = $tag['nickname'] = $fans_data['nickname'];
					$tag['headimgurl'] = $fans_data['headimg'];
					$fans_data['tag'] = base64_encode(serialize($tag));
					model('fans')->edit(array('fanid' => $fanid), $fans_data);
                }
                //发送微信短信
				if ($insert == true) {
					$flag = logic('weixin_message')->addmemberself($access_token, config(), $config, $weixin_info['nickname'], $openid, $fanid);
					if ($ownerid > 0) {
						$flag = logic('weixin_message')->addmember($access_token, config(), $config, $weixin_info['nickname'], $ownerid);
					}
				}	
            }
        }
		return true;
	}
}