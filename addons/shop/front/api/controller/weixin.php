<?php
namespace api\controller;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class weixin extends control
{
	protected $wechatsn = '';
	protected $rsconfig = array();
	public function __construct() {
		parent::_initialize();
		$wsn = input('get.wsn', '', 'trim');
		if (!empty($wsn)) {
			$this->wechatsn = $wsn;
		} else {
			exit('参数错误');
		}
		$this->rsconfig = model('wechat')->getInfoOne('weixin_wechat', array('wechat_sn' => $this->wechatsn, 'uniacid' => $this->uniacid));
	}
	public function indexOp() {
		//调试
        try {
            $appid = isset($this->rsconfig['wechat_appid']) ? $this->rsconfig['wechat_appid'] : ''; //AppID(应用ID)
            $token = isset($this->rsconfig['wechat_token']) ? $this->rsconfig['wechat_token'] : ''; //微信后台填写的TOKEN
            $crypt = isset($this->rsconfig['wechat_encoding']) ? $this->rsconfig['wechat_encoding'] : ''; //消息加密KEY（EncodingAESKey）
            
            /* 加载微信SDK */
            $wechat = new lib\wxSDK\Wechat($token, $appid, $crypt, $this->uniacid);
            
            /* 获取请求信息 */
            $data = $wechat->request();

            if ($data && is_array($data)) {
                /**
                 * 你可以在这里分析数据，决定要返回给用户什么样的信息
                 * 接受到的信息类型有10种，分别使用下面10个常量标识
                 * lib\wxSDK\Wechat::MSG_TYPE_TEXT       //文本消息
                 * lib\wxSDK\Wechat::MSG_TYPE_IMAGE      //图片消息
                 * lib\wxSDK\Wechat::MSG_TYPE_VOICE      //音频消息
                 * lib\wxSDK\Wechat::MSG_TYPE_VIDEO      //视频消息
                 * lib\wxSDK\Wechat::MSG_TYPE_SHORTVIDEO //视频消息
                 * lib\wxSDK\Wechat::MSG_TYPE_MUSIC      //音乐消息
                 * lib\wxSDK\Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
                 * lib\wxSDK\Wechat::MSG_TYPE_LOCATION   //位置消息
                 * lib\wxSDK\Wechat::MSG_TYPE_LINK       //连接消息
                 * lib\wxSDK\Wechat::MSG_TYPE_EVENT      //事件消息
                 *
                 * 事件消息又分为下面五种
                 * lib\wxSDK\Wechat::MSG_EVENT_SUBSCRIBE    //订阅
                 * lib\wxSDK\Wechat::MSG_EVENT_UNSUBSCRIBE  //取消订阅
                 * lib\wxSDK\Wechat::MSG_EVENT_SCAN         //二维码扫描
                 * lib\wxSDK\Wechat::MSG_EVENT_LOCATION     //报告位置
                 * lib\wxSDK\Wechat::MSG_EVENT_CLICK        //菜单点击
                 */

                //记录微信推送过来的数据
                //file_put_contents(__DIR__ . '/data.json', json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);

                /* 响应当前请求(自动回复) */
                //$wechat->response($content, $type);

                /**
                 * 响应当前请求还有以下方法可以使用
                 * 具体参数格式说明请参考文档
                 * 
                 * $wechat->replyText($text); //回复文本消息
                 * $wechat->replyImage($media_id); //回复图片消息
                 * $wechat->replyVoice($media_id); //回复音频消息
                 * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
                 * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
                 * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
                 * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
                 * 
                 */
                
                //执行Demo
                $this->demo($wechat, $data);
				//$Data = array(
				//	'HTTP_RAW_POST_DATA' => json_encode($data),
				//	'CreateTime' => time()
				//);
				//model('HTTP_RAW_POST_DATA')->insert($Data);
            }
        } catch(\Exception $e) {
            file_put_contents(__DIR__ . '/error.json', var_export($e->getMessage(), true) . PHP_EOL, FILE_APPEND);
        }
	}
	/**
     * DEMO
     * @param  Object $wechat Wechat对象
     * @param  array  $data   接受到微信推送的消息
     */
    private function demo($wechat, $data){
		$model_wechat = model('wechat');
        switch ($data['MsgType']) {
            case lib\wxSDK\Wechat::MSG_TYPE_EVENT:
                switch ($data['Event']) {
                    case lib\wxSDK\Wechat::MSG_EVENT_SUBSCRIBE:
						$rsReply = $model_wechat->getInfoOne('weixin_attention', array('uniacid' => $this->uniacid));
						if ($rsReply) {
							if($rsReply['reply_msgtype']){
								$array = $this->get_material($rsReply['reply_materialid']);
								$wechat->replyNews($array);
							}else{
								$contentStr=$rsReply['reply_textcontents'];
							}
						} else {
							$contentStr = 'I have not decided yet';
						}
                        if (isset($contentStr)) {
							$wechat->replyText($contentStr);
						}
						
						//会员处理
						if (!empty($data['EventKey'])) {//扫码
							$ownerid = intval(str_replace('qrscene_', '', $data['EventKey']));
						} else {
							$ownerid = 0;
						}
						$user_info = $this->qrscene_register($data['FromUserName'], $ownerid);
						//注册成功
						if (!empty($user_info['fanid'])) {	
							model('fans')->edit(array('fanid' => $user_info['fanid']), array('follow' => 1));
						}
						exit;
                        break;

                    case lib\wxSDK\Wechat::MSG_EVENT_UNSUBSCRIBE:
                        //取消关注
						$fans_info = model('fans')->getInfo(array('unionid' => $data['FromUserName'], 'uniacid' => $this->uniacid));
						if (!empty($fans_info)) {
							model('fans')->edit(array('fanid' => $fans_info['fanid']), array('follow' => 0));
						}
						exit;
                        break;
					case lib\wxSDK\Wechat::MSG_EVENT_SCAN://二维码扫描
					    
					    break;
					case lib\wxSDK\Wechat::MSG_EVENT_LOCATION://报告位置
					    
					    break;
					case lib\wxSDK\Wechat::MSG_EVENT_CLICK://菜单点击
					    $EventKey = explode('_', $data['EventKey']);
						if ($EventKey[0] == 'MenuID') {
							$rsMenu = $model_wechat->getInfoOne('weixin_menu_detail', array('detail_id' => $EventKey[1], 'uniacid' => $this->uniacid), 'detail_textcontents');
							if($rsMenu){
								$contentStr = $rsMenu['detail_textcontents'];
							}
						} elseif ($EventKey[0] == 'MaterialID') {
							$array = $this->get_material($EventKey[1]);
							$wechat->replyNews($array);
						} elseif ($EventKey[0] == 'changwenben') {
							$rsMenu = $model_wechat->getInfoOne('weixin_menu_detail', array('detail_id' => $EventKey[1], 'uniacid' => $this->uniacid), 'detail_textcontents');
							if ($rsMenu) {
								$contentStr = $rsMenu['detail_textcontents'];
							} else {
								$contentStr = $EventKey[0];
							}
						} else {
							$contentStr = $EventKey[0];
						}
						if (isset($contentStr)) {
							$wechat->replyText($contentStr);
						}
						exit;
					    break;
					case lib\wxSDK\Wechat::MSG_EVENT_VIEW:// ...
					    
					    break;
                    default:
                        $wechat->replyText('您的事件类型：' . $data['Event'] . '，EventKey：' . (isset($data['EventKey']) ? $data['EventKey'] : ''));
						exit;
                        break;
                }
                break;

            case lib\wxSDK\Wechat::MSG_TYPE_TEXT://关键词自动回复
                if (empty($data['Content'])) {
					$contentStr = '请说些什么...';
				} else {
					$rsReply = model()->table('weixin_reply')->field('reply_id, reply_msgtype,reply_textcontents,reply_materialid')->where(array('reply_patternmethod' => 0, 'reply_keywords' => '%|' . $data['Content'] . '|%', 'uniacid' => $this->uniacid))->order('reply_id desc')->find();
					//file_put_contents(__DIR__ . '/data.json', json_encode($rsReply, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
					if (empty($rsReply['reply_id'])) {
						$rsReply = model()->table('weixin_reply')->field('reply_id,reply_msgtype,reply_textcontents,reply_materialid')->where(array('reply_patternmethod' => 1, 'reply_keywords' => '%' . $data['Content'] . '%', 'uniacid' => $this->uniacid))->order('reply_id desc')->find();
						if (empty($rsReply['reply_id'])) {
							$rsReply = model()->table('weixin_attention')->field('reply_msgtype,reply_textcontents,reply_materialid')->where(array('reply_subscribe' => 1, 'uniacid' => $this->uniacid))->find();
						}
					}
					//file_put_contents(__DIR__ . '/data.json', json_encode($rsReply, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
					if ($rsReply) {
						if ($rsReply['reply_msgtype']) {
							$array = $this->get_material($rsReply['reply_materialid']);
                            $wechat->replyNews($array);
						} else {
							$contentStr = $rsReply['reply_textcontents']; 
						}
					} else {
						$contentStr = 'I have not decided yet';
					}
				}
				if (isset($contentStr)) {
					$wechat->replyText($contentStr);
				}
				exit;
                break;
            
            default:
                //other code
                break;
        }
    }
	private function get_material($id) {
		$rsMaterial = model('weixin_material')->field('material_type,material_content')->where(array('material_id' => $id, 'uniacid' => $this->uniacid))->find();
		$Material_Json = fxy_unserialize($rsMaterial['material_content']);
		$array = array();
		foreach($Material_Json as $key => $value) {
		    $preg_match = '/^http|^https/is';
            if (!preg_match($preg_match, $value['Url'])) {
                $value['Url'] = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $value['Url'];
            }
			$array[] = array(
				$value['Title'],
				preg_replace('/<br\\s*?\/??>/i', chr(13), $value['TextContents']),
				$value['Url'],
				tomedia($value['ImgPath'])
			);
		}
		return $array;
	}
	
