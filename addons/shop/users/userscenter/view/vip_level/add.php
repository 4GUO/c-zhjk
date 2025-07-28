<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.g_commission{width: 300px; border: 1px solid #dfdfdf; border-top: none}
.g_commission thead {background: #f5f5f5}
.g_commission td{height: 30px; line-height: 30px; text-align: center; padding: 3px 0px; border-top: 1px #dfdfdf solid}
.g_commission .td_left{border-right: 1px #dfdfdf solid}
</style>
<style>
	#warning {
		display: none;
	}
</style>
<div class='item-publish'>
	<div id='warning' class='alert alert-error'></div>
	<form id='level_form' method='post' target='_parent' action='<?=users_url('vip_level/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>级别名称：</dt>
				<dd>
					<input class='text w200' type='text' name='level_name' id='level_name' value='' />
				</dd>
			</dl>
			<dl>
				<dt>序号：</dt>
				<dd>
					<input name='level_sort' value='0' class='text w60' type='text'>
					<span></span>
					<p class='hint'>数字越大级别越高</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>描述：</dt>
				<dd>
					<?php showEditor('level_desc', '');?>
					<div class='handle'>
						<div class='css-upload-btn'> 
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='level_desc_fileupload' id='level_desc_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a> 
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='level_desc_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'level_desc'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
					</div>
				</dd>
			</dl>
		</div>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
$(function(){
    $('#level_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            level_name : {
                required : true
            },
			level_sort : {
                number : true
            }
        },
        messages : {
            level_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>级别名称不能为空'
            },
			level_sort : {
                required : '<i class=\'icon-exclamation-sign\'></i>级别序号不能为空，且为数字'
            }
        }
    });
	editor_upload_file('level_desc', '<?=users_url('album/image_upload')?>', function(e){
		level_desc.appendHtml('level_desc', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
	});
	$('.submit').click(function(e){
		if($('#level_form').valid()){
			ajax_form_post('level_form');
		};
	});
});
</script>