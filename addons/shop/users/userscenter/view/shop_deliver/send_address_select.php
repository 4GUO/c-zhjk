<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div class='adds' style=' min-height:240px;'>
		<table class='css-default-table'>
			<?php if (is_array($output['address_list']) && !empty($output['address_list'])){?>
			<thead>
				<tr>
					<th class='w80'>发货人</th>
					<th>发货地址</th>
					<th class='w100'>电话</th>
					<th class='w80'>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($output['address_list'] as $key => $value) {?>
				<tr class='bd-line'>
					<td class='tc'><?php echo $value['seller_name'];?></td>
					<td><?php echo $value['area_info'];?> <?php echo $value['address'];?></td>
					<td class='tc'><?php echo $value['telphone'];?></td>
					<td class='tc'><a href='javascript:void(0);' nc_type='select' class='css-btn' address_id='<?php echo $value['address_id'];?>' address_value='<?php echo $value['seller_name'].'&nbsp;'.$value['telphone'].'&nbsp;'.$value['area_info'].'&nbsp;'.$value['address'];?>'>选择</a></td>
				</tr>
				<?php }?>
				<tr class='bd-line'>
					<td colspan='20'></td>
				</tr>
			</tbody>
			<?php } else {?>
			<tboby>
				<tr>
					<td colspan='5'>还未设置发货地址，请进入订单系统 > 发货地址中添加</td>
				</tr>
			</tboby>
			<?php }?>
		</table>
	</div>
</div>
<script type='text/javascript'>
$(function(){
	$('a[nc_type=select]').on('click',function(){
        var daddress_id = $(this).attr('address_id');
        var address_value = $(this).attr('address_value');
		
        $.post(
            '<?php echo users_url('shop_deliver/send_address_select');?>', 
            {order_id: <?php echo $output['order_id'];?>, daddress_id: daddress_id, form_submit: 'ok'}
        )
        .done(function(data) {
            $('#daddress_id').val(daddress_id);
            $('#seller_address_span').html(address_value);
            DialogManager.close('modfiy_daddress');            
        }); 
	});
});
</script>