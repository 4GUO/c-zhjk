<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('vip_level/config')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>描述：</dt>
				<dd>
					<?php showEditor('level_rule', isset($output['config']['level_rule']) ? htmlspecialchars_decode($output['config']['level_rule']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='level_rule_fileupload' id='level_rule_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='level_rule_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'level_rule'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
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
editor_upload_file('level_rule', '<?=users_url('album/image_upload')?>', function(e){
	level_rule.appendHtml('level_rule', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>