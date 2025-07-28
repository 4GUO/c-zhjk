<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table' id='my_category'>
	<thead>
		<tr nc_type='table_header'>
			<th style='text-align: left;'>支付方式</th>
			<th class='w60'>状态</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line'>
			<td class='tl'><?=$val['payment_desc']?></td>
			<td><?=$val['payment_state'] == 1 ? '开启' : '关闭'?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='<?=users_url('config/pay_edit', array('payment_id' => $val['payment_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
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