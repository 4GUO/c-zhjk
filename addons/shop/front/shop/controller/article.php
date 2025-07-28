<?php
namespace shop\controller;
use lib;
class article extends home {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp() {
		if (IS_API) {
			$this->title = $this->config['name'];
			$class_list_tmp = model('article_class')->getList(array('uniacid' => $this->uniacid));
			$class_list = array();
			foreach($class_list_tmp['list'] as $k => $v){
				$class_list[$v['ac_id']] = $v;
			}
			$model_article = model('article');
			$where = array();
			$where['uniacid'] = $this->uniacid;
			$where['article_show'] = 1;
			$ac_id = input('ac_id', 0, 'intval');
			if ($ac_id) {
				$where['ac_id'] = $ac_id;
				$class_info = model('article_class')->where(array('ac_id' => $ac_id))->find();
				$this->title = $class_info['ac_name'] . '-' . $this->config['name'];
			}
			$type = input('type', 0, 'intval');
			if (!empty($type)) {
				if ($type == 1) {
					$this->title = '帮助中心';
				}
				if ($type == 2) {
					$this->title = '系统公告';
				}
				$where['article_type'] = $type;
			}
			$list = $model_article->getList($where, '*', 'article_id DESC', 20, input('page', 1, 'intval'));
			foreach($list['list'] as $k => $v){
				$list['list'][$k]['article_time'] = date('Y-m-d H:i', $v['article_time']);
				$list['list'][$k]['ac_name'] = $class_list[$v['ac_id']]['ac_name'];
				$list['list'][$k]['article_desc'] = !empty($v['article_desc']) ? htmlspecialchars_decode($v['article_desc']) : '';
			}
			$return = array(
				'title' => $this->title,
				'list' => $list['list'],
				'totalpage' => $list['totalpage'],
				'hasmore' => $list['hasmore'],
			);
			output_data($return);
		}
	}
	public function infoOp() {
		if (IS_API) {
			$article_id = input('article_id', 0, 'intval');
			$model_article = model('article');
			$article_info = $model_article->where(array('article_id' => $article_id, 'article_show' => 1))->find();
			if(!$article_info){
				output_error('文章不存在或已下架！');
			}
			$model_article->where(array('article_id' => $article_id, 'article_show' => 1))->update(array('article_click_num' => $article_info['article_click_num'] + 1));
			$this->title = $article_info['article_title'];
			$article_info['article_time'] = date('Y-m-d', $article_info['article_time']);
			$article_info['article_content'] = !empty($article_info['article_content']) ? htmlspecialchars_decode(htmlspecialchars_decode($article_info['article_content'])) : '';
			$return = array(
				'title' => $this->title,
				'info' => $article_info,
			);
			output_data($return);
		}
	}
	public function article_contentOp() {
		$article_id = input('article_id', 0, 'intval');
		$result = model('article')->getInfo(array('article_id' => $article_id), 'article_content');
		echo empty($result['article_content']) ? '' : htmlspecialchars_decode($result['article_content']);
	}
	public function dianzanOp() {
		$fans_list = model('fans')->getList(array('uniacid' => $this->uniacid));
		$fans_list_format = array();
		foreach($fans_list['list'] as $k => $v) {
			$v['tag'] = isset($v['tag']) ? fxy_unserialize(base64_decode($v['tag'])) : array();
			$fans_list_format[$v['fanid']] = $v;
		}
		if (IS_API) {
			$article_id = input('article_id', 0, 'intval');
			$model_article = model('article');
			$article_info = $model_article->where(array('article_id' => $article_id, 'article_show' => 1))->field('dianzan_uids')->find();
			if(!$article_info){
				output_error('文章不存在或已下架！');
			}
			$dianzan_fanids = explode(',', $article_info['dianzan_uids']);
			$type = 0;//未点赞
			$index = 0;
			if(in_array($this->member_info['fanid'], $dianzan_fanids)){
				$type = 1;
				foreach($dianzan_fanids as $k => $v){
					if($this->member_info['fanid'] == $v){
						$index = $k;
						array_splice($dianzan_fanids, $k, 1);
					}
				}
			}else{
				$type = 0;
				array_unshift($dianzan_fanids, $this->member_info['fanid']);
			}
			$model_article->where(array('article_id' => $article_id))->update(array('dianzan_uids' => implode(',', $dianzan_fanids)));
			$return = array(
				'type' => $type,
				'item' => $fans_list_format[$this->member_info['fanid']],
				'index' => $index,
			);
			output_data($return);
		}
	}
	function comment_listOp() {
		$comment_model = model('article_comment');
		$list_tmp = $comment_model->getList(array('uniacid' => $this->uniacid, 'article_id' => input('article_id', 0, 'intval')), '*', 'id desc');
		$list = array();
		foreach($list_tmp['list'] as $k => $v){
			$v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
			$list[] = $v;
		}
		output_data(array('list' => $list));
	}
	function comment_publishOp() {
		$comment_model = model('article_comment');
		$data = array();
		if(!input('content', '')){
			output_error('评论内容不能为空！');
		}
		$data['uniacid'] = $this->uniacid;
		$data['article_id'] = input('article_id', 0, 'intval');
		$data['openid'] = $this->member_info['openid'];
		$data['nickname'] = $this->member_info['tag']['nickname'];
		$data['headimgurl'] = $this->member_info['tag']['headimgurl'];
		$data['content'] = input('content', '');
		$data['add_time'] = TIMESTAMP;
		$flag = $comment_model->add($data);
		if($flag){
			$data['add_time'] = date('Y-m-d H:i:s', $data['add_time']);
			$data['is_author'] = 1;
			output_data(array('data' => $data));
		}else{
			output_error('网络错误');
		}
	}
	function comment_delOp() {
		$comment_model = model('article_comment');
		$id = input('id', 0, 'intval');
		$article_id = input('article_id', 0, 'intval');
		$info = $comment_model->getInfo(array('uniacid' => $this->uniacid, 'id' => $id));
		if(!$info){
			output_error('评论内容不存在！');
		}
		if($info['openid'] == $this->member_info['openid']){
			$flag = $comment_model->del(array('id' => $id, 'article_id' => $article_id));
			if($flag){
				output_data(true);
			}else{
				output_error('网络错误');
			}
		}else{
			output_error('无权操作');
		}
	}
}