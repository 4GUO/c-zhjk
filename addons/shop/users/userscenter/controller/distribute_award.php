<?php

namespace userscenter\controller;

use lib;

class distribute_award extends control
{
    public function __construct()
    {
        parent::_initialize();
    }

    public function other_indexOp()
    {
        $distribute_detail = model('distribute_detail_other');
        $where = array();
        $where['uniacid'] = $this->uniacid;
        $type = input('get.type', '');
        if ($type) {
            $where['detail_type'] = $type;
        }
        $keyword = input('get.keyword', '');
        $search_type = input('get.search_type', '');
        if ($keyword) {
            $search_uids = array();
            $search_uids[] = 0;
            $result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
            foreach ($result['list'] as $r) {
                $search_uids[] = $r['uid'];
            }
            $where['uid'] = $search_uids;
        }

        $query_start_date = input('query_start_date', '');
        $query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['detail_addtime >='] = $start_unixtime;
        }
        if ($end_unixtime > 0) {
            $where['detail_addtime <='] = $end_unixtime;
        }
        $list = $distribute_detail->getList($where, '*', 'detail_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('distribute_award/other_index')));
        $this->assign('list', $list['list']);

        $uids = array();
        $record_list = array();
        if (!empty($list['list'])) {
            $record_ids = array();
            foreach ($list['list'] as $r) {
                if (!in_array($r['record_id'], $record_ids)) {
                    $record_ids[] = $r['record_id'];
                }
            }
            $result = model('distribute_other')->getList(array('record_id' => $record_ids), 'record_id,buyer_id,goods_id,goods_name,goods_image,goods_num');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    if (!in_array($rr['buyer_id'], $uids)) {
                        $uids[] = $rr['buyer_id'];
                    }
                    $record_list[$rr['record_id']] = $rr;
                }
            }
            unset($result);
        }
        $this->assign('record_list', $record_list);


        $member_list = array();
        if (!empty($list['list'])) {
            foreach ($list['list'] as $r) {
                if (!in_array($r['uid'], $uids)) {
                    $uids[] = $r['uid'];
                }
            }
            $result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $member_list[$rr['uid']] = array('nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png', 'mobile' => $rr['mobile']);
                }
            }
            unset($result);
        }
        $this->assign('member_list', $member_list);
        $this->display();
    }

    public function area_indexOp()
    {
        $distribute_detail = model('distribute_area_record_detail');
        $where = array();
        $where['uniacid'] = $this->uniacid;
        $keyword = input('get.keyword', '');
        $search_type = input('get.search_type', '');
        if ($keyword) {
            $search_uids = array();
            $search_uids[] = 0;
            $result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
            foreach ($result['list'] as $r) {
                $search_uids[] = $r['uid'];
            }
            $where['uid'] = $search_uids;
        }
        $query_start_date = input('query_start_date', '');
        $query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['detail_addtime >='] = $start_unixtime;
        }
        if ($end_unixtime > 0) {
            $where['detail_addtime <='] = $end_unixtime;
        }
        $list = $distribute_detail->getList($where, '*', 'detail_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('distribute_award/fenhong_index')));
        $this->assign('list', $list['list']);

        $mapping_fans = array();
        if (!empty($list['list'])) {
            $uids = array();
            foreach ($list['list'] as $r) {
                if (!in_array($r['uid'], $uids)) {
                    $uids[] = $r['uid'];
                }
            }
            $result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $mapping_fans[$rr['uid']] = array('nickname' => $rr['nickname'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png', 'mobile' => $rr['mobile']);
                }
            }
            unset($result);
        }
        $this->assign('mapping_fans', $mapping_fans);
        $this->display();
    }

    public function fenhong_indexOp()
    {
        $distribute_detail = model('distribute_fenhong_record_detail');
        $where = array();
        $where['uniacid'] = $this->uniacid;
        $keyword = input('get.keyword', '');
        $search_type = input('get.search_type', '');
        if ($keyword) {
            $search_uids = array();
            $search_uids[] = 0;
            $result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
            foreach ($result['list'] as $r) {
                $search_uids[] = $r['uid'];
            }
            $where['uid'] = $search_uids;
        }
        $fenhong_type = input('get.fenhong_type', 0, 'intval');
        if ($fenhong_type) {
            $where['type'] = $fenhong_type;
        }
        $query_start_date = input('query_start_date', '');
        $query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : 0;
        $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : 0;
        if ($start_unixtime > 0) {
            $where['detail_addtime >='] = $start_unixtime;
        }
        if ($end_unixtime > 0) {
            $where['detail_addtime <='] = $end_unixtime;
        }
        $list = $distribute_detail->getList($where, '*', 'detail_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'fenhong_type' => $fenhong_type, 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('distribute_award/fenhong_index')));
        $this->assign('list', $list['list']);

        $mapping_fans = array();
        if (!empty($list['list'])) {
            $uids = array();
            foreach ($list['list'] as $r) {
                if (!in_array($r['uid'], $uids)) {
                    $uids[] = $r['uid'];
                }
            }
            $result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $mapping_fans[$rr['uid']] = array('nickname' => $rr['nickname'], 'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png', 'mobile' => $rr['mobile']);
                }
            }
            unset($result);
        }
        $this->assign('mapping_fans', $mapping_fans);
        $this->display();
    }

    public function indexOp()
    {
        $model_distribute = model('distribute');
        $where = array();
        $where['uniacid'] = $this->uniacid;
        $keyword = input('get.keyword', '');
        $search_type = input('get.search_type', '');
        if ($keyword) {
            if ($search_type == 'goods_name') {
                $search_goodsid = array();
                $search_goodsid[] = 0;
                $condition['uniacid'] = $this->uniacid;
                $condition['goods_name'] = '%' . trim($keyword) . '%';
                $result = model('goods')->getList($condition, 'goods_id');
                if (!empty($result['list']) && is_array($result['list'])) {
                    foreach ($result['list'] as $r) {
                        $search_goodsid[] = $r['goods_id'];
                    }
                }
                unset($condition);
                unset($result);
                $where['goods_id'] = $search_goodsid;
            } else {
                $search_uids = array();
                $search_uids[] = 0;
                $condition['uniacid'] = $this->uniacid;
                $condition[$search_type] = '%' . trim($keyword) . '%';
                $result = model('member')->getList($condition, 'uid');
                if (!empty($result['list']) && is_array($result['list'])) {
                    foreach ($result['list'] as $r) {
                        $search_uids[] = $r['uid'];
                    }
                }
                unset($condition);
                unset($result);
                $where['buyer_id'] = $search_uids;
            }
        }

        $status = input('get.status', -1, 'intval');
        if ($status > -1) {
            $where['record_status'] = $status;
        }

        $query_start_date = input('query_start_date', '');
        $query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
        $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;

        if ($start_unixtime || $end_unixtime) {
            $where['record_addtime >='] = $start_unixtime;
            $where['record_addtime <='] = $end_unixtime;
        }

        $list = $model_distribute->getList($where, '*', 'record_id DESC', 10, input('get.page', 1, 'intval'));
        $this->assign('list', $list['list']);
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'search_type' => $search_type, 'keyword' => $keyword, 'status' => $status, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('distribute_award/index')));
        $member_list = $goods_info = $award_list = array();
        if (!empty($list['list'])) {
            $uids = array();
            $record_ids = array();
            $goods_ids = array();
            foreach ($list['list'] as $r) {
                if (!in_array($r['buyer_id'], $uids)) {
                    $uids[] = $r['buyer_id'];
                }
                if (!in_array($r['goods_id'], $goods_ids)) {
                    $goods_ids[] = $r['goods_id'];
                }
                if (!in_array($r['record_id'], $record_ids)) {
                    $record_ids[] = $r['record_id'];
                }
            }

            //获得分销奖金
            $result = model('distribute_detail')->getList(array('record_id' => $record_ids), 'record_id, detail_level, detail_bonus, uid', 'record_id desc, detail_level asc');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $award_list[$rr['record_id']][] = array(
                        'level' => $rr['detail_level'] == 0 ? '自销' : $rr['detail_level'] . '级',
                        'bonus' => $rr['detail_bonus'],
                        'uid' => $rr['uid']
                    );

                    if (!in_array($rr['uid'], $uids)) {
                        $uids[] = $rr['uid'];
                    }
                }
            }
            unset($result);

            $result = model('member')->getList(array('uid' => $uids), 'uid,nickname,truename,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $member_list[$rr['uid']] = $rr;
                }
            }
            unset($result);

            $result = model('shop_goods')->getList(array('goods_id' => $goods_ids), 'goods_name, goods_id, goods_image');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rrr) {
                    $goods_info[$rrr['goods_id']] = $rrr;
                }
            }
            unset($result);
        }

        $this->assign('award_list', $award_list);
        $this->assign('member_list', $member_list);
        $this->assign('goods_info', $goods_info);
        $this->display();
    }

    public function yeji_fenhong_sendOp()
    {
        $lianchuang_level = model('vip_level')->where(array('level_default' => 0))->order('level_sort DESC')->find();
        if (chksubmit()) {
            //发放分红
            $yeji = input('yeji', 0, 'floatval');
            if ($yeji <= 0) {
                output_error('绩效不能为空');
            }
            $query_start_date = input('query_start_date', '');
            $query_end_date = input('query_end_date', '');
            $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
            $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
            $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
            $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;
            if (!$start_unixtime || !$end_unixtime) {
                output_error('请选择时间');
            }
            $res = model('member')->field('SUM(fenhong_quan) as total_fenhong_quan')->where(array('level_id' => $lianchuang_level['id']))->find();
            if (!$res['total_fenhong_quan']) {
                output_error('当前暂无分红券');
            }
            $lc_members = model('member')->field('uid,fenhong_quan')->where(array('level_id' => $lianchuang_level['id']))->select();
            foreach ($lc_members as $v) {
                $detail_bonus = priceFormat(bcdiv(($yeji * config('yeji_fenhong_bili') * 0.01), $res['total_fenhong_quan'], 2) * $v['fenhong_quan']);//加权分红
                if ($detail_bonus <= 0) {
                    continue;
                }
                $desc = '获得绩效分红' . $detail_bonus . '元，可用分红券数量' . $v['fenhong_quan'] . '，每张分红券值' . priceFormat(bcdiv(($yeji * config('yeji_fenhong_bili') * 0.01), $res['total_fenhong_quan'], 2)) . '元';
                $detail_data[] = array(
                    'uniacid' => 1,
                    'uid' => $v['uid'],
                    'detail_bonus' => $detail_bonus,
                    'detail_desc' => $desc,
                    'detail_addtime' => time(),
                    'detail_status' => ORDER_STATE_SUCCESS,
                    'type' => 2,
                    'total_turnover' => $yeji,
                );
                //model('member')->where(array('uid' => $v['uid']))->update('fenhong_quan=fenhong_quan-1');
                model('member')->where(array('level_id' => $lianchuang_level['id']))->update('fenhong_quan=0');//edit by 20240702
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
            output_data(array('msg' => '分红成功', 'url' => _url('distribute_award/yeji_fenhong_send')));
        } else {
            $where = array();
            $query_start_date = input('query_start_date', '');
            $query_end_date = input('query_end_date', '');
            $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
            $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
            $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
            $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;
            if ($start_unixtime || $end_unixtime) {
                $where['used_time >='] = $start_unixtime;
                $where['used_time <='] = $end_unixtime;
            }
            if ($where) {
                //$where['state'] = array(0,1,2);
                $where['state'] = array(1, 2);
                //所有联创绩效总和
                $lc_uids = array();
                $res = model('member')->getList(array('level_id' => $lianchuang_level['id']), 'uid');
                foreach ($res['list'] as $v) {
                    $lc_uids[] = $v['uid'];
                }
                //$where['uid'] = $lc_uids;
                $total_yeji = 0;
                $shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where($where)->select();
                foreach ($shop_goods_tihuoquan as $v) {
                    $total_yeji += $v['amount'];
                }
                $this->assign('total_yeji', $total_yeji);
            }
            $res = model('member')->field('SUM(fenhong_quan) as total_fenhong_quan')->where(array('level_id' => $lianchuang_level['id']))->find();
            $this->assign('total_fenhong_quan', $res['total_fenhong_quan'] ?? 0);
            $this->display();
        }
    }

    public function jiaquan_fenhong_sendOp()
    {
        if (chksubmit()) {
            $yeji = input('yeji', 0, 'floatval');
            if ($yeji <= 0) {
                output_error('业绩不能空');
            }
            $query_start_date = input('query_start_date', '');
            $query_end_date = input('query_end_date', '');
            $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
            $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
            $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
            $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;
            if (!$start_unixtime || !$end_unixtime) {
                output_error('请选择时间');
            }
            logic('yewu')->jiaquan_fenhong_new($yeji, $start_unixtime, $end_unixtime);
            output_data(array('msg' => '分红成功', 'url' => _url('distribute_award/jiaquan_fenhong_send')));
        } else {
            $where = array();
            $query_start_date = input('query_start_date', '');
            $query_end_date = input('query_end_date', '');
            $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
            $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
            $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
            $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;
            if ($start_unixtime || $end_unixtime) {
                $where['add_time >='] = $start_unixtime;
                $where['add_time <='] = $end_unixtime;
            }
            if ($where) {
                $where['state'] = array(0, 1, 2);
                $total_yeji = 0;
                $shop_goods_tihuoquan = model('shop_goods_tihuoquan')->field('amount')->where($where)->select();
                foreach ($shop_goods_tihuoquan as $v) {
                    $total_yeji += $v['amount'];
                }
                $this->assign('total_yeji', $total_yeji);
            }
            $level_list = logic('yewu')->get_level_list();
            $total_fenshu = 0;
            foreach ($level_list as $v) {
                if ($v['jiaquan_fenhong_bili'] <= 0) {
                    continue;
                }
                $total_fenshu += model('member')->where(array('level_id' => $v['id']))->total();
            }
            $this->assign('total_fenshu', $total_fenshu);
            $this->display();
        }
    }

    //零售加权分红
    public function lingshou_fenhong_sendOp()
    {
        if (chksubmit()) {
            $yeji = input('yeji', 0, 'floatval');
            if ($yeji <= 0) {
                output_error('业绩不能空');
            }
            logic('yewu')->yue_deal_fenhong($yeji);
            output_data(array('msg' => '分红成功', 'url' => _url('distribute_award/lingshou_fenhong_send')));
        } else {
            $where = array();
            $where['lock_state'] = 0;
            //$month = lib\timer::lastMonth();/
            //$month = lib\timer::month();//测试
            $query_start_date = input('query_start_date', '');
            $query_end_date = input('query_end_date', '');
            $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
            $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
            $start_unixtime = $if_start_date ? strtotime($query_start_date . '00:00:00') : null;
            $end_unixtime = $if_end_date ? strtotime($query_end_date . '23:59:59') : null;
            if ($start_unixtime || $end_unixtime) {
                $where['payment_time >='] = $start_unixtime;
                $where['payment_time <='] = $end_unixtime;
            }
            $order_list = model('shop_order')->field('uid,order_amount')->where($where)->select();
            $total_yeji = 0;
            foreach ($order_list as $v) {
                $total_yeji += $v['order_amount'];
            }
            unset($order_list);
            $this->assign('total_yeji', $total_yeji);
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
                $inviter_num = 0;
                /*foreach ($result['list'] as $vv) {
                    if ($vv['inviter_id'] == $rr['uid'] && $vv['level_id'] >= $tiyanguan_level['id']) {
                        $inviter_num = $inviter_num + 1;
                    }
                }*/
                //统计购买过体验套餐的人
                $inviter_num = model('member')->where(array('inviter_id' => $rr['uid'], 'has_buy_tygoods' => 1))->total();
                if ($inviter_num >= config('linshou_fenhong_inviter_num')) {
                    $gudong_list[$rr['uid']] = $rr;
                }
                if ($inviter_num >= config('linshou_fenhong_inviter_num')) {
                    $gudong_list[$rr['uid']] = $rr;
                }
            }
            unset($result);
            $this->assign('total_fenshu', count($gudong_list));
            $this->display();
        }
    }

    public function fgjdjl_indexOp()
    {
        $distribute_detail = model('distribute_fgjdjl_record_detail');
        $where = array();
        $where['uniacid'] = $this->uniacid;
        $keyword = input('get.keyword', '');
        $search_type = input('get.search_type', '');
        if ($keyword) {
            $search_uids = array();
            $search_uids[] = 0;
            $result = model('member')->getList(array('uniacid' => $this->uniacid, $search_type => '%' . trim($keyword) . '%'), 'uid');
            foreach ($result['list'] as $r) {
                $search_uids[] = $r['uid'];
            }
            $where['uid'] = $search_uids;
        }

        // 复购见单奖励状态筛选
        $detail_status = input('get.detail_status', -1, 'intval');
        if ($detail_status > -1) {
            $where['detail_status'] = $detail_status;
        }

        $query_start_date = input('query_start_date', '');
        $query_end_date = input('query_end_date', '');
        $if_start_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_start_date);
        $if_end_date = preg_match('/^20\\d{2}-\\d{2}-\\d{2}$/', $query_end_date);
        $start_date = $if_start_date ? $query_start_date . ' 00:00:00' : '';
        $end_date = $if_end_date ? $query_end_date . ' 23:59:59' : '';
        if ($start_date) {
            $where['detail_addtime >='] = $start_date;
        }
        if ($end_date) {
            $where['detail_addtime <='] = $end_date;
        }

        $list = $distribute_detail->getList($where, '*', 'detail_id DESC', 20, input('get.page', 1, 'intval'));
        $this->assign('page', page($list['totalpage'], array('page' => input('get.page', 1, 'intval'), 'detail_status' => $detail_status, 'search_type' => $search_type, 'keyword' => $keyword, 'query_start_date' => $query_start_date, 'query_end_date' => $query_end_date), users_url('distribute_award/fgjdjl_index')));
        $this->assign('list', $list['list']);

        // 获取用户信息映射
        $mapping_fans = array();
        $mapping_from_users = array();
        if (!empty($list['list'])) {
            $uids = array();
            $from_uids = array();
            foreach ($list['list'] as $r) {
                if (!in_array($r['uid'], $uids)) {
                    $uids[] = $r['uid'];
                }
                if (!in_array($r['from_uid'], $from_uids)) {
                    $from_uids[] = $r['from_uid'];
                }
            }

            // 获取获奖人信息
            $result = model('member')->getList(array('uid' => $uids), 'uid,nickname,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $mapping_fans[$rr['uid']] = array(
                        'nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'],
                        'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png',
                        'mobile' => $rr['mobile']
                    );
                }
            }
            unset($result);

            // 获取下单人信息
            $result = model('member')->getList(array('uid' => $from_uids), 'uid,nickname,headimg,mobile');
            if (!empty($result['list']) && is_array($result['list'])) {
                foreach ($result['list'] as $rr) {
                    $mapping_from_users[$rr['uid']] = array(
                        'nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'],
                        'headimg' => !empty($rr['headimg']) ? $rr['headimg'] : STATIC_URL . '/shop/img/default_user.png',
                        'mobile' => $rr['mobile']
                    );
                }
            }
            unset($result);
        }

        $this->assign('mapping_fans', $mapping_fans);
        $this->assign('mapping_from_users', $mapping_from_users);
        $this->display();
    }

    // 复购见单奖励日志查看
    public function fgjdjl_logOp()
    {
        $log_file = BASE_PATH . '/data/fgjdjl_log.txt';


        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
            // 将日志内容按行分割
            $log_lines = explode("\n", $log_content);
            // 过滤空行
            $log_lines = array_filter($log_lines, function ($line) {
                return trim($line) !== '';
            });
            // 反转数组，让最新的日志在前面
            $log_lines = array_reverse($log_lines);
        } else {
            $log_content = '日志文件不存在或无法读取，尝试路径：' . $log_file;
            $log_lines = array();
        }

        // 统计信息
        $total_records = isset($log_lines) ? count($log_lines) : 0;
        $success_count = 0;
        $fail_count = 0;

        if (isset($log_lines) && is_array($log_lines)) {
            foreach ($log_lines as $line) {
                if (strpos($line, '成功发放复购见单奖励') !== false) {
                    $success_count++;
                } else {
                    $fail_count++;
                }
            }
        }

        $this->assign('log_content', $log_content);
        $this->assign('log_lines', $log_lines);
        $this->assign('total_records', $total_records);
        $this->assign('success_count', $success_count);
        $this->assign('fail_count', $fail_count);

        $this->display();
    }

    /**
     * 手动发放分红券
     * 显示待合成的分红券信息，并提供手动发放功能
     */
    public function manual_fenhongquanOp()
    {
        if (IS_API) {
            // 获取联创级别（最高级别）
            $lianchuang_level = model('vip_level')->field('id,level_name')->where(array('uniacid' => $this->uniacid, 'level_default' => 0))->order('level_sort DESC')->find();
            if (!$lianchuang_level) {
                output_error('未找到联创级别配置');
            }

            // 获取所有联创级别的用户
            $lianchuang_users = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('uniacid' => $this->uniacid, 'level_id' => $lianchuang_level['id']))->select();
            
            $synthesis_list = array(); // 可合成的列表
            $total_synthesis_count = 0; // 总可合成数量
            
            foreach ($lianchuang_users as $lianchuang_user) {
                $uid = $lianchuang_user['uid'];
                
                // 获取该联创用户的直推用户列表 - 完全按照原有逻辑
                $invite_list = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('inviter_id' => $uid))->select();
                
                // 检查是否有足够的直推用户（需要3个或以上）
                if (count($invite_list) < 3) {
                    continue;
                }
                
                // 检查是否有足够的激活提货券进行合成 - 重新设计逻辑
                $available_accounts = array();
                $used_uids_in_this_synthesis = array(); // 当前合成中已使用的用户ID
                
                foreach ($invite_list as $invite_user) {
                    if (count($available_accounts) >= 3) {
                        break;
                    }
                    
                    // 检查该用户是否已经在当前合成中使用过
                    if (in_array($invite_user['uid'], $used_uids_in_this_synthesis)) {
                        continue;
                    }
                    
                    if ($invite_user['can_tihuoquan_num'] > 0) {
                        $available_accounts[] = $invite_user;
                        $used_uids_in_this_synthesis[] = $invite_user['uid'];
                    } else {
                        // 如果直推用户没有激活提货券，则在团队中寻找有激活提货券的用户
                        $team_user = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('can_tihuoquan_num >' => 0, 'dis_path' => '%,' . $invite_user['uid'] . ',%'))->find();
                        if ($team_user && !in_array($team_user['uid'], $used_uids_in_this_synthesis)) {
                            $available_accounts[] = $team_user;
                            $used_uids_in_this_synthesis[] = $team_user['uid'];
                        }
                    }
                }
                
                // 如果找到足够的用户且有足够的激活提货券，则添加到可合成列表
                if (count($available_accounts) >= 3) {
                    // 获取联创用户信息
                    $lianchuang_info = model('member')->field('uid,nickname,mobile')->where(array('uniacid' => $this->uniacid, 'uid' => $uid))->find();
                    
                    // 获取消耗用户信息
                    $consumed_users_info = array();
                    foreach ($available_accounts as $account) {
                        $user_info = model('member')->field('uid,nickname,mobile,can_tihuoquan_num')->where(array('uniacid' => $this->uniacid, 'uid' => $account['uid']))->find();
                        if ($user_info) {
                            $consumed_users_info[] = $user_info;
                        }
                    }
                    
                    $synthesis_item = array(
                        'lianchuang_uid' => $uid,
                        'lianchuang_nickname' => $lianchuang_info['nickname'] ?: $lianchuang_info['mobile'],
                        'lianchuang_mobile' => $lianchuang_info['mobile'],
                        'consumed_users' => $consumed_users_info,
                        'consumed_tihuoquan_count' => 3,
                        'can_synthesis' => true
                    );
                    
                    $synthesis_list[] = $synthesis_item;
                    $total_synthesis_count++;
                }
            }
            
            // 添加调试信息
            $debug_info = array();
            $debug_info['total_lianchuang_users'] = count($lianchuang_users);
            
            $result = array(
                'lianchuang_level' => $lianchuang_level['level_name'],
                'total_synthesis_count' => $total_synthesis_count,
                'synthesis_list' => $synthesis_list,
                'can_synthesis' => $total_synthesis_count > 0,
                'debug' => $debug_info
            );
            
            output_data($result);
            
        } else {
            $this->assign('title', '手动发放分红券');
            $this->display();
        }
    }

    /**
     * 执行手动发放分红券
     */
    public function execute_manual_fenhongquanOp()
    {
        if (IS_API) {
            // 记录开始时间
            $start_time = time();
            $log_data = array();
            $log_data['start_time'] = date('Y-m-d H:i:s', $start_time);
            
            // 获取联创级别
            $lianchuang_level = model('vip_level')->field('id,level_name')->where(array('uniacid' => $this->uniacid, 'level_default' => 0))->order('level_sort DESC')->find();
            if (!$lianchuang_level) {
                output_error('未找到联创级别配置');
            }
            
            $log_data['lianchuang_level'] = $lianchuang_level['level_name'] . '(ID:' . $lianchuang_level['id'] . ')';
            
            // 获取所有联创级别的用户
            $lianchuang_users = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('uniacid' => $this->uniacid, 'level_id' => $lianchuang_level['id']))->select();
            
            $synthesis_count = 0; // 合成次数
            $synthesis_logs = array(); // 合成日志
            
            // 调试信息
            $log_data['debug'] = array();
            $log_data['debug']['total_lianchuang_users'] = count($lianchuang_users);
            $log_data['debug']['lianchuang_user_ids'] = array_column($lianchuang_users, 'uid');
            
            foreach ($lianchuang_users as $lianchuang_user) {
                $uid = $lianchuang_user['uid'];
                
                // 调试信息
                $debug_info = array();
                $debug_info['lianchuang_uid'] = $uid;
                
                // 每个联创用户独立的已消耗用户列表
                $consumed_users_for_this_lianchuang = array();
                
                // 获取该联创用户的直推用户列表 - 完全按照原有逻辑
                $invite_list = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('inviter_id' => $uid))->select();
                
                // 检查是否有足够的直推用户
                if (count($invite_list) < 3) {
                    $debug_info['skip_reason'] = '直推用户不足3个';
                    $log_data['debug']['skipped_users'][] = $debug_info;
                    continue;
                }
                
                // 检查是否有足够的激活提货券进行合成
                $available_accounts = array();
                $debug_info['available_search'] = array();
                
                foreach ($invite_list as $invite_user) {
                    if (count($available_accounts) >= 3) {
                        break;
                    }
                    
                    $search_info = array();
                    $search_info['check_uid'] = $invite_user['uid'];
                    $search_info['can_tihuoquan_num'] = $invite_user['can_tihuoquan_num'];
                    $search_info['already_consumed'] = in_array($invite_user['uid'], $consumed_users_for_this_lianchuang);
                    
                    // 检查该用户是否已经被当前联创消耗过
                    if (in_array($invite_user['uid'], $consumed_users_for_this_lianchuang)) {
                        $search_info['result'] = '已消耗，跳过';
                        $debug_info['available_search'][] = $search_info;
                        continue;
                    }
                    
                    if ($invite_user['can_tihuoquan_num'] > 0) {
                        $available_accounts[] = $invite_user;
                        $search_info['result'] = '直接可用';
                        $debug_info['available_search'][] = $search_info;
                    } else {
                        // 在团队中寻找有激活提货券的用户 - 完全按照原有逻辑
                        $team_user = model('distribute_account')->field('uid,can_tihuoquan_num')->where(array('can_tihuoquan_num >' => 0, 'dis_path' => '%,' . $invite_user['uid'] . ',%'))->find();
                        if ($team_user && !in_array($team_user['uid'], $consumed_users_for_this_lianchuang)) {
                            $available_accounts[] = $team_user;
                            $search_info['result'] = '团队中找到: ' . $team_user['uid'];
                            $debug_info['available_search'][] = $search_info;
                        } else {
                            $search_info['result'] = '团队中未找到可用用户';
                            $debug_info['available_search'][] = $search_info;
                        }
                    }
                }
                
                $debug_info['available_count'] = count($available_accounts);
                $debug_info['available_users'] = array_column($available_accounts, 'uid');
                
                // 如果找到足够的用户且有足够的激活提货券，则进行合成
                if (count($available_accounts) >= 3) {
                    $debug_info['synthesis_result'] = '可以合成';
                    $log_data['debug']['processed_users'][] = $debug_info;
                    
                    // 先标记为已被当前联创消耗（与预览方法保持一致）
                    foreach ($available_accounts as $account) {
                        $consumed_users_for_this_lianchuang[] = $account['uid'];
                    }
                    
                    // 开始事务
                    model()->beginTransaction();
                    try {
                        // 从3个用户中各扣除1个激活提货券
                        foreach ($available_accounts as $account) {
                            $update_result1 = model('member')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
                            $update_result2 = model('distribute_account')->where(array('uid' => $account['uid']))->update('can_tihuoquan_num=can_tihuoquan_num-1');
                            
                            // 记录更新结果
                            $log_data['update_results'][] = array(
                                'uid' => $account['uid'],
                                'member_update' => $update_result1,
                                'distribute_update' => $update_result2
                            );
                        }
                        
                        // 给联创用户增加1张分红券
                        model('member')->where(array('uid' => $uid))->update('fenhong_quan=fenhong_quan+1,total_fenhong_quan=total_fenhong_quan+1');
                        
                        // 提交事务
                        model()->commit();
                        
                        $synthesis_count++;
                        
                        // 记录合成日志
                        $synthesis_logs[] = array(
                            'lianchuang_uid' => $uid,
                            'consumed_users' => array_column($available_accounts, 'uid'),
                            'consumed_tihuoquan_count' => 3,
                            'synthesis_time' => date('Y-m-d H:i:s')
                        );
                        
                    } catch (\Exception $e) {
                        // 回滚事务
                        model()->rollBack();
                        // 如果事务失败，需要从已消耗列表中移除这些用户
                        foreach ($available_accounts as $account) {
                            $key = array_search($account['uid'], $consumed_users_for_this_lianchuang);
                            if ($key !== false) {
                                unset($consumed_users_for_this_lianchuang[$key]);
                            }
                        }
                        $log_data['errors'][] = '用户ID:' . $uid . ' 合成失败: ' . $e->getMessage();
                    }
                } else {
                    $debug_info['synthesis_result'] = '可用用户不足3个，无法合成';
                    $log_data['debug']['skipped_users'][] = $debug_info;
                }
            }
            
            // 记录结束时间和统计信息
            $end_time = time();
            $log_data['end_time'] = date('Y-m-d H:i:s', $end_time);
            $log_data['execution_time'] = $end_time - $start_time . '秒';
            $log_data['synthesis_count'] = $synthesis_count;
            $log_data['synthesis_logs'] = $synthesis_logs;
            
            // 输出结果
            $result = array(
                'msg' => '手动发放分红券任务执行完成',
                'synthesis_count' => $synthesis_count,
                'execution_time' => $log_data['execution_time'],
                'log_data' => $log_data
            );
            
            output_data($result);
            
        } else {
            output_error('无效的请求方式');
        }
    }
}
