<?php
/**
 * 业务模块处理
 *
 */

namespace logic;

use base;
use lib;

defined('SAFE_CONST') or exit('Access Invalid!');

class yewu
{
    // 根据fgjdtj_nums 确定要往上面找多少层
    // 返回用户信息
    public function get_fgjdjl_user_info($puid, $fgjdjl_nums)
    {
        for ($i = 1; $i <= $fgjdjl_nums; $i++) {
            $user_info = model('member')->where(array('uid' => $puid))->find();
            if (!$user_info) {
                return false;
            }
            if ($user_info['inviter_id'] == 0) {
                return false;
            }
            if ($user_info['inviter_id'] == $puid) {
                return false;
            }
            $puid = $user_info['inviter_id'];
        }
        return $user_info;
    }

    // 计算用户的复购见单次数


    // 计算用户的复购见单次数
    public function get_fgjdjl_user_buy_nums($order_info)
    {
        // 检查参数类型，如果是整数（用户ID），则直接使用
        if (is_int($order_info) || is_numeric($order_info)) {
            $uid = $order_info;
            // 获取用户的所有订单，按支付时间排序
            $buy_nums = model('shop_order')->where(array('uid' => $uid, 'order_state' => array(20, 30, 40, 50)))->total();
            return $buy_nums;
        }

        // 如果是数组（订单信息），使用原来的逻辑
        if (is_array($order_info) && isset($order_info['uid']) && isset($order_info['payment_time'])) {
            $buy_nums = model('shop_order')->where(array('uid' => $order_info['uid'], 'order_state' => array(20, 30, 40, 50), 'payment_time <=' => $order_info['payment_time']))->total();
            return $buy_nums;
        }

        // 如果参数无效，返回0
        return 0;
    }


    // 写一个方法专门写复购见单奖励的日志
    public function fgjdjl_log($order_info, $fgjdtj_user_buy_nums, $content)
    {
        $file_path = BASE_PATH . '/data/fgjdjl_log.txt';
        $fgjdtj_nums = config('fgjdtj');

        $file_content = date('Y-m-d H:i:s') . ' 用户ID【' . $order_info['uid'] . '】 订单号【' . $order_info['order_sn'] . '】 复购见单数【' . $fgjdtj_nums . '】-【' . $fgjdtj_user_buy_nums . '】 关键内容【' . $content . '】' . "\n";
        file_put_contents($file_path, $file_content, FILE_APPEND);
        return $file_content;
    }


    public function deal_fugou_reward($order_info)
    {


        $buyer_info = model('member')->where(array('uid' => $order_info['uid']))->find();
        if (!$buyer_info) {
            // 记录日志
            return $this->fgjdjl_log($order_info, 0, '用户不存在，不发放奖励');
        }

        // 获取复购见单的层数配置
        $fgjdtj_nums = config('fgjdtj');
        //订单状态：10:待付款（默认）;20:已支付;30:已发货;40:已收货;50已完成;2已取消
        // 计算用户购买过多少单零售区商品 限制状态为已支付、已发货，已收获、已完成
        $user_buy_nums = $this->get_fgjdjl_user_buy_nums($order_info);

        // 检测用户是否已经发放过奖励
        $detail_info = model('distribute_fgjdjl_record_detail')->where(array('order_sn' => $order_info['order_sn']))->find();
        if ($detail_info) {
            // 记录日志
            return $this->fgjdjl_log($order_info, $user_buy_nums, '用户已经发放过奖励，不发放奖励');
        }


        if ($user_buy_nums > $fgjdtj_nums) {
            // 记录日志
            return $this->fgjdjl_log($order_info, $user_buy_nums, '用户购买单数大于复购见单的层数，不发放奖励');

        }
        // 根据单数对应往上面找几层上级
        $parent_user_info = $this->get_fgjdjl_user_info($buyer_info['inviter_id'], $user_buy_nums);
        if (!$parent_user_info) {
            // 记录日志
            return $this->fgjdjl_log($order_info, $user_buy_nums, '用户第' . $user_buy_nums . '层没有上级，不发放奖励');

        }

        // 计算上级用户购买过多少单零售区的商品
        $parent_user_buy_nums = $this->get_fgjdjl_user_buy_nums($parent_user_info['uid']);
        if ($parent_user_buy_nums < $user_buy_nums) {
            // 记录日志
            return $this->fgjdjl_log($order_info, $user_buy_nums, '上级用户【' . $parent_user_info['uid'] . '】【' . $parent_user_info['nickname'] . '】 购买单数小于用户购买单数，不发放奖励');
        }
        // 获取当前用户的级别配置，获得奖励的额度
        $level_info = model('vip_level')->where(array('id' => $parent_user_info['level_id']))->find();
        if (!isset($level_info['fgjdjl']) || $level_info['fgjdjl'] <= 0) {
            // 记录日志
            return $this->fgjdjl_log($order_info, $user_buy_nums, '上级用户级别没有配置复购见单奖励，不发放奖励');

        }
        // 给用户发放奖励。写入奖励明细记录，写清楚奖励的计算过程
        $fgjdjl_moeny = $level_info['fgjdjl'];


        // 开启事务
        $model = model();
        $model->beginTransaction();
        // 写入奖励明细记录

        $desc = '用户' . $buyer_info['nickname'] . '第' . $user_buy_nums . '次购买，您获得复购见单奖励' . $fgjdjl_moeny . '元';
        $detail_data = array(
            'uniacid' => 1,
            'uid' => $parent_user_info['uid'],
            'nickname' => $parent_user_info['nickname'],
            'mobile' => $parent_user_info['mobile'],
            'from_uid' => $order_info['uid'],
            'from_nickname' => $buyer_info['nickname'],
            'from_mobile' => $buyer_info['mobile'],
            'order_sn' => $order_info['order_sn'],
            'order_amount' => $order_info['order_amount'],
            'detail_bonus' => $fgjdjl_moeny,
            'detail_desc' => $desc,
            'detail_addtime' => date('Y-m-d H:i:s'),
            'detail_status' => 10,
            'user_orders' => $user_buy_nums,
            'parent_orders' => $parent_user_buy_nums,
            'create_date' => date('Y-m-d H:i:s'),
            'update_date' => null
        );


        // 插入奖励记录
        $detail_id = model('distribute_fgjdjl_record_detail')->add($detail_data);
        if ($detail_id) {
            // 给用户增加余额
            $logic_pd = logic('predeposit');
            $data_pd = array();
            $data_pd['uid'] = $parent_user_info['uid'];
            $data_pd['member_name'] = $parent_user_info['nickname'];
            $data_pd['amount'] = $fgjdjl_moeny;
            $data_pd['order_sn'] = $order_info['order_sn'];
            $data_pd['lg_desc'] = $desc;
            $logic_pd->changePd('commission_in', $data_pd);
            // 记录日志
            $this->fgjdjl_log($order_info, $user_buy_nums, '成功发放复购见单奖励' . $fgjdjl_moeny . '元给用户 【' . $parent_user_info['uid'] . '】【' . $parent_user_info['nickname'] . '】');
        }
        $model->commit();
        return true;
    }

