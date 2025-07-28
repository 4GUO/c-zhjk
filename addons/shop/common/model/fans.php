<?php
namespace model;
use base;
class fans extends base\model
{
    protected $tableName = 'fans';

	public function getList($condition = array(), $field = '*', $order = '', $page = null, $get_p = null) {
		if ($page && $get_p) {
			$total = $this->where($condition)->total();
			$totalpage = ceil($total / $page);//总计页数
			$limitpage = ($get_p - 1) * $page;//每次查询取记录
			$list = $this->where($condition)->field($field)->order($order)->limit($limitpage, $page)->select();
		} else {
			$totalpage = 0;
			$list = $this->where($condition)->field($field)->order($order)->select();
		}
		return array('list' => $list, 'totalpage' => $totalpage);
	}
	public function getInfo($condition = array(), $field = '*') {
		$result = $this->where($condition)->field($field)->find();
		return $result;
	}
	public function add($data) {
		$result = $this->insert($data);
		return $result;
	}
	public function edit($condition = array(), $data = array()) {
		$result = $this->where($condition)->update($data);
		return $result;
	}
	public function del($condition = array()) {
		$result = $this->where($condition)->delete();
		return $result;
	}
	public function register($response, $type, $uniacid = 0) {
	    $model_member = model('member');
		$model_distribute_account = model('distribute_account');
		$model_fans = model('fans');
		$openid = !empty($response['openid']) ? $response['openid'] : '';
		$unionid = !empty($response['unionid']) ? $response['unionid'] : '';
		$nickname = !empty($response['nickname']) ? filterEmoji($response['nickname']) : '';
		$fans_info = $model_fans->getInfo(array('openid' => $openid));
		//\lib\logging::write(var_export($response, true));
		$oid = !empty($response['oid']) ? $response['oid'] : 0;
		//edit20230318
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
		if (empty($fans_info['openid'])) {
			$model = model();
            $model->beginTransaction();
			try {
				$data = array();
				$data['uniacid'] = $uniacid;
				$data['inviter_id'] = $oid;
				$data['openid'] = $openid;
				$data['unionid'] = $unionid;
				$data['nickname'] = $nickname;
				$data['headimg'] = $response['headimgurl'];
				$data['followtime'] = TIMESTAMP;
				$data['updatetime'] = TIMESTAMP;
				$data['tag'] = base64_encode(serialize($response));
				$fanid  = $model_fans->add($data);
				$model->commit();
				return callback(true, '', array('fanid' => $fanid));
			} catch (\Exception $e) {
				$model->rollBack();
				return callback(false, '授权失败');
			}
		} else {
			$data = array();
			$data['updatetime'] = TIMESTAMP;
			$data['nickname'] = $nickname;
			$data['headimg'] = $response['headimgurl'];
			$data['tag'] = base64_encode(serialize($response));
			$model_fans->edit(array('fanid' => $fans_info['fanid']), $data);
			return callback(true, '', array('fanid' => $fans_info['fanid']));
		}
		return callback(false, '授权失败');
	}
}