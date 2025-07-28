<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='youwei_form' method='post' target='_parent'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt>图片：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='image' src='<?=$output['image'] ?: STATIC_URL . '/images/default_image.png'?>'> </div>
						<input nctype='image' value='<?=$output['image']?>' type='hidden' id='image_src' name='image_src' />
						<span></span>
						<p class='hint'>建议使用<font color='red'>尺寸<?=$output['size']?>像素、大小不超过2M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class='handle'>
							<div class='css-upload-btn'> 
							    <a href='javascript:void(0);'>
									<span>
										<input hidefocus='true' size='1' class='input-file' name='image' id='image' type='file' />
									</span>
									<p><i class='icon-upload-alt'></i>图片上传</p>
								</a> 
							</div>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='image' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt>链接：</dt>
			<dd>
				<input nctype='link' name='link' class='text w250' value='<?=$output['data']?>' type='text' style='float:left;' />
				<span style='float:left;'>
					<div class='handle'>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择链接' dialog_id='video' dialog_width='830' dialog_height='550' uri='<?=users_url('config/sys_list', array('input_name' => 'link'))?>' style='margin:0 10px;'><i class='icon-link'></i>选择链接</a>
					</div>
				</span>
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
upload_file('image', '<?=users_url('album/image_upload')?>');
$(function(){
    $('#youwei_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            image_src : {
                required : true
            }
        },
        messages : {
            image_src : {
                required : '<i class=\'icon-exclamation-sign\'></i>图片不能为空'

            }
        }
    });
	$('.submit').click(function(e) {
		if ($('#youwei_form').valid()) {
			var img_src = $('input[nctype=image]').val();
			var link = $('input[nctype=link]').val();
			<?php if ($output['item_type'] == 'adv_list' || $output['item_type'] == 'home3') { ?>
			add_item_image_save(img_src, 'url', link, function() {
				$('#fwin_image_publish').hide();
				$('#image_publish_locker').remove();
			});
			<?php } else { ?>
			edit_item_image_save(img_src, 'url', link, function() {
				$('#fwin_image_publish').hide();
				$('#image_publish_locker').remove();
			}, '<?=$output['imgbox']?>');
			<?php } ?>
		};
	});
});
</script>