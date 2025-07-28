<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加地址' dialog_id='my_daddress_add' dialog_width='600' uri='<?=_url('shop_daddress/add')?>'>新增地址</a>
</div>
<table class='css-default-table' id='my_daddress'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w70'>是否默认</th>
			<th class='w90'>联系人</th>
			<th class='tl'>发货地址</th>
			<th class='w110'>电话</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line' nc_type='table_item' idvalue='<?=$val['address_id']?>'>
			<td>
				<label for='is_default_<?php echo $val['address_id'];?>'>
					<input type='radio' id='is_default_<?php echo $val['address_id'];?>' name='is_default' <?php if ($val['is_default'] == 1) echo 'checked';?> value='<?php echo $val['address_id'];?>'> 默认
				</label>
			</td>
			<td><?php echo $val['seller_name'];?></td>
			<td class='tl'><?php echo $val['area_info'];?>&nbsp;<?php echo $val['address'];?></td>
			<td><span class='tel'><?php echo $val['telphone'];?></span></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='600' dialog_title='编辑地址' dialog_id='my_daddress_edit' uri='<?=_url('shop_daddress/edit', array('address_id' => $val['address_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=_url('shop_daddress/del', array('address_id' => $val['address_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
				    </a>
				</span>
			</td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>	
</table>
<script>
$(function (){
	$('input[name=is_default]').on('click',function(){
		getAjax('<?=_url('shop_daddress/default_set')?>', {address_id: $(this).val()}, '', 'json');
	});
});
</script>