<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table' id='my_method'>
	<thead>
		<tr>
			<th class='tl'>提现方式</th>
			<th class='w110'>是否审核</th>
			<th class='w110'>是否启用</th>
			<th class='w200'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line'>
			<td class='tl'><?php echo $val['method_name'];?></td>
			<td><?php echo $val['method_check']==0 ? '<font style=\'padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;\'>否</font>' : '<font style=\'padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;\'>是</font>'?></td>
			<td><?php echo $val['method_status']==0 ? '<font style=\'padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;\'>禁用</font>' : '<font style=\'padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;\'>启用</font>'?></td>
			<td class='nscs-table-handle'>
			    <span>
				    <a href='javascript:void(0)' nc_type='dialog' dialog_width='600' dialog_title='编辑提现方式' dialog_id='my_method_edit' uri='<?=users_url('withdraw_method/edit', array('method_id' => $val['method_id']))?>' class='btn-blue'><i class='icon-edit'></i>
				        <p>编辑</p>
				    </a>
				</span>
				<?php if($val['method_code'] == 'bank_' . $val['method_id']){?>
				<?php }?>
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