    // 订单退款奖励退回
    public function deal_fugou_revoke($order_info)
    {
        // 获取订单的奖励明细，并且状态为已发放
        $detail_list = model('distribute_fgjdjl_record_detail')->where(array('order_sn' => $order_info['order_sn'], 'detail_status' => 10))->select();
        if (empty($detail_list)) {
            return "奖励不存在或者，奖励已经退回";
        }

        // 开启事务
        
        // 退回奖励
        foreach ($detail_list as $v) {
            $desc = '用户' . $v['from_nickname'] . '退款，您获得的复购见单奖励' . $v['detail_bonus'] . '元退回';
            // 退回奖励
            $logic_pd = logic('predeposit');
            $data_pd = array();
            $data_pd['uid'] = $v['uid'];
            $data_pd['member_name'] = $v['nickname'];
            $data_pd['amount'] = $v['detail_bonus'];
            $data_pd['order_sn'] = $order_info['order_sn'];
            $data_pd['lg_desc'] = $desc;
            $logic_pd->changePd('commission_out', $data_pd);
            //更新 distribute_fgjdjl_record_detail 状态和退款时间
            model('distribute_fgjdjl_record_detail')->where(array('detail_id' => $v['detail_id']))->update(array('detail_status' => 20, 'detail_addtime' => date('Y-m-d H:i:s')));
        }
        
        return true;
    }

    //获取分销商级别
    public function get_level_list($field = '*', $order = 'level_sort ASC')
    {
        $result = model('vip_level')->getList(array(), $field, $order);
        $list = array();
        foreach ($result['list'] as $k => $v) {
            $list[$v['id']] = $v;
        }
        return $list;
    }

    //购买体验商品成为分销商
    public function upgrade_level_deal($order_info)
    {
        $buyer_info = model('member')->getInfo(array('uid' => $order_info['uid']));
        $buyer_level_info = model('vip_level')->getInfo(array('id' => $buyer_info['level_id']));
        $has_experience = false;
        foreach ($order_info['extend_order_goods'] as $v) {
            $check = model('shop_goods_common')->field('is_experience_goods')->where(array('goods_commonid' => $v['goods_commonid']))->find();
            //lib\logging::write(var_export($check, true));
            if (!empty($check['is_experience_goods'])) {
                $has_experience = true;
                break;
            }
        }
        //lib\logging::write(var_export($order_info['extend_order_goods'], true));
        //lib\logging::write(var_export($has_experience, true));
        //如果有体验商品
        if ($has_experience) {
            model('member')->where(array('uid' => $order_info['uid']))->update(array('has_buy_tygoods' => 1));//更新是否购买过体验商品
            $level_list = $this->get_level_list('*', 'level_sort DESC');
            foreach ($level_list as $v) {
                //防止降级
                if ($buyer_level_info['level_sort'] >= $v['level_sort']) {
                    continue;
                }
                if ($v['need_buy_experience_goods'] == 1) {
                    model('member')->where(array('uid' => $buyer_info['uid']))->update(array('level_id' => $v['id']));
                    model('distribute_account')->where(array('uid' => $buyer_info['uid']))->update(array('level_id' => $v['id']));
                    break;
                }
            }
        }
        return true;
    }

