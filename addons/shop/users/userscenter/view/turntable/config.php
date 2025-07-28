<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link href='<?=STATIC_URL?>/admin/js/colorpicker/spectrum.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/colorpicker/spectrum.js'></script>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('turntable/config')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>活动名称：</dt>
				<dd>
					<input name='turntable_name' class='text w400' value='<?=$output['config']['turntable_name']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>抽奖次数：</dt>
				<dd>
					<input name='turntable_cishu' class='text w60' value='<?=$output['config']['turntable_cishu']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>每周活动时间段：</dt>
				<dd>
					<div>
						周 <input name='turntable_day_start' id='turntable_day_start' class='text w60' value='<?=$output['config']['turntable_day_start']?>' type='text'> ~ <input name='turntable_day_end' id='turntable_day_end' class='text w60' value='<?=$output['config']['turntable_day_end']?>' type='text'> 几			
						<p class='hint'>注：具体到周几，0表示不限制，周一到周日分别是数字1~7</p>
					</div>
					<div style='margin-top: 10px;'>
						具体到 <input name='turntable_time_start' id='turntable_time_start' class='text w60' value='<?=$output['config']['turntable_time_start']?>' type='text'> ~ <input name='turntable_time_end' id='turntable_time_end' class='text w60' value='<?=$output['config']['turntable_time_end']?>' type='text'>			
						<p class='hint'>注：10:00 ~ 15:00</p>
					</div>
				</dd>
				
			</dl>
			<dl>
				<dt>活动时间提示：</dt>
				<dd>
					<textarea name='turntable_tip' class='textarea h60 w400'><?=$output['config']['turntable_tip']?></textarea>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>活动说明：</dt>
				<dd>
					<textarea name='turntable_rule_tip' class='textarea h100 w400'><?=$output['config']['turntable_rule_tip']?></textarea>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>周业绩比例：</dt>
				<dd>
					<input name='week_sales_bili' class='text w50' value='<?=$output['config']['week_sales_bili']?>' type='text' />&nbsp;%
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>规则颜色：</dt>
				<dd>
					<input class='text w80' type='text' name='rulercolor' id='rulercolor' value='<?=$output['config']['rulercolor']?>' />
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>文字颜色：</dt>
				<dd>
					<input class='text w80' type='text' name='fontcolor' id='fontcolor' value='<?=$output['config']['fontcolor']?>' />
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>外圈颜色：</dt>
				<dd>
					<input class='text w80' type='text' name='outercolor' id='outercolor' value='<?=$output['config']['outercolor']?>' />
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>内圈颜色：</dt>
				<dd>
					<input class='text w80' type='text' name='innercolor' id='innercolor' value='<?=$output['config']['innercolor']?>' />
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>装饰点颜色1：</dt>
				<dd>
					<input class='text w80' type='text' name='pointcolor1' id='pointcolor1' value='<?=$output['config']['pointcolor1']?>' />
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>装饰点颜色2：</dt>
				<dd>
					<input class='text w80' type='text' name='pointcolor2' id='pointcolor2' value='<?=$output['config']['pointcolor2']?>' />
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
$('#rulercolor').spectrum({
	color: '<?=$output['config']['rulercolor']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
$('#fontcolor').spectrum({
	color: '<?=$output['config']['fontcolor']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
$('#outercolor').spectrum({
	color: '<?=$output['config']['outercolor']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
$('#innercolor').spectrum({
	color: '<?=$output['config']['innercolor']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
$('#pointcolor1').spectrum({
	color: '<?=$output['config']['pointcolor1']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
$('#pointcolor2').spectrum({
	color: '<?=$output['config']['pointcolor2']?>',
	showInput: true,//显示输入
	cancelText: '取消',//取消按钮,按钮文字
	chooseText: '确定',//选择按钮,按钮文字
	preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
});
</script>