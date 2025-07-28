<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=_url('formguide/publish')?>' class='css-btn css-btn-green'>新增表单模型</a>
</div>
<form method='get' action='<?=_url('formguide/index')?>'>
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
			<th class='w100'>表单标题</th>
			<th class='w100'>表名</th>
			<th>简介</th>
			<th class='w100'>状态</th>
			<th class='w150'>发布时间</th>
			<th class='w250'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=_url('formguide/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['id']?>' type='checkbox'></td>
			<td><?=$val['name']?></td>
			<td><?=$val['tablename']?></td>
			<td><?=$val['description']?></td>
			<td><?php if($val['status'] == 0) { ?>禁用<?php } else { ?>正常<?php } ?></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['create_time'])?></td>
			<td class='nscs-table-handle'>
			    <span>
					<a href='<?=_url('formguide_info/index', array('modelid' => $val['id']))?>' class='btn-blue'>
						<p>数据列表</p>
					</a>
				</span>
			    <span>
					<a href='<?=_url('formguide_field/index', array('modelid' => $val['id']))?>' class='btn-blue'>
						<p>字段管理</p>
					</a>
				</span>
				<br />
				<span>
					<a href='<?=front_url('formguide/index', array('formid' => $val['id']))?>' class='btn-blue' target='_blank'>
						<p>查看</p>
					</a>
				</span>
				<span>
					<a href='<?=_url('formguide/publish', array('id' => $val['id']))?>' class='btn-blue'>
						<p>编辑</p>
					</a>
				</span>
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=_url('formguide/del', array('id' => $val['id']))?>' class='btn-red delete'>
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