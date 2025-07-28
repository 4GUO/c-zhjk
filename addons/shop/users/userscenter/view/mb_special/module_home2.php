<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if($item_edit_flag) { ?>
<div class='help'>
	<h4>操作提示</h4>
	<li>鼠标移动到内容上出现编辑按钮可以对内容进行修改</li>
	<li>操作完成后点击保存编辑按钮进行保存</li>
</div>
<?php } ?>
<div class='index_block home2'>
	<?php if($item_edit_flag) { ?>
	<h3>模型版块布局B</h3>
	<?php } ?>
	<div class='title'>
		<?php if($item_edit_flag) { ?>
		<h5>标题：</h5>
		<input id='home1_title' type='text' class='txt w200' name='item_data[title]' value='<?php echo $item_data['title'];?>' />
		<?php } else { ?>
		<span><?php echo $item_data['title'];?></span>
		<?php } ?>
	</div>
	<div class='content'>
		<?php if($item_edit_flag) { ?>
		<h5>内容：</h5>
		<?php } ?>
		<div class='home2_1'>
			<div nctype='item_image' class='item'> <img nctype='imagea' src='<?php echo $item_data['square_image'];?>' alt=''>
				<?php if($item_edit_flag) { ?>
				<input nctype='image_name' name='item_data[square_image]' type='hidden' value='<?php echo $item_data['square_image'];?>'>
				<input nctype='image_type' name='item_data[square_type]' type='hidden' value='<?php echo $item_data['square_type'];?>'>
				<input nctype='image_data' name='item_data[square_data]' type='hidden' value='<?php echo $item_data['square_data'];?>'>
				<a href='javascript:;' nc_type='dialog' dialog_title='编辑模型版块布局B' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('image' => encrypt($item_data['square_image']), 'type' => $item_data['square_type'], 'data' => encrypt($item_data['square_data']), 'size' => '320*260', 'imgbox' => 'home2_1', 'item_type' => 'home2'))?>'><i class='icon-edit'></i>编辑</a>
				<?php } ?>
			</div>
		</div>
		<div class='home2_2'>
			<div class='home2_2_1'>
				<div nctype='item_image' class='item'> <img nctype='imagea' src='<?php echo $item_data['rectangle1_image'];?>' alt=''>
					<?php if($item_edit_flag) { ?>
					<input nctype='image_name' name='item_data[rectangle1_image]' type='hidden' value='<?php echo $item_data['rectangle1_image'];?>'>
					<input nctype='image_type' name='item_data[rectangle1_type]' type='hidden' value='<?php echo $item_data['rectangle1_type'];?>'>
					<input nctype='image_data' name='item_data[rectangle1_data]' type='hidden' value='<?php echo $item_data['rectangle1_data'];?>'>
					<a href='javascript:;' nc_type='dialog' dialog_title='编辑模型版块布局B' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('image' => encrypt($item_data['rectangle1_image']), 'type' => $item_data['rectangle1_type'], 'data' => encrypt($item_data['rectangle1_data']), 'size' => '320*130', 'imgbox' => 'home2_2_1', 'item_type' => 'home2'))?>'><i class='icon-edit'></i>编辑</a>
					<?php } ?>
				</div>
			</div>
			<div class='home2_2_2'>
				<div nctype='item_image' class='item'> <img nctype='imagea' src='<?php echo $item_data['rectangle2_image'];?>' alt=''>
					<?php if($item_edit_flag) { ?>
					<input nctype='image_name' name='item_data[rectangle2_image]' type='hidden' value='<?php echo $item_data['rectangle2_image'];?>'>
					<input nctype='image_type' name='item_data[rectangle2_type]' type='hidden' value='<?php echo $item_data['rectangle2_type'];?>'>
					<input nctype='image_data' name='item_data[rectangle2_data]' type='hidden' value='<?php echo $item_data['rectangle2_data'];?>'>
					<a href='javascript:;' nc_type='dialog' dialog_title='编辑模型版块布局B' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('image' => encrypt($item_data['rectangle2_image']), 'type' => $item_data['rectangle2_type'], 'data' => encrypt($item_data['rectangle2_data']), 'size' => '320*130', 'imgbox' => 'home2_2_2', 'item_type' => 'home2'))?>'><i class='icon-edit'></i>编辑</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type='text/javascript'>
	function edit_item_image_save(image, type, data, callback, imgbox) {
		console.log(imgbox);
		$('.' + imgbox + ' [nctype=imagea]').attr('src', image);
		$('.' + imgbox + ' [nctype=image_name]').val(image);
		$('.' + imgbox + ' [nctype=image_type]').val(type);
		$('.' + imgbox + ' [nctype=image_data]').val(data);
		callback();
	}
</script>