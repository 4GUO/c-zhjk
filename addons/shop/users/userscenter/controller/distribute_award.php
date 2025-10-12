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
            
            // 新增：获取联创用户详细信息
            $detailed_member_list = array();
            $lc_members = model('member')->field('uid,nickname,mobile,fenhong_quan,total_fenhong_quan')->where(array('level_id' => $lianchuang_level['id']))->select();
            
            foreach ($lc_members as $member) {
                if ($member['fenhong_quan'] > 0) {
                    $detailed_member_list[] = array(
                        'uid' => $member['uid'],
                        'nickname' => !empty($member['nickname']) ? $member['nickname'] : $member['mobile'],
                        'mobile' => $member['mobile'],
                        'level_name' => $lianchuang_level['level_name'],
                        'fenhong_quan' => $member['fenhong_quan'],
                        'total_fenhong_quan' => $member['total_fenhong_quan'],
                        'fenhong_bili' => config('yeji_fenhong_bili')
                    );
                }
            }
            
            $this->assign('detailed_member_list', $detailed_member_list);
            $this->assign('lianchuang_level', $lianchuang_level);
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
            $detailed_member_list = array(); // 新增：详细成员列表
            
            // 获取显示模式参数
            $show_detail = input('show_detail', 0, 'intval');
            
            // 如果需要显示详细信息，先批量获取团队业绩和累计分红
            $member_performance = array();
            $member_total_bonus = array();
            if ($show_detail) {
                // 获取所有有绩效分红比例的级别用户
                $all_members = array();
                foreach ($level_list as $v) {
                    if ($v['jiaquan_fenhong_bili'] <= 0) {
                        continue;
                    }
                    $members = model('member')->field('uid')->where(array('level_id' => $v['id']))->select();
                    foreach ($members as $member) {
                        $all_members[] = $member['uid'];
                    }
                }
                
                // 批量查询累计分红金额
                if (!empty($all_members)) {
                    $bonus_data = model('distribute_fenhong_record_detail')->field('uid, SUM(detail_bonus) as total_bonus')->where(array('uid' => $all_members, 'type' => 1))->group('uid')->select();
                    foreach ($bonus_data as $item) {
                        $member_total_bonus[$item['uid']] = $item['total_bonus'];
                    }
                }
            }
            
            foreach ($level_list as $v) {
                if ($v['jiaquan_fenhong_bili'] <= 0) {
                    continue;
                }
                $member_list = model('member')->field('uid,nickname,mobile')->where(array('level_id' => $v['id']))->select();
                $level_total_fenshu = 0;
                $level_member_list = array();
                
                foreach ($member_list as $kk => $vv) {
                    // 根据显示模式决定是否查询详细信息
                    if ($show_detail) {
                        // 详细模式：查询团队业绩和累计分红
                        $yue_yeji = logic('yewu')->get_team_yeji_by_month2($vv['uid'], $start_unixtime, $end_unixtime);
                        $total_bonus = isset($member_total_bonus[$vv['uid']]) ? $member_total_bonus[$vv['uid']] : 0;
                        
                        if (($yue_yeji >= $v['jiaquan_fenhong_yue_yeji']) && ($total_bonus < $v['jiaquan_fenhong_total'])) {
                            $level_total_fenshu++;
                            $level_member_list[] = $vv;
                            
                            // 构建详细成员信息
                            $detailed_member_list[] = array(
                                'uid' => $vv['uid'],
                                'nickname' => !empty($vv['nickname']) ? $vv['nickname'] : $vv['mobile'],
                                'mobile' => $vv['mobile'],
                                'level_name' => $v['level_name'],
                                'level_id' => $v['id'],
                                'yue_yeji' => $yue_yeji,
                                'required_yue_yeji' => $v['jiaquan_fenhong_yue_yeji'],
                                'total_bonus' => $total_bonus,
                                'required_total_bonus' => $v['jiaquan_fenhong_total'],
                                'fenhong_bili' => $v['jiaquan_fenhong_bili']
                            );
                        }
                    } else {
                        // 快速模式：简单统计该级别的所有用户数量，并显示基本信息
                        $level_total_fenshu++;
                        $level_member_list[] = $vv;
                        
                        // 构建基本成员信息（不包含详细的业绩和分红数据）
                        $detailed_member_list[] = array(
                            'uid' => $vv['uid'],
                            'nickname' => !empty($vv['nickname']) ? $vv['nickname'] : $vv['mobile'],
                            'mobile' => $vv['mobile'],
                            'level_name' => $v['level_name'],
                            'level_id' => $v['id'],
                            'yue_yeji' => 0, // 快速模式不显示具体数值
                            'required_yue_yeji' => $v['jiaquan_fenhong_yue_yeji'],
                            'total_bonus' => 0, // 快速模式不显示具体数值
                            'required_total_bonus' => $v['jiaquan_fenhong_total'],
                            'fenhong_bili' => $v['jiaquan_fenhong_bili']
                        );
                    }
                }
                $total_fenshu += $level_total_fenshu;
            }
            
            $this->assign('total_fenshu', $total_fenshu);
            $this->assign('detailed_member_list', $detailed_member_list); // 新增：传递详细列表到视图
            $this->assign('show_detail', $show_detail); // 新增：传递显示模式
            $this->display();
        }
    }

    //零售加权分红
    public function lingshou_fenhong_sendOp()
    {
        if (chksubmit()) {
            $yeji = input('yeji', 0, 'floatval');
            $range = input('range', 0, 'intval'); // 获取发放范围参数
            if ($yeji <= 0) {
                output_error('业绩不能空');
            }
            // 传递range参数到业务逻辑
            logic('yewu')->yue_deal_fenhong($yeji, $range);
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
            $detailed_gudong_list = array(); // 新增：详细股东列表
            
            // 获取显示模式参数
            $show_detail = input('show_detail', 0, 'intval');
            
            $result = model('member')->getList(array(), 'uid,level_id,inviter_id,nickname,mobile');
            
            // 如果需要显示详细信息，先批量获取邀请人数
            $inviter_counts = array();
            if ($show_detail) {
                // 获取所有符合条件的用户ID
                $potential_users = array();
                foreach ($result['list'] as $rr) {
                    $member_level = model('vip_level')->field('level_sort')->where(array('id' => $rr['level_id']))->find();
                    if ($member_level['level_sort'] >= $fenhong_level['level_sort']) {
                        $potential_users[] = $rr['uid'];
                    }
                }
                
                // 批量查询邀请人数
                if (!empty($potential_users)) {
                    $inviter_data = model('member')->field('inviter_id, COUNT(*) as count')->where(array('inviter_id' => $potential_users, 'has_buy_tygoods' => 1))->group('inviter_id')->select();
                    foreach ($inviter_data as $item) {
                        $inviter_counts[$item['inviter_id']] = $item['count'];
                    }
                }
            }
            
            foreach ($result['list'] as $rr) {
                $member_level = model('vip_level')->field('level_sort,level_name')->where(array('id' => $rr['level_id']))->find();
                if ($member_level['level_sort'] < $fenhong_level['level_sort']) {//排除自身级别没有分红资格的
                    continue;
                }
                
                // 根据显示模式决定是否查询邀请人数
                if ($show_detail) {
                    // 详细模式：使用预查询的数据
                    $inviter_num = isset($inviter_counts[$rr['uid']]) ? $inviter_counts[$rr['uid']] : 0;
                } else {
                    // 快速模式：不查询邀请人数，只检查是否符合条件
                    $inviter_num = model('member')->where(array('inviter_id' => $rr['uid'], 'has_buy_tygoods' => 1))->total();
                }
                
                if ($inviter_num >= config('linshou_fenhong_inviter_num')) {
                    $gudong_list[$rr['uid']] = $rr;
                    
                    // 新增：构建详细股东信息
                    $detailed_gudong_list[] = array(
                        'uid' => $rr['uid'],
                        'nickname' => !empty($rr['nickname']) ? $rr['nickname'] : $rr['mobile'],
                        'mobile' => $rr['mobile'],
                        'level_name' => $member_level['level_name'],
                        'inviter_num' => $inviter_num,
                        'required_num' => config('linshou_fenhong_inviter_num')
                    );
                }
            }
            unset($result);
            
            $this->assign('total_fenshu', count($gudong_list));
            $this->assign('detailed_gudong_list', $detailed_gudong_list); // 新增：传递详细列表到视图
            $this->assign('fenhong_level', $fenhong_level); // 新增：传递分红级别信息
            $this->assign('required_inviter_num', config('linshou_fenhong_inviter_num')); // 新增：传递要求的邀请人数
            $this->assign('show_detail', $show_detail); // 新增：传递显示模式
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

    /**
     * 保存零售分红名单
     */
    public function save_lingshou_fenhong_listOp()
    {
        if (!chksubmit()) {
            $this->display();
            return;
        }

        // 获取配置参数
        $level_id = config('linshou_fenhong_level_id');
        $required_inviter_num = config('linshou_fenhong_inviter_num');
        
        // 验证分红级别配置
        $fenhong_level = model('vip_level')->where(array('id' => $level_id))->find();
        if (!$fenhong_level) {
            output_error('分红级别配置错误');
        }
        
        // 检查是否已经存在相同的分红级别和推荐人数的记录
        $existing_record = model('ls_his')->getInfo(array(
            'level' => $level_id,
            'yqgmtytcrs' => $required_inviter_num
        ));
        
        if ($existing_record) {
            output_error('当前分红级别和推荐人数已经保存过，不能重复添加');
        }
        
        // 获取符合条件的用户列表
        $gudong_list = $this->getFenhongMemberList($fenhong_level, $required_inviter_num);
        if (empty($gudong_list)) {
            output_error('没有符合条件的用户');
        }
        
        // 开始事务处理
        $model = model();
        $model->beginTransaction();
        
        try {
            // 保存分红设置
            $current_time = time();
            $ls_his_data = array(
                'date' => date('Ym'),
                'level' => $level_id,
                'level_name' => $fenhong_level['level_name'],
                'yqgmtytcrs' => $required_inviter_num,
                'tjrs' => count($gudong_list),
                'stat' => 1,
                'create_time' => $current_time,
                'update_time' => $current_time
            );
            
            $ls_his_id = model('ls_his')->add($ls_his_data);
            if (!$ls_his_id) {
                throw new \Exception('保存分红配置失败');
            }
            
            // 批量保存分红名单详情
            $detail_data = array();
            foreach ($gudong_list as $member) {
                $detail_data[] = array(
                    'his_id' => $ls_his_id,
                    'uid' => $member['uid'],
                    'tjrs' => $member['inviter_count'],
                    'stat' => 1,
                    'create_time' => $current_time,
                    'update_time' => $current_time
                );
            }
            
            if (!empty($detail_data)) {
                $result = model('ls_his_dtl')->insertAll($detail_data);
                if (!$result) {
                    throw new \Exception('保存分红名单失败');
                }
            }
            
            $model->commit();
            
            output_data(array(
                'msg' => '保存成功',
                'ls_his_id' => $ls_his_id,
                'total_members' => count($gudong_list),
                'url' => _url('distribute_award/lingshou_fenhong_send')
            ));
            
        } catch (\Exception $e) {
            $model->rollBack();
            output_error('保存失败：' . $e->getMessage());
        }
    }

    /**
     * 获取符合条件的分红用户列表（复用现有逻辑）
     */
    private function getFenhongMemberList($fenhong_level, $required_inviter_num)
    {
        $gudong_list = array();
        $result = model('member')->getList(array(), 'uid,level_id,inviter_id,nickname,mobile');
        
        foreach ($result['list'] as $member) {
            $member_level = model('vip_level')->field('level_sort,level_name')->where(array('id' => $member['level_id']))->find();
            
            // 检查级别条件（和现有逻辑一致）
            if ($member_level['level_sort'] < $fenhong_level['level_sort']) {
                continue;
            }
            
            // 统计邀请人数（和现有逻辑一致）
            $inviter_count = model('member')->where(array('inviter_id' => $member['uid'], 'has_buy_tygoods' => 1))->total();
            
            if ($inviter_count >= $required_inviter_num) {
                $gudong_list[] = array(
                    'uid' => $member['uid'],
                    'nickname' => !empty($member['nickname']) ? $member['nickname'] : $member['mobile'],
                    'mobile' => $member['mobile'],
                    'level_id' => $member['level_id'],
                    'level_name' => $member_level['level_name'],
                    'inviter_count' => $inviter_count
                );
            }
        }
        
        return $gudong_list;
    }

    /**
     * 分红名单列表（主数据+从数据合并显示）
     */
    public function lingshou_fenhong_listOp()
    {
        $where = array();
        
        // 状态筛选
        $stat = input('stat', -1, 'intval');
        if ($stat > -1) {
            $where['stat'] = $stat;
        }
        
        // 日期筛选
        $date = input('date', '', 'trim');
        if ($date) {
            $where['date'] = $date;
        }
        
        $list = model('ls_his')->getList($where, '*', 'id DESC', 20, input('get.page', 1, 'intval'));
        
        // 处理列表数据，同时获取每个配置对应的用户名单
        foreach ($list['list'] as &$item) {
            $item['create_time_text'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['update_time_text'] = date('Y-m-d H:i:s', $item['update_time']);
            $item['stat_text'] = $item['stat'] == 1 ? '生效' : '不生效';
            $item['date_text'] = $item['date'] ? substr($item['date'], 0, 4) . '-' . substr($item['date'], 4, 2) : '';
            
            // 获取该配置下的用户名单详情
            $detail_list = model('ls_his_dtl')->getList(array('his_id' => $item['id']), '*', 'id ASC', 100);
            
            // 处理用户详情数据
            foreach ($detail_list['list'] as &$detail) {
                $member = model('member')->getInfo(array('uid' => $detail['uid']), 'nickname,mobile,level_id');
                $detail['nickname'] = !empty($member['nickname']) ? $member['nickname'] : $member['mobile'];
                $detail['mobile'] = $member['mobile'];
                
                $level = model('vip_level')->getInfo(array('id' => $member['level_id']), 'level_name');
                $detail['level_name'] = $level['level_name'];
                
                $detail['stat_text'] = $detail['stat'] == 1 ? '启用' : '不启用';
            }
            
            $item['member_list'] = $detail_list['list'];
        }
        
        $this->assign('page', page($list['totalpage'], array(
            'page' => input('get.page', 1, 'intval'),
            'stat' => $stat,
            'date' => $date
        ), users_url('distribute_award/lingshou_fenhong_list')));
        
        $this->assign('list', $list['list']);
        $this->assign('stat', $stat);
        $this->assign('date', $date);
        $this->display();
    }

    /**
     * 启用/禁用分红配置
     */
    public function toggle_fenhong_statusOp()
    {
        $id = input('id', 0, 'intval');
        $stat = input('stat', 0, 'intval');
        
        if ($id <= 0) {
            output_error('参数错误');
        }
        
        $stat = $stat == 1 ? 1 : 0;
        
        $result = model('ls_his')->edit(array('id' => $id), array(
            'stat' => $stat,
            'update_time' => time()
        ));
        
        if ($result) {
            output_data(array('msg' => $stat == 1 ? '启用成功' : '禁用成功'));
        } else {
            output_error('操作失败');
        }
    }

    /**
     * 启用/禁用分红名单中的用户
     */
    public function toggle_user_statusOp()
    {
        $id = input('id', 0, 'intval');
        $stat = input('stat', 0, 'intval');
        
        if ($id <= 0) {
            output_error('参数错误');
        }
        
        $stat = $stat == 1 ? 1 : 0;
        
        $result = model('ls_his_dtl')->edit(array('id' => $id), array(
            'stat' => $stat,
            'update_time' => time()
        ));
        
        if ($result) {
            output_data(array('msg' => $stat == 1 ? '启用成功' : '禁用成功'));
        } else {
            output_error('操作失败');
        }
    }

    /**
     * 获取分红名单详情
     */
    public function get_member_listOp()
    {
        if (IS_API) {
            $his_id = input('his_id', 0, 'intval');
            
            if ($his_id <= 0) {
                output_error('参数错误');
            }
            
            // 获取名单详情
            $detail_list = model('ls_his_dtl')->getList(array('his_id' => $his_id), '*', 'id ASC', 100);
            
            // 处理用户详情数据
            foreach ($detail_list['list'] as &$detail) {
                $member = model('member')->getInfo(array('uid' => $detail['uid']), 'nickname,mobile,level_id');
                $detail['nickname'] = !empty($member['nickname']) ? $member['nickname'] : $member['mobile'];
                $detail['mobile'] = $member['mobile'];
                
                $level = model('vip_level')->getInfo(array('id' => $member['level_id']), 'level_name');
                $detail['level_name'] = $level['level_name'];
                
                $detail['stat_text'] = $detail['stat'] == 1 ? '启用' : '不启用';
            }
            
            output_data(array(
                'member_list' => $detail_list['list'],
                'total_count' => count($detail_list['list'])
            ));
            
        } else {
            output_error('无效的请求方式');
        }
    }

    /**
     * 获取历史名单选项卡数据（按日期分组）
     */
    public function get_history_tabsOp()
    {
        if (IS_API) {
            // 1. 首先获取当前符合分红条件的人员UID列表（用于过滤）
            $current_fenhong_uids = $this->getCurrentFenhongUids();
            
            // 获取所有历史记录，按日期分组
            $list = model('ls_his')->getList(array(), '*', 'date DESC, id DESC', 100);
            
            $tabs = array();
            $tab_data = array();
            
            if (!empty($list['list'])) {
                // 按日期分组，显示所有配置（包括禁用的）
                $grouped_by_date = array();
                foreach ($list['list'] as $item) {
                    $date_text = "(" . $item['id'] . ") ";
                    $date_text .= $item['date'] ? $item['date'] : '未知日期';
                
                    $grouped_by_date[$date_text][] = $item;
                }
                
                // 生成选项卡和数据
                foreach ($grouped_by_date as $date_text => $items) {
                    // 生成安全的选项卡ID，只保留字母数字和下划线
                    $tab_id = 'history_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $date_text);
                    
                    // 计算启用状态的用户总数（排除当前分红人员和禁用状态的用户）
                    $total_user_count = 0;
                    $all_members = array();
                    
                    $processed_configs = array();
                    foreach ($items as $item) {
                        // 处理配置信息
                        $item['create_time_text'] = date('Y-m-d H:i:s', $item['create_time']);
                        $item['stat_text'] = $item['stat'] == 1 ? '生效' : '不生效';
                        
                        // 获取该配置下的所有用户详情（包括禁用状态的用户）
                        $detail_list = model('ls_his_dtl')->getList(array('his_id' => $item['id']), '*', 'id ASC', 100);
                        
                        // 2. 计算过滤后的人数（只统计启用状态）
                        $config_user_count = 0;
                        $config_enabled_count = 0; // 启用状态的人数
                        
                        foreach ($detail_list['list'] as $detail) {
                            // 过滤掉当前分红人员
                            if (in_array($detail['uid'], $current_fenhong_uids)) {
                                continue;
                            }
                            
                            $member = model('member')->getInfo(array('uid' => $detail['uid']), 'nickname,mobile,level_id');
                            $level = model('vip_level')->getInfo(array('id' => $member['level_id']), 'level_name');
                            
                            // 所有记录都添加到列表中（包括禁用的）
                            $all_members[] = array(
                                'uid' => $detail['uid'],
                                'nickname' => !empty($member['nickname']) ? $member['nickname'] : $member['mobile'],
                                'mobile' => $member['mobile'],
                                'level_name' => $level['level_name'],
                                'tjrs' => $detail['tjrs'],
                                'stat' => $detail['stat'],
                                'stat_text' => $detail['stat'] == 1 ? '启用' : '不启用',
                                'detail_id' => $detail['id'],
                                'config_stat' => $item['stat'], // 添加配置状态
                                'config_id' => $item['id'] // 添加配置ID
                            );
                            
                            $config_user_count++; // 所有过滤后的人数（含禁用）
                            
                            // 只有配置启用且用户启用时才计入统计人数
                            if ($item['stat'] == 1 && $detail['stat'] == 1) {
                                $config_enabled_count++;
                                $total_user_count++;
                            }
                        }
                        
                        // 3. 更新配置中的人数统计（tjrs字段显示总人数，enabled_count显示启用人数）
                        $item['tjrs'] = $config_user_count; // 总人数（含禁用）
                        $item['enabled_count'] = $config_enabled_count; // 启用人数
                        $processed_configs[] = $item;
                    }
                    
                    $tabs[] = array(
                        'id' => $tab_id,
                        'name' => $date_text,
                        'count' => $total_user_count  // 4. 显示启用状态的总人数
                    );
                    
                    $tab_data[$tab_id] = array(
                        'configs' => $processed_configs,
                        'members' => $all_members
                    );
                }
            }
            
            output_data(array(
                'tabs' => $tabs,
                'data' => $tab_data
            ));
            
        } else {
            output_error('无效的请求方式');
        }
    }
    
    /**
     * 获取当前符合分红条件的用户UID列表
     * 复用 lingshou_fenhong_sendOp 的逻辑
     */
    private function getCurrentFenhongUids()
    {
        $fenhong_level = model('vip_level')->where(array('id' => config('linshou_fenhong_level_id')))->order('level_sort ASC')->find();
        $required_inviter_num = config('linshou_fenhong_inviter_num');
        
        $current_uids = array();
        $result = model('member')->getList(array(), 'uid,level_id');
        
        foreach ($result['list'] as $rr) {
            $member_level = model('vip_level')->field('level_sort')->where(array('id' => $rr['level_id']))->find();
            
            // 检查级别条件
            if ($member_level['level_sort'] < $fenhong_level['level_sort']) {
                continue;
            }
            
            // 检查邀请人数条件
            $inviter_num = model('member')->where(array('inviter_id' => $rr['uid'], 'has_buy_tygoods' => 1))->total();
            
            if ($inviter_num >= $required_inviter_num) {
                $current_uids[] = $rr['uid'];
            }
        }
        
        return $current_uids;
    }
    
}
