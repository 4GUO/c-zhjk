<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('shop_vr_order/order_cancel')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='order_id' value='<?=$output['order_info']['order_id']?>' />
		<dl>
			<dt>订单编号：</dt>
			<dd>
				<span class='num'><?=$output['order_info']['order_sn']?></span>
			</dd>
		</dl>
		<dl>
			<dt>取消原因：</dt>
			<dd>
				<ul class='checked'>
					<li>
						<input type='radio' checked name='state_info' id='d1' value='无法备齐货物' />
						<label for='d1'>无法备齐货物</label>
					</li>
					<li>
						<input type='radio' name='state_info' id='d2' value='不是有效的订单' />
						<label for='d2'>不是有效的订单</label>
					</li>
					<li>
						<input type='radio' name='state_info' id='d3' value='买家主动要求' />
						<label for='d3'>买家主动要求</label>
					</li>
					<li>
						<input type='radio' name='state_info' flag='other_reason' id='d4' value='' />
						<label for='d4'>其他原因</label>
					</li>
					<li id='other_reason' style='display:none; height:48px;'>
						<textarea name='state_info1' rows='2' id='other_reason_input' style='width:200px;'></textarea>
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
	$('input[name=state_info]').click(function(){
        if ($(this).attr('flag') == 'other_reason'){
            $('#other_reason').show();
        } else {
            $('#other_reason').hide();
        }
	});
	$('.submit').click(function(e){
		ajax_form_post('category_form');
	});
});
</script>