<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='spec_form' method='post' target='_parent' action='<?=users_url('shop_spec/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['spec_info']['spec_id']?>' />
		<dl>
			<dt><i class='required'>*</i>规格名称：</dt>
			<dd>
				<input class='text w200' type='text' name='spec_name' id='spec_name' value='<?=$output['spec_info']['spec_name']?>' />
			</dd>
		</dl>
		<dl style='display:none'>
			<dt><i class='required'>*</i>所属分类：</dt>
			<dd>
				<select name='gc_id' id='gc_id'>
					<?php foreach($output['gc_list'] as $gc_id=>$gc_info){?>
					<option value='<?php echo $gc_id;?>'<?php echo $output['spec_info']['gc_id'] == $gc_id ? ' selected' : '';?>><?php echo $gc_info;?></option>
					<?php }?>
				</select>
			</dd>
		</dl>
		<dl>
			<dt>区分图片：</dt>
			<dd>
				<label><input type='radio' name='is_image' value='0'<?php if($output['spec_info']['is_image'] == 0){?> checked='checked'<?php }?> />否</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type='radio' name='is_image' value='1'<?php if($output['spec_info']['is_image'] == 1){?> checked='checked'<?php }?> />是</label>
				<span></span>
				<p class='hint'>选择是，编辑商品时选择该规格，需要上传相应的商品图片；例如颜色</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='spec_sort' value='<?=$output['spec_info']['spec_sort']?>'  />
				<p class='hint'>数字越小越靠前</p>
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='spec_state' value='1'<?php if($output['spec_info']['spec_state'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
				<label><input type='radio' name='spec_state' value='0'<?php if($output['spec_info']['spec_state'] == 0){?> checked='checked'<?php }?> />否</label>
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
    $('#spec_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            spec_name : {
                required : true
            },
			gc_id : {
                required : true
            },
            spec_sort : {
                number   : true
            }
        },
        messages : {
            spec_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>名称不能为空'
            },
			gc_id : {
                required : '<i class=\'icon-exclamation-sign\'></i>请选择分类'
            },
            spec_sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#spec_form').valid()){
			ajax_form_post('spec_form', function(e){
				if(e.state == 200){					
					select_object.html('<a class=\'classDivClick\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>' + e.data.spec_info.spec_name + '</a>');
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