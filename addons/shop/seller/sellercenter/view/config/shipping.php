<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='active'><a href='javascript:;'>综合设置</a></li>
		<li class='normal'><a href='<?=_url('config/shipping_transport')?>'>区域运费</a></li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=_url('config/shipping')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>统一运费：</dt>
				<dd>
					<input name='freight_in' class='text w60' value='<?=$output['config']['freight_in']?>' type='text' /> 元
					<p class='hint'>该设置项仅做记录，具体运费在编辑商品时设置</p>
				</dd>
			</dl>
			<dl>
				<dt>运费统一到所有商品：</dt>
				<dd>
					<label><input type='radio' name='freight_in_all' value='1'<?php echo $output['config']['freight_in_all'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='freight_in_all' value='0'<?php echo $output['config']['freight_in_all'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
					<p class='hint'>开启后，系统自动将所有商品的运费统一设置</p>
				</dd>
			</dl>
			<dl>
				<dt>免运费条件：</dt>
				<dd>
					订单总额到达 <input name='freight_infree' class='text w60' value='<?=$output['config']['freight_infree']?>' type='text' /> 元
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