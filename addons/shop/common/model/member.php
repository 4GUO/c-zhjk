<?php
namespace model;
use base;
class member extends base\model
{
    protected $tableName = 'fxy_member';
	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null)
	{
		if($page && $get_p){
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
			$hasmore = $total > $get_p * $page ? true : false;
		}else{
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
			$hasmore = false;
		}
		return array('list' => $list, 'totalpage' => $totalpage, 'hasmore' => $hasmore);
	}
	public function getInfo($condition = array(), $field = '*')
	{
		$result = $this->where($condition)->field($field)->find();
		return $result;
	}
	public function add($data)
	{
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array())
	{
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition = array())
	{
		$result = $this->where($condition)->delete();
		return $result;
	}
	public function count($condition = array())
	{
		$result = $this->where($condition)->total();
		return $result;
	}
	public function register($response, $type, $uniacid = 0)
	{
		$model_fans = model('fans');
		$model_member = model('member');
		$model_distribute_account = model('distribute_account');
		$oid = !empty($response['oid']) ? $response['oid'] : 0;
		if ($oid > 0) {
			$inviter_agent = $model_distribute_account->getInfo(array('uniacid' => $uniacid, 'uid' => $oid), 'dis_path');
			if (empty($inviter_agent)) {
				return callback(false, '推荐人无效');
			}
			$inviter_member = $model_member->getInfo(array('uniacid' => $uniacid, 'uid' => $oid), 'is_distributor,nickname');
			if (empty($inviter_member['is_distributor'])) {
				return callback(false, '推荐人无效，请推荐人购买商品');
			}
		} else {
			$inviter_agent = array();
			if (config('member_inviter') == 1) {
				return callback(false, '必须通过邀请人成为会员');
			}
		}
		$level_info = model('vip_level')->getInfo(array('uniacid' => $uniacid), '*', 'level_sort ASC');
		$level_id = empty($level_info['id']) ? 0 : $level_info['id'];
		if (!empty($response['fanid'])) {
			$fans_info = $model_fans->getInfo(array('fanid' => $response['fanid']));
			if (!$fans_info) {
			    return callback(false, '粉丝信息错误');
			}
			$openid = $fans_info['openid'];
			$weixin_unionid = $fans_info['unionid'];
			$nickname = $fans_info['nickname'];
			$headimg = $fans_info['headimg'];
			$oid = $oid ? $oid : $fans_info['inviter_id'];
		} else {
			$openid = 'reg_' . random(32);
			$weixin_unionid = 'reg_' . random(32);
			$nickname = !empty($response['nickname']) ? filterEmoji($response['nickname']) : '';
			$headimg = '/static/shop/img/default_user.png';
		}
		try {
		    $model = model();
            $model->beginTransaction();
			$member_data = array();
			$member_data['uniacid'] = $uniacid;
			$member_data['weixin_unionid'] = $weixin_unionid;
			$member_data['openid'] = $openid;
			$member_data['nickname'] = $nickname;
			$member_data['truename'] = !empty($response['truename']) ? $response['truename'] : '';
			$member_data['headimg'] = $headimg;
			$member_data['mobile'] = !empty($response['mobile']) ? $response['mobile'] : '';
			$member_data['password'] = !empty($response['password']) ? f_hash($response['password']) : f_hash('123456');
			$member_data['paypwd'] = !empty($response['password']) ? f_hash($response['password']) : f_hash('123456');
			$member_data['status'] = isset($response['status']) ? $response['status'] : 1;
			$member_data['add_time'] = TIMESTAMP;
			$member_data['level_id'] = $level_id;
			$member_data['is_distributor'] = 1;
			$member_data['inviter_id'] = $oid;
			$uid = $model_member->add($member_data);
			if ($uid) {
				if (!empty($fans_info)) {
					$model_fans->edit(array('fanid' => $fans_info['fanid']), array('uid' => $uid));
				}
				$agent_data = array();
				$agent_data['uniacid'] = $uniacid;
				$agent_data['level_id'] = $level_id;
				$agent_data['inviter_id'] = $oid;
				$agent_data['dis_path'] = $dis_path = !empty($inviter_agent['dis_path']) ? $inviter_agent['dis_path'] . $oid . ',' : ($oid > 0 ? ',' . $oid . ',' : '');
				$agent_data['add_time'] = TIMESTAMP;
				$agent_data['uid'] = $uid;
				$agent_id = $model_distribute_account->add($agent_data);
				model('points_log')->savePointsLog('points_reg', array('pl_memberid' => $uid, 'pl_membername' => $nickname), true);
				
				if ($oid) {
					$model_distribute_account->edit(array('uid' => $oid), 'inviter_num=inviter_num+1');
					$model_distribute_account->edit(array('uid' => explode(',', trim($dis_path, ','))), 'team_num=team_num+1');
					model('points_log')->savePointsLog('points_invite', array('pl_memberid' => $oid, 'pl_membername' => $inviter_member['nickname'], 'invited' => $nickname), true);
					$member_data['uid'] = $uid;
					//diy yewu
					
				}
				$model->commit();
				if (!empty($response['headimgurl'])) {
				    logic('poster')->get_headimg(str_replace('https://', 'http://', $response['headimgurl']), UPLOADFILES_PATH . '/headimg/' . $uid . '.jpg');
				}
				return callback(true, '', array('uid' => $uid));
			}
		} catch (\Exception $e) {
			$model->rollBack();
			return callback(false, '注册失败');
		}
	}
	/**
     * 登录生成token
     */
    public function get_token($member_id, $member_name, $openid, $client, $uniacid) {
        $model_mb_user_token = model('mb_user_token');
		$check = $model_mb_user_token->where(array('member_id' => $member_id))->find();
		$token = md5($member_id);
		if ($check) {
			return $token;
		} else {
			//生成新的token
			$mb_user_token_info = array();
			$mb_user_token_info['uniacid'] = $uniacid;
			$mb_user_token_info['member_id'] = $member_id;
			$mb_user_token_info['member_name'] = $member_name;
			$mb_user_token_info['token'] = $token;
			$mb_user_token_info['login_time'] = TIMESTAMP;
			$mb_user_token_info['client_type'] = $client;
			$mb_user_token_info['openid'] = $openid;
			$result = $model_mb_user_token->addMbUserToken($mb_user_token_info);
			if ($result) {
				return $token;
			} else {
				return null;
			}
		}
    }
}