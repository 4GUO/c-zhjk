<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
		<dl>
			<dt><i class='required'>*</i>选择物流：</dt>
			<dd>
				<select name='express_id'>
					<?php if(!empty($output['express_list']) && is_array($output['express_list'])) {?>
					<?php foreach($output['express_list'] as $value) {?>
					<option value='<?php echo $value['id'];?>'><?php echo $value['e_name'];?></option>
					<?php } ?>
					<?php } ?>
				</select>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
</div>
<script type='text/javascript'>
$(function(){
	$('.submit').click(function(e){
		var print_url = '<?=users_url('shop_deliver/waybill_print', array('order_id' => $output['order_id']));?>';
		var express_id = $('select[name=express_id]').val();
		DialogManager.close('waybill_express');
		if(express_id > 0){
			print_url = print_url + '/express_id/' + express_id;
			window.open(print_url, '_blank')
		}		
	});
});
</script>