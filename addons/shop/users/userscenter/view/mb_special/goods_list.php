<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>

<ul class='search-goods-list'>
	<?php foreach($output['goods_list'] as $key => $value) { ?>
	<li>
		<div class='goods-name'><?php echo $value['goods_name'];?></div>
		<div class='goods-price'>￥<?php echo $value['goods_price'];?></div>
		<div class='goods-pic'><img title='<?php echo $value['goods_name'];?>' src='<?php echo $value['goods_image'];?>' /></div>
		<a nctype='btn_add_goods' data-goods-id='<?php echo $value['goods_id'];?>' data-goods-name='<?php echo $value['goods_name'];?>' data-goods-price='<?php echo $value['goods_price'];?>' data-goods-image='<?php echo $value['goods_image'];?>' href='javascript:;'>添加</a> 
	</li>
	<?php } ?>
</ul>
<div id='goods_pagination' class='pagination'> <?php echo $output['page'];?> </div>
<?php } else { ?>
<p class='no-record'>暂无记录</p>
<?php } ?>
<script type='text/javascript'>
    $(document).ready(function() {
        $('#goods_pagination').ajaxContent({
            event: 'click', 
            loaderType: 'img',
            loadingMsg: '<?=STATIC_URL?>/admin/images/transparent.gif',
            target: '#mb_special_goods_list'
        });
    });
</script> 
