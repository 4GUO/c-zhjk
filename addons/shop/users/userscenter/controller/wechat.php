<?php
namespace userscenter\controller;

use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class wechat extends control
{

	public function __construct(){
		parent::_initialize();
	}

	/**
	 * 模块设置
	 */
	public function setting_manageOp() {
		$model_wechat = model('wechat');
		if (chksubmit()) {
			$isuse = input('isuse', 0, 'intval');
			$conf_array = array(
			    'wechat_isuse' => $isuse,
			);
            model('config')->edit(array('uniacid' => $this->uniacid), $conf_array);
			$wechatid = input('wid', 0, 'intval');
			if(empty($wechatid)){
				output_error('参数错误');
			}
			$update_array = array(
				'wechat_share_title' => input('sharetitle', '', 'trim'),
				'wechat_share_logo' => input('share_logo', '', 'trim'),
				'wechat_share_desc' => input('sharedesc', '', 'trim'),
				'wxappid' => input('appid', ''),
				'wxappsecret' => input('appsecret', ''),
				'wxapp_ercode' => input('wxapp_ercode', ''),
				'apiclient_cert_pem' => input('apiclient_cert_pem', ''),
				'apiclient_key_pem' => input('apiclient_key_pem', ''),
				'reward_template_id' => input('reward_template_id', ''),
			);
			
			$condition = array('wechat_id' => $wechatid);
			
			$result = $model_wechat->editInfo('weixin_wechat', $update_array, $condition);
			
			output_data(array('msg' => '保存成功', 'url' => users_url('wechat/setting_manage')));
		} else {
			$api_account = $model_wechat->getInfoOne('weixin_wechat', array('uniacid' => $this->uniacid));
			if (empty($api_account)) {
				$api_account = array(
					'uniacid' => $this->uniacid,
					'wechat_token' => strtolower(random(10)),
					'wechat_sn' => strtolower(random(10)),
					'wechat_type' => 3,
				);
				$wechat_id = $model_wechat->addInfo('weixin_wechat', $api_account);
				$api_account['wechat_id'] = $wechat_id;
			}
			$this->assign('api_account', $api_account);
			$this->display();
		}
	}

    /**
     * 接口设置
     **/
    public function api_manageOp() {
        $model_wechat = model('wechat');
		if (chksubmit()) {
			$wechatid = input('wid', 0, 'intval');
			if(empty($wechatid)){
				output_error('参数错误');
			}
			
			$update_array = array(
				'wechat_type' => input('type', 0, 'intval'),
				'wechat_appid' => input('appid', '', 'trim'),
				'wechat_appsecret' => input('appsecret', '', 'trim'),
				'wechat_name' => input('name', '', 'trim'),
				'wechat_email' => input('email', '', 'trim'),
				'wechat_preid' => input('preid', '', 'trim'),
				'wechat_account' => input('account', '', 'trim'),
				'wechat_encodingtype' => input('encodingtype', 0, 'intval'),
				'wechat_encoding' => input('encoding', '', 'trim'),
			);
			
			$condition = array('wechat_id' => $wechatid);
			
			$result = $model_wechat->editInfo('weixin_wechat', $update_array, $condition);
			if ($result) {
				output_data(array('msg' => '保存成功', 'url' => users_url('wechat/api_manage')));
			} else {
				output_error('保存失败');
			}
		} else {
			$api_account = $model_wechat->getInfoOne('weixin_wechat', '');
			if (empty($api_account)) {
				$api_account = array(
					'uniacid' => $this->uniacid,
					'wechat_token' => strtolower(random(10)),
					'wechat_sn' => strtolower(random(10)),
					'wechat_type' => 3,
					'wechat_appid' => '',
					'wechat_appsecret' => '',
					'wechat_name' => '',
					'wechat_email' => '',
					'wechat_preid' => '',
					'wechat_account' => '',
					'wechat_encodingtype' => 0,
					'wechat_encoding' => ''
				);
				$wechat_id = $model_wechat->addInfo('weixin_wechat', $api_account);
				$api_account['wechat_id'] = $wechat_id;
			}
			$this->assign('api_account', $api_account);
			$this->display();
		}
    }
	/**
     * 首次关注设置
     **/
    public function subcribe_manageOp() {
        $model_wechat = model('wechat');
		if (chksubmit()) {
			$rid = input('rid', 0, 'intval');
			if(empty($rid)){
				output_error('参数错误');
			}
			$update_array = array(
				'reply_msgtype' => input('msgtype', 0, 'intval'),
				'reply_textcontents' => input('textcontents', '', 'trim'),
				'reply_materialid' => input('materialid', 0, 'intval'),
				'reply_subscribe' => input('subscribe', 0, 'intval'),
				'reply_membernotice' => input('membernotice', 0, 'intval')
			);
			
			$condition = array('reply_id' => $rid);
			
			$result = $model_wechat->editInfo('weixin_attention', $update_array, $condition);
			if ($result){
				output_data(array('msg' => '保存成功', 'url' => users_url('wechat/subcribe_manage')));
			}else {
				output_error('保存失败');
			}
		} else {
			$attention_account = $model_wechat->getInfoOne('weixin_attention', array('uniacid' => $this->uniacid));
			if(empty($attention_account)){
				$attention_account = array(
					'uniacid' => $this->uniacid,
					'reply_msgtype' => 0,
					'reply_textcontents' => '很高兴认识你，新朋友！',
					'reply_materialid' => 0,
					'reply_subscribe' => 1,
					'reply_membernotice' => 1	
				);
				$reply_id = $model_wechat->addInfo('weixin_attention', $attention_account);
				$attention_account['reply_id'] = $reply_id;
			}
			
			$material_info = array();
			if(!empty($attention_account['reply_materialid'])){
				$material_info = $model_wechat->getInfoOne('weixin_material', array('material_id' => intval($attention_account['reply_materialid'])));
				if (!empty($material_info)){
					$material_info['items'] = fxy_unserialize($material_info['material_content']);
				}
			}
			$this->assign('material_info', $material_info);
			$this->assign('attention_account', $attention_account);
			$this->display();
		}
    }
    /**
     * 素材管理
     **/
    public function material_manageOp() {
		$model_wechat = model('wechat');
		$condition = array();
		$material_type = input('material_type', 0, 'intval');
        if (!empty($material_type)) {
            $condition['material_type'] = $material_type;
        }
        $result = $model_wechat->getInfoList('weixin_material', $condition, '*', 'material_id desc', 8, input('page', 1, 'intval'));
		$material_list = array();
		if(!empty($result['list'])){
			foreach($result['list'] as $key=>$value){
				$value['material_content'] = fxy_unserialize($value['material_content']);
				$material_list[] = $value;
			}
		}
		$this->assign('page', page($result['totalpage'], array('page' => input('get.page', 1, 'intval'), 'material_type' => $material_type), users_url('wechat/material_manage')));
		$this->assign('material_list', $material_list);
		$this->display();
    }
	
	/*
	弹框获取素材列表
	*/
	public function material_listOp() {
		$is_ajax = input('is_ajax', 0, 'intval');
		if ($is_ajax) {
			$model_wechat = model('wechat');
			
			$condition = array();
			$material_type = input('material_type', 0, 'intval');
			if (!empty($material_type)) {
				$condition['material_type'] = $material_type;
			}
			$result = $model_wechat->getInfoList('weixin_material', $condition, '*', 'material_id desc', 8, input('page', 1, 'intval'));
			$material_list = array();
			if(!empty($result['list'])){
				foreach($result['list'] as $key=>$value){
				    $value['material_addtime'] = date('Y-m-d', $value['material_addtime']);
					$value['material_content'] = fxy_unserialize($value['material_content']);
					$material_list[] = $value;
				}
			}
			output_data(array('list' => $material_list, 'totalpage' => $result['totalpage'], 'page_html' => page($result['totalpage'], array('page' => input('page', 1, 'intval')), users_url('wechat/material_list', array('material_type' => $material_type)), true)));
		} else {
			$this->view->_layout_file = 'null_layout';
			$this->display();
		}
    }
	
	/**
     * 素材编辑
     **/
    public function material_editOp() {
		$model_wechat = model('wechat');
		if (chksubmit()) {
			$ImgPath = input('ImgPath/a', array());
			if (empty($ImgPath)) {
				web_error('封面图不能为空');
			}
			$submit_content = array();
			$TextContents = input('TextContents/a', array());
			$Title = input('Title/a', array());
			$Url = input('Url/a', array());
			foreach($ImgPath as $key => $value){
				if (empty($value)) {
					continue;
				}
				$submit_content[] = array(
					'ImgPath' => trim($value),
					'Title' => trim($Title[$key]),
					'Url' => trim($Url[$key]),
					'TextContents' => trim($TextContents[$key])
				);
			}
			
			if(empty($submit_content)){
				web_error('内容不能为空');
			}
			$update_array = array();
			$update_array['material_type'] = count($submit_content) == 1 ? 1 : 2;
			$update_array['material_content'] = serialize($submit_content);
			$mid = input('mid', 0, 'intval');
			if (!empty($mid)) {
				$condition = array('material_id' => $mid);
				$result = $model_wechat->editInfo('weixin_material', $update_array, $condition);
			} else {
				$update_array['material_addtime'] = time();
				$result = $model_wechat->addInfo('weixin_material', $update_array);
			}
			if ($result) {
				//web_success(array('msg' => '保存成功', 'url' => users_url('wechat/material_manage')));
				web_success('保存成功', users_url('wechat/material_manage'));
			} else {
				web_error('保存失败');
			}
		} else {
			$mid = input('mid', 0, 'intval');
			if (!empty($mid)) {
				$material_info = $model_wechat->getInfoOne('weixin_material', array('material_id' => $mid));
				if (empty($material_info)) {
					web_error('信息不存在');
				}
				
				$material_info['items'] = fxy_unserialize($material_info['material_content']);
			} else {
				$material_info = array();
				$material_info['material_addtime'] = time();
			}
			
			$this->assign('material', $material_info);
			$this->display();
		}
    }
	
	public function material_delOp() {
		$mid = input('mid', 0, 'intval');
		if (empty($mid)) {
			output_error('参数错误');
		}
		
		if ($mid > 0) {
			$model_wechat = model('wechat');
			$condition = array('material_id' => $mid);
			$material_info = $model_wechat->getInfoOne('weixin_material', $condition);
			if (empty($material_info)) {
				output_error('信息不存在');
			}
			
			$result = $model_wechat->delInfo('weixin_material', $condition);
			
			//delete images
			$material_info['items'] = fxy_unserialize($material_info['material_content']);
			foreach($material_info['items'] as $key => $value){
				//unlink(str_replace($this->_upload_img_url, $this->_upload_img_dir, $value['ImgPath']));
			}	
			output_data(array('msg' => '操作成功', 'url' => users_url('wechat/material_manage')));
		} else {
			output_error('操作失败');
		}
	}
	/*关键词列表*/
	public function keyword_manageOp(){
		$model_wechat = model('wechat');
        $type = input('type', 0, 'intval');
		$condition = array();
        if (!empty($type)) {
            $condition['reply_msgtype'] = $type - 1;
        }
		$keywords = input('keywords', '', 'trim');
		if (!empty($keywords)) {
            $condition['reply_keywords'] = array('like', '%' . $keywords . '%');
        }
		
        $reply_list = $model_wechat->getInfoList('weixin_reply', $condition, '*', 'reply_id desc', 10, input('page', 1, 'intval'));
		$this->assign('page', page($reply_list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'type' => $type, 'keywords' => $keywords), users_url('wechat/keyword_manage')));
		$this->assign('list', $reply_list['list']);
		$this->display();
	}
	
	/**
     * 关键词添加
     **/
    public function keyword_addOp() {
        $model_wechat = model('wechat');
		if (chksubmit()) {
			$keywords = input('keywords', '', 'trim');
			if (!empty($keywords)) {
				$keywords = trim($keywords, '|');
				$keywords = str_replace(array('||', '｜', '｜｜'), '|', $keywords);
			}
			
			if (empty($keywords)) {
				output_error('关键词不能为空');
			}
			
			$array = explode('|', $keywords);
			
			$keywords = array();
			foreach($array as $a) {
				$a = trim($a);
				if($a=='') continue;
				$condition['reply_keywords'] = array('like', '%|' . $a . '|%');
				$reply_info = $model_wechat->getInfoOne('weixin_reply', $condition);
				if (!empty($reply_info)) {
					output_error('关键词已存在：' . $a);
				}
				$keywords[] = $a;
			}
			
			if (empty($keywords)) {
				output_error('关键词不能为空');
			}
			
			$update_array = array(
				'reply_keywords' => '|' . implode('|', $keywords) . '|',
				'uniacid' => $this->uniacid,
				'reply_patternmethod' => input('patternmethod', 0, 'intval'),
				'reply_msgtype' => input('msgtype', 0, 'intval'),
				'reply_textcontents' => input('textcontents', '', 'trim'),
				'reply_materialid' => input('materialid', 0, 'intval'),
				'reply_addtime' => time()
			);
			
			$result = $model_wechat->addInfo('weixin_reply', $update_array);
			if ($result) {
				output_data(array('msg' => '操作成功', 'url' => users_url('wechat/keyword_manage')));
			} else {
				output_error('操作失败');
			}
		} else {
			$this->display();
		}
    }
	
	/**
     * 关键词修改
     **/
    public function keyword_editOp() {
        $model_wechat = model('wechat');		
		if (chksubmit()) {
			$rid = input('rid', 0, 'intval');
			if(empty($rid)){
				output('参数错误');
			}
			$keywords = input('keywords', '', 'trim');
			if(!empty($keywords)){
				$keywords = trim($keywords, '|');
				$keywords = str_replace(array('||', '｜', '｜｜'), '|', $keywords);
			}
			
			if (empty($keywords)) {
				output('关键词不能为空');
			}
			
			$array = explode('|', $keywords);
			$keywords = array();
			foreach ($array as $a) {
				$a = trim($a);
				if ($a == '') continue;
				$condition['reply_keywords'] = array('like', '%|' . $a . '|%');
				$condition['reply_id'] = array('neq', $rid);
				
				$reply_info = $model_wechat->getInfoOne('weixin_reply', $condition);
				if (!empty($reply_info)) {
					output_error('关键词已存在：' . $a);
				}
				$keywords[] = $a;
			}
			
			if(empty($keywords)){
				error($lang['not_info_keywords']);
			}
			
			$update_array = array(
				'reply_keywords' => '|' . implode('|', $keywords) . '|',
				'reply_patternmethod' => input('patternmethod', 0, 'intval'),
				'reply_msgtype' => input('msgtype', 0, 'intval'),
				'reply_textcontents' => input('textcontents', '', 'trim'),
				'reply_materialid' => input('materialid', 0, 'intval'),
			);
			
			$condition = array('reply_id' => $rid);
			
			$result = $model_wechat->editInfo('weixin_reply', $update_array, $condition);
			if ($result){
				output_data(array('msg' => '操作成功', 'url' => users_url('wechat/keyword_manage')));
			}else {
				output_error('操作失败');
			}
		}else{
			$reply_info = $material_info = array();
			$rid = input('rid', 0, 'intval');
			if (empty($rid)) {
				output('参数错误');
			}
			$reply_info = $model_wechat->getInfoOne('weixin_reply', array('reply_id' => $rid));
			if (empty($reply_info)) {
				output_error('内容不存在');
			}
			
			if (!empty($reply_info['reply_materialid'])) {
				$material_info = $model_wechat->getInfoOne('weixin_material', array('material_id' => intval($reply_info['reply_materialid'])));
				if (!empty($material_info)) {
					$material_info['items'] = fxy_unserialize($material_info['material_content']);
				}
			}
			$this->assign('reply_info', $reply_info);
			$this->assign('material_info', $material_info);
			$this->display();
		}
    }
	
	/**
     * 关键词删除
     **/
    public function keyword_delOp() {
        $model_wechat = model('wechat');
		$rid = input('rid', 0, 'intval');
		if (!$rid) {
			output_error('参数错误');
		}
		
		$result = false;
		if (!empty($rid)) {
			$result = $model_wechat->delInfo('weixin_reply', array('reply_id' => $rid));
		}
		
		if ($result) {
			output_data(array('msg' => '操作成功', 'url' => users_url('wechat/keyword_manage')));
		} else {
			output_error('操作失败');
		}
    }
	/*自定义菜单列表*/
	public function menu_manageOp(){
		$model_wechat = model('wechat');
		$condition = array();
		$menu_list = $model_wechat->getInfoList('weixin_menu', $condition, '*', 'menu_id desc', 10, input('page', 1, 'intval'));
		$this->assign('page', page($menu_list['totalpage'], array('page' => input('page', 1, 'intval')), users_url('wechat/menu_manage')));
		$this->assign('menu_list', $menu_list['list']);
		$this->display();
	}
	
	/**
     * 自定义菜单添加
     **/
    public function menu_addOp() {
        $model_wechat = model('wechat');
		if (chksubmit()) {
			$MenuTitle = input('MenuTitle', '', 'trim');
			$Title = input('Title', '');
			$MsgType = input('MsgType', '');
			$TextContents = input('TextContents', '');
			$MaterialID = input('MaterialID', '');
			
			$Url = input('Url', '');
			if (empty($MenuTitle)) {
				web_error('菜单名称不能为空');
			}
			
			if (empty($Title)) {
				web_error('请设置菜单');
			}
			$flag = true;
			$model_wechat->beginTransaction();
			$menu = array(
				'menu_name' => $MenuTitle,
				'menu_addtime' => time()
			);
			$menuid = $model_wechat->addInfo('weixin_menu', $menu);
			$flag = $flag && $menuid;
			$i = 0;
			foreach($Title as $key => $value){
				if (empty($Title[$key][0])) {
					continue;
				}
				$i++;
				if (!empty($Url[$key][0])) {
					$Url[$key][0] = $this->spite_url($Url[$key][0]);
				}
				$first = array(
					'detail_name' => $Title[$key][0],
					'menu_id' => $menuid,
					'detail_msgtype' => $MsgType[$key][0],
					'detail_textcontents' => $TextContents[$key][0],
					'detail_materialid' => !empty($MaterialID[$key][0]) ? $MaterialID[$key][0] : 0,
					'detail_url' => $Url[$key][0],
					'detail_sort' => $i
				);
				$parentid = $model_wechat->addInfo('weixin_menu_detail', $first);
				$flag = $flag && $parentid;
				$j = 0;
				$detail = array();
				ksort($value);
				foreach($value as $k=>$v) {
					if (empty($Title[$key][$k]) || $k == 0) {
						continue;
					}
					$j++;
					if (!empty($Url[$key][$k])) {
						$Url[$key][$k] = $this->spite_url($Url[$key][$k]);
					}
					$detail[] = array(
						'detail_name' => $Title[$key][$k],
						'menu_id' => $menuid,
						'detail_msgtype' => $MsgType[$key][$k],
						'detail_textcontents' => $TextContents[$key][$k],
						'detail_materialid' => !empty($MaterialID[$key][$k]) ? $MaterialID[$key][$k] : 0,
						'detail_url' => $Url[$key][$k],
						'detail_sort' => $j,
						'parent_id' => $parentid
					);
				}
				if(!empty($detail)){
					$child = $model_wechat->addAll('weixin_menu_detail', $detail);
					$flag = $flag && $child;
				}
				
			}
			
			if ($flag) {
				$model_wechat->commit();
				web_success('操作成功', users_url('wechat/menu_manage'));
			} else {
				$model_wechat->rollBack();
				web_error('保存失败');
			}
		} else {
			$this->display();
		}
    }
	
	/**
     * 自定义菜单删除
     **/
    public function menu_delOp() {
        $model_wechat = model('wechat');
		$result = false;
		$mid = input('mid', 0, 'intval');
		if (empty($mid)) {
			output_error('参数错误');
		}
		$where['menu_id'] = $mid;
		$where['menu_status'] = 1;
		$menu_info = $model_wechat->getInfoOne('weixin_menu', $where);
		
		$condition['menu_id'] = $mid;
		$result = $model_wechat->delInfo('weixin_menu_detail',$condition);
		$result = $model_wechat->delInfo('weixin_menu',$condition);
		if(!empty($menu_info)){
			$response = $this->deletemenu();
			if($response['status'] == 0){
				output_error($response['msg']);
			}else{
				output_data(array('msg' => $response['msg'], 'url' => users_url('wechat/menu_manage')));
			}
		}else{
			if($result){
				output_data(array('msg' => '操作成功', 'url' => users_url('wechat/menu_manage')));
			}else{
				output_error('操作失败');
			}
		}
    }
	
	/**
     * 自定义菜单添加
     **/
    public function menu_editOp() {
        $model_wechat = model('wechat');
		if (chksubmit()) {
			$mid = input('mid', 0, 'intval');
			$MenuTitle = input('MenuTitle', '', 'trim');
			$Title = input('Title', '');
			$MsgType = input('MsgType', '');
			$TextContents = input('TextContents', '');
			$MaterialID = input('MaterialID', '');
			$Url = input('Url', '');
			if (empty(trim($MenuTitle))) {
				web_error('请填写标题');
			}
			
			if(empty($Title)){
				web_error('请设置菜单');
			}
			
			if(empty($mid)){
				web_error('参数错误');
			}
			
			$menu_info = $model_wechat->getInfoOne('weixin_menu', array('menu_id' => intval($mid)), 'menu_status');
			if (empty($menu_info)) {
				web_error('信息不存在');
			}
			
			$flag = true;
			$model_wechat->beginTransaction();
			
			$result = $model_wechat->delInfo('weixin_menu_detail', array('menu_id' => intval($mid)));
			$flag = $flag && $result;
			
			$menu = array(
				'menu_name' => trim($MenuTitle)
			);
			
			$result = $model_wechat->editInfo('weixin_menu', $menu, array('menu_id' => intval($mid)));
			//$flag = $flag && $result;
			
			$i = 0;
			foreach($Title as $key => $value) {
				if (empty($Title[$key][0])) {
					continue;
				}
				$i++;
				if (!empty($Url[$key][0])) {
					$Url[$key][0] = $this->spite_url($Url[$key][0]);
				}
				$first = array(
					'detail_name' => $Title[$key][0],
					'menu_id' => $mid,
					'detail_msgtype' => $MsgType[$key][0],
					'detail_textcontents' => $TextContents[$key][0],
					'detail_materialid' => !empty($MaterialID[$key][0]) ? $MaterialID[$key][0] : 0,
					'detail_url' => $Url[$key][0],
					'detail_sort' => $i
				);
				$parentid = $model_wechat->addInfo('weixin_menu_detail', $first);
				$flag = $flag && $parentid;
				$j = 0;
				$detail = array();
				ksort($value);
				foreach ($value as $k => $v) {
					if (empty($Title[$key][$k]) || $k == 0) {
						continue;
					}
					$j++;
					if (!empty($Url[$key][$k])) {
						$Url[$key][$k] = $this->spite_url($Url[$key][$k]);
					}
					$detail[] = array(
						'detail_name' => $Title[$key][$k],
						'menu_id' => $mid,
						'detail_msgtype' => $MsgType[$key][$k],
						'detail_textcontents' => $TextContents[$key][$k],
						'detail_materialid' => !empty($MaterialID[$key][$k]) ? $MaterialID[$key][$k] : 0,
						'detail_url' => $Url[$key][$k],
						'detail_sort' => $j,
						'parent_id' => $parentid
					);
				}
				if (!empty($detail)) {
					$child = $model_wechat->addAll('weixin_menu_detail', $detail);
					$flag = $flag && $child;
				}
				
			}
			
			if ($flag) {
				$model_wechat->commit();
				if ($menu_info['menu_status']==1) {
					$response = $this->publish($mid);
					if ($response['status'] == 0) {
						web_error($response['msg']);
					} else {
						web_success($response['msg'], users_url('wechat/menu_manage'));
					}
				} else {
					web_success('保存成功', users_url('wechat/menu_manage'));
				}				
			} else {
				$model_wechat->rollBack();
				web_error('保存失败');
			}
			
		} else {
			$menu_info = array();
			$mid = input('mid', 0, 'intval');
			$this->assign('mid', $mid);
			if (empty($mid)) {
				web_error('参数错误');
			}
			$menu_info = $model_wechat->getInfoOne('weixin_menu', array('menu_id' => $mid));
			if (empty($menu_info)) {
				web_error('信息不存在');
			}
			
			$first_info = $second_info = array();
			$result = $model_wechat->getInfoList('weixin_menu_detail', array('menu_id' => $mid), '*', 'parent_id asc, detail_sort asc');
			$i = $j = 0;
			$child_info = array();
			if (!empty($result['list'])) {
				foreach($result['list'] as $key => $value){
					if ($value['parent_id'] == 0) {
						$i++;
						$child_info[$value['detail_id']] = 0;
						$first_info[$i] = $value;
					} else {
						$j++;
						$child_info[$value['parent_id']] = $child_info[$value['parent_id']] + 1;
						$second_info[$value['parent_id']]['child'][$j] = $value;
					}
				}
			}
			
			$this->assign('firstnum', count($first_info));
			$this->assign('menu_info', $menu_info);
			$this->assign('child_info', $child_info);
			$this->assign('first_info', $first_info);
			$this->assign('j', $j);
			$this->assign('second_info', $second_info);
			$this->display();
		}
    }
	
	public function menu_publishOp() {
		$mid = input('mid', 0, 'intval');
        $response = $this->publish($mid);
		if ($response['status'] == 0) {
			web_error($response['msg']);
		}else{
			web_success($response['msg'], users_url('wechat/menu_manage'));
		}
	}
	
	/**
	 * ajax操作
	 */
	public function ajaxOp(){
		$model_wechat = model('wechat');
		$branch = input('branch', '', 'trim');
		switch ($branch) {
			case 'check_keywords':
				$keywords = trim(input('keywords', ''), '|');
				$rid = input('rid', 0, 'intval');
				$array = explode('|', $keywords);
				foreach($array as $a) {
					if (trim($a) == '') continue;
					
					$condition['reply_keywords'] = '%|' . trim($a) . '|%';
					if ($rid > 0) {
						$condition['reply_id !='] = $rid;
					}
					
					$reply_info = $model_wechat->getInfoOne('weixin_reply', $condition);
					if (!empty($reply_info)) {
						echo 'false';
						exit;
					}
				}
				
				echo 'true';
				exit;
			break;
			case 'get_material':
				$mid = input('mid', 0, 'intval');
				if (empty($mid)) {
					$data['msg'] = '<div class=\'item\'></div>';
					echo json_encode($data);
					exit;
				}
				$material_info = $model_wechat->getInfoOne('weixin_material', array('material_id' => $mid));
				if (empty($material_info)) {
					$data['msg'] = '<div class=\'item\'></div>';
					echo json_encode($data);
					exit;
				}
				
				$items = fxy_unserialize($material_info['material_content']);
				if (!is_array($items)) {
					$data['msg'] = '<div class=\'item\'></div>';
					echo json_encode($data);
					exit;
				}
				
				$html = '';
				if ($material_info['material_type'] == 1) {
					$html .= '<div class=\'item one\'>';
					foreach($items as $k => $v) {
                  		$html .= '<div class=\'title\'>' . $v['Title'] . '</div><div>' . date('Y-m-d', $material_info['material_addtime']) . '</div><div class=\'img\'><img src=\'' . $v['ImgPath'] . '\' /></div><div class=\'txt\'>' . str_replace(PHP_EOL, '<br />', $v['TextContents']) . '</div>';
                 	}
					$html .= '</div>';
				} else {
					$html .= '<div class=\'item multi\'>';
					$html .= '<div class=\'time\'>' . date('Y-m-d', $material_info['material_addtime']) . '</div>';
                  	foreach($items as $k=>$v) {
                  		$html .= '<div class=\'' . ($k>0 ? 'list' : 'first') . '\'><div class=\'info\'><div class=\'img\'><img src=\'' . $v['ImgPath'] . '\' /></div><div class=\'title\'>' . $v['Title'] . '</div></div></div>';
                  	}
					$html .= '</div>';
				}
				$data['msg'] = $html;
				echo json_encode($data);
				exit;
			break;
		}
	}
	
	private function publish($menuid) {
		$model_wechat = model('wechat');
		
		if (empty($menuid)) {
			return array('status' => 0, 'msg' => '请选择信息');
		}
		
		$api_account = $model_wechat->getInfoOne('weixin_wechat', '');
	
		if (empty($api_account['wechat_appid']) || empty($api_account['wechat_appsecret'])) {
			return array('status' => 0, 'msg' => '还没有配置AppID和AppSecret，请到【接口配置】中进行配置');
		}
		
		$result = $model_wechat->getInfoList('weixin_menu_detail', array('menu_id' => $menuid), '*', 'parent_id asc, detail_sort asc');
		if (empty($result['list'])) {
			return array('status' => 0, 'msg' => '暂无菜单数据，请先设置菜单');
		}
		
		$ACCESS_TOKEN = logic('weixin_token')->get_access_token(config());
		if (!$ACCESS_TOKEN) {
			return array('status' => 0,'msg' => '获取access_token失败，请检查配置信息');
		}
		
		$first_menu = $child_menu = array();
		foreach($result['list'] as $key => $value){
			if ($value['parent_id'] == 0) {
				$first_menu[] = $value;
			} else {
				$child_menu[$value['parent_id']][] = $value;
			}
		}
		
		$Menu = array();
		foreach($first_menu as $key => $value) {
			if (!empty($child_menu[$value['detail_id']])) {
				$Data = array(
					'name' => $value['detail_name'],
					'sub_button' => array()
				);
				$sub_button = array_reverse($child_menu[$value['detail_id']]);
				foreach($sub_button as $k => $v) {
					if ($v['detail_msgtype'] == 0) {
						$Data['sub_button'][] = array(
							'type' => 'click',
							'name' => $v['detail_name'],
							'key' => strlen($v['detail_textcontents']) >= 120 ? 'changwenben_' . $v['detail_id'] : $v['detail_textcontents']
						);
					} elseif ($v['detail_msgtype'] == 1) {
						$Data['sub_button'][] = array(
							'type' => 'click',
							'name' => $v['detail_name'],
							'key' => 'MaterialID_' . ($v['detail_materialid'] ? $v['detail_materialid'] : 0)
						);
					} elseif ($v['detail_msgtype'] == 2) {
						$v['detail_url'] = $this->get_short_url($ACCESS_TOKEN, $v['detail_url']);
						$Data['sub_button'][] = array(
							'type' => 'view',
							'name' => $v['detail_name'],
							'url' => $v['detail_url']
						);
					}
				}
			} else {
				if ($value['detail_msgtype'] == 0) {
					$Data=array(
						'type'=>'click',
						'name'=>$value['detail_name'],
						'key'=>strlen($value['detail_textcontents']) >= 120 ? 'changwenben_'.$value['detail_id'] : $value['detail_textcontents']
					);
				} elseif($value['detail_msgtype'] == 1) {
					$Data = array(
						'type' => 'click',
						'name' => $value['detail_name'],
						'key' => 'MaterialID_' . ($value['detail_materialid'] ? $value['detail_materialid'] : 0)
					);
				} elseif($value['detail_msgtype'] == 2) {
					$value['detail_url'] = $this->get_short_url($ACCESS_TOKEN, $value['detail_url']);
					$Data = array(
						'type' => 'view',
						'name' => $value['detail_name'],
						'url' => $value['detail_url']
					);
				}
			}
			$Menu['button'][] = $Data;
		}
		$response = logic('weixin_token')->curl_post('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $ACCESS_TOKEN, $Menu);
		lib\logging::write(var_export($response, true));
		if (empty($response['errcode'])) {
			$model_wechat->editInfo('weixin_menu', array('menu_status' => 0), '');
			$model_wechat->editInfo('weixin_menu', array('menu_status' => 1), array('menu_id' => $menuid));
			return array('status' => 1, 'msg' => '菜单已同步到微信');
			
		} else {
			return array('status' => 0, 'msg' => '菜单发布失败');
		}
	}
	//获取短链接
	private function get_short_url($access_token, $long_url) {
		$data = array(
			'action' => 'long2short',
			'long_url' => $long_url,
		);
		$response = logic('weixin_token')->curl_post('https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $access_token, $data);
		$this->log(json_encode($response));
		if (empty($response['errcode'])) {
			return $response['short_url'];
		}else{
			return $long_url;
		}
	}
	private function deletemenu() {
		$model_wechat = model('wechat');
		
		$api_account = $model_wechat->getInfoOne('weixin_wechat', '');
	
		if(empty($api_account['wechat_appid']) || empty($api_account['wechat_appsecret'])){
			return array('status' => 0,'msg' => '还没有配置AppID和AppSecret，请到【接口配置】中进行配置');
		}
		
		$ACCESS_TOKEN = logic('weixin_token')->get_access_token(config());
		if(!$ACCESS_TOKEN){
			return array('status' => 0,'msg' => '获取access_token失败，请检查配置信息');
		}
		
		$response = logic('weixin_token')->curl_get('https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $ACCESS_TOKEN);
		
		if(empty($response['errcode'])){
			return array('status' => 1, 'msg' => '微信菜单成功删除');
		}else{
			return array('status' => 0, 'msg' => '微信菜单删除失败');
		}
	}
	
	private function spite_url($url) {
		$url = (strpos($url, 'http://') > -1 || strpos($url, 'https://') > -1) ? trim($url) : 'http://' . trim($url);
		return $url;
	}
}