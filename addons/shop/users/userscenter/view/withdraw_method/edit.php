<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='method_form' method='post' action='<?=users_url('withdraw_method/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='method_id' value='<?php echo $output['method_info']['method_id'];?>' />
		<dl>
			<dt><i class='required'>*</i>方式名称：</dt>
			<dd>
				<input class='text w150' type='text' name='name' id='name' value='<?php echo $output['method_info']['method_name'];?>'<?php if($output['method_info']['method_code']!='bank_' . $output['method_info']['method_id']){?> readonly='readonly' style='background: #eee'<?php }?> />
			</dd>
		</dl>
		<?php if($output['method_info']['method_code']=='wxhongbao' || $output['method_info']['method_code']=='wxzhuanzhang'){?>
		<dl>
			<dt>是否审核：</dt>
			<dd>
				<ul class='css-form-radio-list'>
					<li>
						<label><input name='check' value='1'<?php if($output['method_info']['method_check'] == 1){?> checked='checked'<?php }?> type='radio' />是</label>
					</li>
					<li>
						<label><input name='check' value='0'<?php if($output['method_info']['method_check'] == 0){?> checked='checked'<?php }?> type='radio' />否</label>
					</li>
				</ul>
			</dd>
		</dl>
		<?php }?>
		<dl>
			<dt>每次提现最小额度：</dt>
			<dd>
				<input class='text w150' type='text' name='min' id='min' value='<?php echo $output['method_info']['method_min'];?>' />
			</dd>
		</dl>
		<dl>
			<dt>每次提现最大额度：</dt>
			<dd>
				<input class='text w150' type='text' name='max' id='max' value='<?php echo $output['method_info']['method_max'];?>' />
			</dd>
		</dl>
		<dl>
			<dt>手续费：</dt>
			<dd>
				<input class='text w150' type='text' name='fee' id='fee' value='<?php echo $output['method_info']['method_fee'];?>' /> %
				<span></span>
				<p class='hint'>占提现总额的百分比</p>
			</dd>
		</dl>
		<dl>
			<dt>转入余额比例：</dt>
			<dd>
				<input class='text w150' type='text' name='yue' id='yue' value='<?php echo $output['method_info']['method_yue'];?>' /> %
				<span></span>
				<p class='hint'>占提现总额的百分比</p>
			</dd>
		</dl>
		<dl>
			<dt>状态：</dt>
			<dd>
				<ul class='css-form-radio-list'>
					<li>
						<label><input name='status' value='1'<?php if($output['method_info']['method_status'] == 1){?> checked='checked'<?php }?> type='radio' />启用</label>
					</li>
					<li>
						<label><input name='status' value='0'<?php if($output['method_info']['method_status'] == 0){?> checked='checked'<?php }?> type='radio' />禁用</label>
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
    $('#method_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            name : {
                required : true
            }
        },
        messages : {
            name : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写名称'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#method_form').valid()){
			ajax_form_post('method_form');
		};
	});
});
</script>