<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	#warning {
		display: none;
	}
</style>
<div class='tabmenu'>
    <?php if (!empty($output['parent_area_info']['area_name'])) { ?>
    <li style='font-size: 16px;color: #5BB75B; padding: 15px;'>添加“<?=$output['parent_area_info']['area_name'] ?? ''?>”下属地区</li>
    <?php } else if (!empty($output['info'])) { ?>
    <li style='font-size: 16px;color: #5BB75B; padding: 15px;'>默认数据不要修改</li>
    <?php } ?>
</div>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('area/publish')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
	    <input type='hidden' name='area_parent_id' value='<?=$output['parent_area_info']['area_id'] ?? 0?>' />
	    <input type='hidden' name='id' value='<?=$output['info']['area_id'] ?? 0?>' />
		<dl>
			<dt><i class='required'>*</i>名称：</dt>
			<dd>
				<input class='text w200' type='text' name='area_name' id='area_name' value='<?=isset($output['info']['area_name']) ? $output['info']['area_name'] : ''?>' />
			</dd>
		</dl>
		<dl style='display: none;'>
			<dt>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='area_sort' value='0' />
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
$(function() {	
    $('#category_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            area_name : {
                required : true
            }
        },
        messages : {
            area_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>名称不能为空'

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