<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='spec_value_form' method='post' target='_parent' action='<?=users_url('shop_spec_value/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='sp_id' id='sp_id' value='<?php echo $output['sp_id'];?>' />
		<dl>
			<dt><i class='required'>*</i>规格值：</dt>
			<dd>
				<input class='text w200' type='text' name='sp_value_name' id='sp_value_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='sp_value_sort' value='9999'  />
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
			sp_id : {
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
			sp_id : {
                required : '<i class=\'icon-exclamation-sign\'></i>请选择规格'
            },
            sp_value_sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(){
		if($('#spec_value_form').valid()){
			ajax_form_post('spec_value_form', function(e){
				if(e.state == 200){
					$('#class_div_3').parent().removeClass('blank'); 
					$('#class_div_3 ul').append('<li gcid=\'' + e.data.sp_value_info.sp_value_id + '\' deep=\'3\' type=\'specvalue\'><a class=\'\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>'
                        + e.data.sp_value_info.sp_value_name + '</a></li>')
						.find('li:last').click(function(){
								selClass($(this));
							});
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