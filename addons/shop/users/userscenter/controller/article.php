<?php
namespace userscenter\controller;
use lib;
class article extends control {
	public function __construct() {
		parent::_initialize();
	}
	public function indexOp(){
		$model_article = model('article');
		$where = array();
        $where['uniacid'] = $this->uniacid;
		$ac_id = input('get.ac_id', 0, 'intval');
        if ($ac_id) {
            $where['ac_id'] = $ac_id;
        }
		$keyword = input('get.keyword', '');
        if ($keyword) {
            $where['article_title'] = '%' . trim($keyword) . '%';
        }
        $list = $model_article->getList($where, '*', 'article_id DESC', 10, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'ac_id' => $ac_id, 'keyword' => $keyword), users_url('article/index')));
        $class_list_tmp = model('article_class')->getList(array('uniacid' => $this->uniacid));
		$class_list = array();
		foreach($class_list_tmp['list'] as $k => $v){
			$class_list[$v['ac_id']] = $v;
		}
		$this->assign('class_list', $class_list);
		$this->assign('list', $list['list']);
		$this->display();
	}
	public function publishOp() {
		$model_article = model('article');
		if (chksubmit()) {
			// 验证表单
            $obj_validate = new lib\validate();
            $obj_validate->validateparam = array(array('input' => input('ac_id', ''), 'require' => 'true', 'message' => '请选择分类'), array('input' => input('article_title', ''), 'require' => 'true', 'message' => '文章标题不能为空'));
			$error = $obj_validate->validate();
            if ($error != '') {
                output_error($error);
            }
			$get_article_id = input('id', 0, 'intval');
			$class = model('article_class')->where(array('ac_id' => input('ac_id', 0, 'intval')))->find();
			$common_array = array();
			$common_array['article_title'] = input('article_title', '');
			$common_array['ac_id'] = input('ac_id', 0, 'intval');
			$common_array['ac_name'] = $class['ac_name'];
			$common_array['article_show'] = input('article_show', 0, 'intval');
			$common_array['article_url'] = input('article_url', '');
			$common_array['article_sort'] = input('article_sort', 1);
			$common_array['article_desc'] = input('article_desc', '');
			$common_array['article_content'] = input('article_content', '');
			$common_array['article_buy'] = input('article_buy', 0, 'intval');
			$common_array['article_author'] = input('article_author', '');
			$common_array['article_keyworlds'] = input('article_keyworlds', '');
			$common_array['article_thumb'] = input('thumb', '');
			$common_array['article_type'] = input('article_type', 0);
			if ($get_article_id) {
				$article_id = $get_article_id;
				$model_article->where(array('uniacid' => $this->uniacid, 'article_id' => $article_id))->update($common_array);
			} else {
				$common_array['article_time'] = TIMESTAMP;
				$common_array['uniacid'] = $this->uniacid;
				$article_id = $model_article->insert($common_array); // 保存数据
			}
			if ($article_id) {
				output_data(array('msg' => '操作成功', 'url' => users_url('article/index')));
			} else {
				output_error('操作失败！');
			}
		} else {
			//分类
			$class_list = model('article_class')->getList(array('uniacid' => $this->uniacid));
			$this->assign('class_list', $class_list['list']);
			$article_id = input('id', 0, 'intval');
			if($article_id){
				$article_info = $model_article->where(array('article_id' => $article_id))->find();
				$this->assign('article_info', $article_info);
			}
			$this->display();
		}
	}
	public function delOp(){
		$model_class = model('article');
		$id_array = explode(',', input('get.id', ''));
		foreach ($id_array as $key => $val) {
			if (!is_numeric($val)) {
				unset($id_array[$key]);
			}
		}
		$where = array();
		$where['article_id'] = $id_array;
		$where['uniacid'] = $this->uniacid;
		$state = $model_class->where($where)->delete();
        if ($state) {
            output_data(array('url' => users_url('article/index')));
        } else {
			output_error('删除失败！');
        }
    }
	public function contentOp(){
		$model = model('fxy_web_content');
		$content = $model->where(array('uniacid' => $this->uniacid))->find();
		if(IS_API){
			$data = array(
				'about_company' => input('about_company', ''),
				'company_rongyu' => input('company_rongyu', ''),
			);
			if($content){
				$model->where(array('uniacid' => $this->uniacid))->update($data);
			}else{
				$data['uniacid'] = $this->uniacid;
				$model->insert($data);
			}
			output_data(array('msg' => '操作成功', 'url' => users_url('article/content')));
		}
		
		$this->assign('content', $content);
		$this->display();
	}
	public function selectArticleOp() {
		if (IS_API) {
			$model_article = model('article');
			$where['uniacid'] = $this->uniacid;
			$goods_title = input('goods_title', '');
			if ($goods_title) {
				$where['goods_title'] = '%' . $goods_title . '%';
			}
			$result = $model_article->getList($where, '*', 'article_id DESC', 20, input('page', 1, 'intval'));
			$list = array();
			foreach($result['list'] as $k => $v) {
				$v['link'] = uni_url('/pages/article/info', array('article_id' => $v['article_id']));
				$list[] = $v;
			}
			output_data(array('list' => $list, 'totalpage' => $result['totalpage'], 'page_html' => page($result['totalpage'], array('page' => input('get.page', 1, 'intval')), users_url('article/selectArticle'), true)));
		}
	}
}