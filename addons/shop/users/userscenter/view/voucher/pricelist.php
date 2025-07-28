<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('voucher/index')?>'>商家代金券</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('voucher/pricelist')?>'>面额管理</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/priceadd')?>'>添加面额</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/config')?>'>设置</a>
		</li>
	</ul>
</div>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'>&nbsp;</th>
			<th>描述</th>
			<th class='w100'>代金券面额(元)</th>
			<th class='w100'>兑换积分数</th>
			<th class='w120'>操作</th>
		</tr>
		<?php if (!empty($output['list'])) { ?>
		<tr>
			<td class='tc'><input id='all' type='checkbox' class='checkall' /></td>
			<td colspan='20'>
			    <label for='all'>全选</label>
				<a href='javascript:void(0)' class='css-btn-mini' nc_type='batchbutton' uri='<?=users_url('voucher/pricedel')?>' name='id' confirm='确定删除?'><i class='icon-trash'></i>删除</a>
			</td>
		</tr>
		<?php } ?>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr>
			<td><input class='checkitem tc' value='<?=$val['voucher_price_id']?>' type='checkbox'></td>
			<td><?=$val['voucher_price_describe']?></td>
			<td><?=$val['voucher_price']?></td>
			<td><?=$val['voucher_defaultpoints']?></td>
			<td class='nscs-table-handle'>
				<span>
					<a href='<?=users_url('voucher/priceedit', array('id' => $val['voucher_price_id']))?>' class='btn-blue'>
						<i class='icon-edit'></i>
						<p>编辑</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('voucher/pricedel', array('id' => $val['voucher_price_id']))?>' class='btn-red delete'>
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