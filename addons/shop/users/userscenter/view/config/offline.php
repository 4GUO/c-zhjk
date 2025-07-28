<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/offline')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>支付方式：</dt>
				<dd id='offline_type'>
					<label><input type='radio' name='offline_type' value='1'<?=isset($output['config']['offline_type']) && $output['config']['offline_type'] == 1 ? ' checked=\'checked\'' : '';?> />银行卡 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='offline_type' value='2'<?=isset($output['config']['offline_type']) && $output['config']['offline_type'] == 2 ? ' checked=\'checked\'' : '';?> />支付宝 </label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='offline_type' value='3'<?=isset($output['config']['offline_type']) && $output['config']['offline_type'] == 3 ? ' checked=\'checked\'' : '';?> />微信 </label>&nbsp;&nbsp;&nbsp;&nbsp;
				</dd>
			</dl>
			<dl class='type1' style='<?php if ($output['config']['offline_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>开户行：</dt>
				<dd>
					<input name='bank_name' class='text w200' value='<?=$output['config']['bank_name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type1' style='<?php if ($output['config']['offline_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>开户支行：</dt>
				<dd>
					<input name='bank_address' class='text w400' value='<?=$output['config']['bank_address']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type1' style='<?php if ($output['config']['offline_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>开户姓名：</dt>
				<dd>
					<input name='bank_username' class='text w100' value='<?=$output['config']['bank_username']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type1' style='<?php if ($output['config']['offline_type'] != 1) { ?>display: none;<?php } ?>'>
				<dt>账号：</dt>
				<dd>
					<input name='bank_no' class='text w200' value='<?=$output['config']['bank_no']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type2' style='<?php if ($output['config']['offline_type'] != 2) { ?>display: none;<?php } ?>'>
				<dt>收款二维码：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='zhifubao_ercode' src='<?=!empty($output['config']['zhifubao_ercode']) ? $output['config']['zhifubao_ercode'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='zhifubao_ercode' nctype='zhifubao_ercode' value='<?=!empty($output['config']['zhifubao_ercode']) ? $output['config']['zhifubao_ercode'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x640像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='zhifubao_ercode' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'zhifubao_ercode'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl class='type2' style='<?php if ($output['config']['offline_type'] != 2) { ?>display: none;<?php } ?>'>
				<dt>账号：</dt>
				<dd>
					<input name='zhifubao_account' class='text w100' value='<?=$output['config']['zhifubao_account']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type2' style='<?php if ($output['config']['offline_type'] != 2) { ?>display: none;<?php } ?>'>
				<dt>姓名：</dt>
				<dd>
					<input name='zhifubao_name' class='text w200' value='<?=$output['config']['zhifubao_name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl class='type3' style='<?php if ($output['config']['offline_type'] != 3) { ?>display: none;<?php } ?>'>
				<dt>收款二维码：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='weixin_ercode' src='<?=!empty($output['config']['weixin_ercode']) ? $output['config']['weixin_ercode'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='weixin_ercode' nctype='weixin_ercode' value='<?=!empty($output['config']['weixin_ercode']) ? $output['config']['weixin_ercode'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>建议使用<font color='red'>尺寸640x640像素、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='weixin_ercode' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'weixin_ercode'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
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
upload_file('zhifubao_ercode', '<?=users_url('album/image_upload')?>');
upload_file('weixin_ercode', '<?=users_url('album/image_upload')?>');
$('.submit').click(function(e){
	ajax_form_post('form');
});
$(function() {
	$('#offline_type input[type=radio]').click(function() {
		var s_val = $(this).val();
		if (s_val == 1) {
			$('.type1').show();
			$('.type2').hide();
			$('.type3').hide();
		} else if (s_val == 2) {
			$('.type1').hide();
			$('.type2').show();
			$('.type3').hide();
		} else {
			$('.type1').hide();
			$('.type2').hide();
			$('.type3').show();
		}
	})
})
</script>