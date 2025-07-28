<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form id='form' method='post' target='_parent' action='<?=_url('promotion_xianshi/xianshi_add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>活动名称：</dt>
				<dd>
					<input name='xianshi_name' value='' class='text w200' type='text' />
					<p class='hint'>活动名称将显示在限时折扣活动列表中，方便商家管理使用，最多可输入25个字符。</p>
				</dd>
			</dl>
			<dl>
				<dt>活动标题：</dt>
				<dd>
					<input name='xianshi_title' value='' class='text w200' type='text'>
					<p class='hint'> 活动标题是商家对限时折扣活动的别名操作，请使用例如“新品打折”、“月末折扣”类短语表现，最多可输入10个字符；非必填选项，留空商品优惠价格前将默认显示“限时折扣”字样。</p>
				</dd>
			</dl>
			<dl>
				<dt>活动描述：</dt>
				<dd>
					<input name='xianshi_explain' value='' class='text w200' type='text'>
					<p class='hint'>活动描述是商家对限时折扣活动的补充说明文字，在商品详情页-优惠信息位置显示；非必填选项，最多可输入30个字符。</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>开始时间：</dt>
				<dd>
					<input class='text w100' name='start_time' id='start_time' value='' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>结束时间：</dt>
				<dd>
					<input class='text w100' name='end_time' id='end_time' value='' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>购买下限：</dt>
				<dd>
					<input class='text w100' name='lower_limit' id='lower_limit' value='1' type='text' />
					<p class='hint'>参加活动的最低购买数量，默认为1</p>
				</dd>
			</dl>
		</div>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#start_time').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d H:i',
	formatDate: 'Y-m-d H:i',
	step: 5
});
$('#end_time').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d H:i',
	formatDate: 'Y-m-d H:i',
	step: 5
});
</script>
<script type='text/javascript'>
$(function(){
    $('.submit').click(function(e){
		ajax_form_post('form');
	});
});
</script>