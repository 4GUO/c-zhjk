<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('shop_goods_taocan/publish')?>' class='css-btn css-btn-green'>新增套餐</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>分类名称</th>
			<!--<th class='w50'>图标</th>-->
			<th class='w100'>排序</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('shop_goods_taocan/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='<?php echo !empty($val['child']) ? 'ld-line' : 'bd-line';?>' nc_type='table_item' idvalue='<?=$val['tc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['tc_id']?>' /></td>
			<td class='tl'><strong><?=$val['tc_name']?></strong></td>
			<!--<td><?php echo $val['tc_image'] ? '<img src=\'' . $val['tc_image'] . '\' width=\'40\'/>' : ''?></td>-->
			<td><?=$val['tc_sort']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('shop_goods_taocan/publish', array('id' => $val['tc_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('shop_goods_taocan/del', array('id' => $val['tc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
				        <p>删除</p>
				    </a>
				</span>
			</td>
		</tr>
		<?php if(!empty($val['child'])){ $child_count = count($val['child']);?>
		<?php foreach($val['child'] as $k => $v){?>
		<tr class='<?php echo $k == $child_count - 1 ? 'bd-line' : 'ld-line';?>' nc_type='table_item' idvalue='<?=$v['tc_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$v['tc_id']?>' /></td>
			<td class='tl'>&nbsp;&nbsp;&nbsp;&nbsp;└ └ <?=$v['tc_name']?></td>
			<td><?php echo $v['tc_image'] ? '<img src=\'' . $v['tc_image'] . '\' width=\'40\'/>' : ''?></td>
			<td><?=$v['tc_sort']?></td>
			<td><?=!empty($v['tc_virtual']) ? '是' : '否';?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('shop_goods_taocan/edit', array('id' => $v['tc_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('shop_goods_taocan/del', array('id' => $v['tc_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('shop_goods_taocan/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>