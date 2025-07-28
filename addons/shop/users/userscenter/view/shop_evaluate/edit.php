<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.chosed_info{
	width: 300px;
	position: relative;
	cursor: pointer
}
.chosed_info img{
	display: block;
	height: 80px;
	width: 80px;
	margin: 0px auto;
	border-radius: 5px;
	position: absolute;
	top: 0px;
	left: 8px;
}
.chosed_info p{
	width: 180px;
	height: 20px;
	line-height: 20px;
	overflow: hidden;
	font-size: 12px;
	margin:0px 0px 0px 96px;
	overflow: hidden
	color: #333;
}
.chosed_info p span{
	color: #999
}
</style>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='evaluate_form' method='post' action='<?=users_url('shop_evaluate/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='geval_id' value='<?php echo isset($output['geval_info']['geval_id']) ? $output['geval_info']['geval_id'] : 0;?>' />
		<dl>
			<dt>评分：</dt>
			<dd>
				<span class='orange'><?=$output['geval_info']['geval_scores']?></span> 分
			</dd>
		</dl>
		<dl>
			<dt>评论内容：</dt>
			<dd>
				<?=$output['geval_info']['geval_content']?>
			</dd>
		</dl>
		<dl>
			<?php if(!empty($output['geval_info']['geval_image'])){ ?>
			<dt>晒图：</dt>
			<dd>
				<?php foreach($output['geval_info']['geval_image'] as $image){?>
				<a href='<?php echo $image['src'];?>' target='_blank'><img src='<?php echo $image['src'];?>' width='100' style='margin-right: 5px' /></a>
				<?php }?>
			</dd>
			<?php }?>
		</dl>
		<dl>
			<dt>评论内容：</dt>
			<dd>
				<ul class='css-form-radio-list'>
						<li>
							<label><input name='status' value='1' <?php if($output['geval_info']['geval_state'] == 1){?> checked='checked'<?php }?> type='radio' />显示</label>
						</li>
						<li>
							<label><input name='status' value='0' <?php if($output['geval_info']['geval_state'] == 0){?> checked='checked'<?php }?> type='radio' />不显示</label>
						</li>
					</ul>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript'>
$(function(){
	$('.submit').click(function(e){
		ajax_form_post('evaluate_form');
	});
});
</script>