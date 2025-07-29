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
}
