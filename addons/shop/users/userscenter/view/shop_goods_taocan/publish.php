<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	#warning {
		display: none;
	}
	.goods_list {
		display: block;
		margin-top: 10px;
	}
	.goods_list .goods_item {
		width: 150px;
		float: left;
		margin: 0 10px 10px 0;
		background-color: #FFF;
		text-align: center;
		border: dashed 1px #DDD;
		position: relative;
		z-index: 1;
		overflow: hidden;
		padding: 0 0 10px 0;
	}
	.goods_list .goods_item .goods_image {
		display: block;
		max-height: 150px;
	}
	.goods_list .goods_item .goods_name {
		line-height: 18px;
		color: #555;
		white-space: normal;
		height: 36px;
		margin: 5px 5px 10px 5px;
		text-align: left;
		overflow: hidden;
		text-overflow: ellipsis;
		display: -webkit-box;
		-webkit-line-clamp: 2;
		-webkit-box-orient: vertical;
	}
	.goods_list .goods_item a {
		color: #FFF;
		background-color: #19AEDE;
		display: none;
		padding: 4px 8px;
		position: absolute;
		z-index: 1;
		top: 4px;
		right: 4px;
		cursor: pointer;
	}
	.goods_list .goods_item a i {
		font-size: 14px;
		margin-right: 4px;
	}

	.goods_list .goods_item:hover a {
		display: block;
	}
</style>
<div class='item-publish'>
	<div id='warning' class='alert alert-error'></div>
	<form id='category_form' method='post' target='_parent' action='<?=users_url('shop_goods_taocan/publish')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='id' value='<?=$output['class_info']['tc_id'] ?? 0?>' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>套餐名称：</dt>
				<dd>
					<input class='text w200' type='text' name='tc_name' id='tc_name' value='<?=$output['class_info']['tc_name'] ?? ''?>' />
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>分类图标：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='tc_image' src='<?=isset($output['class_info']['tc_image']) ? $output['class_info']['tc_image'] : STATIC_URL . '/images/default_image.png'?>'> </div>
							<input name='image_path' nctype='tc_image' value='<?=isset($output['class_info']['tc_image']) ? $output['class_info']['tc_image'] : ''?>' type='hidden' />
							<span></span>
							<p class='hint'>上传分类图标；支持jpg、gif、png格式上传或从图片空间中选择，建议使用<font color='red'>尺寸100x100像素以上、大小不超过1M的正方形图片</font>，上传后的图片将会自动保存在图片空间的默认分类中。</p>
							<div class='handle'>
								<a class='css-btn mt5' nc_type='dialog' dialog_title='选择图片' dialog_id='tc_image' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'tc_image'))?>'><i class='icon-picture'></i>从图片空间选择</a> 
							</div>
						</div>
					</div>
					<div id='demo'></div>
				</dd>
			</dl>
			<dl>
			<dt><i class='required'>*</i>选择商品：</dt>
				<dd>
					<span>
						<div class='handle'>
							<a class='css-btn mt5' nc_type='dialog' dialog_title='选择商品' dialog_id='taocan_goods' dialog_width='830' uri='<?=users_url('shop_goods_taocan/selectView', array('input_name' => 'selectall'))?>' style='margin:0 10px;'>选择商品</a>
						</div>
					</span>
					<div nctype='selectall0' class='goods_list'>
						<?php foreach($output['goods_list'] as $v) { ?>
						<li class='goods_item' goods_commonid='<?=$v['goods_commonid']?>' title='点击可删除'><img class='goods_image' src='<?=$v['goods_image']?>'><p class='goods_name'><?=$v['goods_name']?></p><p class='goods_num'>数量：<input class='text w60' type='text' value='<?=$v['goods_num']?>' name='goods_nums[<?=$v['goods_commonid']?>]'>&nbsp;件</p><input type='hidden' value='<?=$v['goods_commonid']?>' name='goods_commonids[<?=$v['goods_commonid']?>]'><a href='javascript:;' onclick='del_link(this);'><i class='icon-trash'></i>删除</a></li>
						<?php } ?>
					</div>
					<p class='hint'>可多选</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>排序：</dt>
				<dd>
					<input class='text w60' type='text' name='tc_sort' value='<?=$output['class_info']['tc_sort'] ?? ''?>'  />
					<p class='hint'>数字越小越靠前</p>
				</dd>
			</dl>
			<dl>
				<dt>启用：</dt>
				<dd>
					<label><input type='radio' name='tc_state' value='1'<?php if(isset($output['class_info']['tc_state']) && $output['class_info']['tc_state'] == 1){?> checked='checked'<?php }?> />是</label>&nbsp;
					<label><input type='radio' name='tc_state' value='0'<?php if(empty($output['class_info']['tc_state'])){?> checked='checked'<?php }?> />否</label>
				</dd>
			</dl>
			<div class='bottom'>
				<label class='submit-border'>
					<input type='button' class='submit' value='提交' />
				</label>
			</div>
		</div>
	</form>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script type='text/javascript'>
upload_file('tc_image', '<?=users_url('album/image_upload')?>');
$(function(){
	$('.submit').click(function(e){
		ajax_form_post('category_form');
	});
	$('.goods_num input').bind('input propertychange change', function() {
		var goods_num = $(this).val();
		var goods_commonid = $(this).parents('li').attr('goods_commonid');
		var taocan_id = $('input[name=id]').val();
		postAjax('<?=users_url('shop_goods_taocan/edit_goods_num')?>', {goods_commonid: goods_commonid, taocan_id: taocan_id, goods_num: goods_num}, function() {
			
		});
	})
});
function del_link(that) {
	var goods_commonid = $(that).parents('li').attr('goods_commonid');
	var taocan_id = $('input[name=id]').val();
	postAjax('<?=users_url('shop_goods_taocan/del_goods')?>', {goods_commonid: goods_commonid, taocan_id: taocan_id}, function() {
		$(that).parents('li').remove();
	});
}
</script>