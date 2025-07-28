<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/base')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>是否强制完善信息：</dt>
				<dd>
					<label><input type='radio' name='perfect_information' value='1'<?php if($output['config']['perfect_information'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='perfect_information' value='0'<?php if($output['config']['perfect_information'] == 0){?> checked='checked'<?php }?> />否</label>
					<p class='hint'>开启后，会强制会员完善手机号、真实姓名、微信号</p>
				</dd>
			</dl>
			<dl>
				<dt>网站名称：</dt>
				<dd>
					<input name='name' class='text w400' value='<?=$output['config']['name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>系统LOGO：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='login_logo' src='<?=!empty($output['config']['login_logo']) ? $output['config']['login_logo'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='login_logo' nctype='login_logo' value='<?=!empty($output['config']['login_logo']) ? $output['config']['login_logo'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x640像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='login_logo' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'login_logo'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
				<dt>隐私协议：</dt>
				<dd>
					<?php showEditor('xieyi_content', isset($output['config']['xieyi_content']) ? htmlspecialchars_decode($output['config']['xieyi_content']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='xieyi_content_fileupload' id='xieyi_content_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='xieyi_content_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'xieyi_content'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
					</div>
				</dd>
			</dl>
			<dl>
				<dt>APP下载链接：</dt>
				<dd>
					<input name='apploadurl' class='text w400' value='<?=$output['config']['apploadurl']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>提货运费：</dt>
				<dd>
					<input name='tihuo_freight' class='text w60' value='<?=$output['config']['tihuo_freight']?>' type='text' />&nbsp;元
					<span></span>
				</dd>
			</dl>
			<div style='height: 40px; line-height: 40px; font-size: 16px; font-weight: blod; background: #f5f5f5; text-indent: 20px'>物流跟踪配置</div>
			<dl>
				<dt>customer：</dt>
				<dd>
					<input name='kuaidi100_customer' class='text w400' value='<?=$output['config']['kuaidi100_customer']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>授权key：</dt>
				<dd>
					<input name='kuaidi100_key' class='text w400' value='<?=$output['config']['kuaidi100_key']?>' type='text' />
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
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
upload_file('share_logo', '<?=users_url('album/image_upload')?>');
editor_upload_file('xieyi_content', '<?=users_url('album/image_upload')?>', function(e){
	xieyi_content.appendHtml('xieyi_content', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>