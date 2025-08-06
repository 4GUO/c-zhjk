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
			<a href='<?=users_url('shop_order/index')?>'>所有订单</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_new' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_order/index', array('state_type' => 'state_new'))?>'>待付款</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_pay' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_order/index', array('state_type' => 'state_pay'))?>'>待发货</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_send' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_order/index', array('state_type' => 'state_send'))?>'>已发货</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_success' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_order/index', array('state_type' => 'state_success'))?>'>已完成</a>
		</li>
		<li class='<?=input('state_type', '') == 'state_cancel' ? 'active' : 'normal'?>'>
			<a href='<?=users_url('shop_order/index', array('state_type' => 'state_cancel'))?>'>已取消</a>
		</li>
	</ul>
</div>
<form method='get' action='<?=users_url('shop_order/index', array('state_type' => input('state_type')))?>'>
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
				<td class='tc w70'>
					<label class='submit-border'>
						<a class='submit' id='export' href='javascript:;'>导出</a>
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
			<th colspan='20'><span class='ml10'>订单编号：<em><?=$val['order_sn']?></em>
			<i class='icon-mobile-phone'></i>
			</span> <span>下单时间：<em class='goods-time'><?=$val['add_time']?></em></span>
			<span class='fr mr5'> <a href='<?=users_url('shop_order/print_order', array('order_id' => $val['order_id']))?>' class='css-btn-mini' target='_blank' title='打印发货单'/><i class='icon-print'></i>打印发货单</a></span>
			</th>
		</tr>
		<?php $i = 0;?>
		<?php foreach($val['extend_order_goods'] as $k => $v){?>
		<tr>
			
			<td class='bdl'></td>
			<td class='w70'><div class='css-goods-thumb'><img src='<?=$v['goods_image']?>' style='max-width:100%; max-height: 100%;' /></div></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt><?=$v['goods_name']?></dt>
					<?php if (!empty($val['extend_order_common']['zengpin_list'])) { ?>
					<dd>
						<font color='#C00'>赠：</font>
						<?php foreach ($val['extend_order_common']['zengpin_list'] as $kk => $vv) { ?>
						<?=$vv['goods_name']?>&nbsp;X&nbsp;<?=$vv['goods_num']?><br />
						<?php } ?>
					</dd>
					<?php } ?>
				</dl>
			</td>
			<td><?=$v['goods_price']?></td>
			<td><?=$v['goods_num']?></td>
			<?php if (($val['goods_count'] > 1 && $k ==0) || ($val['goods_count']) == 1){ ?>
			<td class='bdl' rowspan='<?php echo $val['goods_count'];?>'><div class='buyer'>
			        <?php echo $val['member_name'];?>
			        <p>TTM<?php echo $val['extend_member']['uid'];?></p>
			        <p></p><?php echo $val['extend_member']['mobile'];?></p>
					<div class='buyer-info'> <em></em>
						<div class='con'>
							<h3><i></i><span>联系方式：</span></h3>
							<dl>
								<dt>姓名：</dt>
								<dd><?php echo $val['extend_order_common']['reciver_name'];?>（ID：ZH<?=padNumber($val['uid'])?>）</dd>
							</dl>
							<dl>
								<dt>电话：</dt>
								<dd><?php echo empty($val['extend_order_common']['reciver_info']['tel_phone']) ? '' : $val['extend_order_common']['reciver_info']['tel_phone'];?></dd>
							</dl>
							<dl>
								<dt>地址：</dt>
								<dd><?php echo empty($val['extend_order_common']['reciver_info']['address']) ? '' : $val['extend_order_common']['reciver_info']['address'];?></dd>
							</dl>
						</div>
					</div>
				</div>
			</td>
			<td class='bdl' rowspan='<?php echo $val['goods_count'];?>'>
				<p class='css-order-amount'><?php echo $val['order_amount']; ?></p>
				<p class='goods-freight'>
					<?php if ($val['shipping_fee'] > 0){?>
					(含运费<?php echo $val['shipping_fee'];?>)
					<?php }else{?>
					无运费
					<?php }?>
				</p>
				<?php if($val['order_state'] == ORDER_STATE_PAY){?>
				<p class='goods-pay'><?php echo $val['payment_name']; ?></p>
				<?php }?>
			</td>
			<td class='bdl bdr' rowspan='<?php echo $val['goods_count'];?>'><p><?php echo $val['state_desc']; ?></p>
				<!-- 订单查看 -->
				<p><a href='<?=users_url('shop_order/show_order', array('order_id' => $val['order_id']))?>'>订单详情</a></p>
				<?php if ($val['if_deliver']) { ?>
				<p><a href='<?=users_url('shop_deliver/search_deliver', array('order_sn' => $val['order_sn']))?>'>查看物流</a></p>
				<?php } ?>
			</td>
			
			<!-- 取消订单 -->
			<td class='bdl bdr' rowspan='<?php echo $val['goods_count'];?>'>
				<?php if($val['if_cancel']) { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-red mt5' nc_type='dialog' uri='<?=users_url('shop_order/order_cancel', array('order_id' => $val['order_id'], 'order_sn' => $val['order_sn']))?>' dialog_title='取消订单' dialog_id='seller_order_cancel_order' dialog_width='400' id='order<?php echo $val['order_id']; ?>_action_cancel' /><i class='icon-remove-circle'></i>取消订单</a></p>
				<?php } ?>
				
				<!-- 修改运费 -->
				
				<?php if ($val['if_modify_price']) { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-orange mt10' uri='<?=users_url('shop_order/shipping_price', array('order_id' => $val['order_id'], 'order_sn' => $val['order_sn']))?>' dialog_width='480' dialog_title='修改运费' nc_type='dialog'  dialog_id='seller_order_adjust_fee' id='order<?php echo $val['order_id']; ?>_action_adjust_fee' /><i class='icon-pencil'></i>修改运费</a></p>
				<?php }?>
				
				<!-- 修改价格 -->
				
				<?php if ($val['if_spay_price']) { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-green mt10' uri='<?=users_url('shop_order/order_price', array('order_id' => $val['order_id'], 'order_sn' => $val['order_sn']))?>' dialog_width='480' dialog_title='修改价格' nc_type='dialog'  dialog_id='seller_order_adjust_fee' id='order<?php echo $val['order_id']; ?>_action_adjust_fee' /><i class='icon-pencil'></i>修改价格</a></p>
				<?php }?>
				
				<!-- 发货 -->
				
				<?php if ($val['if_send']) { ?>
				<p><a class='css-btn css-btn-green mt10' href='<?=users_url('shop_deliver/send', array('order_id' => $val['order_id']))?>'/><i class='icon-truck'></i>设置发货</a></p>
				<?php } ?>
				
				<!-- 锁定 -->
				
				<?php if ($val['if_lock']) {?>
				<p><?php echo '退款退货中';?></p>
				<?php }?>
				
				<!-- 发放复购见单奖励 -->
				
				<?php if (in_array($val['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS)) && input('debug', '') == '1') { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-purple mt10' onclick="grantReward(<?php echo $val['order_id']; ?>)" id='order<?php echo $val['order_id']; ?>_action_grant_reward'><i class='icon-gift'></i>发放见单奖</a></p>
				<?php }?>
				
				<!-- 收回复购见单奖励 -->
				
				<?php if (in_array($val['order_state'], array(ORDER_STATE_PAY, ORDER_STATE_SEND, ORDER_STATE_SUCCESS)) && input('debug', '') == '1') { ?>
				<p><a href='javascript:void(0)' class='css-btn css-btn-purple mt10' onclick="revokeReward(<?php echo $val['order_id']; ?>)" id='order<?php echo $val['order_id']; ?>_action_revoke_reward'><i class='icon-undo'></i>收回见单奖</a></p>
				<?php }?></td>
			<?php } ?>
		</tr>
		<?php $i++;?>
		<?php } ?>
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
$('#export').click(function() {
	var url = '<?=users_url('shop_order/index', array('state_type' => input('state_type')))?>?<?php echo $_SERVER['QUERY_STRING'];?>&is_export=1';
	var totalpage = <?=$output['totalpage']?>;
	var i = 1;
	let t = setInterval(function () {
		url = url + '&export_page=' + i;
		location.href = url;
		if (i == totalpage) {
			clearInterval(t);
			return;
		}
		i++;
	}, 2000);
});

// 发放复购见单奖励
function grantReward(orderId) {
	if (confirm('确定要发放复购见单奖励吗？')) {
		$.ajax({
			url: '<?=users_url('shop_order/grant_reward')?>',
			type: 'POST',
			data: {
				order_id: orderId,
				form_submit: 'ok'
			},
			dataType: 'json',
			success: function(response) {
				if (response.state == 400) {
					alert('操作失败：' + response.msg);
				} else if (response.state) {
					alert('奖励发放成功！');
					location.reload();
				} else {
					alert('操作失败：' + response.msg);
				}
			},
			error: function() {
				alert('网络错误，请重试！');
			}
		});
	}
}

// 收回复购见单奖励
function revokeReward(orderId) {
	if (confirm('确定要收回复购见单奖励吗？')) {
		$.ajax({
			url: '<?=users_url('shop_order/revoke_reward')?>',
			type: 'POST',
			data: {
				order_id: orderId,
				form_submit: 'ok'
			},
			dataType: 'json',
			success: function(response) {
				if (response.state == 400) {
					alert('操作失败：' + response.msg);
				} else if (response.state) {
					alert('奖励收回成功！');
					location.reload();
				} else {
					alert('操作失败：' + response.msg);
				}
			},
			error: function() {
				alert('网络错误，请重试！');
			}
		});
	}
}
</script>