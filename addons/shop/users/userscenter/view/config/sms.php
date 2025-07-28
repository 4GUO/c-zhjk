<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/sms')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>是否开启：</dt>
				<dd>
					<label><input type='radio' name='sms_status' value='1'<?php if($output['config']['sms_status'] == 1){?> checked='checked'<?php }?> />开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='sms_status' value='0'<?php if($output['config']['sms_status'] == 0){?> checked='checked'<?php }?> />关闭</label>
					<p class='hint'>是否开启短信验证功能</p>
				</dd>
			</dl>
			<dl>
				<dt>类型：</dt>
				<dd>
					<label><input type='radio' name='mobile_host_type' value='1'<?=isset($output['config']['mobile_host_type']) && $output['config']['mobile_host_type'] == 1 ? ' checked=\'checked\'' : '';?> />短信宝 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='mobile_host_type' value='4'<?=isset($output['config']['mobile_host_type']) && $output['config']['mobile_host_type'] == 4 ? ' checked=\'checked\'' : '';?> />阿里云 </label>&nbsp;&nbsp;&nbsp;&nbsp;
				</dd>
			</dl>
			<dl class='opt mobile_host_type1' style='<?php if ($output['config']['mobile_host_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>短信平台账号：</dt>
				<dd>
					<input name='mobile_username' value='<?=isset($output['config']['mobile_username']) ? $output['config']['mobile_username'] : ''?>' class='text w200' type='text' />
				</dd>
			</dl>
			<dl class='opt mobile_host_type1' style='<?php if ($output['config']['mobile_host_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>短信平台密码：</dt>
				<dd>
					<input name='mobile_pwd' value='<?=isset($output['config']['mobile_pwd']) ? $output['config']['mobile_pwd'] : ''?>' class='text w200' type='password' />
				</dd>
			</dl>
			<dl class='opt mobile_host_type4' style='<?php if ($output['config']['mobile_host_type'] != 4) { ?>display: none;<?php } ?>'>
				<dt>AccessKeyId：</dt>
				<dd>
					<input name='mobile_accessKeyId' value='<?=isset($output['config']['mobile_accessKeyId']) ? $output['config']['mobile_accessKeyId'] : ''?>' class='text w200' type='text' />
				</dd>
			</dl>
			<dl class='opt mobile_host_type4' style='<?php if ($output['config']['mobile_host_type'] != 4) { ?>display: none;<?php } ?>'>
				<dt>AccessKeySecret：</dt>
				<dd>
					<input name='mobile_accessKeySecret' value='<?=isset($output['config']['mobile_accessKeySecret']) ? $output['config']['mobile_accessKeySecret'] : ''?>' class='text w200' type='text' />
				</dd>
			</dl>
			<dl class='opt mobile_host_type4' style='<?php if ($output['config']['mobile_host_type'] != 4) { ?>display: none;<?php } ?>'>
				<dt>短信签名：</dt>
				<dd>
					<input name='mobile_sign_name' value='<?=isset($output['config']['mobile_sign_name']) ? $output['config']['mobile_sign_name'] : ''?>' class='text w200' type='text' />
				</dd>
			</dl>
			<dl class='opt mobile_host_type4' style='<?php if ($output['config']['mobile_host_type'] != 4) { ?>display: none;<?php } ?>'>
				<dt>验证码模板ID：</dt>
				<dd>
					<input name='mobile_templateCode' value='<?=isset($output['config']['mobile_templateCode']) ? $output['config']['mobile_templateCode'] : ''?>' class='text w200' type='text' />
					<p class='hint'>模板示例：您的验证码是${code}</p>
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
$('input[name=mobile_host_type]').change(function() {
    var mobile_host_type = $(this).val();
    $('.opt').hide();
    $('.mobile_host_type' + mobile_host_type).show();
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>