<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='article_form' action='<?=users_url('article/content')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['content']['id']) ? $output['content']['id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3>关于公司</h3>
			<dl>
				<dt></dt>
				<dd>
					<?php showEditor('about_company', isset($output['content']['about_company']) ? htmlspecialchars_decode($output['content']['about_company']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='about_company_fileupload' id='about_company_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='about_company_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'about_company'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
					</div>
				</dd>
			</dl>
			<h3>资质荣誉</h3>
			<dl>
				<dt></dt>
				<dd>
					<?php showEditor('company_rongyu', isset($output['content']['company_rongyu']) ? htmlspecialchars_decode($output['content']['company_rongyu']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='company_rongyu_fileupload' id='company_rongyu_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='company_rongyu_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'company_rongyu'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
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
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
upload_file('thumb', '<?=users_url('album/image_upload')?>');
editor_upload_file('about_company', '<?=users_url('album/image_upload')?>', function(e){
	about_company.appendHtml('about_company', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
editor_upload_file('company_rongyu', '<?=users_url('album/image_upload')?>', function(e){
	company_rongyu.appendHtml('company_rongyu', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
});
$('.submit').click(function(e){
	ajax_form_post('article_form');
});
</script>