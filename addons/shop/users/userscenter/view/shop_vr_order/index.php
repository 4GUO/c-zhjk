<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
#my_agent .member_info{
	height:20px;
	line-height:20px;
	color:#333;
}
#my_agent .member_info span{
	color:#999
}
.send_btn{padding: 5px 15px; border-radius: 8px; background: #51A351; color:#FFF}
</style>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='<?=empty(input('state_type', '')) ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_vr_order/index')?>'>所有订单</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_new' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_new'))?>'>待付款</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_pay' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_pay'))?>'>已付款</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_success' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_success'))?>'>已完成</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_cancel' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_vr_order/index', array('state_type' => 'state_cancel'))?>'>已取消</a>
		</li>
		<li>
			<a href='<?=users_url('shop_vr_order/exchange')?>'>兑换码兑换</a>
		</li>
	</ul>
</div>
<form method='get' action='<?=users_url('shop_vr_order/index')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<td></td>
				<th>下单时间</th>
				<td class='w240'>
					<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					&nbsp;–&nbsp;
					<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</td>
				<th>买家</th>
				<td class='w100'><input class='text w80' name='buyer_name' value='<?=input('buyer_name', '')?>' type='text' /></td>
				<th>订单编号</th>
				<td class='w160'><input class='text w150' name='order_sn' value='<?=input('order_sn', '')?>' type='text' /></td>
				<td class='w70 tc'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit' />
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table order' id='my_agent'>
	<thead>
		<tr>
			<th class='w10'></th>
			<th colspan='2'>商品</th>
			<th class='w100'>单价（元）</th>
			<th class='w40'>数量</th>
			<th class='w110'>买家</th>
			<th class='w120'>订单金额</th>
			<th class='w100'>交易状态</th>
			<th class='w150'>操作</th>
		</tr>
	</thead>
	<?php if (!empty($output['list'])) { ?>
	<?php foreach ($output['list'] as $key => $val) {?>
	<tbody>
		<tr>
			<td colspan='20' class='sep-row'></td>
		</tr>
		<tr>
			<th colspan='20'>
				<span class='ml10'>订单编号：<em><?=$val['order_sn']?></em>
					<i class='icon-mobile-phone'></i>
				</span> 
				<span>下单时间：<em class='goods-time'><?=$val['add_time']?></em></span>
			</th>
		</tr>	
		<tr>
			<td class='bdl'></td>
			<td class='w70'><div class='css-goods-thumb'><img src='<?=$val['goods_image']?>' style='width:100%;' /></div></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt><?=$val['goods_name']?></dt>
				</dl>
			</td>
			<td><?=$val['goods_price']?></td>
			<td><?=$val['goods_num']?></td>
			<td class='bdl'>
				<div class='buyer'>
					<?php echo $val['buyer_name'];?>					
					<div class='buyer-info'> <em></em>
						<div class='con'>
							<h3><i></i><span>联系方式：</span></h3>
							<dl>
								<dt>电话：</dt>
								<dd><?php echo empty($val['buyer_phone']) ? '' : $val['buyer_phone'];?></dd>
							</dl>
						</div>
					</div>
				</div>
			</td>
			<td class='bdl'>
				<p class='css-order-amount'><?php echo $val['order_amount']; ?></p>
				<?php if($val['order_state'] == ORDER_STATE_PAY) { ?>
				<p class='goods-pay'><?php echo $val['payment_name']; ?></p>
				<?php } ?>
			</td>
			<td class='bdl bdr'><p><?php echo $val['state_desc']; ?></p>
				<!-- 订单查看 -->
				<p><a href='<?=users_url('shop_vr_order/show_order', array('order_id' => $val['order_id']))?>'>订单详情</a></p>
			</td>
			
			<!-- 取消订单 -->
			<td class='bdl bdr'>
				<?php if($val['if_cancel']) { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-red mt5' nc_type='dialog' uri='<?=users_url('shop_vr_order/order_cancel', array('order_id' => $val['order_id'], 'order_sn' => $val['order_sn']))?>' dialog_title='取消订单' dialog_id='seller_order_cancel_order' dialog_width='400' id='order<?php echo $val['order_id']; ?>_action_cancel' /><i class='icon-remove-circle'></i>取消订单</a></p>
				<?php } ?>
			</td>
		</tr>
	</tbody>
	<?php } ?>		
	<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
	<?php } ?>
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#query_start_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});

$('#query_end_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});
</script>