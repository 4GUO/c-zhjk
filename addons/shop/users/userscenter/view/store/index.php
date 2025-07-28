<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='<?=users_url('store/publish')?>' class='css-btn css-btn-green'>新增</a>
</div>
<form method='get' action='<?=users_url('store/index')?>'>
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
	</tbody></table>
</form>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th class='w30'>ID</th>
			<th>店铺名称</th>
			<th class='w200'>店铺联系人</th>
			<th class='w100'>商品数量</th>
			<th class='w100'>状态</th>
			<th class='w200'>加入时间</th>
			<th class='w200'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('store/del')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['id']?>' type='checkbox'></td>
			<td><?=$val['id']?></td>
			<td><?=$val['name']?></td>
			<td><?=$val['mobile']?></td>
			<td><?=!empty($output['goods_array'][$val['id']]) ? $output['goods_array'][$val['id']] : 0?></td>
			<td><?php if($val['state'] == 0){?>禁用<?php }else{?>正常<?php }?></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['addtime'])?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('store/publish', array('id' => $val['id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('store/del', array('id' => $val['id']))?>' class='btn-red delete'>
						<i class='icon-trash'></i>
						<p>删除</p>
					</a>
				</span>
				<span>
					<a href='<?=users_url('store_goods_class/index', array('store_id' => $val['id']))?>' class='btn-blue'>
						<i class='icon-align-justify'></i>
						<p>分类</p>
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
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无店铺</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>
</table>