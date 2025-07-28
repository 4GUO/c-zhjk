<?php
/**
 * 余额
 *
 */
namespace logic;
use base;
use lib;
defined('SAFE_CONST') or exit('Access Invalid!');
class seller_predeposit
{
    /**
     * 生成充值编号
     * @return string
     */
    public function makeSn($store_id)
    {
        return mt_rand(10, 99) . sprintf('%010d', time() - 946656000) . sprintf('%03d', (double) microtime() * 1000) . sprintf('%03d', (int) $store_id % 1000);
    }
    /**
     * 变更余额
     * @param unknown $change_type
     * @param unknown $data
     * @throws Exception
     * @return unknown
     */
    public function changePd($change_type, $data = array())
    {
        $data_log = array();
        $data_pd = '';
        $data_msg = array();
        $data_log['lg_store_id'] = $data['store_id'];
        $data_log['lg_store_name'] = $data['store_name'];
        $data_log['lg_add_time'] = TIMESTAMP;
        $data_log['lg_type'] = $change_type;
        $data_msg['time'] = date('Y-m-d H:i:s');
        switch ($change_type) {
            case 'order_pay':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '下单，支付余额，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'order_freeze':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '下单，冻结余额，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit+' . $data['amount'] . ',available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'order_cancel':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '取消订单，解冻余额，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'] . ',available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = -$data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'order_comb_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '下单，支付被冻结的余额，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = 0;
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'recharge':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '充值，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = isset($data['admin_name']) ? $data['admin_name'] : '';
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'refund':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '确认退款，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'vr_refund':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '虚拟兑码退款成功，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'cash_apply':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '申请提现，冻结余额，提现单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'] . ',freeze_predeposit=freeze_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'cash_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '提现成功，提现单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = 0;
                $data_msg['freeze_amount'] = -$data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'cash_del':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '取消提现申请，解冻余额，提现单号: ' . $data['order_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'] . ',freeze_predeposit=freeze_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = -$data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'sys_add_money':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '管理员调节余额【增加】，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'sys_del_money':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '管理员调节余额【减少】，充值单号: ' . $data['pdr_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'sys_freeze_money':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = '管理员调节余额【冻结】，充值单号: ' . $data['pdr_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'] . ',freeze_predeposit=freeze_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'sys_unfreeze_money':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = '管理员调节余额【解冻】，充值单号: ' . $data['pdr_sn'];
                $data_log['lg_admin_name'] = $data['admin_name'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'] . ',freeze_predeposit=freeze_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = -$data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
			case 'commission_come':
				$data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_desc'] = '提现转入余额，记录编号: ' . $data['order_sn'];
                $data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
				break;
			case 'partner_pay':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'] . '，支付余额，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = 0;
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'partner_freeze':
                $data_log['lg_av_amount'] = -$data['amount'];
                $data_log['lg_freeze_amount'] = $data['amount'];
                $data_log['lg_desc'] = $data['order_desc'] . '，冻结余额，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit+' . $data['amount'] . ',available_predeposit=available_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = -$data['amount'];
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
			case 'partner_comb_pay':
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'] . '，支付被冻结的余额，订单号: ' . $data['order_sn'];
				$data_pd = 'freeze_predeposit=freeze_predeposit-' . $data['amount'];
                $data_msg['av_amount'] = 0;
                $data_msg['freeze_amount'] = $data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            case 'partner_return':
                $data_log['lg_av_amount'] = $data['amount'];
                $data_log['lg_freeze_amount'] = -$data['amount'];
                $data_log['lg_desc'] = $data['order_desc'] . '，退款余额，订单号: ' . $data['order_sn'];
				$data_pd = 'available_predeposit=available_predeposit+' . $data['amount'];
                $data_msg['av_amount'] = $data['amount'];
                $data_msg['freeze_amount'] = -$data['amount'];
                $data_msg['desc'] = $data_log['lg_desc'];
                break;
            default:
				throw new \Exception('参数错误');
                break;
        }
        $update = model('seller')->edit(array('id' => $data['store_id']), $data_pd);
        if (!$update) {
			throw new \Exception('操作失败1');
        }
		$r = model('seller')->field('available_predeposit')->where(array('id' => $data['store_id']))->find();
		$data_log['available_predeposit'] = $r['available_predeposit'];
        $insert = model('seller_pd_log')->add($data_log);
        if (!$insert) {
			throw new \Exception('操作失败2');
        }
        return $insert;
    }
}