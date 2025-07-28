<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/module_edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='id' value='<?=$output['info']['id']?>' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>名称：</dt>
				<dd>
					<input name='name' class='text w400' value='<?=$output['info']['name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>图标：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='thumb' src='<?=!empty($output['info']['thumb']) ? $output['info']['thumb'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='thumb' nctype='thumb' value='<?=!empty($output['info']['thumb']) ? $output['info']['thumb'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸64x64像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='thumb' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'thumb'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
				<dt>链接：</dt>
				<dd>
					<input nctype='link' name='link' class='text w400' value='<?=$output['info']['link']?>' type='text' style='float:left;' />
					<span style='float:left;'>
						<div class='handle'>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择链接' dialog_id='video' dialog_width='830' dialog_height='550' uri='<?=users_url('config/sys_list', array('input_name' => 'link'))?>' style='margin:0 10px;'><i class='icon-link'></i>选择链接</a>
						</div>
					</span>
				</dd>
			</dl>
			<dl>
				<dt>商城首页：</dt>
				<dd>
					<label><input type='radio' name='status' value='1'<?php if($output['info']['status'] == 1){?> checked='checked'<?php }?> />显示</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='status' value='0'<?php if($output['info']['status'] == 0){?> checked='checked'<?php }?> />不显示</label>
					<p class='hint'>商城首页九宫格显示状态</p>
				</dd>
			</dl>
			<dl>
				<dt>排序：</dt>
				<dd>
					<input name='m_sort' class='text w400' value='<?=$output['info']['m_sort']?>' type='text' />
					<span></span>
					<p class='hint'>数字越大越靠前</p>
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
upload_file('thumb', '<?=users_url('album/image_upload')?>');
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>