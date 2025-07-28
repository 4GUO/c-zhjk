<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('store_goods_class/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['class_info']['stc_id']?>' />
		<input type='hidden' name='store_id' value='<?=input('store_id', 0, 'intval')?>' />
		<dl>
			<dt><i class='required'>*</i>分类名称：</dt>
			<dd>
				<input class='text w200' type='text' name='stc_name' id='stc_name' value='<?=$output['class_info']['stc_name']?>' />
			</dd>
		</dl>
		<?php if($output['class_info']['has_child'] == 0){?>
		<dl>
			<dt>上级分类：</dt>
			<dd>
				<select name='stc_parent_id'>
					<option value='0'>一级分类</option>
				<?php foreach($output['first_gc'] as $value){ if($value['stc_id'] == $output['class_info']['stc_id']){ continue; }?>
					<option value='<?php echo $value['stc_id'];?>'<?php echo $value['stc_id'] == $output['class_info']['stc_parent_id'] ? ' selected' : '';?>><?php echo $value['stc_name'];?></option>
				<?php }?>
				</select>
			</dd>
		</dl>
		<?php } else {?>		
		<input type='hidden' name='stc_parent_id' value='0' />
		<?php }?>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='stc_sort' value='<?=$output['class_info']['stc_sort']?>'  />
				<p class='hint'>数字越小越靠前</p>
			</dd>
		</dl>
		<dl style='display: none'>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='stc_state' value='1'<?php if($output['class_info']['stc_state'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
				<label><input type='radio' name='stc_state' value='0'<?php if($output['class_info']['stc_state'] == 0){?> checked='checked'<?php }?> />否</label>
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
            stc_name : {
                required : true
            },
            stc_sort : {
                number   : true
            }
        },
        messages : {
            stc_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            stc_sort  : {
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