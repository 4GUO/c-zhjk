<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='cert_form' method='post' target='_parent' action='<?=users_url('poster/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt>标题：</dt>
			<dd>
				<input class='text w200' type='text' name='cert_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt>选择按钮：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb' style='width: 100px; height: 100px;'> <img style='width: 100px;' nctype='cert_ico' src='<?=STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='cert_ico' nctype='cert_ico' value='' type='hidden' />
						<span></span>
						<p class='hint'>建议使用<font color='red'>尺寸100x100像素、大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。
							提示：供前台选择按钮使用</p>
						<div class='handle'>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='cert_ico' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'cert_ico'))?>'><i class='fa fa-picture-o'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>海报背景：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='cert_image' src='<?=STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='cert_image' nctype='cert_image' value='' type='hidden' />
						<span></span>
						<p class='hint'>建议使用<font color='red'>尺寸640x1200像素、大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class='handle'>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='cert_image' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'cert_image'))?>'><i class='fa fa-picture-o'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='cert_sort' value=''  />
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='cert_usable' value='1' checked='checked' />是</label>
				<label><input type='radio' name='cert_usable' value='0' />否</label>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
$(function(){
	$('.submit').click(function(e){
		ajax_form_post('cert_form');
	});
});
</script>