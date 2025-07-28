<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('mb_special/special_save')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['info']['special_id']) ? $output['info']['special_id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>基本信息</h3>
			<dl>
				<dt><i class='required'>*</i>标题：</dt>
				<dd>
					<input name='special_desc' class='text w400' value='<?=isset($output['info']['special_desc']) ? $output['info']['special_desc'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>标题名称长度至少3个字符，最长50个汉字</p>
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