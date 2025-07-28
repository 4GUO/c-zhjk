<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='spec_value_form' method='post' target='_parent' action='<?=users_url('shop_spec_value/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['sp_value_info']['sp_value_id']?>' />
		<dl>
			<dt><i class='required'>*</i>规格名称：</dt>
			<dd>
				<input class='text w200' type='text' name='sp_value_name' id='sp_value_name' value='<?=$output['sp_value_info']['sp_value_name']?>' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='sp_value_sort' value='<?=$output['sp_value_info']['sp_value_sort']?>'  />
				<p class='hint'>数字越小越靠前</p>
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
    $('#spec_value_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            sp_value_name : {
                required : true
            },
            sp_value_sort : {
                number   : true
            }
        },
        messages : {
            sp_value_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>规格值不能为空'
            },
            sp_value_sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#spec_value_form').valid()){
			ajax_form_post('spec_value_form', function(e){
				if(e.state == 200){					
					select_object.html('<a class=\'classDivClick\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>' + e.data.sp_value_info.sp_value_name + '</a>');
					if(typeof(CUR_DIALOG) != 'undefined'){
						CUR_DIALOG.close();
					}
				} else {
					showError(e.msg);
				}
			});
		};
	});
});
</script>