<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('account_group/publish')?>' class='css-btn css-btn-green'>添加组</a>
</div>
<table class='css-default-table'>
	<thead>
		<tr>
			<th>组名</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><?=$val['group_name']?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('account_group/publish', array('id' => $val['group_id']))?>' class='btn-blue'><i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('account_group/del', array('id' => $val['group_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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