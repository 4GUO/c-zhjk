<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.sp-hidden {
		display: none;
	}
</style>
<link href='<?=STATIC_URL?>/admin/js/colorpicker/spectrum.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/colorpicker/spectrum.js'></script>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('turntable/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['info']['id']?>' />
		<dl>
			<dt><i class='required'>*</i>奖项名称：</dt>
			<dd>
				<input class='text w200' type='text' name='title' id='title' value='<?=$output['info']['title']?>' />
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>奖项概率：</dt>
			<dd>
				<input class='text w200' type='text' name='percent' id='percent' value='<?=$output['info']['percent']?>' /><em class='add-on'><i>%</i></em>
				<p class='hint'>所有奖项概率相加为100</p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>奖励库存：</dt>
			<dd>
				<input class='text w200' type='text' name='stock' id='stock' value='<?=$output['info']['stock']?>' />
				<p class='hint'></p>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>奖金比例：</dt>
			<dd>
				<input class='text w200' type='text' name='reward_ratio' id='reward_ratio' value='<?=$output['info']['reward_ratio']?>' /><em class='add-on'><i>%</i></em>
				<p class='hint'>每周业绩奖励的百分比</p>
			</dd>
		</dl>
		<dl>
			<dt>区块颜色：</dt>
			<dd>
				<input class='text w80' type='text' name='color' id='colorpicker' value='<?=$output['info']['color']?>' value='0' />
				<p class='hint'></p>
			</dd>
		</dl>
		<dl>
			<dt>图片：</dt>
			<dd>
				<div class='css-goods-default-pic'>
					<div class='goodspic-uplaod'>
						<div class='upload-thumb'> <img nctype='thumb' src='<?=!empty($output['info']['thumb']) ? $output['info']['thumb'] : STATIC_URL . '/images/default_image.png'?>'> </div>
						<input name='thumb' nctype='thumb' value='<?=!empty($output['info']['thumb']) ? $output['info']['thumb'] : ''?>' type='hidden' />
						<span></span>
						<p class='hint'>建议尺寸<font color='red'>32 X 32、大小不超过1M的图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
						<div class='handle'>
							<div class='css-upload-btn'> 
							    <a href='javascript:void(0);'>
									<span>
										<input hidefocus='true' size='1' class='input-file' name='thumb' id='thumb' type='file' />
									</span>
									<p><i class='icon-upload-alt'></i>图片上传</p>
								</a> 
							</div>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='thumb' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'thumb'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
						</div>
					</div>
				</div>
				<div id='demo'></div>
			</dd>
		</dl>
		<dl>
			<dt><i class='required'>*</i>排序：</dt>
			<dd>
				<input class='text w60' type='text' name='sort' value='<?=$output['info']['sort']?>'  />
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
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
upload_file('thumb', '<?=users_url('album/image_upload')?>');
$(function(){
    $('#category_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            title : {
                required : true
            },
            sort : {
                number   : true
            }
        },
        messages : {
            title : {
                required : '<i class=\'icon-exclamation-sign\'></i>分类名称不能为空'

            },
            sort  : {
                number   : '<i class=\'icon-exclamation-sign\'></i>排序不能为空，必须是数字'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#category_form').valid()){
			ajax_form_post('category_form');
		};
	});
	$('#colorpicker').spectrum({
		color: '<?=$output['info']['color']?>',
		showInput: true,//显示输入
		cancelText: '取消',//取消按钮,按钮文字
		chooseText: '确定',//选择按钮,按钮文字
		preferredFormat: 'hex',//输入框颜色格式,(hex十六进制,hex3十六进制可以的话只显示3位,hsl,rgb三原色,name英文名显示)
	});
});
</script>