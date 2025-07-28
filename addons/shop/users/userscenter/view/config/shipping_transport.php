<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'><a href='<?=users_url('config/shipping')?>'>综合设置</a></li>
		<li class='active'><a href='<?=users_url('config/shipping_transport')?>'>区域运费</a></li>
	</ul>
	<a class='css-btn css-btn-green' href='<?=users_url('config/shipping_transport_add')?>'>新增区域运费 </a> 
</div>
<div class='alert alert-block mt10'>
	<ul class='mt5'>
		<li>如果某商品选择使用了区域运费，运费为指定地区的运费。</li>
	</ul>
</div>
<?php if (is_array($output['list'])){?>
<table class='css-default-table order'>
	<thead>
		<tr>
			<th class='w20'></th>
			<th class='cell-area tl'>运送到</th>
			<th class='w150'>运费(元)</th>
		</tr>
	</thead>
	<?php foreach ($output['list'] as $v){?>
	<tbody>
		<tr>
			<td colspan='20' class='sep-row'></td>
		</tr>
		<tr>
			<th colspan='20'><?php if (isset($_GET['type']) && $_GET['type'] == 'select'){?>
				<a class='ml5 css-btn-mini css-btn-orange yinyong' name='<?php echo $v['title'];?>' id='<?php echo $v['id'];?>' price='<?=isset($output['extend'][$v['id']]['price']) ? $output['extend'][$v['id']]['price'] : 0;?>' href='javascript:void(0)'><i class='icon-truck'></i>应用</span></a>
				<?php }?>
				<h3><?php echo $v['title'];?></h3>
				<span class='fr mr5'>
					<time title='编辑时间'><i class='icon-time'></i><?php echo date('Y-m-d H:i:s', $v['update_time']);?></time>
					<a class='J_Modify css-btn-mini' href='javascript:void(0)' data-id='<?php echo $v['id'];?>'><i class='icon-edit'></i>编辑</a> 
					<a class='J_Delete css-btn-mini' href='javascript:void(0)' data-id='<?php echo $v['id'];?>'><i class='icon-trash'></i>删除</a>
				</span>
			</th>
		</tr>
		<?php if (!empty($output['extend'][$v['id']]['data']) && is_array($output['extend'][$v['id']]['data'])){?>
		<?php foreach ($output['extend'][$v['id']]['data'] as $value){?>
		<tr>
			<td></td>
			<td class='cell-area tl'><?php echo $value['area_name'];?></td>
			<td><?php echo $value['sprice'];?></td>
		</tr>
		<?php }?>
		<?php }?>
	</tbody>
	<?php }?>
</table>
<?php } else {?>
<div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span></div>
<?php } ?>
<?php if (is_array($output['list'])){?>
<div class='pagination'><?php echo $output['page']; ?></div>
<?php }?>
<script> 
$(function(){	
	$('a.J_Delete').click(function(){
		var id = $(this).attr('data-id');
		if(typeof(id) == 'undefined') return false;
		ajax_get_confirm('确认删除吗？', '<?=users_url('config/shipping_transport_delete')?>?transport_id=' + id);
	});

	$('a.J_Modify').click(function(){
		var id = $(this).attr('data-id');
		if(typeof(id) == 'undefined') return false;
		$(this).attr('href', '<?=users_url('config/shipping_transport_add')?>?transport_id=' + id);
		return true;
	});
	$('a.yinyong').click(function(){
		var data_str = {};
		data_str.name = $(this).attr('name');
		data_str.id = $(this).attr('id');
		data_str.price = $(this).attr('price');
		$('#postageName', opener.document).css('display', 'inline-block').html(data_str.name);
		$('#transport_title', opener.document).val(data_str.name);
		$('#transport_id', opener.document).val(data_str.id);
		$('#g_freight', opener.document).val(data_str.price);
		window.close();
	});	
});
</script>