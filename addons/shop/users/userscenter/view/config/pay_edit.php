<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.css-form-goods dl dt {
		width: 23%;
	}
	.css-form-goods dl dd {
		width: 74%;
	}
</style>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/pay_edit')?>'>
		<input name='form_submit' value='ok' type='hidden'/>
		<input name='payment_id' value='<?=$output['mb_payment_info']['payment_id']?>' type='hidden'/>
		<div class='css-form-goods'>
		<?php if ($output['mb_payment_info']['payment_code'] == 'wxapp') {?>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>微信小程序支付配置</div>
			<dl>
				<dt>AppId：</dt>
				<dd>
					<input name='appid' class='text w400' value='<?=isset($output['config']['wxappid']) ? $output['config']['wxappid'] : ''?>' type='text' readonly='readonly' style='background: #eee' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>AppSecret：</dt>
				<dd>
					<input name='appsecret' class='text w400' value='<?=isset($output['config']['wxappsecret']) ? $output['config']['wxappsecret'] : ''?>' type='text' readonly='readonly' style='background: #eee' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>微信支付商户号：</dt>
				<dd>
					<input name='mchid' class='text w400' value='<?php echo empty($output['payment_config']['mchid']) ? '' : $output['payment_config']['mchid'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>微信支付秘钥：</dt>
				<dd>
					<input name='signkey' class='text w400' value='<?php echo empty($output['payment_config']['signkey']) ? '' : $output['payment_config']['signkey'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
		<?php } else if ($output['mb_payment_info']['payment_code'] == 'wxpay_jsapi') {?>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>微信jsapi支付配置</div>
			<dl>
				<dt>APP唯一凭证（appid）：</dt>
				<dd>
					<input name='appId' class='text w400' value='<?=isset($output['config']['wechat_appid']) ? $output['config']['wechat_appid'] : ''?>' type='text' readonly='readonly' style='background: #eee' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>应用密钥（appsecret）：</dt>
				<dd>
					<input name='appSecret' class='text w400' value='<?=isset($output['config']['wechat_appsecret']) ? $output['config']['wechat_appsecret'] : ''?>' type='text' readonly='readonly' style='background: #eee' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>微信支付商户号（partner ID）：</dt>
				<dd>
					<input name='partnerId' class='text w400' value='<?php echo empty($output['payment_config']['partnerId']) ? '' : $output['payment_config']['partnerId'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>API密钥：</dt>
				<dd>
					<input name='apiKey' class='text w400' value='<?php echo empty($output['payment_config']['apiKey']) ? '' : $output['payment_config']['apiKey'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
		<?php } else if ($output['mb_payment_info']['payment_code'] == 'wxpay_h5') {?>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>微信H5支付配置</div>
			<dl>
				<dt>APP唯一凭证（appid）：</dt>
				<dd>
					<input name='appId' class='text w400' value='<?=isset($output['config']['wechat_appid']) ? $output['config']['wechat_appid'] : ''?>' type='text' readonly='readonly' style='background: #eee' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>微信支付商户号（partner ID）：</dt>
				<dd>
					<input name='partnerId' class='text w400' value='<?php echo empty($output['payment_config']['partnerId']) ? '' : $output['payment_config']['partnerId'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>API密钥：</dt>
				<dd>
					<input name='apiKey' class='text w400' value='<?php echo empty($output['payment_config']['apiKey']) ? '' : $output['payment_config']['apiKey'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
		<?php } else if ($output['mb_payment_info']['payment_code'] == 'alipay') {?>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>支付宝wap支付配置</div>
			<dl>
				<dt>应用ID（appid）：</dt>
				<dd>
					<input name='appid' class='text w400' value='<?php echo empty($output['payment_config']['appid']) ? '' : $output['payment_config']['appid'];?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>应用私钥（private_key）：</dt>
				<dd>
					<textarea name='private_key' class='text w400' style='height: 80px;' /><?php echo empty($output['payment_config']['private_key']) ? '' : $output['payment_config']['private_key'];?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>支付宝公钥（public_key）：</dt>
				<dd>
					<textarea name='public_key' class='text w400' style='height: 80px;' /><?php echo empty($output['payment_config']['public_key']) ? '' : $output['payment_config']['public_key'];?></textarea>
					<span></span>
				</dd>
			</dl>
		<?php }?>
			<dl>
				<dt>启用：</dt>
				<dd>
					<label><input type='radio' name='payment_state' value='1'<?php if($output['mb_payment_info']['payment_state'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
					<label><input type='radio' name='payment_state' value='0'<?php if($output['mb_payment_info']['payment_state'] == 0){?> checked='checked'<?php }?> />否</label>
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
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>