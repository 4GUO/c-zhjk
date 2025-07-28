<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/kefu')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>客服电话：</dt>
				<dd>
					<input name='telphone' class='text w400' value='<?=$output['config']['telphone']?>' type='text' />
					<span></span>
					<p class='hint'>客户退款页面显示</p>
				</dd>
			</dl>
			<dl>
				<dt>客服二维码：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='kf_ercode' src='<?=!empty($output['config']['kf_ercode']) ? $output['config']['kf_ercode'] : STATIC_URL . '/images/kefu.png'?>'> </div>
							<input name='kf_ercode' nctype='kf_ercode' value='<?=!empty($output['config']['kf_ercode']) ? $output['config']['kf_ercode'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸128x128像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='kf_ercode' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'kf_ercode'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>客服是否开启：</dt>
				<dd>
					<label><input type='radio' name='kf_open' value='1'<?php echo $output['config']['kf_open'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='kf_open' value='0'<?php echo $output['config']['kf_open'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>客服图标：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='kf_ico' src='<?=!empty($output['config']['kf_ico']) ? $output['config']['kf_ico'] : STATIC_URL . '/images/kefu.png'?>'> </div>
							<input name='image_path' nctype='kf_ico' value='<?=!empty($output['config']['kf_ico']) ? $output['config']['kf_ico'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸128x128像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='kf_ico' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'kf_ico'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
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
upload_file('kf_ercode', '<?=users_url('album/image_upload')?>');
upload_file('kf_ico', '<?=users_url('album/image_upload')?>');
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>