<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('wechat/setting_manage')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='wid' value='<?=$output['api_account']['wechat_id']?>' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>微信模块开关：</dt>
				<dd>
					<label><input type='radio' name='isuse' value='1'<?php if($output['setting']['wechat_isuse'] == 1){?> checked='checked'<?php }?> />开启</label>&nbsp;
					<label><input type='radio' name='isuse' value='0'<?php if($output['setting']['wechat_isuse'] == 0){?> checked='checked'<?php }?> />关闭</label>
				</dd>
			</dl>
			<dl>
				<dt>自定分享标题：</dt>
				<dd>
					<input name='sharetitle' class='text w400' value='<?=$output['api_account']['wechat_share_title']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>自定分享简介：</dt>
				<dd>
					<textarea name='sharedesc' class='text w400'><?=$output['api_account']['wechat_share_desc']?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>自定分享图片：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='share_logo' src='<?=!empty($output['api_account']['wechat_share_logo']) ? $output['api_account']['wechat_share_logo'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='share_logo' nctype='share_logo' value='<?=!empty($output['api_account']['wechat_share_logo']) ? $output['api_account']['wechat_share_logo'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x640像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='share_logo' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'share_logo'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>微信小程序配置</div>
			<dl>
				<dt>AppId：</dt>
				<dd>
					<input name='appid' class='text w400' value='<?=$output['api_account']['wxappid']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>AppSecret：</dt>
				<dd>
					<input name='appsecret' class='text w400' value='<?=$output['api_account']['wxappsecret']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>小程序二维码：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='wxapp_ercode' src='<?=!empty($output['api_account']['wxapp_ercode']) ? $output['api_account']['wxapp_ercode'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='wxapp_ercode' nctype='wxapp_ercode' value='<?=!empty($output['api_account']['wxapp_ercode']) ? $output['api_account']['wxapp_ercode'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x640像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='wxapp_ercode' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'wxapp_ercode'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<!--<dl>
				<dt>退款成功通知：</dt>
				<dd>
					<input name='refund_success_template_id' class='text w400' value='<?=$output['api_account']['refund_success_template_id']?>' type='text' />
					<span></span>
					<p class='hint'>订单号、退款金额、退款理由</p>
				</dd>
			</dl>
			<dl>
				<dt>退款拒绝通知：</dt>
				<dd>
					<input name='refund_pass_template_id' class='text w400' value='<?=$output['api_account']['refund_pass_template_id']?>' type='text' />
					<span></span>
					<p class='hint'>订单编号、退款金额、拒绝原因</p>
				</dd>
			</dl>-->
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>模板消息</div>
			<dl>
				<dt>获取奖励提醒：</dt>
				<dd>
					<input name='reward_template_id' class='text w400' value='<?=isset($output['api_account']['reward_template_id']) ? $output['api_account']['reward_template_id'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>模板分类：消费品->消费品->收入提醒</p>
				</dd>
			</dl>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>退款证书</div>
			<dl>
				<dt>apiclient_cert.pem 证书：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<input name='apiclient_cert_pem' nctype='apiclient_cert_pem' value='<?=!empty($output['api_account']['apiclient_cert_pem']) ? $output['api_account']['apiclient_cert_pem'] : ''?>' type='text' class='text w400' readonly='readonly' />
							<span></span>
							<p class='hint'>仅支持pem格式，退款或用户提现时使用</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='apiclient_cert_pem' id='apiclient_cert_pem' type='file' />
										</span>
										<p><i class='icon-upload-alt'></i>证书上传</p>
									</a> 
								</div>
							</div>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>apiclient_key.pem 证书：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<input name='apiclient_key_pem' nctype='apiclient_key_pem' value='<?=!empty($output['api_account']['apiclient_key_pem']) ? $output['api_account']['apiclient_key_pem'] : ''?>' type='text' class='text w400' readonly='readonly' />
							<span></span>
							<p class='hint'>仅支持pem格式，退款或用户提现时使用</p>
							<div class='handle'>
								<div class='css-upload-btn'> 
								    <a href='javascript:void(0);'>
										<span>
											<input hidefocus='true' size='1' class='input-file' name='apiclient_key_pem' id='apiclient_key_pem' type='file' />
										</span>
										<p><i class='icon-upload-alt'></i>证书上传</p>
									</a> 
								</div>
							</div>
						</div>
					</div>
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
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
upload_file('share_logo', '<?=users_url('album/image_upload')?>');
upload_file('apiclient_cert_pem', '<?=users_url('file_upload/pem_upload')?>');
upload_file('apiclient_key_pem', '<?=users_url('file_upload/pem_upload')?>');
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>