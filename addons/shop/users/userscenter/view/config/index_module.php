<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('config/module_add')?>' class='css-btn css-btn-green'>新增</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>名称</th>
			<th>链接</th>
			<th class='w60'>图标</th>
			<th class='w60'>商城首页</th>
			<th class='w60'>排序</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('config/module_del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line' nc_type='table_item' idvalue='<?=$val['id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['id']?>' /></td>
			<td class='tl'><?=$val['name']?></td>
			<td><?=$val['link']?></td>
			<td><?php if(!empty($val['thumb'])) { ?><img src='<?=$val['thumb']?>' style='width: 30px; height: 30px;'/><?php } ?></td>
			<td><?=empty($val['status']) ? '不显示' : '显示';?></td>
			<td><?=$val['m_sort']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('config/module_edit', array('id' => $val['id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=users_url('config/module_del', array('id' => $val['id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('config/module_del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>