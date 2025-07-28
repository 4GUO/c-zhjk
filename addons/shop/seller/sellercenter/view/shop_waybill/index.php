<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
	<a href='javascript:void(0)' class='css-btn css-btn-green' nc_type='dialog' dialog_title='添加地址' dialog_id='my_daddress_add' dialog_width='600' uri='<?=_url('shop_waybill/add')?>'>新建模板</a>
</div>
<table class='css-default-table' id='my_daddress'>
	<thead>
		<tr nc_type='table_header'>
			<th class='w30'></th>
			<th class='w120 tl'>模板名称</th>
			<th class='w120 tl'>物流公司</th>
			<th class='tl'>运单图例</th>
			<th class='w80'>上偏移量</th>
			<th class='w80'>左偏移量</th>
			<th class='w80'>启用</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $value) { ?>
		<tr class='bd-line'>
			<td></td>
			<td class='tl'><?php echo $value['waybill_name'];?></td>
			<td class='tl'><?php echo $value['express_name'];?></td>
			<td class='tl'><div class='waybill-img-thumb'><a class='nyroModal' rel='gal' href='<?php echo $value['waybill_image'];?>'><img src='<?php echo $value['waybill_image'];?>'></a></div>
				<div class='waybill-img-size'>
					<p>宽度：<?php echo $value['waybill_width'];?>(mm)</p>
					<p>高度：<?php echo $value['waybill_height'];?>(mm)</p>
				</div></td>
				</td>
			<td><?php echo $value['waybill_top'];?></td>
			<td><?php echo $value['waybill_left'];?></td>
			<td><?php echo $value['waybill_usable'] == 0 ? '<font style=\'color: red\'>未启用</font>' : '<font style=\'color: green\'>已启用</font>';?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a nc_type='dialog' dialog_title='添加地址' dialog_id='my_daddress_edit' dialog_width='600' uri='<?=_url('shop_waybill/edit', array('waybill_id' => $value['waybill_id']))?>' href='javascript:;' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<span>
				    <a href='<?=_url('shop_waybill/design', array('waybill_id' => $value['waybill_id']))?>' class='btn-blue'><i class='icon-print'></i>
				        <p>设计</p>
				    </a>
				</span>
				<span>
				    <a href='javascript:void(0)' confirm='确定删除?' url='<?=_url('shop_waybill/del', array('waybill_id' => $value['waybill_id']))?>' class='btn-red delete'><i class='icon-trash'></i>
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
</table>