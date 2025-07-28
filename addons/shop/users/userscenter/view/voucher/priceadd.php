<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('voucher/index')?>'>商家代金券</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/pricelist')?>'>面额管理</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('voucher/priceadd')?>'>添加面额</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/config')?>'>设置</a>
		</li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('voucher/priceadd')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>代金券面额(元)：</dt>
				<dd>
					<input name='voucher_price' class='text w400' value='' type='text' />
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>描述：</dt>
				<dd>
					<textarea name='voucher_price_describe' class='textarea h60 w400'></textarea>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>兑换积分数：</dt>
				<dd>
					<input name='voucher_points' class='text w400' value='' type='text' />
					<span></span>
					<p class='hint'>当兑换代金券时，消耗的积分数</p>
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