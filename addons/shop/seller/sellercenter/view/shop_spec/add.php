<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='spec_form' method='post' target='_parent' action='<?=users_url('shop_spec/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='gc_id' id='gc_id' value='<?php echo $output['gc_id'];?>' />
		<dl>
			<dt><i class='required'>*</i>规格名称：</dt>
			<dd>
				<input class='text w200' type='text' name='spec_name' id='spec_name' value='' />
			</dd>
		</dl>
		<dl>
			<dt>区分图片：</dt>
			<dd>
				<label><input type='radio' name='is_image' value='0' checked='checked' />否</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type='radio' name='is_image' value='1' />是</label>
				<span></span>
				<p class='hint'>选择是，编辑商品时选择该规格，需要上传相应的商品图片；例如颜色</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='spec_sort' value='9999'  />
				<p class='hint'>数字越小越靠前</p>
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<label><input type='radio' name='spec_state' value='1' checked='checked' />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<label><input type='radio' name='spec_state' value='0' />否</label>
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
	$('.submit').click(function(){
		if($('#spec_form').valid()){
			ajax_form_post('spec_form', function(e){
				if(e.state == 200){
					$('#class_div_2').parent().removeClass('blank'); 
					$('#class_div_2 ul').append('<li gcid=\'' + e.data.spec_info.spec_id + '\' deep=\'2\' type=\'specvalue\'><a class=\'\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>'
                        + e.data.spec_info.spec_name + '</a></li>')
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