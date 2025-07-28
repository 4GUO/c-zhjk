<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('wechat/keyword_edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='rid' value='<?=$output['reply_info']['reply_id']?>' type='hidden' />
		<input name='materialid' id='materialid' value='<?=$output['reply_info']['reply_materialid']?>' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>关键词：</dt>
				<dd>
					<input name='keywords' class='text w400' value='<?=trim($output['reply_info']['reply_keywords'], '|')?>' type='text' />
					<span></span>
					<p class='hint'>多个关键词用“|”隔开</p>
				</dd>
			</dl>
			<dl>
				<dt>回复类型：</dt>
				<dd>
					<label><input type='radio' name='msgtype' value='0'<?php if($output['reply_info']['reply_msgtype'] == 0) {?> checked='checked'<?php } ?>/>文字消息</label>&nbsp;
					<label><input type='radio' name='msgtype' value='1'<?php if($output['reply_info']['reply_msgtype'] == 1) {?> checked='checked'<?php } ?>/>图文消息</label>
				</dd>
			</dl>
			<dl class='msgtype_0' <?php if($output['reply_info']['reply_msgtype'] == 1) {?> style='display: none;'<?php } ?>>
				<dt>回复内容：</dt>
				<dd>
					<textarea name='textcontents' class='text w400'><?=$output['reply_info']['reply_textcontents']?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl class='msgtype_1' <?php if($output['reply_info']['reply_msgtype'] == 0) {?> style='display: none;'<?php } ?>>
				<dt>回复图文：</dt>
				<dd>
					<a href='javascript:;' nc_type='dialog' dialog_title='选择素材' dialog_id='sucai' dialog_width='675' dialog_height='550' uri='<?=users_url('wechat/material_list')?>'>【选择图文】</a>
					<span></span>
					<div id='material_confirm' class='material_dialog'<?php echo $output['reply_info']['reply_msgtype'] == 0 ? ' style=\'display:none\'' : '';?>>
						<div class='list'>
							<?php if (!empty($output['material_info'])) { ?>
							<?php if ($output['material_info']['material_type'] == 2) {?>
							<div class='item multi'>
								<div class='time'><?php echo date('Y-m-d', $output['material_info']['material_addtime']);?></div>
								<?php foreach($output['material_info']['items'] as $k => $v) {?>
								<div class='<?php echo $k>0 ? 'list' : 'first' ?>'>
									<div class='info'>
										<div class='img'><img src='<?=$v['ImgPath'] ?>' /></div>
										<div class='title'><?php echo $v['Title'] ?></div>
									</div>
								</div>
								<?php } ?>
							</div>
							<?php } else { ?>
							<div class='item one'>
								<?php foreach($output['material_info']['items'] as $k => $v) { ?>
								<div class='title'><?php echo $v['Title'] ?></div>
								<div><?php echo date('Y-m-d', $output['material_info']['material_addtime']) ?></div>
								<div class='img'><img src='<?=$v['ImgPath'] ?>' /></div>
								<div class='txt'><?php echo str_replace(PHP_EOL, '<br />', $v['TextContents']);?></div>
								<?php }?>
							</div>
							<?php } ?>
							<?php } else {?>
							<div class='item'></div>
							<?php } ?>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>匹配模式：</dt>
				<dd>
					<label><input type='radio' name='patternmethod' value='0'<?php if($output['reply_info']['reply_patternmethod'] == 0) {?> checked='checked'<?php } ?> />精确模式</label>&nbsp;
					<label><input type='radio' name='patternmethod' value='1'<?php if($output['reply_info']['reply_patternmethod'] == 1) {?> checked='checked'<?php } ?> />模糊匹配</label>
					<p class='hint'>1.精确模式：用户输入的文字和此关键词一样才会触发<br/>2.模糊匹配：只要用户输入的文字包含此关键词就触发</p>
				</dd>
			</dl>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button' />
			</label>
		</div>
	</form>
</div>
<script>
$('.submit').click(function(e){
	ajax_form_post('form');
});
$(function(){
	$('input[name=msgtype]').click(function(){
		if($(this).val()==0){
			$('.msgtype_1').hide();
			$('.msgtype_0').show();
		}else{
			$('.msgtype_0').hide();
			$('.msgtype_1').show();
		}
	});
	 
	$('#submitBtn').click(function(){
        $('#add_form').submit();
    });
});
</script>