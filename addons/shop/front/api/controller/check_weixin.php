<?php
/**
 * 微信相关接口功能
 *
 */
namespace api\controller;
use core;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class check_weixin extends control {
    public function __construct() {
        parent::_initialize();
    }
    public function indexOp() {
		$ref = input('post.ref', '', 'urldecode');
        $wechat_isuse = config('wechat_isuse');
        if (empty($wechat_isuse)) {
            output_error(0);
        } else {
            $relogin = false;
            $fanid = input('fanid', 0, 'intval');
            if ($fanid > 0) {
                $check_clear = model('fans')->where(array('fanid' => $fanid))->find();
                if (!$check_clear) {
                    $relogin = true;
                }
            }
            output_data(array('sign' => $this->sign, 'appid' => config('wechat_appid'), 'relogin' => $relogin));
        }
    }
    public function weixin_jssdkOp() {
		$wechat_isuse = config('wechat_isuse');
		$ref = input('ref', '', 'trim');
		$ownerid = input('oid', 0, 'intval');
        if (empty($wechat_isuse)) {
            //output_error('微信未开启');
        } elseif (empty($ref)) {
            output_error('ref参数错误');
        } else {
            $share_config = logic('weixin_jssdk')->jssdk_get_signature(urldecode($ref), $this->uniacid);
            //lib\logging::write(var_export($share_config, true));
            $share_config['link'] = logic('deal_url')->connect_url($ownerid, urldecode($ref));
				
			$model_wechat = model('wechat');
			$wechat_info = $model_wechat->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid), 'wechat_share_title,wechat_share_logo,wechat_share_desc');
			if (strpos(urldecode($ref), 'article/info') !== false) {
				$pageinfo = logic('deal_url')->split_url(urldecode($ref));
				$article_id = isset($pageinfo['requesturi']['article_id']) ? $pageinfo['requesturi']['article_id'] : 0;
				$article_info = model('article')->where(array('article_id' => $article_id))->find();
				$share_config['title'] = !empty($article_info['article_title']) ? $article_info['article_title'] : htmlspecialchars_decode($wechat_info['wechat_share_title'], ENT_QUOTES);
				$share_config['desc'] = !empty($article_info['article_desc']) ? $article_info['article_desc'] : htmlspecialchars_decode($wechat_info['wechat_share_desc'], ENT_QUOTES);
				$share_config['img_url'] = !empty($article_info['article_thumb']) ? tomedia($article_info['article_thumb']) : tomedia($wechat_info['wechat_share_logo']);
			} else if (strpos(urldecode($ref), 'goods/goods_info') !== false) {
				$pageinfo = logic('deal_url')->split_url(urldecode($ref));
				$goods_id = $pageinfo['requesturi']['goods_id'];
				$goods_info = model('shop_goods')->where(array('goods_id' => $goods_id))->find();
				$share_config['title'] = !empty($goods_info['goods_name']) ? $goods_info['goods_name'] : htmlspecialchars_decode($wechat_info['wechat_share_title'], ENT_QUOTES);
				$share_config['desc'] = !empty($goods_info['goods_name']) ? $goods_info['goods_name'] : htmlspecialchars_decode($wechat_info['wechat_share_desc'], ENT_QUOTES);
				$share_config['img_url'] = !empty($goods_info['goods_image']) ? tomedia($goods_info['goods_image']) : tomedia($wechat_info['wechat_share_logo']);
			} else {
				$share_config['title'] = empty($wechat_info['wechat_share_title']) ? '' : htmlspecialchars_decode($wechat_info['wechat_share_title'], ENT_QUOTES);
				$share_config['desc'] = empty($wechat_info['wechat_share_desc']) ? '' : htmlspecialchars_decode($wechat_info['wechat_share_desc'], ENT_QUOTES);
				$share_config['img_url'] = empty($wechat_info['wechat_share_logo']) ? '' : tomedia($wechat_info['wechat_share_logo']);
			}
			$share_config['sign'] = $this->sign;
            output_data($share_config);
        }
    }
	public function weixin_getlocationOp() {
		$ak = 'XLGBZ-GPTKU-BRVVS-BXBSX-YFGE2-VEFL5';
		$latitude = input('post.latitude', 0, 'trim');
		$longitude = input('post.longitude', 0, 'trim');
		$location = $latitude . ',' . $longitude;
		$url = 'https://apis.map.qq.com/ws/geocoder/v1/?location=' . $location . '&key=' . $ak;
		$data = file_get_contents($url);
		$data = json_decode($data, true);
		if (!empty($data) && $data['status'] == 0) {
			$result['city'] = $data['result']['address_component']['city'];
			$result['district'] = $data['result']['address_component']['district'] ?: '全部地区';
			$result['street_number'] = $data['result']['address_component']['street_number'];
			$city_info = model('area')->getInfo(array('area_name' => $result['city']));
			$district_info = model('area')->getInfo(array('area_name' => $result['district']));
			$result['city_id'] = isset($city_info['area_id']) ? $city_info['area_id'] : 0;
			$result['district_id'] = isset($district_info['area_id']) ? $district_info['area_id'] : 0;
			output_data($result);
		} else {
			output_error($data['message']);
		}
	}
	public function share_successOp() {
		output_data('1');
		$share_link = input('share_link', '', 'trim');
		$shar_type = input('shar_type', '', 'trim');
		$share_uid = input('share_uid', 0, 'intval');
		$data = array(
			'uniacid' => $this->uniacid,
			'share_link' => $share_link,
			'shar_type' => $shar_type,
			'share_uid' => $share_uid,
			'add_time' => time(),
		);
		lib\logging::write(var_export($data, true));
		model('wechat')->addInfo('weixin_share_log', $data);
		output_data('1');
	}
}