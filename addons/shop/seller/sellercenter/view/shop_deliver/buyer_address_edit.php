<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='buyer_address_form' method='post' target='_parent' action='<?=users_url('shop_deliver/buyer_address_edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='order_id' value='<?=$output['order_info']['order_id']?>' />
		<dl>
			<dt><i class='required'>*</i>收货人：</dt>
			<dd>
				<input class='text' type='text' id='new_reciver_name' name='new_reciver_name' value='<?=$output['order_info']['extend_order_common']['reciver_name'];?>'  />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>地区：</dt>
			<dd>
				<input class='text' type='text' id='new_area' name='new_area' value='<?=$output['order_info']['extend_order_common']['reciver_info']['area'];?>'  />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>街道：</dt>
			<dd>
				<input class='text' type='text' id='new_street' name='new_street' value='<?=$output['order_info']['extend_order_common']['reciver_info']['street'];?>'  />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>手机：</dt>
			<dd>
				<input class='text' type='text' id='new_tel_phone' name='new_tel_phone' value='<?=$output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>'  />
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
	$('#buyer_address_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            new_reciver_name : {
                required : true
            },
            new_area : {
            	required : true
            },
            new_street : {
                required : true
            },
            new_tel_phone : {
                required : true,
                minlength : 6
            }
        },
        messages : {
            new_reciver_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写收货人'
            },
            new_area : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写地区'
            },
            new_street : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写街道'
            },
            new_tel_phone : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写电话',
                minlength: '<i class=\'icon-exclamation-sign\'></i>电话最少6位'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#buyer_address_form').valid()){
			ajax_form_post('buyer_address_form', function(e){
				if(e.data.address != ''){
					$('#buyer_address_span').html(e.data.address);
				}
				DialogManager.close('edit_buyer_address');			
			});
		};
	});
});
</script>