<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.g_commission{width: 300px; border: 1px solid #dfdfdf; border-top: none}
.g_commission thead {background: #f5f5f5}
.g_commission td{height: 30px; line-height: 30px; text-align: center; padding: 3px 0px; border-top: 1px #dfdfdf solid}
.g_commission .td_left{border-right: 1px #dfdfdf solid}
.add-on {
	border: none;
	background: none;
	width: 100px !important;
	text-align: left;
}
</style>
<style>
	#warning {
		display: none;
	}
</style>
<div class='item-publish'>
	<div id='warning' class='alert alert-error'></div>
	<form id='level_form' method='post' target='_parent' action='<?=users_url('vip_level/edit')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['level_info']['id']?>' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>级别名称：</dt>
				<dd>
					<input class='text w200' type='text' name='level_name' id='level_name' value='<?=$output['level_info']['level_name']?>' />
				</dd>
			</dl>
			<dl>
				<dt>升级：</dt>
				<dd>
					<input name='tihuoquan_price' value='<?=$output['level_info']['tihuoquan_price']?>' class='text w60' type='text'>&nbsp;元购买&nbsp;
					<input name='tihuoquan_num' value='<?=$output['level_info']['tihuoquan_num']?>' class='text w60' type='text'>&nbsp;个提货券
					<span></span>
					<div>
					    购买体验套餐：
    					<label><input type='radio' name='need_buy_experience_goods' value='0' <?php if(empty($output['level_info']['need_buy_experience_goods'])){?> checked<?php }?> />&nbsp;&nbsp;不需要</label>&nbsp;&nbsp;
    					<label><input type='radio' name='need_buy_experience_goods' value='1' <?php if(!empty($output['level_info']['need_buy_experience_goods'])){?> checked<?php }?> />&nbsp;&nbsp;需要</label><br />
					</div>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>加权分红：</dt>
				<dd>
					<div style='margin-bottom: 5px;'>分红条件1：月业绩&nbsp;<input name='jiaquan_fenhong_yue_yeji' value='<?=$output['level_info']['jiaquan_fenhong_yue_yeji']?>' class='text w100' type='text'>&nbsp;元</div>
					<div style='margin-bottom: 5px;'>分红条件2：分红总额超过&nbsp;<input name='jiaquan_fenhong_total' value='<?=$output['level_info']['jiaquan_fenhong_total']?>' class='text w100' type='text'>&nbsp;元不分红</div>
					<div style='margin-bottom: 5px;'>分红比例：<input name='jiaquan_fenhong_bili' value='<?=$output['level_info']['jiaquan_fenhong_bili']?>' class='text w60' type='text'>&nbsp;%</div>
					<span></span>
					<p class='hint'>不设置则无效</p>
				</dd>
			</dl>
			<dl>
				<dt>同级奖励：</dt>
				<dd>
				    <div style='margin-bottom: 5px;'>
				        一级：<input name='tongji_bonus' value='<?=$output['level_info']['tongji_bonus']?>' class='text w60' type='text'>&nbsp;元
				    </div>
					<div style='margin-bottom: 5px;'>
				        二级：<input name='tongji_bonus2' value='<?=$output['level_info']['tongji_bonus2']?>' class='text w60' type='text'>&nbsp;元
				    </div>
					<span></span>
					<p class='hint'>同级与同级之间的奖励，离自己最近的两个人</p>
				</dd>
			</dl>
            <dl>
                <dt>见单奖励：</dt>
                <dd>
                    <div style='margin-bottom: 5px;'>
                        <input name='fgjdjl' value='<?=$output['level_info']['fgjdjl']?>' class='text w60' type='text'>&nbsp;元
                    </div>
                    <span></span>

                </dd>
            </dl>
			<dl>
				<dt>序号：</dt>
				<dd>
					<input name='level_sort' value='<?=$output['level_info']['level_sort']?>' class='text w60' type='text'>
					<span></span>
					<p class='hint'>数字越大级别越高</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>描述：</dt>
				<dd>
					<?php showEditor('level_desc', isset($output['level_info']['level_desc']) ? htmlspecialchars_decode($output['level_info']['level_desc']) : '');?>
					<div class='handle'>
						<div class='css-upload-btn'>
							<a href='javascript:void(0);'>
								<span><input hidefocus='true' size='1' class='input-file' name='level_desc_fileupload' id='level_desc_fileupload' type='file' /></span>
								<p><i class='icon-upload-alt'></i>图片上传</p>
							</a>
						</div>
						<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='level_desc_fileupload' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'editor', 'input_name' => 'level_desc'))?>'><i class='icon-picture'></i>从图片空间选择</a>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>需要审核：</dt>
				<dd>
					<label><input type='radio' name='is_shenhe' value='0' <?php if(empty($output['level_info']['is_shenhe'])){?> checked<?php }?> />&nbsp;&nbsp;不需要</label><br />
					<label><input type='radio' name='is_shenhe' value='1' <?php if(!empty($output['level_info']['is_shenhe'])){?> checked<?php }?> />&nbsp;&nbsp;需要</label><br />
					<p class='hint'>升级是否需要审核</p>
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
});
</script>
