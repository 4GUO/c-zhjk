<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('mb_special/special_save')?>' class='css-btn css-btn-green'>新增专题</a>
</div>
<form method='get' action='<?=users_url('mb_special/special_list')?>'>
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
			<th class='w100'>专题编号</th>
			<th>标题</th>
			<th class='w200'>URL</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><?=$val['special_id']?></td>
			<td><?=$val['special_desc']?></td>
			<td><a href='javascript:;' onclick='copyUrl(this)' uri='<?=uni_url('pages/mb_special/index', array('id' => $val['special_id']), true)?>' title='<?=uni_url('pages/mb_special/index', array('id' => $val['special_id']), true)?>'>复制链接</a></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('mb_special/special_save', array('id' => $val['special_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span>
				<span>
					<a href='<?=users_url('mb_special/special_edit', array('id' => $val['special_id']))?>' class='btn-blue'>
						<i class='icon-wrench'></i>
						<p>装修</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('mb_special/special_del', array('id' => $val['special_id']))?>' class='btn-red delete'>
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
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php } ?>
</table>
<script>
$(function() {
	
})
function copyUrl(obj) {
	var url = $(obj).attr('uri');
	var input = document.createElement('input');
	document.body.appendChild(input);
	input.setAttribute('value', url);
	input.select();
	document.execCommand('copy'); // 执行浏览器复制命令
	if (document.execCommand('copy')) {
		document.execCommand('copy');
		console.log('复制成功');
		showSucc('url已复制好，可贴粘。');
	}
	document.body.removeChild(input);
}
</script>