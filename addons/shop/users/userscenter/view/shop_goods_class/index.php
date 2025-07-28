<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('shop_goods_class/add')?>' class='css-btn css-btn-green'>新增分类</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>分类名称</th>
			<th class='w50'>图标</th>
			<th class='w100'>排序</th>
			<th class='w100'>发布虚拟</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('shop_goods_class/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='<?php echo !empty($val['child']) ? 'ld-line' : 'bd-line';?>' nc_type='table_item' idvalue='<?=$val['gc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['gc_id']?>' /></td>
			<td class='tl'><strong><?=$val['gc_name']?></strong></td>
			<td><?php echo $val['gc_image'] ? '<img src=\'' . $val['gc_image'] . '\' width=\'40\'/>' : ''?></td>
			<td><?=$val['gc_sort']?></td>
			<td><?=!empty($val['gc_virtual']) ? '是' : '否';?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('shop_goods_class/edit', array('id' => $val['gc_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('shop_goods_class/del', array('id' => $val['gc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
				    </a>
				</span>
			</td>
		</tr>
		<?php if(!empty($val['child'])){ $child_count = count($val['child']);?>
		<?php foreach($val['child'] as $k => $v){?>
		<tr class='<?php echo $k == $child_count - 1 ? 'bd-line' : 'ld-line';?>' nc_type='table_item' idvalue='<?=$v['gc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$v['gc_id']?>' /></td>
			<td class='tl'>&nbsp;&nbsp;&nbsp;&nbsp;└ └ <?=$v['gc_name']?></td>
			<td><?php echo $v['gc_image'] ? '<img src=\'' . $v['gc_image'] . '\' width=\'40\'/>' : ''?></td>
			<td><?=$v['gc_sort']?></td>
			<td><?=!empty($v['gc_virtual']) ? '是' : '否';?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('shop_goods_class/edit', array('id' => $v['gc_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('shop_goods_class/del', array('id' => $v['gc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('shop_goods_class/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>