<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.items_info img{
	width:60px;
	border-radius: 5px;
}
.items_info strong{
	padding-left: 8px
}
</style>
<style type='text/css'>
.chosed_info{
	width: 300px;
	position: relative;
	cursor: pointer;
	height: 80px;
}
.chosed_info img{
	display: inline-block;
	height: 80px;
	width: 80px;
	margin: 0px auto;
	border-radius: 5px;
}
.chosed_info span{
	color: #999
}
</style>
<div class='item-publish'>
	<form method='post' id='member_form' action='<?=_url('area_account/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?=isset($output['account_info']['id']) ? $output['account_info']['id'] : 0;?>'/>
		<div class='css-form-goods'>
			<dl>
				<dt>绑定会员：</dt>
				<dd>
					<div class='chosed_info' nctype='uid0' <?php if(empty($output['member_info']['uid'])) { ?>style='display: none'<?php } ?>>
						<img nctype='uid0' src='<?php echo !empty($output['member_info']['headimg']) ? $output['member_info']['headimg'] : STATIC_URL . '/shop/img/default_user.png'?>' style='vertical-align: middle;' />
						<span>昵称：</span><span nctype='uid0'><?php echo isset($output['member_info']['nickname']) ? $output['member_info']['nickname'] : ''?></span>
					</div>
					<input type='hidden' nctype='uid0' name='uid' id='uid' value='<?php echo empty($output['account_info']['uid']) ? 0 : $output['account_info']['uid']?>' />
					<p><a class='css-btn mt5' nc_type='dialog' id='chose_agent' dialog_title='选择上级' dialog_id='chose_agent' dialog_width='850' uri='<?=_url('member/selectView', array('input_name' => 'uid'))?>'><i class='fa fa-user'></i>选择会员</a> </p>
				</dd>
			</dl>
			<dl>
				<dt>所在地区：</dt>
				<dd>
					<div>
						<input type='hidden' name='region' id='region' value='<?=isset($output['account_info']['area_info']) ? $output['account_info']['area_info'] : '';?>'/>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>级别：</dt>
				<dd>
					<select name='level_id'>
						<?php foreach($output['level_list'] as $level_id => $level_name){?>
						<option value='<?php echo $level_id;?>' <?php if(isset($output['account_info']['level_id']) && $output['account_info']['level_id'] == $level_id){?> selected='selected'<?php }?>><?php echo $level_name;?></option>
						<?php }?>
					</select>				
				</dd>
			</dl>
			<dl>
				<dt>状态：</dt>
				<dd>
					<label><input type='radio' name='status' value='0' <?php if(empty($output['account_info']['status'])){?> checked<?php }?> />&nbsp;&nbsp;待审</label><br />
					<label><input type='radio' name='status' value='1' <?php if(!empty($output['account_info']['status']) && $output['account_info']['status'] == 1){?> checked<?php }?> />&nbsp;&nbsp;正常</label><br />
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
	$('#region').fxy_region();
	$('.submit').click(function(e){
		ajax_form_post('member_form');
	});
</script>