<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('index/password')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>登录密码：</dt>
				<dd>
					<input name='password' class='text w400' value='' type='password' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>确认登录密码：</dt>
				<dd>
					<input name='repassword' class='text w400' value='' type='password' />
					<span></span>
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