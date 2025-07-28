<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='daddress_form' method='post' action='<?=_url('shop_daddress/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' value='0' name='city_id' id='_area_2'>
		<input type='hidden' value='0' name='area_id' id='_area'>
		<dl>
			<dt><i class='required'>*</i>联系人：</dt>
			<dd>
				<input class='text w150' type='text' name='seller_name' id='seller_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>所在地区：</dt>
			<dd>
				<div>
					<input type='hidden' name='region' id='region' value=''/>
				</div>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>街道地址：</dt>
			<dd>
				<input class='text w300' type='text' name='address' id='address' value='' />
				<p class='hint'>不必重复填写地区</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>电话：</dt>
			<dd>
				<input class='text w150' type='text' name='telphone' id='telphone' value='' />
			</dd>
		</dl>
		<dl>
			<dt>公司：</dt>
			<dd>
				<input class='text w150' type='text' name='company' id='company' value='' />
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
	$('#region').fxy_region();
    $('#daddress_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            seller_name : {
                required : true
            },
            region : {
            	checklast: true
            },
            address : {
                required : true
            },
            telphone : {
                required : true,
                minlength : 6
            }
        },
        messages : {
            seller_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写联系人'
            },
            region : {
                checklast : '<i class=\'icon-exclamation-sign\'></i>请选择地区'
            },
            address : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写地址'
            },
            telphone : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写电话',
                minlength: '<i class=\'icon-exclamation-sign\'></i>电弧最少6位'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#daddress_form').valid()){
			ajax_form_post('daddress_form');
		};
	});
});
</script>