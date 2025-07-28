<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.g_commission{width: 300px; border: 1px solid #dfdfdf; border-top: none}
.g_commission thead {background: #f5f5f5}
.g_commission td{height: 30px; line-height: 30px; text-align: center; padding: 3px 0px; border-top: 1px #dfdfdf solid}
.g_commission .td_left{border-right: 1px #dfdfdf solid}
.products_option {margin-bottom: 10px;}
.products_option .search_div{margin-top:8px;}
.products_option .search_div .button_search{padding:3px 6px; cursor:pointer}
.products_option .select_items{margin-top:8px;}
.products_option .select_items .button_add{height:30px; line-height:26px; width:45px; display:block; margin:30px 8px 0px; float:left; border: solid 1px #E6E6E6;}
.products_option .select_items .products_show{height:100px; width:300px; display:block; border:1px #E6E6E6 solid; overflow:scroll; background:#FFF}
.products_option .select_items .products_show p{height:24px; line-height:24px; width:95%; overflow:hidden; padding:0px; margin:0px auto; cursor:pointer}
.products_option .select_items .products_show .p_cur{background:#39F;}
.products_option .options_buttons{height:100px; width:80px; float:left; margin-left:8px}
.products_option .options_buttons button{display:block; height:30px; line-height:26px; width:100%; text-align:center; cursor:pointer; margin:8px 0px 0px 0px; border: solid 1px #E6E6E6;}
.search_cate select{width:120px}
</style>
<style>
	#warning {
		display: none;
	}
</style>
<div class='item-publish'>
	<div id='warning' class='alert alert-error'></div>
	<form id='level_form' method='post' target='_parent' action='<?=users_url('area_level/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>级别名称：</dt>
				<dd>
					<input class='text w200' type='text' name='level_name' id='level_name' value='' />
				</dd>
			</dl>
			<dl>
				<dt>奖励：</dt>
				<dd>
					<input name='bonus_bili' value='' class='text w60' type='text'>&nbsp;%
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>序号：</dt>
				<dd>
					<input name='level_sort' value='0' class='text w60' type='text'>
					<span></span>
					<p class='hint'>数字越大级别越高</p>
				</dd>
			</dl>
		</div>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
$(function(){
    $('#level_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            level_name : {
                required : true
            },
			level_sort : {
                number : true
            }
        },
        messages : {
            level_name : {
                required : '<i class=\'icon-exclamation-sign\'></i>级别名称不能为空'
            },
			level_sort : {
                required : '<i class=\'icon-exclamation-sign\'></i>级别序号不能为空，且为数字'
            }
        }
    });
	editor_upload_file('level_desc', '<?=users_url('album/image_upload')?>', function(e){
		level_desc.appendHtml('level_desc', '<img src=\''+ e.file_url + '\' alt=\''+ e.file_url + '\'>');
	});
	$('.submit').click(function(e){
		if($('#level_form').valid()){
			ajax_form_post('level_form');
		};
	});
	$('.products_option .select_items .button_add').click(function() {
		var text = $(this).parent().children('.select_product0').find('option:selected').text();
		var value = $(this).parent().children('.select_product0').find('option:selected').val();
		if ($(this).parent().children('.select_product1').find('option:contains(' + text + ')').length == 0 && typeof(value) != 'undefined') {
			$(this).parent().children('.select_product1').append('<option value=\'' + value + '\'>' + text + '</option>');
		}
			
		var strids = $(this).parent().children('input').val();
		if (typeof(value)!='undefined') {
			if (strids == '') {
				$(this).parent().children('input').val(',' + value + ',');
			} else {
				strids = strids.replace(',' + value + ',',',');
				$(this).parent().children('input').val(strids + value + ',');
			}
		}
	});
		
	$('.products_option .options_buttons .button_remove').click(function(){//移除选项		
		var select_obj = $(this).parent().parent().children('.select_items').children('.select_product1').find('option:selected');
		var input_obj = $(this).parent().parent().children('.select_items').children('input');
		var strids = input_obj.val();
		select_obj.each(function() {
			$(this).remove();
			strids = strids.replace(',' + $(this).val() + ',',',');
		});
		if (strids == ',') {
			strids = '';
		}
		input_obj.val(strids);
	});
		
	$('.products_option .options_buttons .button_empty').click(function(){//清空选项
		 $(this).parent().parent().children('.select_items').children('.select_product1').empty();
		 $(this).parent().parent().children('.select_items').children('input').val('');
	});
});
</script>