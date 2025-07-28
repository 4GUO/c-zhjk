<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='order_form' method='post' target='_parent' action='<?=users_url('shop_deliver/delay_receive')?>'>
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
			<dt>最晚收货时间：</dt>
			<dd><?php echo date('Y-m-d H:i:s',$output['order_info']['delay_time']);?><br/>
				如果超过该时间买家未点击收货，系统将自动更改为收货状态 </dd>
		</dl>
		<dl>
			<dt>研制：</dt>
			<dd>
				<select name='delay_date'>
					<option value='5'>5</option>
					<option value='10'>10</option>
					<option value='15'>15</option>
				</select>
				天 </dd>
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