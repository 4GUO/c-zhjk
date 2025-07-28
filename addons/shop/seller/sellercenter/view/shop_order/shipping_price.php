<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='order_form' method='post' target='_parent' action='<?=users_url('shop_order/shipping_price')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='order_id' value='<?=$output['order_info']['order_id']?>' />
		<dl>
			<dt>买家：</dt>
			<dd>
				<?=$output['order_info']['member_name']?>
			</dd>
		</dl>
		<dl>
			<dt>订单号：</dt>
			<dd>
				<span><?=$output['order_info']['order_sn']?></span>
			</dd>
		</dl>
		<dl>
			<dt>运费：</dt>
			<dd>
				<input class='text w60' type='text' name='shipping_fee' value='<?=$output['order_info']['shipping_fee']?>'  />
			</dd>
		</dl>
		
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript'>
$(function(){
	$('.submit').click(function(e){
		ajax_form_post('order_form');
	});
});
</script>