<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='waybill_form' method='post' action='<?=_url('shop_waybill/add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<dl>
			<dt><i class='required'>*</i>模板名称：</dt>
			<dd>
				<input class='text w150' type='text' name='waybill_name' id='waybill_name' value='' />
				<p class='hint'>运单模板名称，最多10个字</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>物流公司：</dt>
			<dd>
				<select name='waybill_express'>
					<?php if(!empty($output['express_list']) && is_array($output['express_list'])) {?>
					<?php foreach($output['express_list'] as $value) {?>
					<option value='<?php echo $value['id'];?>|<?php echo $value['e_name'];?>' <?php if(!empty($value['selected'])) { echo 'selected'; }?> ><?php echo $value['e_name'];?></option>
					<?php } ?>
					<?php } ?>
				</select>
				<span></span>
				<p class='hint'>模板对应的物流公司</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>宽度：</dt>
			<dd>
				<input type='text' value='' name='waybill_width' id='waybill_width' class='w60 text'>
				<em class='add-on'>mm</em> <span></span>
				<p class='hint'>运单宽度，单位为毫米(mm)</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>高度：</dt>
			<dd>
				<input type='text' value='' name='waybill_height' id='waybill_height' class='w60 text'>
				<em class='add-on'>mm</em> <span></span>
				<p class='hint'>运单高度，单位为毫米(mm)</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>上偏移量：</dt>
			<dd>
				<input type='text' value='' name='waybill_top' id='waybill_top' class='w60 text'>
				<em class='add-on'>mm</em> <span></span>
				<p class='hint'>运单模板上偏移量，单位为毫米(mm)</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>左偏移量：</dt>
			<dd>
				<input type='text' value='' name='waybill_left' id='waybill_left' class='w60 text'>
				<em class='add-on'>mm</em> <span></span>
				<p class='hint'>运单模板左偏移量，单位为毫米(mm)</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>模板图片：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='waybill_image' src='<?=STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='waybill_image' nctype='waybill_image' value='' type='hidden' />
						<span></span>
						<p class='hint'>请上传扫描好的运单图片，图片尺寸必须与快递单实际尺寸相符。</p>
						<div class='handle'>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='waybill_image' dialog_width='830' dialog_height='550' uri='<?=_url('album/pic_list_view', array('item' => 'waybill_image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt>启用：</dt>
			<dd>
				<ul class='css-form-radio-list'>
					<li>
						<label for='waybill_usable_1'>
							<input id='waybill_usable_1' type='radio' name='waybill_usable' value='1' checked>
							是</label>
					</li>
					<li>
						<label for='waybill_usable_0'>
							<input id='waybill_usable_0' type='radio' name='waybill_usable' value='0'>
							否</label>
					</li>
				</ul>
				<span></span>
				<p class='hint'>请首先设计并测试模板然后再启用，启用后商家可以使用</p>
			</dd>
		</dl>
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
upload_file('waybill_image', '<?=_url('album/image_upload')?>');
$(function(){
    $('#waybill_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
            $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            waybill_name: {
                required : true,
                maxlength : 10
            },
            waybill_width: {
                required : true,
                digits: true 
            },
            waybill_height: {
                required : true,
                digits: true 
            },
            waybill_top: {
                required : true,
                number: true 
            },
            waybill_left: {
                required : true,
                number: true 
            }
        },
        messages : {
            waybill_name: {
                required : '<i class=\'icon-exclamation-sign\'></i>模板名称不能为空',
                maxlength : '<i class=\'icon-exclamation-sign\'></i>模板名称最多10个字' 
            },
            waybill_width: {
                required : '<i class=\'icon-exclamation-sign\'></i>宽度不能为空',
                digits: '<i class=\'icon-exclamation-sign\'></i>宽度必须为数字'
            },
            waybill_height: {
                required : '<i class=\'icon-exclamation-sign\'></i>高度不能为空',
                digits: '<i class=\'icon-exclamation-sign\'></i>高度必须为数字'
            },
            waybill_top: {
                required : '<i class=\'icon-exclamation-sign\'></i>上偏移量不能为空',
                number: '<i class=\'icon-exclamation-sign\'></i>上偏移量必须为数字'
            },
            waybill_left: {
                required : '<i class=\'icon-exclamation-sign\'></i>左偏移量不能为空',
                number: '<i class=\'icon-exclamation-sign\'></i>左偏移量必须为数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#waybill_form').valid()){
			ajax_form_post('waybill_form');
		};
	});
});
</script>