<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if($item_edit_flag) { ?>
<div class='help'>
	<h4>操作提示</h4>
	<li>点击添加新的广告条按钮可以添加新的广告条</li>
	<li>鼠标移动到已有的广告条上点击出现的删除按钮可以删除对应的广告条</li>
	<li>操作完成后点击保存编辑按钮进行保存</li>
</div>
<?php } ?>
<div class='index_block adv_list'>
	<?php if($item_edit_flag) { ?>
	<h3>广告条版块</h3>
	<?php } ?>
	<div nctype='item_content' class='content'>
		<?php if($item_edit_flag) { ?>
		
		<?php } ?>
		<?php if(!empty($item_data['item']) && is_array($item_data['item'])) {?>
		<?php foreach($item_data['item'] as $item_key => $item_value) {?>
		<div nctype='item_image' class='item'>
			<img nctype='imagea' src='<?php echo $item_value['image'];?>' alt='' />
			<?php if($item_edit_flag) { ?>
			<input nctype='image_name' name='item_data[item][<?php echo $item_value['image'];?>][image]' type='hidden' value='<?php echo $item_value['image'];?>' />
			<input nctype='image_type' name='item_data[item][<?php echo $item_value['image'];?>][type]' type='hidden' value='<?php echo $item_value['type'];?>' />
			<input nctype='image_data' name='item_data[item][<?php echo $item_value['image'];?>][data]' type='hidden' value='<?php echo $item_value['data'];?>' />
			<a nctype='btn_del_item_image' href='javascript:;'><i class='icon-trash'></i>删除</a>
			<?php } ?>
		</div>
		<?php } ?>
		<?php } ?>
	</div>
	<?php if($item_edit_flag) { ?>
	<a class='btn-add' href='javascript:;' nc_type='dialog' dialog_title='添加轮播广告' dialog_id='image_publish' dialog_width='580' uri='<?=_url('mb_special_do/image_publish', array('size' => '640*240', 'item_type' => 'adv_list'))?>'>+&nbsp;添加新的广告条</a>
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
		$('.adv_list [nctype=item_content]').append(template.render('item_image_template', item));
		callback();
	}
</script>
