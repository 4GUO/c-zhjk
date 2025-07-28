<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	#warning {
		display: none;
	}
</style>
<div class='item-publish'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('shop_goods_class/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['class_info']['gc_id']?>' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>分类名称：</dt>
				<dd>
					<input class='text w200' type='text' name='gc_name' id='gc_name' value='<?=$output['class_info']['gc_name']?>' />
				</dd>
			</dl>
			<?php if($output['class_info']['has_child'] == 0){?>
			<dl>
				<dt>上级分类：</dt>
				<dd>
					<select name='gc_parent_id'>
						<option value='0'>一级分类</option>
					<?php foreach($output['first_gc'] as $value){ if($value['gc_id'] == $output['class_info']['gc_id']){ continue; }?>
						<option value='<?php echo $value['gc_id'];?>'<?php echo $value['gc_id'] == $output['class_info']['gc_parent_id'] ? ' selected' : '';?>><?php echo $value['gc_name'];?></option>
					<?php }?>
					</select>
				</dd>
			</dl>
			<?php } else { ?>		
			<input type='hidden' name='gc_parent_id' value='0' />
			<?php } ?>
					
			<dl>
				<dt>分类图标：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='gc_image' src='<?=isset($output['class_info']['gc_image']) ? $output['class_info']['gc_image'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='image_path' nctype='gc_image' value='<?=isset($output['class_info']['gc_image']) ? $output['class_info']['gc_image'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>上传分类图标；支持jpg、gif、png格式上传或从图片空间中选择，建议使用<font color='red'>尺寸100x100像素以上、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='gc_image' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'gc_image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>排序：</dt>
				<dd>
					<input class='text w60' type='text' name='gc_sort' value='<?=$output['class_info']['gc_sort']?>'  />
					<p class='hint'>数字越小越靠前</p>
				</dd>
			</dl>
			<dl>
				<dt>在首页显示：</dt>
				<dd>
					<label><input type='radio' name='index_show' value='1'<?php if($output['class_info']['index_show'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
					<label><input type='radio' name='index_show' value='0'<?php if($output['class_info']['index_show'] == 0){?> checked='checked'<?php }?> />否</label>
				</dd>
			</dl>
			<dl>
				<dt>启用：</dt>
				<dd>
					<label><input type='radio' name='gc_state' value='1'<?php if($output['class_info']['gc_state'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
					<label><input type='radio' name='gc_state' value='0'<?php if($output['class_info']['gc_state'] == 0){?> checked='checked'<?php }?> />否</label>
				</dd>
			</dl>
			<dl>
				<dt>发布虚拟商品：</dt>
				<dd>
					<label><input type='radio' name='gc_virtual' value='1'<?php if($output['class_info']['gc_virtual'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
					<label><input type='radio' name='gc_virtual' value='0'<?php if($output['class_info']['gc_virtual'] == 0){?> checked='checked'<?php }?> />否</label>
				</dd>
			</dl>
			<div class='bottom'>
				<label class='submit-border'>
					<input type='button' class='submit' value='提交' />
				</label>
			</div>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
upload_file('gc_image', '<?=users_url('album/image_upload')?>');
$(function(){
    $('#category_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            gc_name : {
                required : true
            },
            gc_sort : {
                number   : true
            }
        },
        messages : {
            gc_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            gc_sort  : {
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