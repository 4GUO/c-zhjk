<?php
namespace shop\controller;
use lib;
class poster extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function get_posterOp() {
		if(IS_API) {
			$id = input('id', 0, 'intval');
			$third_party_id = input('third_party_id', 0, 'intval');
			$member_info = $this->member_info;
			if (empty($member_info)) {
				output_error('分享者未登录，请返回首页');
			}
			if ($id) {
				$poster_info = model('poster')->where(array('id' => $id))->find();
			} else {
				$poster_info = model('poster')->where(array('cert_usable' => 1))->order('cert_sort desc')->find();
			}
			if(!$poster_info){
				output_error('海报未设置');
			}
			if (!$member_info['is_distributor']) {
				output_error('您无推广权限', array('redirect' => '/pages/index/index'));
			}
			if ($this->client_type == 'wxapp') {
				//获取二维码
				$weixin_qrcode_path = UPLOADFILES_PATH . '/qrcode/cert_type' . $poster_info['cert_type'] . '/wxapp_qrcode_' . $member_info['uid'] . '_' . $third_party_id . '.jpg';
				if (file_exists($weixin_qrcode_path)) {
					$code_img = $weixin_qrcode_path;
				} else {
					$code_img = logic('poster')->create_qrcode_wxapp($member_info['uid'], $poster_info['cert_type'], $third_party_id);
				}
		    } else if ($this->client_type == 'wap' || $this->client_type == 'wxweb' || $this->client_type == 'app') {
				$code_img = UPLOADFILES_PATH . '/qrcode/cert_type' . $poster_info['cert_type'] . '/weixin_qrcode_' . $member_info['uid'] . '_' . $third_party_id . '.jpg';
				if (!file_exists($code_img)) {
					if ($this->config['wechat_isuse']) {
						$code_img = logic('poster')->create_qrcode_weixin($member_info['uid'], $poster_info['cert_type'], $third_party_id);
					} else {
						$code_img = logic('poster')->create_qrcode_wap($member_info['uid'], $poster_info['cert_type'], $third_party_id);
					}
				}
			}
			//变
			if($poster_info['cert_type'] == 4) {
				$text_data = array( 
					'nickname' => $member_info['nickname'],
					'headimg' => UPLOADFILES_PATH . '/headimg/' . $member_info['uid'] . '.jpg',
					'ercode' => $code_img,
				);
			}
			$poster_img_file = UPLOADFILES_PATH . '/poster/poster' . $poster_info['cert_type'] . '/poster_' . $member_info['uid'] . '_' . $third_party_id . '.jpg';
			if (!file_exists($poster_img_file)) {
				$poster_img_url = $this->_regenerate($poster_info, $text_data, $poster_img_file);
				if (!$poster_img_url) {
					output_error('文件写入失败');
				}
			}
			$poster_img_url = str_replace(UPLOADFILES_PATH, UPLOADFILES_URL, $poster_img_file);
			$return = array(
				'title' => '我的推广二维码',
				'img_url' => $poster_img_url . '?time=' . time(),
			);
			output_data($return);
		}
	}
	private function _regenerate($base_data, $text_data, $save_poster) {
		//header('content-type:image/jpeg');
		if(!$base_data || !$text_data){
			return false;
		}
		
		$im = imagecreatetruecolor($base_data['cert_width'], $base_data['cert_height']);//绘制指定大小的图像(默认为黑色)
		$bg_color = imagecolorAllocate($im, 255, 255, 255);//分配一个白色
        imagefill($im, 0, 0, $bg_color);// 从左上角开始填充白色
		//文件后缀名
        $ext = pathinfo($base_data['cert_image'], PATHINFO_EXTENSION);
		if (!preg_match('/^(http:\/\/|https:\/\/).*$/', $base_data['cert_image'])) {
			$base_data['cert_image'] = SITE_URL . $base_data['cert_image'];
		}
		if($ext == 'gif') {
			$bg = imagecreatefromgif($base_data['cert_image']);
		}else if($ext == 'jpeg' || $ext == 'jpg' ) {
			$bg = imagecreatefromjpeg($base_data['cert_image']);
		}else if($ext == 'png') {
			$bg = imagecreatefrompng($base_data['cert_image']);
		}else if($ext == 'bmp') {
			$bg = imagecreatefromwbmp($base_data['cert_image']);
		}else {
			$bg = imagecreatefromstring($base_data['cert_image']);
		}
		
		imagecopy($im, $bg, 0, 0, 0, 0, $base_data['cert_width'], $base_data['cert_height']);
		imagedestroy($bg);
		
		$font_family = STATIC_PATH . '/font/' . $base_data['cert_font_family'] . '.ttf';
		$pattern = '/([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i';
		if (preg_match($pattern, $base_data['cert_font_color'], $color)) {
            $red = hexdec($color[1]);
            $green = hexdec($color[2]);
            $blue = hexdec($color[3]);
            $font_color = imagecolorallocate($im, $red, $green, $blue);
        } else {
            $font_color = imagecolorallocate($im, 0, 0, 0);
        }
		$font_size = $base_data['cert_font_size'];
		$base_data_data = fxy_unserialize($base_data['cert_data']);
		if($base_data_data){
			foreach($base_data_data as $key => $value){
				if(!empty($value['check'])){
					$text = $text_data[$key];
					$x = $value['left'];
					$y = $value['top'] + $value['height'];
					if($key == 'headimg') {
						if (file_exists($text)) {
							$src = $this->_radius_img($text);
							//画微信图像
							$src_w = intval(imagesx($src));
							$src_h = intval(imagesy($src));
							imagecopyresized($im, $src, intval($value['left']), intval($value['top']), 0, 0, intval($value['width']), intval($value['height']), $src_w, $src_h);
							imagedestroy($src);
						}
					} else if ($key == 'ercode') {
						if (file_exists($text)) {
							$src = imagecreatefromstring(file_get_contents($text));
							//画二维码
							$src_w = intval(imagesx($src));
							$src_h = intval(imagesy($src));
							imagecopyresized($im, $src, intval($value['left']), intval($value['top']), 0, 0, intval($value['width']), intval($value['height']), $src_w, $src_h);
							imagedestroy($src);
						}
					} else if ($key == 'thumb') {
						if (file_exists($text)) {
							$src = imagecreatefromstring(file_get_contents($text));
							//画缩略图
							$src_w = intval(imagesx($src));
							$src_h = intval(imagesy($src));
							imagecopyresized($im, $src, intval($value['left']), intval($value['top']), 0, 0, intval($value['width']), intval($value['height']), $src_w, $src_h);
							imagedestroy($src);
						}
					} else {
					    $font_size = intval($value['height']);
					    imagettftext($im, $font_size, 0, intval($x), intval($y), $font_color, $font_family, $text);
					}
				}
			}
		}
		if (!is_dir(dirname($save_poster))) {
			if (!mkdir(dirname($save_poster), 0755, true)) {
				lib\logging::write(var_export('创建目录失败，请检查是否有写入权限' . $save_poster, true));
				return false;
			}
		}
		imagejpeg($im, $save_poster);
        imagedestroy($im);
		return $save_poster;
	}
	/**
	 * 处理圆角图片
	 * @param  string  $imgpath 源图片路径
	 * @param  integer $radius  圆角半径长度默认为0,处理成圆型
	 * @return [type]           [description]
	 */
	private function _radius_img($imgpath, $radius = 0) {
		$upload = new lib\uploadfile();
		$ext_type = $upload->get_type($imgpath);
		$src_img = null;
		switch ($ext_type) {
			case 'jpg':
				$src_img = imagecreatefromjpeg($imgpath);
				break;
			case 'png':
				$src_img = imagecreatefrompng($imgpath);
				break;
			default: 
			    $src_img = imagecreatefromjpeg($imgpath);
			    break;
		}
		if (!$src_img) {
			$imgpath = STATIC_PATH . '/shop/img/default_user.png';
			$src_img = imagecreatefrompng($imgpath);
		}
		$wh = getimagesize($imgpath);
		$w  = $wh[0];
		$h  = $wh[1];
		$radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
		$img = imagecreatetruecolor($w, $h);
		//这一句一定要有
		imagesavealpha($img, true);
		//拾取一个完全透明的颜色,最后一个参数127为全透明
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		$r = $radius; //圆 角半径
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$rgbColor = imagecolorat($src_img, $x, $y);
				if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
					//不在四角的范围内,直接画
					imagesetpixel($img, $x, $y, $rgbColor);
				} else {
					//在四角的范围内选择画
					//上左
					$y_x = $r; //圆心X坐标
					$y_y = $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//上右
					$y_x = $w - $r; //圆心X坐标
					$y_y = $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//下左
					$y_x = $r; //圆心X坐标
					$y_y = $h - $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//下右
					$y_x = $w - $r; //圆心X坐标
					$y_y = $h - $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
				}
			}
		}
		return $img;
	}
}
?>