	private function qrscene_register($openid, $ownerid = 0)
    {
		$model_fans = model('fans');
		$fans_info = $model_fans->getInfo(array('unionid' => $openid, 'uniacid' => $this->uniacid));
		if (empty($fans_info)) {
			$nickname = 'name_' . rand(100, 899);
			$fans = array();
			$fans['nickname'] = $nickname;
			$fans['inviter_id'] = $ownerid;
			$fans['unionid'] = $openid;
			$fans['openid'] = $openid;
			$fans['headimgurl'] = '';
			$fanid = $this->register($fans, 'wap');
			
			//异步更新会员微信相关信息
			$postdata = array(
				'uniacid' => $this->uniacid,
				'config' => $this->rsconfig,
				'openid' => $openid,
				'fanid' => $fanid,
				'ownerid' => $ownerid,
				'insert' => true,
			);
			asynRun('weixin_userinfo/subscribe', $postdata);
			
			return array('register' => 1, 'fanid' => $fanid);
		} else {
			if (empty($fans_info['headimg'])) {
				$postdata = array(
					'uniacid' => $this->uniacid,
					'config' => $this->rsconfig,
					'openid' => $fans_info['unionid'],
					'fanid' => $fans_info['fanid'],
					'ownerid' => $ownerid,
					'insert' => false,
				);
				asynRun('weixin_userinfo/subscribe', $postdata);
			}
			return array('register' => 0, 'fanid' => $fans_info['fanid']);
		}
    }
}