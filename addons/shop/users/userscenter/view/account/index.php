<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加账号' dialog_id='account_add' dialog_width='480' uri='<?=users_url('account/add')?>'>添加账号</a>
</div>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w30'></th>
			<th>ID</th>
			<th>账号名</th>
			<th class='w60'>账号组</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('account/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line' nc_type='table_item' idvalue='<?=$val['account_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['account_id']?>' /></td>
			<td class='tc'><?=$val['account_id']?></td>
			<td class='tc'><?=$val['account_name']?></td>
			<td><?=$output['group_list'][$val['group_id']]['group_name']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='480' dialog_title='编辑账号' dialog_id='account_edit' uri='<?=users_url('account/edit', array('id' => $val['account_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('account/del', array('id' => $val['account_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('account/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>