<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('store/class_publish')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=isset($output['info']['sc_id']) ? $output['info']['sc_id'] : 0?>' />
		<dl>
			<dt><i class='required'>*</i>分类名称：</dt>
			<dd>
				<input class='text w200' type='text' name='sc_name' id='sc_name' value='<?=isset($output['info']['sc_name']) ? $output['info']['sc_name'] : ''?>' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='sc_sort' value='<?=isset($output['info']['sc_sort']) ? $output['info']['sc_sort'] : ''?>'  />
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
    $('#category_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            sc_name : {
                required : true
            },
            sc_sort : {
                number   : true
            }
        },
        messages : {
            sc_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            sc_sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#category_form').valid()){
			ajax_form_post('category_form');
		};
	});
});
</script>