<?php
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class poster
{
	/*手机版二维码*/
	public function create_qrcode_wap($member_id, $cert_type, $third_party_id) {
		$file_dir = UPLOADFILES_PATH . '/qrcode/cert_type' . $cert_type . '/';
		$file_name = 'wap_qrcode_' . $member_id . '_' . $third_party_id . '.png';
        require_once COMMON_PATH . '/vendor/phpqrcode/index.php';
        $PhpQRCode = new \PhpQRCode();
        $PhpQRCode->set('pngTempDir', $file_dir);
		$PhpQRCode->set('matrixPointSize', 12);
		if ($cert_type == 4) {
		    $qrcode_url = uni_url('/pages/login/reg', array('oid' => $member_id), true);
		} else if ($cert_type == 8) {
		    $qrcode_url = uni_url('/pages/chat/chat', array('inviter_id' => $member_id, 'room_id' => $third_party_id), true);
		}
        $PhpQRCode->set('date', $qrcode_url);
        $PhpQRCode->set('pngTempName', $file_name);
        $PhpQRCode->init();
		return $file_dir . $file_name;
	}
	/*微信版二维码*/
	public function create_qrcode_weixin($member_id, $cert_type, $third_party_id){
		$file_path = UPLOADFILES_PATH . '/qrcode/cert_type' . $cert_type . '/weixin_qrcode_' . $member_id . '_' . $third_party_id . '.jpg';
		//lib\logging::write(var_export($file_path, true));
        $access_token = logic('weixin_token')->get_access_token(config());
		$weixin_config = model('wechat')->getInfoOne('weixin_wechat', '', 'wechat_appid,wechat_appsecret');
		$wechat = new lib\wxSDK\WechatAuth($weixin_config['wechat_appid'], $weixin_config['wechat_appsecret'], $access_token);
		if (!file_exists($file_path)) {
			$data = $wechat->qrcodeCreate($member_id);
			if (!empty($data['ticket'])) {
				$img_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($data['ticket']);
				$flag = $this->downloadImageFromWeiXin($img_url, $file_path);
				if (file_exists($file_path)) {
					$resizeImage = new lib\resizeimage();
					$resizeImage->newImg($file_path, 370, 370, 1, '.', dirname($file_path), false);
				}
			}
		}
		return $file_path;
	}
	/*小程序版二维码*/
	public function create_qrcode_wxapp($member_id, $cert_type, $third_party_id){
		$file_path = UPLOADFILES_PATH . '/qrcode/cert_type' . $cert_type . '/wxapp_qrcode_' . $member_id . '_' . $third_party_id . '.jpg';
		//变
		if($cert_type == 1) {
			$path = uni_url('/pages/goods/goods_info', array('goods_id' => $third_party_id, 'type' => 'share'));
		} else if($cert_type == 2) {
			$path = uni_url('/pages/article/info', array('article_id' => $third_party_id, 'type' => 'share'));
		} else if($cert_type == 4) {
			$path = uni_url('/pages/login/index', array('oid' => $member_id, 'type' => 'share'));
		} else if($cert_type == 8) {
			$path = uni_url('/pages/chat/chat', array('inviter_id' => $member_id, 'room_id' => $third_party_id));
		}
		$curl = new lib\curl();
        $access_token = get_wxapp_access_token(config('uniacid'), config('wxappid'), config('wxappsecret'));
		$post_data = array(
			'path' => $path,
			'width' => 240,
		);
		$img_url = 'https://api.weixin.qq.com/wxa/getwxacode?access_token=' . $access_token;
		$flag = $this->downloadImageFromWeiXinApp($img_url, $file_path, $post_data);
		if($flag){
			return $file_path;
		}
		return $flag;
	}
	//下载公众号二维码
	private function downloadImageFromWeiXin($url, $filename){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$file = curl_exec($ch);
		curl_close($ch);
		$flag = true;
		if (!is_dir(dirname($filename))) {
			if (!mkdir(dirname($filename), 0755, true)) {
				output_error('创建目录失败，请检查是否有写入权限' . $filename);
			}
		}
			
        $write = fopen ( $filename, 'w' );
        if ($write == false) {
            $flag = false;
        }
        if (fwrite ( $write, $file ) == false) {
            $flag = false;
        }
        if (fclose ( $write ) == false) {
            $flag = false;
        }
		return $flag;
	}
	//下载小程序二维码
	private function downloadImageFromWeiXinApp($url, $file_path, $postdata = array()){
       	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata, JSON_UNESCAPED_UNICODE));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$file = curl_exec($ch);
		curl_close($ch);
		$flag = true;
		if (!is_dir(dirname($file_path))) {
			if (!mkdir(dirname($file_path), 0755, true)) {
				output_error('创建目录失败，请检查是否有写入权限' . $file_path);
			}
		}
        $write = fopen ( $file_path, 'w' );
        if ($write == false) {
            $flag = false;
        }
        if (fwrite ( $write, $file ) == false) {
            $flag = false;
        }
        if (fclose ( $write ) == false) {
            $flag = false;
        }
		return $flag;
	}
	//下载微信头像
	public function get_headimg($url, $file_path) {
		if (is_file($file_path) && file_exists($file_path)) {
			return true;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$file = curl_exec($ch);
		curl_close($ch);
		if (!is_dir(dirname($file_path))) {
			if (!mkdir(dirname($file_path), 0755, true)) {
				output_error('创建目录失败，请检查是否有写入权限' . $file_path);
			}
		}
		$write = fopen ($file_path, 'w');
		if ($write == false) {
			return false;
		}
		if (fwrite ( $write, $file ) == false) {
			return false;
		}
		if (fclose ( $write ) == false) {
			return false;
		}
		return true;
	}
}