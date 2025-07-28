<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('shop_vr_order/receive_pay')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='order_id' value='<?php echo isset($output['order_info']['order_id']) ? $output['order_info']['order_id'] : 0;?>' />
		<div class='css-form-goods'>
			<dl>
				<dt>订单编号：</dt>
				<dd>
					<?php echo isset($output['order_info']['order_sn']) ? $output['order_info']['order_sn'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt>订单总金额：</dt>
				<dd>
					<?php echo isset($output['order_info']['order_amount']) ? $output['order_info']['order_amount'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt>付款时间：</dt>
				<dd>
					<input name='paytime' id='paytime' class='text w100' value='' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>付款方式：</dt>
				<dd>
					<select name='payment_code'>
					    <?php foreach($output['payment_list'] as $k => $v){?>
						<option value='<?=$v['payment_code']?>'><?=$v['payment_name']?></option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt>第三方支付平台交易号：</dt>
				<dd>
					<input name='trade_no' class='text w400' value='' type='text' />
					<span></span>
					<p class='hint'>支付宝、微信等第三方支付平台交易号</p>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#paytime').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d',
	formatDate: 'Y-m-d',
	step: 5
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>