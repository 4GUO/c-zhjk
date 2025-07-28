<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加商品分类' dialog_id='my_category_add' dialog_width='600' uri='<?=users_url('store_goods_class/add', array('store_id' => input('store_id', 0, 'intval')))?>'>新增分类</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>分类名称</th>
			<th class='w100'>排序</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('store_goods_class/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='<?php echo !empty($val['child']) ? 'ld-line' : 'bd-line';?>' nc_type='table_item' idvalue='<?=$val['stc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['stc_id']?>' /></td>
			<td class='tl'><strong><?=$val['stc_name']?></strong></td>
			<td><?=$val['stc_sort']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='600' dialog_title='编辑商品分类' dialog_id='my_category_edit' uri='<?=users_url('store_goods_class/edit', array('id' => $val['stc_id'], 'store_id' => $val['store_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('store_goods_class/del', array('id' => $val['stc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
				    </a>
				</span>
			</td>
		</tr>
		<?php if(!empty($val['child'])){ $child_count = count($val['child']);?>
		<?php foreach($val['child'] as $k => $v){?>
		<tr class='<?php echo $k == $child_count - 1 ? 'bd-line' : 'ld-line';?>' nc_type='table_item' idvalue='<?=$v['stc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$v['stc_id']?>' /></td>
			<td class='tl'>&nbsp;&nbsp;&nbsp;&nbsp;└ └ <?=$v['stc_name']?></td>
			<td><?=$v['stc_sort']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='600' dialog_title='编辑商品分类' dialog_id='my_category_edit' uri='<?=users_url('store_goods_class/edit', array('id' => $v['stc_id'], 'store_id' => $v['store_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('store_goods_class/del', array('id' => $v['stc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
				    </a>
				</span>
			</td>
		</tr>
		<?php }?>
		<?php }?>
		<?php } ?>
		<?php } else { ?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<th class='tc'><input id='all2' type='checkbox' class='checkall' /></th>
			<th colspan='15'><label for='all2'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('store_goods_class/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>