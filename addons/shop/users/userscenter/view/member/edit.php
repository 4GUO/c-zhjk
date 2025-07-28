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
<div class='item-publish'>
	<form method='post' id='member_form' action='<?=users_url('member/edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='uid' value='<?php echo $output['member_info']['uid'];?>' />
		<div class='css-form-goods'>
			<dl>
				<dt>会员信息：</dt>
				<dd>
					<div class='items_info'>
						<?php echo !empty($output['member_info']['headimg']) ? '<img src=\''.$output['member_info']['headimg'].'\' width=\'60\' />' : ''?>
						<strong>ID：<?=config('uid_pre') . padNumber($output['member_info']['uid'])?></strong>
						<strong><?php echo !empty($output['member_info']['nickname']) ? $output['member_info']['nickname'] : ''?></strong>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>姓名：</dt>
				<dd>
					<input name='truename' id='truename' class='text w400' value='<?=$output['member_info']['truename'] ?: $output['member_info']['nickname']?>' type='text' />					
				</dd>
			</dl>
			<dl>
				<dt>手机号：</dt>
				<dd>
					<input name='mobile' id='mobile' class='text w400' value='<?=$output['member_info']['mobile']?>' type='text' />					
				</dd>
			</dl>
			<dl>
				<dt>登录密码：</dt>
				<dd>
					<input name='password' id='password' class='text w400' value='' type='password' />					
				</dd>
			</dl>
			<dl>
				<dt>级别：</dt>
				<dd>
					<select name='level_id'>
						<?php foreach($output['level_list'] as $level_id=>$level_name){?>
						<option value='<?php echo $level_id;?>' <?php if($output['member_info']['level_id'] == $level_id){?> selected='selected'<?php }?>><?php echo $level_name;?></option>
						<?php }?>
					</select>				
				</dd>
			</dl>
			<dl>
				<dt>推荐人信息：</dt>
				<dd>
					<div class='chosed_info' nctype='inviter_id0' <?php if(empty($output['member_info']['inviter_id'])){?>style='display: none'<?php }?>>
						<?php if(!empty($output['inviter_info'])){?>
						<img src='<?php echo $output['inviter_info']['headimg']?>' nctype='inviter_id0' />
						<p><span>ID：</span><span nctype='inviter_id0'><?php echo config('uid_pre') . padNumber($output['inviter_info']['uid'])?></span></p>
						<p><span>昵称：</span><span nctype='inviter_id0'><?php echo $output['inviter_info']['nickname']?></span></p>
						<p><span>手机：</span><span nctype='inviter_id0'><?php echo isset($output['inviter_info']['mobile']) ? $output['inviter_info']['mobile'] : ''?></span></p>
						<?php }?>
					</div>
					<input type='hidden' nctype='inviter_id0' name='inviter_id' id='inviter_id' value='<?php echo empty($output['inviter_info']['uid']) ? 0 : $output['inviter_info']['uid']?>' />
					<p><a class='css-btn mt5' nc_type='dialog' id='chose_agent' dialog_title='选择上级' dialog_id='chose_agent' dialog_width='850' uri='<?=users_url('member/selectView', array('input_name' => 'inviter_id', 'uid' => $output['member_info']['uid']))?>'><i class='fa fa-user'></i>选择上级</a> </p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>管理员：</dt>
				<dd>
					<label><input type='radio' name='is_admin' value='0' <?php if(empty($output['member_info']['is_admin'])){?> checked<?php }?> />&nbsp;&nbsp;否</label><br />
					<label><input type='radio' name='is_admin' value='1' <?php if(!empty($output['member_info']['is_admin'])){?> checked<?php }?> />&nbsp;&nbsp;是</label><br />
					<p class='hint'>管理员可以接收平台消息提醒</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>微课发布权限：</dt>
				<dd>
					<label><input type='radio' name='is_author' value='0' <?php if(empty($output['member_info']['is_author'])){?> checked<?php }?> />&nbsp;&nbsp;无</label><br />
					<label><input type='radio' name='is_author' value='1' <?php if(!empty($output['member_info']['is_author'])){?> checked<?php }?> />&nbsp;&nbsp;有</label><br />
				</dd>
			</dl>
			<dl>
				<dt>获取报单费：</dt>
				<dd>
					<label><input type='radio' name='can_baodan' value='0' <?php if(empty($output['member_info']['can_baodan'])){?> checked<?php }?> />&nbsp;&nbsp;不可以</label><br />
					<label><input type='radio' name='can_baodan' value='1' <?php if(!empty($output['member_info']['can_baodan'])){?> checked<?php }?> />&nbsp;&nbsp;可以</label><br />
					<p class='hint'>授权后，别人下单体验套餐填写他的编号时候才能获取报单费</p>
				</dd>
			</dl>
			<dl>
				<dt>状态：</dt>
				<dd>
					<label><input type='radio' name='status' value='2' <?php if(!empty($output['member_info']['status']) && $output['member_info']['status'] == 2){?> checked<?php }?> />&nbsp;&nbsp;禁用</label><br />
					<label><input type='radio' name='status' value='1' <?php if(!empty($output['member_info']['status']) && $output['member_info']['status'] == 1){?> checked<?php }?> />&nbsp;&nbsp;正常</label><br />
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
		ajax_form_post('member_form');
	});
</script>