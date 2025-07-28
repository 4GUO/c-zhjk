<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=_url('formguide_field/publish', array('modelid' => input('modelid', 0, 'intval')))?>' class='css-btn css-btn-green'>新增字段</a>
</div>
<form method='get' action='<?=_url('formguide_field/index')?>'>
	<table class='search-form'>	
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<td class='w160'><input class='text w150' name='keyword' value='<?=input('get.keyword', '')?>' type='text'></td>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit'>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th class='w50'>排序</th>
			<th class='w100'>字段别名</th>
			<th class='w100'>字段名</th>
			<th class='w100'>字段类型</th>
			<th class='w150'>创建时间</th>
			<th class='w100'>必填</th>
			<th class='w100'>唯一</th>
			<th class='w100'>状态</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=_url('formguide_field/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['id']?>' type='checkbox'></td>
			<td><?=$val['listorder']?></td>
			<td><?=$val['title']?></td>
			<td><?=$val['name']?></td>
			<td><?=$val['type']?></td>
			<td><?=date('Y-m-d H:i:s', $val['create_time'])?></td>
			<td><?php if($val['ifrequire'] == 0) { ?><font style='color:red'>否</font><?php } else { ?><font style='color:green'>是</font><?php } ?></td>
			<td><?php if($val['ifonly'] == 0) { ?><font style='color:red'>否</font><?php } else { ?><font style='color:green'>是</font><?php } ?></td>
			<td><?php if($val['status'] == 0) { ?><font style='color:red'>禁用</font><?php } else { ?><font style='color:green'>正常</font><?php } ?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('formguide_field/publish', array('id' => $val['id'], 'modelid' => input('modelid', 0, 'intval')))?>' class='btn-blue'>
						<p>编辑</p>
					</a>
				</span>
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('formguide_field/del', array('id' => $val['id'], 'modelid' => input('modelid', 0, 'intval')))?>' class='btn-red delete'>
						<p>删除</p>
					</a>
				</span>
			</td>
		</tr>
		<tr style='display: none;'>
			<td colspan='20'>
				<div class='css-goods-sku ps-container'></div>
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
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>