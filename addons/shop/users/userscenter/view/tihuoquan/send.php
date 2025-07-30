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
	
	/* 响应式弹窗样式 */
	@media screen and (max-width: 768px) {
		/* 手机端弹窗自适应 */
		.dialog_wrapper {
			width: 95% !important;
			max-width: 95% !important;
			left: 2.5% !important;
			right: 2.5% !important;
			height: auto !important;
			max-height: 90vh !important;
			top: 5vh !important;
			bottom: 5vh !important;
		}
		
		/* 手机端弹窗内容区域自适应 */
		.dialog_body {
			height: auto !important;
			max-height: calc(90vh - 10px) !important;
			overflow-y: auto !important;
		}
		
		/* 手机端表单元素自适应 */
		.css-form-default {
			padding: 15px;
		}
		
		.css-form-default dl {
			margin-bottom: 15px;
		}
		
		.css-form-default dt {
			display: block;
			width: 100%;
			margin-bottom: 5px;
			font-size: 14px;
		}
		
		.css-form-default dd {
			display: block;
			width: 100%;
		}
		
		.css-form-default input[type="text"] {
			width: 100% !important;
			box-sizing: border-box;
			height: 40px;
			font-size: 16px; /* 防止iOS缩放 */
		}
		
		/* 手机端按钮自适应 */
		.css-btn {
			display: block;
			width: 100%;
			text-align: center;
			margin: 5px 0 !important;
			height: 40px;
			line-height: 40px;
			font-size: 16px;
		}
		
		/* 手机端会员列表自适应 */
		.member_list li {
			max-width: 100%;
			width: 100%;
			float: none;
			padding: 8px 10px;
			margin-bottom: 5px;
			background: #f5f5f5;
			border-radius: 4px;
		}
		
		/* 手机端底部按钮自适应 */
		.bottom {
			text-align: center;
			margin-top: 20px;
		}
		
		.submit-border {
			display: block;
			border: none;
			width: 100%;
		}
		
		.submit {
			display: block;
			width: 100%;
			text-align: center;
			height: 44px;
			line-height: 44px;
			font-size: 16px;
			background: #e33e33;
			color: #fff;
			border-radius: 4px;
		}
		
		/* 手机端提示文字 */
		.hint {
			font-size: 12px;
			color: #999;
			margin-top: 10px;
			line-height: 1.4;
		}
	}
	
	/* 平板端优化 */
	@media screen and (min-width: 769px) and (max-width: 1024px) {
		.dialog_wrapper {
			width: 90% !important;
			max-width: 90% !important;
			height: auto !important;
			max-height: 85vh !important;
			top: 7.5vh !important;
		}
		
		.dialog_body {
			height: auto !important;
			max-height: calc(85vh - 60px) !important;
			overflow-y: auto !important;
		}
		
		.css-form-default {
			padding: 20px;
		}
	}
	
	/* PC端优化 */
	@media screen and (min-width: 1025px) {
		.dialog_wrapper {
			max-height: 80vh !important;
			top: 10vh !important;
		}
		
		.dialog_body {
			max-height: calc(80vh) !important;
			overflow-y: auto !important;
		}
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
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择会员' dialog_id='voucher' dialog_width='830' dialog_height='600' uri='<?=users_url('member/selectView', array('input_name' => 'selectall'))?>' style='margin:0 10px;'>选择会员</a>
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
	
	// 响应式弹窗尺寸设置
	function setDialogSize() {
		var screenWidth = $(window).width();
		var screenHeight = $(window).height();
		var dialogWidth = '830'; // 默认PC端宽度
		var dialogHeight = '600'; // 默认PC端高度
		
		if (screenWidth <= 768) {
			// 手机端：使用95%屏幕宽度，90%屏幕高度
			dialogWidth = Math.floor(screenWidth * 0.95);
			dialogHeight = Math.floor(screenHeight * 0.9);
		} else if (screenWidth <= 1024) {
			// 平板端：使用90%屏幕宽度，85%屏幕高度
			dialogWidth = Math.floor(screenWidth * 0.9);
			dialogHeight = Math.floor(screenHeight * 0.85);
		}
		
		// 更新选择会员按钮的dialog_width和dialog_height属性
		$('a[nc_type="dialog"][dialog_id="voucher"]').attr({
			'dialog_width': dialogWidth,
			'dialog_height': dialogHeight
		});
	}
	
	// 页面加载时设置
	setDialogSize();
	
	// 窗口大小改变时重新设置
	$(window).resize(function() {
		setDialogSize();
	});
});
function del_link(that) {
	$(that).remove();
}
</script>