    //报单奖励
    public function deal_baodan_reward($order_info)
    {
        if ($order_info['reporter_uid'] <= 0) {
            return true;
        }
        $buyer_info = model('member')->getInfo(array('uid' => $order_info['uid']));

        $order_goods = $order_info['extend_order_goods'] ?? array();
        if (!$order_goods) {
            return true;
        }

        try {
            $model = model();
            $model->beginTransaction();
            foreach ($order_goods as $key => $value) {
                $goods_info = model('shop_goods_common')->field('baodan_reward_bili')->where(array('goods_commonid' => $value['goods_commonid']))->find();
                if ($goods_info['baodan_reward_bili'] <= 0) {
                    continue;
                }
                //增加分销记录
                $data = array(
                    'uniacid' => 1,
                    'buyer_id' => $buyer_info['uid'],
                    'order_sn' => $order_info['order_sn'],
                    'goods_id' => $value['goods_commonid'],
                    'goods_name' => $value['goods_name'],
                    'goods_image' => $value['goods_image'],
                    'goods_num' => $value['goods_num'],
                    'record_addtime' => time(),
                    'record_status' => ORDER_STATE_PAY,
                );
                $detail_bonus = $value['goods_price'] * $value['goods_num'] * $goods_info['baodan_reward_bili'] * 0.01;
                $recordid = model('distribute_other')->add($data);
                $detail_desc = $buyer_info['nickname'] . ' 商城消费,你获得报单奖励' . $detail_bonus . '元';
                $detail_data[] = array(
                    'uniacid' => 1,
                    'record_id' => $recordid,
                    'order_sn' => $order_info['order_sn'],
                    'uid' => $order_info['reporter_uid'],
                    'detail_level' => 1,
                    'detail_bonus' => $detail_bonus,
                    'detail_desc' => $detail_desc,
                    'detail_status' => ORDER_STATE_PAY,
                    'detail_addtime' => time(),
                    'detail_type' => 2,
                );
            }
            model('distribute_detail_other')->insertAll($detail_data);
            foreach ($detail_data as $v) {
                $logic_pd = logic('predeposit');
                $data_pd = array();
                $data_pd['uid'] = $v['uid'];
                $data_pd['member_name'] = '';
                $data_pd['amount'] = $v['detail_bonus'];
                $data_pd['order_sn'] = $order_info['order_sn'];
                $data_pd['lg_desc'] = $v['detail_desc'];
                $logic_pd->changePd('commission_in', $data_pd);
            }
            $model->commit();
        } catch (\Exception $e) {
            $model->rollback();
            lib\logging::write(var_export($e, true));
            return false;
        }
    }

    //零售月分红 每月一号凌晨1分执行
    public function yue_deal_fenhong($total_yeji)
    {
        //fenhong_reward_bili
        $this_month = lib\timer::month();
        $lock_submit = model('distribute_fenhong_record_detail')->where(array('detail_addtime >=' => $this_month[0], 'type' => 3))->total();
        if ($lock_submit) {
            return true;
        }
        //推荐config('linshou_fenhong_inviter_num')个体验馆，并且自身级别大于config('linshou_fenhong_level_id')的才能分红
        $tiyanguan_level = model('vip_level')->where(array('need_buy_experience_goods' => 1))->order('level_sort ASC')->find();
        $fenhong_level = model('vip_level')->where(array('id' => config('linshou_fenhong_level_id')))->order('level_sort ASC')->find();
        $gudong_list = array();
        $result = model('member')->getList(array(), 'uid,level_id,inviter_id');
        foreach ($result['list'] as $rr) {
            $member_level = model('vip_level')->field('level_sort')->where(array('id' => $rr['level_id']))->find();
            if ($member_level['level_sort'] < $fenhong_level['level_sort']) {//排除自身级别没有分红资格的
                continue;
            }
            //统计邀请体验馆的人数
            /*$inviter_num = 0;
            foreach ($result['list'] as $vv) {
                if ($vv['inviter_id'] == $rr['uid'] && $vv['level_id'] >= $tiyanguan_level['id']) {
                    $inviter_num = $inviter_num + 1;
                }
            }*/
            //统计购买过体验套餐的人
            $inviter_num = model('member')->where(array('inviter_id' => $rr['uid'], 'has_buy_tygoods' => 1))->total();
            if ($inviter_num >= config('linshou_fenhong_inviter_num')) {
                $gudong_list[$rr['uid']] = $rr;
            }
        }
        unset($result);
        if (count($gudong_list) <= 0) {
            return true;
        }
        $detail_bonus = priceFormat(bcdiv(($total_yeji * config('fenhong_reward_bili') * 0.01), count($gudong_list), 2));//加权分红
        if ($detail_bonus <= 0) {
            return true;
        }
        $detail_data = [];
        foreach ($gudong_list as $vv) {
            $desc = '获得零售商城加权分红' . $detail_bonus . '元';
            $detail_data[] = array(
                'uniacid' => 1,
                'uid' => $vv['uid'],
                'detail_bonus' => $detail_bonus,
                'detail_desc' => $desc,
                'detail_addtime' => time(),
                'detail_status' => ORDER_STATE_SUCCESS,
                'type' => 3,
                'yue_turnover' => $total_yeji,
                'total_fenshu' => count($gudong_list),
                'total_turnover' => $total_yeji,
            );
        }
        if ($detail_data) {
            model('distribute_fenhong_record_detail')->insertAll($detail_data);
            foreach ($detail_data as $v) {
                $logic_pd = logic('predeposit');
                $data_pd = array();
                $data_pd['uid'] = $v['uid'];
                $data_pd['member_name'] = '';
                $data_pd['amount'] = $v['detail_bonus'];
                $data_pd['order_sn'] = '';
                $data_pd['lg_desc'] = $v['detail_desc'];
                $logic_pd->changePd('commission_in', $data_pd);
            }
        }
        return true;
    }


