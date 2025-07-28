<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.sticky .tabmenu {
	padding: 0;
	position: relative;
}
</style>
<span class='fr mr5'> <a href='javascript:void(0)' nc_type='dialog' dialog_title='选择物流公司' dialog_id='waybill_express' uri='<?=users_url('shop_deliver/waybill_express', array('order_id' => $output['order_info']['order_id']));?>' dialog_width='550' class='css-btn-mini' target='_blank' title='打印运单'/><i class='icon-print'></i>打印运单</a></span>
<div class='wrap'>
	<form id='send_form' method='post' action='<?=users_url('shop_deliver/send')?>'>
	<input type='hidden' name='form_submit' value='ok' />
	<input type='hidden' name='order_id' value='<?=$output['order_info']['order_id']?>' />
		<div class='step-title'><em>第一步</em>确认收货信息及交易详情</div>
		<table class='css-default-table order deliver'>
			<tbody>
				<?php if (is_array($output['order_info']) and !empty($output['order_info'])) { ?>
				<tr>
					<td colspan='20' class='sep-row'></td>
				</tr>
				<tr>
					<th colspan='20'><a href='<?=users_url('shop_order/print_order', array('order_id' => $output['order_info']['order_id']))?>' target='_blank' class='css-btn-mini fr' title='打印发货单'/><i class='icon-print'></i>打印发货单</a><span class='fr mr30'></span><span class='ml10'>订单号：<?php echo $output['order_info']['order_sn']; ?></span><span class='ml20'>下单时间：<em class='goods-time'><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']); ?></em></span> 
				</tr>
				<?php foreach($output['order_info']['extend_order_goods'] as $k => $goods_info) { ?>
				<tr>
					<td class='bdl w10'></td>
					<td class='w50'><div class='pic-thumb'><img src='<?php echo $goods_info['goods_image']; ?>' width='50' /></div></td>
					<td class='tl'><dl class='goods-name'>
							<dt><?php echo $goods_info['goods_name']; ?></dt>
							<dd><strong>￥<?php echo $goods_info['goods_price']; ?></strong>&nbsp;x&nbsp;<em><?php echo $goods_info['goods_num'];?></em>件</dd>
						</dl></td>
					<?php if ((count($output['order_info']['extend_order_goods']) > 1 && $k ==0) || (count($output['order_info']['extend_order_goods']) == 1)){?>
					<td class='bdl bdr order-info w500' rowspan='<?php echo count($output['order_info']['extend_order_goods']);?>'><dl>
							<dt>运费：</dt>
							<dd>
								<?php if (!empty($output['order_info']['shipping_fee']) && $output['order_info']['shipping_fee'] != '0.00'){?>
								<?php echo $output['order_info']['shipping_fee'];?>
								<?php }else{?>
								免运费
								<?php }?>
							</dd>
						</dl>
						<dl>
							<dt>发货备注：</dt>
							<dd>
								<textarea name='deliver_explain' cols='100' rows='2' class='w400 tip-t' title='发货备注'><?php echo $output['order_info']['extend_order_common']['deliver_explain'];?></textarea>
							</dd>
						</dl></td>
					<?php }?>
				</tr>
				<?php }?>
				<tr>
					<td colspan='20' class='tl bdl bdr' style='padding:8px' id='address'><strong class='fl'>收货人信息：</strong><span id='buyer_address_span'><?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></span><?php echo !empty($output['order_info']['extend_order_common']['reciver_info']['dlyp']) ? '[自提服务站]' : '';?> <a href='javascript:void(0)' nc_type='dialog' dialog_title='编辑收货人地址' dialog_id='edit_buyer_address' uri='<?=users_url('shop_deliver/buyer_address_edit', array('order_id' => $output['order_info']['order_id']));?>' dialog_width='550' class='css-btn-mini fr'><i class='icon-edit'></i>编辑</a></td>
				</tr>
				<?php } else { ?>
				<tr>
					<td colspan='20' class='norecord'><i>&nbsp;</i><span>暂无记录</span></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<!--<div class='step-title mt30'><em>第二步</em>确认发货信息</div>
		<div class='deliver-sell-info'><strong class='fl'>我的发货信息：</strong> <a href='javascript:void(0)' nc_type='dialog' dialog_title='编辑发货人地址' dialog_id='modfiy_daddress' uri='<?=users_url('shop_deliver/send_address_select', array('order_id' => $output['order_info']['order_id']));?>' dialog_width='550' class='css-btn-mini fr'><i class='icon-edit'></i>编辑</a> <span id='seller_address_span'>
			<?php if (empty($output['daddress_info'])) {?>
			还未设置发货地址，请进入订单系统 > 发货地址中添加 
			<?php } else { ?>
			<?php echo $output['daddress_info']['seller_name'];?>&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?>
			<?php } ?>
			</span>
		</div>-->
		<div class='step-title mt30'><em>第二步</em>填写物流信息</div>
		<div class='deliver-sell-info'>
			<div class='eject_con' style='width: 400px'>
			<dl>
				<dt>物流公司：</dt>
				<dd>
					<select name='shipping_express_id'>
						<option value='0'>不需要物流</option>
						<?php if(!empty($output['express_list']) && is_array($output['express_list'])) {?>
						<?php foreach($output['express_list'] as $value) {?>
						<option value='<?php echo $value['id'];?>'><?php echo $value['e_name'];?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</dd>
			</dl>
			<dl id='shipping_code_dl' style='display: none'>
				<dt>物流单号：</dt>
				<dd>
					<input class='text w150' type='text' name='shipping_code' id='shipping_code' value='' />
				</dd>
			</dl>
			<div class='bottom' style='background-color: #FFF; border-top: none'>
				<label class='submit-border'>
					<input type='button' class='submit' value='提交' />
				</label>
			</div>
			</div>
		</div>
	</form>
</div>
<script type='text/javascript'>
$(function(){
	$('select[name=shipping_express_id]').change(function(){
		if($(this).val() > 0){
			$('#shipping_code_dl').show();
		} else {
			$('#shipping_code_dl').hide();
		}
	});
	$('.submit').click(function(e){
		ajax_form_post('send_form');
	});
});
</script>
