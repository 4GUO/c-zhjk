<?php
/**
 * 预存款
 *
 */
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class predeposit
{
    /**
     * 生成充值编号
     * @return string
     */
    public function makeSn($uid)
    {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (double) microtime() * 1000) . sprintf('%03d', (int) $uid % 1000);
    }
    /**
     * 变更预存款
     * @param unknown $change_type
     * @param unknown $data
     * @throws Exception
     * @return unknown
     */
    public function changePd($change_type, $data = array())
    {
        $data_log = array();
        $data_pd = '';
		$data_log['uniacid'] = 1;
        $data_log['lg_member_id'] = $data['uid'];
        $data_log['lg_member_name'] = $data['member_name'];
        $data_log['lg_add_time'] = TIMESTAMP;
        $data_log['lg_type'] = $change_type;
        switch ($change_type) {
            case 'order_pay':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '下单，支付预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                break;
            case 'order_freeze':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '下单，冻结预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit+' . $data['amount'] . ',available_predeposit=available_predeposit-' . $data['amount'];
                break;
            case 'order_cancel':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '取消订单，解冻预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'] . ',available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'order_comb_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '下单，支付被冻结的预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                break;
            case 'recharge':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '充值，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = isset($data['admin_name']) ? $data['admin_name'] : '';
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'refund':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '确认退款，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'vr_refund':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '虚拟兑码退款成功，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'cash_apply':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '申请提现，冻结预存款，提现单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'] . ',freeze_predeposit=freeze_predeposit+' . $data['amount'];
                break;
            case 'cash_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '提现成功，提现单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                break;
            case 'cash_del':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '取消提现申请，解冻预存款，提现单号: ' . $data['order_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'] . ',freeze_predeposit=freeze_predeposit-' . $data['amount'];
                break;
            case 'sys_add_money':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '管理员调节预存款【增加】，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'sys_del_money':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '管理员调节预存款【减少】，充值单号: ' . $data['pdr_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                break;
            case 'sys_freeze_money':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '管理员调节预存款【冻结】，充值单号: ' . $data['pdr_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'] . ',freeze_predeposit=freeze_predeposit+' . $data['amount'];
                break;
            case 'sys_unfreeze_money':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '管理员调节预存款【解冻】，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'] . ',freeze_predeposit=freeze_predeposit-' . $data['amount'];
                break;
			case 'commission_come':
				$data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '提现转入余额，记录编号: ' . $data['order_sn'];
                $data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
				break;
			case 'partner_pay':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'].'，支付预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                break;
            case 'partner_freeze':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = $data['order_desc'].'，冻结预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit+' . $data['amount'] . ',available_predeposit=available_predeposit-' . $data['amount'];
                break;
            case 'partner_cancel':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'].'取消，解冻预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'] . ',available_predeposit=available_predeposit+' . $data['amount'];
                break;
            case 'partner_comb_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'].'，支付被冻结的预存款，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                break;
            case 'zhuanzhang_in':
				$data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = $data['desc'];
                $data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
				break;
			case 'zhuanzhang_out':
				$data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['desc'];
                $data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
				break;
			case 'commission_in'://收入进入余额
				$data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = $data['lg_desc'];
                $data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
				break;
			case 'commission_out'://从余额提现
				$data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['lg_desc'];
                $data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
				break;
            case 'refundjd'://退款
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['lg_desc'];
                $data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                break;
			case 'tixian_reject'://提现驳回
				$data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = $data['lg_desc'];
                $data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
				break;
            default:
                throw new \Exception('参数错误');
                break;
        }
        $update = model('member')->edit(array('uid' => $data['uid']), $data_pd);
        if (!$update) {
            throw new \Exception('操作失败1');
        }
		$r = model('member')->field('available_predeposit')->where(array('uid' => $data['uid']))->find();
		$data_log['available_predeposit'] = $r['available_predeposit'];
        $insert = model('pd_log')->add($data_log);
        if (!$insert) {
            throw new \Exception('操作失败2');
        }
        return $insert;
    }
}
