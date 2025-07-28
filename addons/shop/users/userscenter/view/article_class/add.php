<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('article_class/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt><i class='required'>*</i>分类名称：</dt>
			<dd>
				<input class='text w200' type='text' name='ac_name' id='ac_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='ac_sort' value='9999'  />
				<p class='hint'>数字越小越靠前</p>
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='ac_state' value='1' checked='checked' />是</label>
				<label><input type='radio' name='ac_state' value='0' />否</label>
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
            ac_name : {
                required : true
            },
            ac_sort : {
                number   : true
            }
        },
        messages : {
            ac_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            ac_sort  : {
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