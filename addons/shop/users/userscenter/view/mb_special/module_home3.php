<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if ($item_edit_flag) { ?>
<div class='help'>
	<h4>操作提示</h4>
	<li>点击添加新的块内容按钮可以添加新的内容</li>
	<li>鼠标移动到已有的内容上点击出现的删除按钮可以对其进行删除</li>
	<li>操作完成后点击保存编辑按钮进行保存</li>
</div>
<?php } ?>
<div class='index_block home3'>
	<?php if ($item_edit_flag) { ?>
	<h3>模型版块布局C</h3>
	<?php } ?>
	<div class='title'>
		<?php if ($item_edit_flag) { ?>
		<h5>标题：</h5>
		<input id='home1_title' type='text' class='txt w200' name='item_data[title]' value='<?php echo isset($item_data['title']) ? $item_data['title'] : '';?>'>
		<?php } else { ?>
		<span><?php echo isset($item_data['title']) ? $item_data['title'] : '';?></span>
		<?php } ?>
	</div>
	<div nctype='item_content' class='content'>
		<?php if ($item_edit_flag) { ?>
		<h5>内容：</h5>
		<?php } ?>
		<?php if (!empty($item_data['item']) && is_array($item_data['item'])) {?>
		<?php foreach($item_data['item'] as $item_key => $item_value) {?>
		<div nctype='item_image' class='item'> <img nctype='imagea' src='<?php echo $item_value['image'];?>' alt=''>
		<?php if ($item_edit_flag) { ?>
			<input nctype='image_name' name='item_data[item][<?php echo $item_value['image'];?>][image]' type='hidden' value='<?php echo $item_value['image'];?>'>
			<input nctype='image_type' name='item_data[item][<?php echo $item_value['image'];?>][type]' type='hidden' value='<?php echo $item_value['type'];?>'>
			<input nctype='image_data' name='item_data[item][<?php echo $item_value['image'];?>][data]' type='hidden' value='<?php echo $item_value['data'];?>'>
			<a nctype='btn_del_item_image' href='javascript:;'><i class='icon-trash'></i>删除</a>
		<?php } ?>
		</div>
		<?php } ?>
		<?php } ?>
	</div>
	<?php if ($item_edit_flag) { ?>
		<a class='btn-add' href='javascript:;' nc_type='dialog' dialog_title='模型版块布局C' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('size' => '320*85', 'item_type' => 'home3'))?>'>+&nbsp;添加新的块内容</a>
	<?php } ?>
</div>
<script id='item_image_template' type='text/html'>
    <div nctype='item_image' class='item'>
        <img nctype='imagea' src='<%=image%>' alt='' />
        <input nctype='image_name' name='item_data[item][<%=image%>][image]' type='hidden' value='<%=image%>' />
        <input nctype='image_type' name='item_data[item][<%=image%>][type]' type='hidden' value='<%=image_type%>' />
        <input nctype='image_data' name='item_data[item][<%=image%>][data]' type='hidden' value='<%=image_data%>' />
        <a nctype='btn_del_item_image' href='javascript:;'>删除</a>
    </div>
</script>
<script type='text/javascript'>
	function add_item_image_save(image, type, data, callback) {
		var item = {};
		item.image = image;
		item.image_type = type;
		item.image_data = data;
		$('.home3 [nctype=item_content]').append(template.render('item_image_template', item));
		callback();
	}
</script>
