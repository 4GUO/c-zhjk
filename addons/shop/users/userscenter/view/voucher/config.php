<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('voucher/index')?>'>商家代金券</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/pricelist')?>'>面额管理</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/priceadd')?>'>添加面额</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('voucher/config')?>'>设置</a>
		</li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('voucher/config')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>买家最大领取数量：</dt>
				<dd>
					<input name='promotion_voucher_buyertimes_limit' class='text w100' value='<?=$output['setting']['promotion_voucher_buyertimes_limit']?>' type='text' />
					<span>张</span>
					<p class='hint'>买家最多只能拥有同一个店铺尚未消费抵用的店铺代金券最大数量</p>
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