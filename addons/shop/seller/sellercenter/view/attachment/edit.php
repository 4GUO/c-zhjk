<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('attachment/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['class_info']['aclass_id']?>' />
		<dl>
			<dt><i class='required'>*</i>分类名称：</dt>
			<dd>
				<input class='text w200' type='text' name='aclass_name' id='aclass_name' value='<?=$output['class_info']['aclass_name']?>' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='aclass_sort' value='<?=$output['class_info']['aclass_sort']?>'  />
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
            aclass_name : {
                required : true
            },
            aclass_sort : {
                number   : true
            }
        },
        messages : {
            aclass_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            aclass_sort  : {
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