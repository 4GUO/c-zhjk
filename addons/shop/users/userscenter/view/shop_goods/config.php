<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('shop_goods/config')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>商品是否需要审核：</dt>
				<dd>
					<label><input type='radio' name='goods_verify' value='1'<?php if($output['config']['goods_verify'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='goods_verify' value='0'<?php if($output['config']['goods_verify'] == 0){?> checked='checked'<?php }?> />否</label>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>开启会员价：</dt>
				<dd>
					<label><input type='radio' name='vip_price_verify' value='1'<?php if($output['config']['vip_price_verify'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='vip_price_verify' value='0'<?php if($output['config']['vip_price_verify'] == 0){?> checked='checked'<?php }?> />否</label>
					<p class='hint'></p>
				</dd>
			</dl>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button'>
			</label>
		</div>
	</form>
</div>
<script>
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>