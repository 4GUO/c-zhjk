<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	#config_box_0,#config_box_1,#config_box_2,#config_box_3{
		display:none;
	}
</style>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('attachment/config')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
		    <dl>
				<dt>是否开启：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='attachment_open' value='1' <?php if((isset($output['setting']['attachment_open']) && $output['setting']['attachment_open'] == 1) || !isset($output['setting']['attachment_open'])){?> checked='checked'<?php }?> type='radio' />开启</label>
						</li>
						<li>
							<label><input name='attachment_open' value='0' <?php if(isset($output['setting']['attachment_open']) && $output['setting']['attachment_open'] == 0){?> checked='checked'<?php }?> type='radio' />关闭</label>
						</li>
					</ul>
				</dd>
			</dl>
			<dl>
				<dt>存储区域：</dt>
				<dd>
					<label><input type='radio' name='qiniu[zone]' value='z0'<?=isset($output['qiniu']['zone']) && $output['qiniu']['zone'] == 'z0' ? ' checked=\'checked\'' : '';?> />华东 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='qiniu[zone]' value='z1'<?=isset($output['qiniu']['zone']) && $output['qiniu']['zone'] == 'z1' ? ' checked=\'checked\'' : '';?> />华北 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='qiniu[zone]' value='z2'<?=isset($output['qiniu']['zone']) && $output['qiniu']['zone'] == 'z2' ? ' checked=\'checked\'' : '';?> />华南 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='qiniu[zone]' value='na0'<?=isset($output['qiniu']['zone']) && $output['qiniu']['zone'] == 'na0' ? ' checked=\'checked\'' : '';?> />北美 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='qiniu[zone]' value='as0'<?=isset($output['qiniu']['zone']) && $output['qiniu']['zone'] == 'as0' ? ' checked=\'checked\'' : '';?> />新加坡 </label>
				</dd>
			</dl>
			<dl>
				<dt>Accesskey：</dt>
				<dd>
					<input name='qiniu[accesskey]' value='<?=isset($output['qiniu']['accesskey']) ? $output['qiniu']['accesskey'] : ''?>' class='text w200' type='text' />
					<p class='hint'>注：用于签名的公钥</p>
				</dd>
			</dl>
			<dl>
				<dt>Secretkey：</dt>
				<dd>
					<input name='qiniu[secretkey]' value='<?=isset($output['qiniu']['secretkey']) ? $output['qiniu']['secretkey'] : ''?>' class='text w200' type='password' />
					<p class='hint'>注：用于签名的私钥</p>
				</dd>
			</dl>
			<dl>
				<dt>Bucket：</dt>
				<dd>
					<input name='qiniu[bucket]' value='<?=isset($output['qiniu']['bucket']) ? $output['qiniu']['bucket'] : ''?>' class='text w200' type='text' />
					<p class='hint'>注：请保证bucket为可公共读取的</p>
				</dd>
			</dl>
			<dl>
				<dt>Url：</dt>
				<dd>
					<input name='qiniu[url]' value='<?=isset($output['qiniu']['url']) ? $output['qiniu']['url'] : ''?>' class='text w200' type='text' />
					<p class='hint'>注：七牛支持用户自定义访问域名。注：url开头加http://或https://结尾加 ‘/’例：http://images.abc.com/</p>
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