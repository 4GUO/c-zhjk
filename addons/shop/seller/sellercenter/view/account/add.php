<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<form id='form' method='post' target='_parent' action='<?=users_url('account/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt><i class='required'>*</i>登陆账号：</dt>
			<dd>
				<input class='text w200' type='text' name='account_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt>登录密码：</dt>
			<dd>
				<input class='text w200' type='text' name='password' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>用户组：</dt>
			<dd>
				<select name='group_id'>
					<option value='0'>请选择用户组</option>
					<?php foreach($output['group_list'] as $val) { ?>
					<option value='<?php echo $val['group_id']; ?>'><?php echo $val['group_name']; ?></option>
					<?php } ?>
				</select>
			</dd>
		</dl>
		<dl>
			<dt>状态：</dt>
			<dd>
				<label><input type='radio' name='state' value='1' checked='checked' />是</label>
				<label><input type='radio' name='state' value='0' />否</label>
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
		ajax_form_post('form');
	});
});
</script>