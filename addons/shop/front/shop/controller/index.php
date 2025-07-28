<?php
namespace shop\controller;
class index extends member {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if (IS_API) {
			$article_list_temp = model('article')->getList(array('uniacid' => $this->uniacid, 'article_show' => 1, 'article_type' => 2), 'article_id,article_title', 'article_sort asc, article_id asc', 5, input('page', 1, 'intval'));
			$model_class_list = model('index_module')->where(array('uniacid' => $this->uniacid, 'status' => 1))->order('m_sort DESC')->select();
			foreach ($model_class_list as $k => $v) {
				$model_class_list[$k]['thumb'] = tomedia($v['thumb']);
			}
			$return  = array(
				'model_class_list' => $model_class_list, 
				'article_list' => $article_list_temp['list'], 
				'title' => $this->config['name'],
				'sign' => $this->sign,
			);
			
			//diy
			$uid = $this->getMemberIdIfExists();
			$member_info = model('member')->getInfo(array('uid' => $uid));
			$level_id = isset($member_info['level_id']) ? $member_info['level_id'] : 0;
			$model_mb_special = model('mb_special');
			$data = $model_mb_special->getMbSpecialIndex($level_id);
			$return['diy_data'] = $data;
			output_data($return);
		}
	}
	/**
     * 专题
     */
    public function specialOp() {
        $model_mb_special = model('mb_special');
        $special_id = input('special_id', 0, 'intval');
        $info = $model_mb_special->where(array('special_id' => $special_id))->find();
        $data = $model_mb_special->getMbSpecialItemUsableListByID($special_id);
        $return['info'] = $info;
        $return['diy_data'] = $data;
        output_data($return);
    }
	public function logoutOp() {
		if (IS_API) {
			$model_mb_user_token = model('mb_user_token');
			$uid = $this->getMemberIdIfExists();
			if ($uid) {
				$condition = array();
				$condition['member_id'] = $uid;
				$condition['client_type'] = $this->client_type;
				$model_mb_user_token->delMbUserToken($condition);
				output_data('1');
			} else {
				//setcookie('key', input('key', ''), time() - 3600 * 24, '/');
				//output_data('1');
				output_error('参数错误');
			}  
		}
	}
}