    public function jiaquan_fenhong_new($yeji, $start_unixtime, $end_unixtime)
    {
        //平台总绩效
        $total_yeji = $yeji;
        $detail_data = array();
        $level_list = $this->get_level_list();
        foreach ($level_list as $v) {
            if ($v['jiaquan_fenhong_bili'] <= 0) {
                continue;
            }
            $member_list = model('member')->field('uid')->where(array('level_id' => $v['id']))->select();
            $total_fenshu = 0;
            $fenhong_member_list = array();
            foreach ($member_list as $kk => $vv) {
                $yue_yeji = $this->get_team_yeji_by_month2($vv['uid'], $start_unixtime, $end_unixtime);
                //累计分红金额
                $res = model('distribute_fenhong_record_detail')->field('SUM(detail_bonus) as total_bonus')->where(array('uid' => $vv['uid'], 'type' => 1))->find();
                if (($yue_yeji >= $v['jiaquan_fenhong_yue_yeji']) && ($res['total_bonus'] < $v['jiaquan_fenhong_total'])) {
                    $total_fenshu++;
                    $fenhong_member_list[] = $vv;
                }
            }
            foreach ($fenhong_member_list as $vv) {
                $detail_bonus = priceFormat(bcdiv(($total_yeji * $v['jiaquan_fenhong_bili'] * 0.01), $total_fenshu, 2));//加权分红
                if ($detail_bonus <= 0) {
                    continue;
                }
                $desc = '获得' . $v['level_name'] . '加权分红' . $detail_bonus . '元';
                $detail_data[] = array(
                    'uniacid' => 1,
                    'uid' => $vv['uid'],
                    'detail_bonus' => $detail_bonus,
                    'detail_desc' => $desc,
                    'detail_addtime' => time(),
                    'detail_status' => ORDER_STATE_SUCCESS,
                    'type' => 1,
                    'yue_turnover' => $yue_yeji,
                    'total_fenshu' => $total_fenshu,
                    'total_turnover' => $total_yeji,
                );
            }
        }
        if ($detail_data) {
            model('distribute_fenhong_record_detail')->insertAll($detail_data);
            foreach ($detail_data as $v) {
                $logic_pd = logic('predeposit');
                $data_pd = array();
                $data_pd['uid'] = $v['uid'];
                $data_pd['member_name'] = '';
                $data_pd['amount'] = $v['detail_bonus'];
                $data_pd['order_sn'] = '';
                $data_pd['lg_desc'] = $v['detail_desc'];
                $logic_pd->changePd('commission_in', $data_pd);
            }
        }
        return true;
    }

    public function get_team_yeji_by_month2($uid, $start_unixtime, $end_unixtime)
    {
        //$month = lib\timer::getMonthBeginAndEnd(0, $month);
        $month = array(
            $start_unixtime,
            $end_unixtime
        );
        $str = ',' . $uid . ',';
        $result = model('distribute_account')->getList(array('dis_path' => '%' . $str . '%'), 'uid');
        $uids = [$uid];//算上自己
        foreach ($result['list'] as $v) {
            $uids[] = $v['uid'];
        }
        $order_list = model('shop_order')->field('uid,order_amount')->where(array('uid' => $uids, 'payment_time >=' => $month[0], 'payment_time <=' => $month[1], 'lock_state' => 0))->select();
        $team_yeji = 0;
        foreach ($order_list as $v) {
            //$team_yeji += $v['order_amount'];
        }
        $shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where(array('uid' => $uids, 'add_time >=' => $month[0], 'add_time <=' => $month[1], 'state' => array(0, 1, 2)))->select();
        foreach ($shop_goods_tihuoquan as $v) {
            $team_yeji += $v['amount'];
        }
        return $team_yeji;
    }

