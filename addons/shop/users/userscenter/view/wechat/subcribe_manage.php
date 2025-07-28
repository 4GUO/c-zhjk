<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('wechat/subcribe_manage')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='rid' value='<?=$output['attention_account']['reply_id']?>' type='hidden' />
		<input type='hidden' name='materialid' id='materialid' value='<?php echo $output['attention_account']['reply_materialid'];?>' />		
		<div class='css-form-goods'>
			<dl>
				<dt>回复类型：</dt>
				<dd>
					<label><input type='radio' name='msgtype' value='0'<?php if($output['attention_account']['reply_msgtype'] == 0){?> checked='checked'<?php }?> />文字消息</label>&nbsp;
					<label><input type='radio' name='msgtype' value='1'<?php if($output['attention_account']['reply_msgtype'] == 1){?> checked='checked'<?php }?> />图文消息</label>
				</dd>
			</dl>
			<dl class='msgtype_0'<?php if($output['attention_account']['reply_msgtype'] == 1){?> style='display: none;'<?php }?>>
				<dt>回复内容：</dt>
				<dd>
					<textarea name='textcontents' class='text w400'><?=$output['attention_account']['reply_textcontents']?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl class='msgtype_1'<?php if($output['attention_account']['reply_msgtype'] == 0){?> style='display: none;'<?php }?>>
				<dt>回复图文：</dt>
				<dd>
					<a href='javascript:;' nc_type='dialog' dialog_title='选择素材' dialog_id='sucai' dialog_width='675' dialog_height='550' uri='<?=users_url('wechat/material_list')?>'>【选择图文】</a>
					<span></span>
					<div id='material_confirm' class='material_dialog'<?php echo $output['attention_account']['reply_msgtype'] == 0 ? ' style=\'display:none\'' : '';?>>
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
				<dt>任意关键词：</dt>
				<dd>
					<label><input type='radio' name='subscribe' value='1'<?php if($output['attention_account']['reply_subscribe'] == 1){?> checked='checked'<?php }?> />开启</label>&nbsp;
					<label><input type='radio' name='subscribe' value='0'<?php if($output['attention_account']['reply_subscribe'] == 0){?> checked='checked'<?php }?> />关闭</label>
					<p class='hint'>开启后，当输入的关键词无相关匹配内容时，则使用本设置回复</p>
				</dd>
			</dl>
			<dl>
				<dt>成为会员提醒：</dt>
				<dd>
					<label><input type='radio' name='membernotice' value='1'<?php if($output['attention_account']['reply_membernotice'] == 1){?> checked='checked'<?php }?> />开启</label>&nbsp;
					<label><input type='radio' name='membernotice' value='0'<?php if($output['attention_account']['reply_membernotice'] == 0){?> checked='checked'<?php }?> />关闭</label>
					<p class='hint'>开启后，用户关注公众号收到的消息中会包含会员信息，例如：您好**，您已成为第***位会员。此设置仅对“文字消息”有效</p>
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