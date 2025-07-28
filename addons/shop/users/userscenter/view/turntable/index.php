<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.sp-hidden {
		display: none;
	}
</style>
<div class='tabmenu'>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加抽奖选项' dialog_id='my_category_add' dialog_width='480' uri='<?=users_url('turntable/add')?>'>新增选项</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>选项名称</th>
			<th class='w60'>排序</th>
			<th>轮盘概率（总和必须是100%）</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('turntable/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line' nc_type='table_item' idvalue='<?=$val['id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['id']?>' /></td>
			<td class='tl'><?=$val['title']?></td>
			<td><?=$val['sort']?></td>
			<td><?=$val['percent']?>%</td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='480' dialog_title='编辑' dialog_id='my_category_edit' uri='<?=users_url('turntable/edit', array('id' => $val['id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('turntable/del', array('id' => $val['id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<th class='tc'><input id='all2' type='checkbox' class='checkall' /></th>
			<th colspan='15'><label for='all2'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('turntable/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>