    //加权分红,每月初第一天分上个月的(废弃)
    public function jiaquan_fenhong_old()
    {
        $this_month = lib\timer::month();
        $lock_submit = model('distribute_fenhong_record_detail')->where(array('detail_addtime >=' => $this_month[0], 'type' => 1))->find();
        if ($lock_submit) {
            return true;
        }
        $month = lib\timer::lastMonth();
        //$month = lib\timer::month();//测试
        $month_num = date('m', $month[0]);
        //平台总业绩
        $order_list = model('shop_order')->field('order_amount')->where(array('payment_time >' => 0, 'lock_state' => 0))->select();
        $total_yeji = 0;
        foreach ($order_list as $v) {
            //$total_yeji += $v['order_amount'];
        }
        $shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where(array('state' => array(0, 1, 2)))->select();
        foreach ($shop_goods_tihuoquan as $v) {
            $total_yeji += $v['amount'];
        }
        $detail_data = array();
        $level_list = $this->get_level_list();
        foreach ($level_list as $v) {
            if ($v['jiaquan_fenhong_bili'] <= 0) {
                continue;
            }
            $member_list = model('member')->field('uid')->where(array('level_id' => $v['id']))->select();
            $total_fenshu = 0;
            $fenhong_member_list = array();
            foreach ($member_list as $kk => $vv) {
                $yue_yeji = $this->get_team_yeji_by_month($vv['uid'], $month_num);
                //累计分红金额
                $res = model('distribute_fenhong_record_detail')->field('SUM(detail_bonus) as total_bonus')->where(array('uid' => $vv['uid'], 'type' => 1))->find();
                if (($yue_yeji >= $v['jiaquan_fenhong_yue_yeji']) && ($res['total_bonus'] < $v['jiaquan_fenhong_total'])) {
                    $total_fenshu++;
                    $fenhong_member_list[] = $vv;
                }
            }
            foreach ($fenhong_member_list as $vv) {
                $detail_bonus = priceFormat(bcdiv(($total_yeji * $v['jiaquan_fenhong_bili'] * 0.01), $total_fenshu, 2));//加权分红
                if ($detail_bonus <= 0) {
                    continue;
                }
                $desc = '获得' . $v['level_name'] . '加权分红' . $detail_bonus . '元';
                $detail_data[] = array(
                    'uniacid' => 1,
                    'uid' => $vv['uid'],
                    'detail_bonus' => $detail_bonus,
                    'detail_desc' => $desc,
                    'detail_addtime' => time(),
                    'detail_status' => ORDER_STATE_SUCCESS,
                    'type' => 1,
                    'yue_turnover' => $yue_yeji,
                    'total_fenshu' => $total_fenshu,
                    'total_turnover' => $total_yeji,
                );
            }
        }
        if ($detail_data) {
            model('distribute_fenhong_record_detail')->insertAll($detail_data);
            foreach ($detail_data as $v) {
                $logic_pd = logic('predeposit');
                $data_pd = array();
                $data_pd['uid'] = $v['uid'];
                $data_pd['member_name'] = '';
                $data_pd['amount'] = $v['detail_bonus'];
                $data_pd['order_sn'] = '';
                $data_pd['lg_desc'] = $v['detail_desc'];
                $logic_pd->changePd('commission_in', $data_pd);
            }
        }
        return true;
    }

