<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('article/publish')?>' class='css-btn css-btn-green'>新增文章</a>
</div>
<form method='get' action='<?=users_url('article/index')?>'>
	<table class='search-form'>	
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>文章分类</th>
				<td class='w160'>
					<select name='ac_id' class='w150'>
						<option value='0'>请选择...</option>
						<?php foreach($output['class_list'] as $val){?>
						<option <?php if(input('get.ac_id', 0) == $val['ac_id']){?> selected='selected'<?php }?> value='<?=$val['ac_id']?>'><?=$val['ac_name']?></option>
						<?php }?>
					</select>
				</td>
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
			<th coltype='editable' column='goods_name' checker='check_required' inputwidth='230px'>文章标题</th>
			<th class='w100'>分类</th>
			<th class='w100'>状态</th>
			<th class='w100'>发布时间</th>
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
			<td><input class='checkitem tc' value='<?=$val['article_id']?>' type='checkbox'></td>
			<td><?=$val['article_title']?></td>
			<td><?=isset($output['class_list'][$val['ac_id']]) ? $output['class_list'][$val['ac_id']]['ac_name'] : '暂无';?></td>
			<td><?php if($val['article_show'] == 0){?>禁用<?php }else{?>正常<?php }?></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['article_time'])?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('article/publish', array('id' => $val['article_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('article/del', array('id' => $val['article_id']))?>' class='btn-red delete'>
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