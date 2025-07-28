<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('wechat/keyword_add')?>' class='css-btn css-btn-green'>新增</a>
</div>
<form method='get' action='<?=users_url('wechat/keyword_manage')?>'>
	<table class='search-form'>	
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>类型</th>
				<td class='w160'>
					<select name='ac_id' class='w150'>
						<option value='0'>请选择...</option>
						<option <?php if(input('get.type', 0) == 1){?> selected='selected'<?php }?> value='1'>文字消息</option>
						<option <?php if(input('get.type', 0) == 2){?> selected='selected'<?php }?> value='2'>图文消息</option>
					</select>
				</td>
				<td class='w160'><input class='text w150' name='keywords' value='<?=input('get.keywords', '')?>' type='text'></td>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit' />
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
			<th class='w120'>关键词</th>
			<th class='w100'>回复类型</th>
			<th>回复内容</th>
			<th class='w100'>匹配模式</th>
			<th class='w120'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
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
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['reply_id']?>' type='checkbox'></td>
			<td><?=trim($val['reply_keywords'], '|')?></td>
			<td><?=$val['reply_msgtype'] == 0 ? '文字消息' : '图文消息'?></td>
			<td><?=$val['reply_textcontents']?></td>
			<td><?=$val['reply_patternmethod'] == 0 ? '精确匹配' : '模糊匹配'?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('wechat/keyword_edit', array('rid' => $val['reply_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('wechat/keyword_del', array('rid' => $val['reply_id']))?>' class='btn-red'>
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