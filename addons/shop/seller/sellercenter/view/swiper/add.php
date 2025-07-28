<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=_url('swiper/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt><i class='required'>*</i>标题：</dt>
			<dd>
				<input class='text w200' type='text' name='title' id='title' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>页面：</dt>
			<dd>
				<select name='module'>
					<?php foreach($output['module_list'] as $module_id=>$module_name){?>
					<option value='<?php echo $module_id;?>'><?php echo $module_name;?></option>
					<?php }?>
				</select>
			</dd>
		</dl>
		<dl>
			<dt>图片：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='image' src='<?=STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='image' nctype='image' value='' type='hidden' />
						<span></span>
						<p class='hint'>建议使用<font color='red'>尺寸640x320像素、大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class='handle'>
							<div class='css-upload-btn'> 
							    <a href='javascript:void(0);'>
									<span>
										<input hidefocus='true' size='1' class='input-file' name='image' id='image' type='file' />
									</span>
									<p><i class='icon-upload-alt'></i>图片上传</p>
								</a> 
							</div>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='image' dialog_width='830' dialog_height='550' uri='<?=_url('album/pic_list_view', array('item' => 'image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl style='display: none'>
			<dt>链接：</dt>
			<dd>
				<input class='text w200' type='text' name='href' id='href' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='sort' value=''  />
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='state' value='1' checked='checked' />是</label>
				<label><input type='radio' name='state' value='0' />否</label>
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
upload_file('image', '<?=_url('album/image_upload')?>');
$(function(){
    $('#category_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            title : {
                required : true
            },
            sort : {
                number   : true
            }
        },
        messages : {
            title : {
                required : '<i class=\'icon-exclamation-sign\'></i>标题不能为空'

            },
            sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#category_form').valid()){
			ajax_form_post('category_form');
		};
	});
});
</script>