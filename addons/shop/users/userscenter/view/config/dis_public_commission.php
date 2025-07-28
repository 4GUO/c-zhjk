<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.dd {
		margin-top: 5px;
	}
	.selectUser {
		padding: 5px 10px;
		background: #F5F5F5;
		color: #777777;
	}
</style>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/dis_public_commission')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>见点奖：</dt>
				<dd>
					<div style='width:300px;'>
						<?php for ($i = 0; $i < $output['config']['public_bonus_level']; $i++) { ?>
						<div style='margin-bottom: 10px;'>
							<span style='display: inline-block; width:60px; border-right:1px #dfdfdf solid; text-align:right; padding-right:20px;'><?php echo $i+1;?>级</span>
							<span><input type='text' name='public_commission[<?php echo $i + 1;?>]' value='<?php echo empty($output['public_commission'][$i + 1]) ? '' : $output['public_commission'][$i + 1];?>' class='text' style='width:60px; text-align:center; margin-left:10px' /> 元</span>
						</div>
						<?php } ?>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>直推奖：</dt>
				<dd>
					<input name='public_inviter' class='text w60' value='<?=$output['config']['public_inviter']?>' type='text'>&nbsp;元&nbsp;
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>懒人奖：</dt>
				<dd>
					<input name='public_parent' class='text w60' value='<?=$output['config']['public_parent']?>' type='text'>&nbsp;元&nbsp;
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>感恩奖：</dt>
				<dd>
					<input name='public_thankful' class='text w60' value='<?=$output['config']['public_thankful']?>' type='text'>&nbsp;元&nbsp;
					<p class='hint'></p>
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
<script>
$('.submit').click(function(e){
	ajax_form_post('form');
});
$(function(){
	
})
</script>