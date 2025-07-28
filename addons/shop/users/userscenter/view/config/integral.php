<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/integral')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>会员注册：</dt>
				<dd>
					送&nbsp;<input name='points_reg' class='text w60' value='<?=$output['config']['points_reg']?>' type='text'>&nbsp;积分&nbsp;
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>商品评论：</dt>
				<dd>
					送&nbsp;<input name='points_comments' class='text w60' value='<?=$output['config']['points_comments']?>' type='text'>&nbsp;积分&nbsp;
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>邀请注册：</dt>
				<dd>
					送&nbsp;<input name='points_invite' class='text w60' value='<?=$output['config']['points_invite']?>' type='text'>&nbsp;积分&nbsp;
					<p class='hint'>邀请非会员注册时给邀请人的积分数</p>
				</dd>
			</dl>
			<dl>
				<dt>消费额与赠送积分比例：</dt>
				<dd>
					<input name='points_orderrate' class='text w60' value='<?=$output['config']['points_orderrate']?>' type='text'>&nbsp;积分&nbsp;
					<p class='hint'>例：设置为10，表明消费1单位货币赠送10积分，设置0表示下单不送积分</p>
				</dd>
			</dl>
			<dl>
				<dt>每订单最多赠送积分：</dt>
				<dd>
					<input name='points_ordermax' class='text w60' value='<?=$output['config']['points_ordermax']?>' type='text'>&nbsp;积分&nbsp;
					<p class='hint'>例：设置为100，表明每订单赠送积分最多为100积分，0表示不限制</p>
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