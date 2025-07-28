<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('wechat/api_manage')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='wid' value='<?=$output['api_account']['wechat_id']?>' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>接口URL：</dt>
				<dd>
					<?=_url('api/weixin/index', array(), false, true)?>/?i=1&wsn=<?=$output['api_account']['wechat_sn']?>
				</dd>
			</dl>
			<dl>
				<dt>Token：</dt>
				<dd>
					<?=$output['api_account']['wechat_token']?>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>公众号类型：</dt>
				<dd>
					<label><input type='radio' name='type' value='0'<?php if($output['api_account']['wechat_type'] == 0){?> checked='checked'<?php }?> />订阅号未认证</label>&nbsp;
					<label><input type='radio' name='type' value='1'<?php if($output['api_account']['wechat_type'] == 1){?> checked='checked'<?php }?> />订阅号已认证</label>&nbsp;
					<label><input type='radio' name='type' value='2'<?php if($output['api_account']['wechat_type'] == 2){?> checked='checked'<?php }?> />服务号未认证</label>&nbsp;
					<label><input type='radio' name='type' value='3'<?php if($output['api_account']['wechat_type'] == 3){?> checked='checked'<?php }?> />服务号已认证</label>
				</dd>
			</dl>
			<dl>
				<dt>AppID：</dt>
				<dd>
					<input name='appid' class='text w400' value='<?=$output['api_account']['wechat_appid']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>AppSecret：</dt>
				<dd>
					<input name='appsecret' class='text w400' value='<?=$output['api_account']['wechat_appsecret']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>公众号名称：</dt>
				<dd>
					<input name='name' class='text w400' value='<?=$output['api_account']['wechat_name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>公众号邮箱：</dt>
				<dd>
					<input name='email' class='text w400' value='<?=$output['api_account']['wechat_email']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>公众号原始ID：</dt>
				<dd>
					<input name='preid' class='text w400' value='<?=$output['api_account']['wechat_preid']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>微信号：</dt>
				<dd>
					<input name='account' class='text w400' value='<?=$output['api_account']['wechat_account']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>消息加解密方式：</dt>
				<dd>
					<label><input type='radio' name='encodingtype' value='0'<?php if($output['api_account']['wechat_encodingtype'] == 0){?> checked='checked'<?php }?> />明文模式</label>&nbsp;
					<label><input type='radio' name='encodingtype' value='1'<?php if($output['api_account']['wechat_encodingtype'] == 1){?> checked='checked'<?php }?> />兼容模式</label>&nbsp;
					<label><input type='radio' name='encodingtype' value='2'<?php if($output['api_account']['wechat_encodingtype'] == 2){?> checked='checked'<?php }?> />安全模式</label>
				</dd>
			</dl>
			<dl>
				<dt>EncodingAESKey：</dt>
				<dd>
					<input name='encoding' class='text w400' value='<?=$output['api_account']['wechat_encoding']?>' type='text' />
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