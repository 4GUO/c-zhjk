<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('wechat/menu_add')?>' class='css-btn css-btn-green'>新增</a>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th class='w120'>菜单标题</th>
			<th class='w100'>是否生效</th>
			<th class='w100'>添加时间</th>
			<th class='w120'>操作</th>
		</tr>
		<?php if (!empty($output['menu_list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('article/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['menu_list'])) { ?>
		<?php foreach ($output['menu_list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['menu_id']?>' type='checkbox'></td>
			<td><?=$val['menu_name']?></td>
			<td><?=$val['menu_status'] == 0 ? '未启用' : '已在微信端启用'?></td>
			<td><?=date('Y-m-d H:i:s', $val['menu_addtime'])?></td>
			<td class='nscs-table-handle'>
				<?php if($val['menu_status'] == 0) { ?>
				<span>
					<a href='<?=users_url('wechat/menu_publish', array('mid' => $val['menu_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>发布到微信</p>
					</a>
				</span>
				<?php } ?>
				<span>
					<a href='<?=users_url('wechat/menu_edit', array('mid' => $val['menu_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('wechat/menu_del', array('mid' => $val['menu_id']))?>' class='btn-red delete'>
						<i class='icon-trash'></i>
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
	<?php if (!empty($output['menu_list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>