    //同级奖：联创A推荐联创B，公司奖励联创A每张提货券100元
    public function tongji_reward($uid, $num)
    {
        $buyer_info = model('member')->getInfo(array('uid' => $uid));
        if (empty($buyer_info['inviter_id'])) {
            return true;
        }
        //获得上级ids
        $dis_account = model('distribute_account')->getInfo(array('uid' => $uid));
        $parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
        if (!$parent) {
            return true;
        }
        $parent = array_reverse($parent);

        $parests_list = array();
        if ($parent) {
            $result = model('member')->getList(array('uid' => $parent));
            foreach ($result['list'] as $rr) {
                $parests_list[$rr['uid']] = $rr;
            }
            unset($result);
        }
        //获取分销商的级别
        $distributor_levels = array();
        $result = model('distribute_account')->getList(array('uid' => $parent), 'uid,level_id');
        foreach ($result['list'] as $rr) {
            $distributor_levels[$rr['uid']] = $rr['level_id'];
        }
        unset($result);

        $level_list = $this->get_level_list();
        $n = 0;
        foreach ($parent as $key => $value) {
            if ($n >= 2) {
                break;
            }
            $detail_data = array();
            $p_info = $parests_list[$value];
            foreach ($level_list as $v) {
                if ($v['tongji_bonus'] <= 0 && $v['tongji_bonus2'] <= 0) {
                    continue;
                }
                if ($buyer_info['level_id'] == $p_info['level_id'] && $p_info['level_id'] == $v['id']) {
                    $record_data = array(
                        'uniacid' => 1,
                        'buyer_id' => $uid,
                        'order_sn' => '',
                        'goods_id' => 0,
                        'goods_name' => '提货券',
                        'goods_image' => '',
                        'goods_num' => $num,
                        'record_addtime' => time(),
                        'record_status' => ORDER_STATE_PAY
                    );
                    $recordid = model('distribute_other')->add($record_data);
                    if ($recordid > 0) {
                        if ($n == 0) {
                            $detail_bonus = number_format($v['tongji_bonus'] * $num, 2, '.', '');
                        } else if ($n == 1) {
                            $detail_bonus = number_format($v['tongji_bonus2'] * $num, 2, '.', '');
                        }

                        if (isset($detail_bonus) && $detail_bonus > 0) {
                            $detail_desc = $buyer_info['nickname'] . ' 购买提货券,你获得' . ($n + 1) . '级同级奖励' . $detail_bonus . '元';

                            $detail_data[] = array(
                                'uniacid' => 1,
                                'record_id' => $recordid,
                                'order_sn' => '',
                                'uid' => $value,
                                'detail_level' => 1,
                                'detail_bonus' => $detail_bonus,
                                'detail_desc' => $detail_desc,
                                'detail_status' => ORDER_STATE_PAY,
                                'detail_addtime' => time(),
                                'detail_type' => 1,
                            );
                            model('distribute_detail_other')->insertAll($detail_data);
                            foreach ($detail_data as $v) {
                                $logic_pd = logic('predeposit');
                                $data_pd = array();
                                $data_pd['uid'] = $v['uid'];
                                $data_pd['member_name'] = '';
                                $data_pd['amount'] = $v['detail_bonus'];
                                $data_pd['order_sn'] = '';
                                $data_pd['lg_desc'] = $v['detail_desc'];
                                $logic_pd->changePd('commission_in', $data_pd);
                            }
                            //成功发奖励的才累计加1
                            $n++;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function get_team_yeji_by_month($uid, $month)
    {
        $month = lib\timer::getMonthBeginAndEnd(0, $month);
        $str = ',' . $uid . ',';
        $result = model('distribute_account')->getList(array('dis_path' => '%' . $str . '%'), 'uid');
        $uids = [$uid];//算上自己
        foreach ($result['list'] as $v) {
            $uids[] = $v['uid'];
        }
        $order_list = model('shop_order')->field('uid,order_amount')->where(array('uid' => $uids, 'payment_time >=' => $month[0], 'payment_time <=' => $month[1], 'lock_state' => 0))->select();
        $team_yeji = 0;
        foreach ($order_list as $v) {
            //$team_yeji += $v['order_amount'];
        }
        $shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where(array('uid' => $uids, 'add_time >=' => $month[0], 'add_time <=' => $month[1], 'state' => array(0, 1, 2)))->select();
        foreach ($shop_goods_tihuoquan as $v) {
            $team_yeji += $v['amount'];
        }
        return $team_yeji;
    }

    public function get_self_yeji_by_month($uid, $month)
    {
        $month = lib\timer::getMonthBeginAndEnd(0, $month);
        $order_list = model('shop_order')->field('uid,order_amount')->where(array('uid' => $uid, 'payment_time >=' => $month[0], 'payment_time <=' => $month[1], 'lock_state' => 0))->select();
        $self_yeji = 0;
        foreach ($order_list as $v) {
            $self_yeji += $v['order_amount'];
        }
        return $self_yeji;
    }

    //分销商品获得佣金
    public function add_distributor_good_commission($order_goods, $buyerid, $buyer_name, $type = 0)
    {
        if (!config('distributor_open_goods')) {
            return true;
        }
        //获得上级ids
        $dis_account = model('distribute_account')->getInfo(array('uid' => $buyerid), 'dis_path,uniacid,level_id');
        $parent = $dis_account['dis_path'] ? explode(',', trim($dis_account['dis_path'], ',')) : array();
        $parent = array_reverse($parent);
        if (count($parent) > config('distributor_level_goods')) {
            $parent = array_slice($parent, 0, config('distributor_level_goods'));
        }
        if (config('distributor_self_goods') == 1) {//开启了自销
            $parent[count($parent)] = $buyerid;
        }

        if (empty($parent)) {
            return true;
        }
        $parests_list = array();
        if ($parent) {
            $result = model('member')->getList(array('uid' => $parent));
            foreach ($result['list'] as $rr) {
                $parests_list[$rr['uid']] = $rr;
            }
            unset($result);
        }
        //获取分销商的级别
        $distributor_levels = array();
        $result = model('distribute_account')->getList(array('uid' => $parent), 'uid,level_id');
        foreach ($result['list'] as $rr) {
            $distributor_levels[$rr['uid']] = $rr['level_id'];
        }
        unset($result);
        $goods_info = array();
        foreach ($order_goods as $good) {
            $goods_info[$good['goods_id']] = array('price' => $good['goods_price'], 'num' => $good['goods_num'], 'name' => $good['goods_name'], 'image' => $good['goods_image'], 'order_sn' => $good['order_sn']);
        }
        unset($order_goods);
        //获取goods_commonid
        $common_info = array();
        $result = model('shop_goods')->getList(array('goods_id' => array_keys($goods_info)), 'goods_commonid,goods_id,goods_costprice');
        foreach ($result['list'] as $g) {
            $common_info[$g['goods_commonid']][] = $g;
            $goods_info[$g['goods_id']]['costprice'] = $g['goods_costprice'];
        }
        unset($result);
        //获得每个商品的分销信息
        $commission_result = array();
        $result = model('shop_goods_common')->getList(array('goods_commonid' => array_keys($common_info)), 'goods_commonid,goods_commission,good_profit,yeji_price');
        foreach ($result['list'] as $r) {
            if ($r['good_profit'] > 0 && !empty($r['goods_commission'])) {
                $commission_result[$r['goods_commonid']] = array('profit' => $r['good_profit'], 'commission' => $r['goods_commission'], 'yeji_price' => $r['yeji_price']);
            }
        }
        unset($result);
        if (!$commission_result) {
            return true;
        }

        foreach ($commission_result as $key => $value) {
            foreach ($common_info[$key] as $g_info) {
                $order_sn = $goods_info[$g_info['goods_id']]['order_sn'];
                $record_data = array(
                    'uniacid' => $dis_account['uniacid'],
                    'buyer_id' => $buyerid,
                    'order_sn' => $order_sn,
                    'goods_id' => $g_info['goods_id'],
                    'goods_name' => $goods_info[$g_info['goods_id']]['name'],
                    'goods_image' => $goods_info[$g_info['goods_id']]['image'],
                    'order_type' => $type,
                    'goods_price' => $goods_info[$g_info['goods_id']]['price'],
                    'goods_num' => $goods_info[$g_info['goods_id']]['num'],
                    'record_addtime' => time(),
                    'record_status' => ORDER_STATE_PAY
                );
                $recordid = model('distribute')->add($record_data);
                if ($recordid > 0) {
                    //$bonus_profit = ($goods_info[$g_info['goods_id']]['price'] - $goods_info[$g_info['goods_id']]['costprice']) * $value['profit'] * 0.01;
                    $bonus_profit = $value['yeji_price'];
                    $commission_detail = fxy_unserialize($value['commission']);
                    $detail_data = array();
                    foreach ($parent as $k => $mid) {
                        $my_bonus_price = 0;
                        if ($mid != $buyerid) {
                            if (!empty($commission_detail[$distributor_levels[$mid]])) {
                                if (!empty($commission_detail[$distributor_levels[$mid]][$k])) {
                                    $my_bonus_price = $bonus_profit * floatval($commission_detail[$distributor_levels[$mid]][$k]) * 0.01;
                                }
                            }
                        } else {
                            //自销
                            if (!empty($commission_detail[$distributor_levels[$mid]])) {
                                if (!empty($commission_detail[$distributor_levels[$mid]][config('distributor_level_goods')])) {
                                    $my_bonus_price = $bonus_profit * floatval($commission_detail[$distributor_levels[$mid]][config('distributor_level_goods')]) * 0.01;
                                }
                            }
                        }

                        $detail_bonus = $my_bonus_price == 0 ? 0 : number_format(($my_bonus_price * $goods_info[$g_info['goods_id']]['num']), 2, '.', '');
                        if ($detail_bonus) {
                            $detail_desc = ($mid == $buyerid ? '自己销售自己购买' : '你的' . ($k + 1) . '楼会员 ' . $buyer_name . ' 购买') . $goods_info[$g_info['goods_id']]['name'] . '(单价：' . $goods_info[$g_info['goods_id']]['price'] . '元,数量：' . $goods_info[$g_info['goods_id']]['num'] . '),你获得' . config('bonus_name_goods') . $detail_bonus . '元';
                            $detail_data[] = array(
                                'uniacid' => $dis_account['uniacid'],
                                'record_id' => $recordid,
                                'good_id' => $g_info['goods_id'],
                                'order_sn' => $order_sn,
                                'uid' => $mid,
                                'detail_level' => ($mid == $buyerid ? 0 : ($k + 1)),
                                'detail_bonus' => $detail_bonus,
                                'detail_price' => $my_bonus_price == 0 ? 0 : number_format($my_bonus_price, 2, '.', ''),
                                'detail_num' => $goods_info[$g_info['goods_id']]['num'],
                                'detail_desc' => $detail_desc,
                                'detail_status' => ORDER_STATE_PAY,
                                'detail_addtime' => time()
                            );
                            $tpl_data = array(
                                'openid' => $parests_list[$mid]['openid'],
                                'type_name' => config('bonus_name_goods'),
                                'detail_bonus' => $detail_bonus,
                                'detail_desc' => $detail_desc,
                            );
                            logic('tpl_message')->get_reward($tpl_data);
                        }
                    }
                    if ($detail_data) {
                        model('distribute_detail')->insertAll($detail_data);
                        foreach ($detail_data as $v) {
                            $logic_pd = logic('predeposit');
                            $data_pd = array();
                            $data_pd['uid'] = $v['uid'];
                            $data_pd['member_name'] = '';
                            $data_pd['amount'] = $v['detail_bonus'];
                            $data_pd['order_sn'] = '';
                            $data_pd['lg_desc'] = $v['detail_desc'];
                            $logic_pd->changePd('commission_in', $data_pd);
                        }
                        /*发送消息*/
                        $access_token = logic('weixin_token')->get_access_token(config());
                        logic('weixin_message')->senddismess($access_token, config(), $detail_data);
                    }
                }
            }
        }
        return true;
    }

    /*
    自定义奖励更改状态
    */
    public function update_goods_other_state($order_sn, $state)
    {
        model('distribute_other')->edit(array('order_sn' => $order_sn), array('record_status' => $state));
        model('distribute_detail_other')->edit(array('order_sn' => $order_sn), array('detail_status' => $state));
        return true;
    }

    /*
    商品分销奖励更改状态
    */
    public function update_goods_commission_state($order_sn, $state)
    {
        model('distribute')->edit(array('order_sn' => $order_sn), array('record_status' => $state));
        model('distribute_detail')->edit(array('order_sn' => $order_sn), array('detail_status' => $state));
        return true;
    }

    //团队增加业绩
    public function team_performance_add($order_info, $goods_num)
    {
        $account = model('distribute_account')->getInfo(array('uid' => $order_info['uid']), 'id,dis_path');
        if (!$account) {
            return true;
        }
        $parent = explode(',', trim($account['dis_path'], ','));
        return model('distribute_account')->where(array('uid' => $parent))->update('team_performance_money=team_performance_money+' . $order_info['order_amount'] . ',team_performance_num=team_performance_num+' . $goods_num);
    }

    //个人增加业绩
    public function self_performance_add($order_info, $goods_num)
    {
        $account = model('distribute_account')->getInfo(array('uid' => $order_info['uid']), 'id');
        if (!$account) {
            return true;
        }
        return model('distribute_account')->where(array('id' => $account['id']))->update('self_performance_money=self_performance_money+' . $order_info['order_amount'] . ',self_performance_num=self_performance_num+' . $goods_num);
    }

    //团队消减业绩
    public function team_performance_minus($order_info, $goods_num)
    {
        $account = model('distribute_account')->getInfo(array('uid' => $order_info['uid']), 'id,dis_path');
        if (!$account) {
            return true;
        }
        $parent = explode(',', trim($account['dis_path'], ','));
        return model('distribute_account')->where(array('uid' => $parent))->update('team_performance_money=team_performance_money-' . $order_info['order_amount'] . ',team_performance_num=team_performance_num-' . $goods_num);
    }

    //个人消减业绩
    public function self_performance_minus($order_info, $goods_num)
    {
        $account = model('distribute_account')->getInfo(array('uid' => $order_info['uid']), 'id');
        if (!$account) {
            return true;
        }
        return model('distribute_account')->where(array('id' => $account['id']))->update('self_performance_money=self_performance_money-' . $order_info['order_amount'] . ',self_performance_num=self_performance_num-' . $goods_num);
    }

    //购买成为分销商
    public function add_distributer($uid = 0, $goods_commonids = array(), $pay_amount = 0)
    {
        if (!$uid) {
            return false;
        }
        $add_flag = false;
        if (config('dis_cometype') == 1 && $pay_amount >= config('dis_come_money')) {
            $add_flag = true;
        }
        if (config('dis_cometype') == 2) {
            $goods_ids = explode(',', trim(config('dis_goods_ids'), ','));
            $check_intersect = array_intersect($goods_commonids, $goods_ids);
            if (!empty($check_intersect)) {
                $add_flag = true;
            }
        }
        if (config('dis_cometype') == 3) {
            $add_flag = true;
        }
        if ($add_flag) {
            $buyer_info = model('member')->getInfo(array('uid' => $uid), 'level_id');
            $buyer_level = model('vip_level')->getInfo(array('id' => $buyer_info['level_id']));
            $dis_level = model('vip_level')->getInfo(array('level_default' => 0), '*', 'level_sort ASC');

            $upgrade_account = $upgrade_member = array();
            $upgrade_member = array(
                'is_distributor' => 1,
            );
            if ($buyer_level['level_sort'] < $dis_level['level_sort']) {
                $upgrade_member['level_id'] = $dis_level['id'];
                $upgrade_account = array(
                    'level_id' => $dis_level['id'],
                );
            }
            $flag = model('member')->edit(array('uid' => $uid), $upgrade_member);
            if ($flag) {
                if ($upgrade_account) {
                    model('distribute_account')->edit(array('uid' => $uid), $upgrade_account);
                }
                $buyer_account = model('distribute_account')->field('inviter_id,dis_path')->where(array('uid' => $uid))->find();
                if (empty($buyer_account['inviter_id'])) {
                    return true;
                }
                $parent = explode(',', trim($buyer_account['dis_path'], ','));
                //更新直推分销商人数&&团队分销商人数
                model('distribute_account')->edit(array('uid' => $buyer_account['inviter_id']), 'inviter_dis_num=inviter_dis_num+1');
                model('distribute_account')->edit(array('uid' => $parent), 'team_dis_num=team_dis_num+1');
            }
        }
        return true;
    }

    //统计直推分销商
    public function get_inviter_distributer($uid = 0)
    {
        if (!$uid) {
            return false;
        }
        return model('member')->where(array('is_distributor' => 1, 'inviter_id' => $uid))->select();
    }

    //统计团队分销商
    public function get_team_distributer($uid = 0)
    {
        if (!$uid) {
            return false;
        }
        $distribute_account_list = model('distribute_account')->field('uid')->where(array('dis_path' => '%,' . $uid . ',%'))->select();
        $uids = [];
        foreach ($distribute_account_list as $k => $v) {
            $uids[] = $v['uid'];
        }
        return model('member')->where(array('is_distributor' => 1, 'uid' => $uids))->select();
    }

    public function turntable_reward($result, $member_info, $config)
    {
        //得到上周销售额
        $lastWeek = lib\timer::lastWeek();
        $condition['payment_time >='] = $lastWeek[0];
        $condition['payment_time <='] = $lastWeek[1];
        $order = model('fxy_shop_order')->field('order_amount,order_state,lock_state')->where($condition)->select();
        $lastWeek_turnover = 0;
        foreach ($order as $v) {
            if ($v['order_state'] >= ORDER_STATE_PAY && $v['lock_state'] == 0) {
                $lastWeek_turnover += $v['order_amount'];
            }
        }
        if ($lastWeek_turnover <= 0) {
            return callback(false, '上周无业绩');
        }
        $jiangchi = $lastWeek_turnover * $config['week_sales_bili'] * 0.01;
        if ($jiangchi <= 0) {
            return callback(false, '奖池未设置');
        }
        $reward_bouns = $jiangchi * $result['reward_ratio'] * 0.01;
        if ($reward_bouns <= 0) {
            return callback(false, '暂无奖励');
        }
        $member_reward = array(
            'uniacid' => config('uniacid'),
            'uid' => $member_info['uid'],
            'detail_bonus' => $reward_bouns,
            'detail_desc' => '您抽中' . $result['name'] . '，获取奖励' . $reward_bouns . '元',
            'detail_desc2' => '抽中' . $result['name'],
            'detail_addtime' => time(),
            'detail_status' => ORDER_STATE_SUCCESS,
            'week_turnover' => $lastWeek_turnover,
        );
        model('distribute_turntable_record_detail')->insert($member_reward);
        return callback(true);
    }
}
