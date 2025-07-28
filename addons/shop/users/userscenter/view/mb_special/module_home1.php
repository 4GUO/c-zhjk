<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if($item_edit_flag) { ?>
<div class='help'>
	<h4>操作提示</h4>
	<li>鼠标移动到内容上出现编辑按钮可以对内容进行修改</li>
	<li>操作完成后点击保存编辑按钮进行保存</li>
</div>
<?php } ?>
<div class='index_block home1'>
	<?php if($item_edit_flag) { ?>
	<h3>模型版块布局A</h3>
	<?php } ?>
	<div class='title'>
		<?php if($item_edit_flag) { ?>
		<h5>标题：</h5>
		<input id='home1_title' type='text' class='txt w200' name='item_data[title]' value='<?php echo $item_data['title'];?>'>
		<?php } else { ?>
		<span><?php echo $item_data['title'];?></span>
		<?php } ?>
	</div>
	<div nctype='item_content' class='content'>
		<?php if($item_edit_flag) { ?>
		<h5>内容：</h5>
		<?php } ?>
		<div nctype='item_image' class='item'> <img nctype='imagea' src='<?php echo $item_data['image'];?>' alt=''>
		<?php if($item_edit_flag) { ?>
			<input nctype='image_name' name='item_data[image]' type='hidden' value='<?php echo $item_data['image'];?>'>
			<input nctype='image_type' name='item_data[type]' type='hidden' value='<?php echo $item_data['type'];?>'>
			<input nctype='image_data' name='item_data[data]' type='hidden' value='<?php echo $item_data['data'];?>'>
			<a href='javascript:;' nc_type='dialog' dialog_title='编辑模型版块布局A' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('image' => encrypt($item_data['image']), 'type' => $item_data['type'], 'data' => encrypt($item_data['data']), 'size' => '640*260', 'item_type' => 'home1'))?>'><i class='icon-edit'></i>编辑</a>
		<?php } ?>
		</div>
	</div>
</div>
<script type='text/javascript'>
	function edit_item_image_save(image, type, data, callback, imgbox) {
		$('.home1 [nctype=imagea]').attr('src', image);
		$('.home1 [nctype=image_name]').val(image);
		$('.home1 [nctype=image_type]').val(type);
		$('.home1 [nctype=image_data]').val(data);
		callback();
	}
</script>