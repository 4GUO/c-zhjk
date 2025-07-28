<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
    <?php if (!empty($output['parent_area_info']['area_name'])) { ?>
    <li style='font-size: 16px;color: #5BB75B;'>“<?=$output['parent_area_info']['area_name'] ?? ''?>”下属地区</li>
    <?php } ?>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加城市' dialog_id='my_category_add' dialog_width='480' uri='<?=users_url('area/publish', array('area_parent_id' => input('area_parent_id', 0, 'intval')))?>'>新增地区</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th>地区名称</th>
			<th class='w60'>排序</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('area/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line' nc_type='table_item' idvalue='<?=$val['area_id']?>' >
			<td class='tc'><input type='checkbox' class='checkitem' value='<?=$val['area_id']?>' /></td>
			<td class='tl'><?=$val['area_name']?></td>
			<td><?=$val['area_sort']?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('area/index', array('area_parent_id' => $val['area_id']))?>' class='btn-blue'>
				        <p>下级</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='480' dialog_title='编辑' dialog_id='my_category_edit' uri='<?=users_url('area/publish', array('id' => $val['area_id']))?>' class='btn-blue'>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='谨慎操作！！！确定删除?' url='<?=users_url('area/del', array('id' => $val['area_id']))?>' class='btn-red delete'>
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
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('area/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</th>
		</tr>
	</tfoot>
	<?php } ?>
</table>