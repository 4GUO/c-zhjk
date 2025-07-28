<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.member_list {
		clear: both;
		overflow: hidden;
	}
	.member_list li {
		float: left;
		max-width: 25%;
		box-sizing: border-box;
		padding: 5px 10px 5px 0;
		margin-bottom: 10px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
		word-break: break-all;
		cursor: pointer;
	}
	.member_list li:hover {
		color: #e33e33;
	}
	#warning {
		display: none;
	}
</style>
<div class='css-form-default'>
	<form id='form' method='post' target='_parent' action='<?=_url('tihuoquan/send')?>'>
		<input type='hidden' id='form_submit' name='form_submit' value='ok'/>
		<dl>
			<dt><i class='required'>*</i>数量：</dt>
			<dd>
				<input name='num' value='1' class='text w60' type='text' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>选择会员：</dt>
			<dd>
				<span style='float:left;'>
					<div class='handle'>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择会员' dialog_id='voucher' dialog_width='830' uri='<?=users_url('member/selectView', array('input_name' => 'selectall'))?>' style='margin:0 10px;'>选择会员</a>
					</div>
				</span>
				<div nctype='selectall0' class='member_list'>

				</div>
				<p class='hint'>可多选，点击会员名称可删除，只能给最高级别发券，其他级别用户选择也无效</p>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'> <a id='btn_add' class='submit' href='javascript:void(0);'>发送</a> </label>
		</div>
	</form>
</div>
<script>
$(function() {
	var btn_check = true;
	$('.submit').click(function(e) {
		if (btn_check) {
			ajax_form_post('form');
		}
		btn_check = false;
	});
});
function del_link(that) {
	$(that).remove();
}
</script>