<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if ($output['edit_goods_sign']) {?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('shop_goods/publish', array('commonid' => $output['commonid']))?>'>编辑商品</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('shop_goods/edit_images', array('commonid' => $output['commonid'], 'type' => 'edit'))?>'>编辑图片</a>
		</li>
	</ul>
</div>
<?php } else {?>
<ul class='add-goods-step'>
	<li><i class='icon icon-edit'></i>
		<h6>STEP.1</h6>
		<h2>填写商品详情</h2>
		<i class='arrow icon-angle-right'></i> </li>
	<li class='current'><i class='icon icon-camera-retro '></i>
		<h6>STEP.2</h6>
		<h2>上传商品图片</h2>
		<i class='arrow icon-angle-right'></i> </li>
	<li><i class='icon icon-ok-circle'></i>
		<h6>STEP.3</h6>
		<h2>商品发布成功</h2>
	</li>
</ul>
<?php }?>
<form method='post' id='form' action='<?=users_url('shop_goods/edit_images')?>'>
	<input type='hidden' name='form_submit' value='ok'>
	<input type='hidden' name='commonid' value='<?php echo $output['commonid'];?>'>
	<input type='hidden' name='type' value='<?php echo $output['type'];?>'>
	<?php if (!empty($output['value_array'])) {?>
	<div class='css-form-goods-pic'>
		<div class='container'>
			<?php foreach ($output['value_array'] as $value) {?>
			<div class='css-goodspic-list'>
				<div class='title'>
					<h3>属性：
						<?php echo $value['sp_value_name']?>
					</h3>
				</div>
				<ul nctype='ul<?php echo $value['sp_value_id'];?>'>
					<?php for ($i = 0; $i < 5; $i++) {?>
					<li class='css-goodspic-upload'>
						<div class='upload-thumb'><img src='<?php echo isset($output['img'][$value['sp_value_id']][$i]['goods_image']) ? $output['img'][$value['sp_value_id']][$i]['goods_image'] : STATIC_URL . '/images/default_image.png';?>' nctype='file_<?php echo $value['sp_value_id'] . $i;?>'>
							<input type='hidden' name='img[<?php echo $value['sp_value_id'];?>][<?php echo $i;?>][name]' value='<?php echo isset($output['img'][$value['sp_value_id']][$i]['goods_image']) ? $output['img'][$value['sp_value_id']][$i]['goods_image'] : '';?>' nctype='file_<?php echo $value['sp_value_id'] . $i;?>'>
						</div>
						<div class='show-default<?php if (isset($output['img'][$value['sp_value_id']][$i]['is_default']) && $output['img'][$value['sp_value_id']][$i]['is_default'] == 1) {echo ' selected';}?>' nctype='file_<?php echo $value['sp_value_id'] . $i;?>'>
							<p><i class='icon-ok-circle'></i>默认主图
								<input type='hidden' name='img[<?php echo $value['sp_value_id'];?>][<?php echo $i;?>][default]' value='<?php if (isset($output['img'][$value['sp_value_id']][$i]['is_default']) && $output['img'][$value['sp_value_id']][$i]['is_default'] == 1) {echo '1';}else{echo '0';}?>'>
							</p>
							<a href='javascript:void(0)' nctype='del' class='del' title='移除'>X</a> </div>
						<div class='show-sort'>排序：
							<input name='img[<?php echo $value['sp_value_id'];?>][<?php echo $i;?>][sort]' type='text' class='text' value='<?php echo isset($output['img'][$value['sp_value_id']][$i]['goods_image_sort']) ? intval($output['img'][$value['sp_value_id']][$i]['goods_image_sort']) : '';?>' size='1' maxlength='1'>
						</div>
						<div class='css-upload-btn'><a class='css-btn' nc_type='dialog' dialog_title='选择图片' dialog_id='file_<?php echo $value['sp_value_id'] . $i;?>' dialog_width='830' dialog_height='550' uri='<?=users_url('album/pic_list_view', array('item' => 'file_' . $value['sp_value_id'] . $i))?>'>
							<p style='border: none'>选择图片</p>
							</a>
						</div>
					</li>
					<?php }?>
				</ul>
				<div nctype='album-<?php echo $value['sp_value_id'];?>'></div>
			</div>
			<?php }?>
		</div>
		<div class='sidebar'>
			<div class='alert alert-info alert-block' id='uploadHelp'>
				<div class='faq-img'></div>
				<h4>上传要求：</h4>
				<ul>
					<li>1. 请使用jpg\jpeg\png等格式、单张大小不超过1M的正方形图片。</li>
					<li>3. 从图片空间中选择已有的图片，上传后的图片也将被保存在图片空间中以便其它使用。</li>
					<li>4. 通过更改排序数字修改商品图片的排列显示顺序。</li>
					<li>5. 图片质量要清晰，不能虚化，要保证亮度充足。</li>
					<li>6. 操作完成后请点确定，否则无法在网站生效。</li>
				</ul>
				<h4>建议:</h4>
				<ul>
					<li>1. 主图为白色背景正面图。</li>
					<li>2. 排序依次为正面图->背面图->侧面图->细节图。</li>
				</ul>
			</div>
		</div>
	</div>
	<?php }?>
	<div class='bottom tc hr32'>
		<label class='submit-border'>
			<input class='submit' value='确定' type='button'>
		</label>
	</div>
</form>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.iframe-transport.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script>
$(function(){
	//默认主图
    $('div[nctype^=file]').click(function(){
		if ($(this).prev().find('input[type=hidden]').val() != '') {
			$(this).parents('ul:first').find('.show-default').removeClass('selected').find('input').val('0');
			$(this).addClass('selected').find('input').val('1');
		}
    });
    //删除
    $('a[nctype=del]').click(function(){
		var that = $(this).parents('div[nctype^=file]');
		if ($(this).prev().find('input[type=hidden]').val() != '') {
			that.unbind('click').removeClass('selected').find('input').val('0');
			that.prev().find('input').val('').end().find('img').attr('src', '<?php echo STATIC_URL . '/images/default_image.png';?>');
		}
    });
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>