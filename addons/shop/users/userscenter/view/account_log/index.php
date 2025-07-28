<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('account_log/index')?>'>
	<table class='search-form'>
		<tr>
			<td>&nbsp;</td>
			<th>账号</th>
			<td class='w100'><input type='text' class='text w80' name='log_users_name' value='<?=input('log_users_name', '')?>' /></td>
			<th>日志内容</th>
			<td class='w160'><input type='text' class='text w150' name='log_content' value='<?=input('log_content', '')?>' /></td>
			<th>时间</th>
			<td class='w240'>
				<input name='add_time_from' id='add_time_from' type='text' class='text w70' value='<?=input('add_time_from', '')?>' />
				<label class='add-on'><i class='icon-calendar'></i></label>
				&nbsp;&#8211;&nbsp;
				<input name='add_time_to' id='add_time_to' type='text' class='text w70' value='<?=input('add_time_to', '')?>' />
				<label class='add-on'><i class='icon-calendar'></i></label>
			</td>
			<td class='w70 tc'>
				<label class='submit-border'>
					<input type='submit' class='submit' value='搜索' />
				</label>
			</td>
		</tr>
	</table>
</form>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w100'>账号</th>
			<th class='tl'>日志内容</th>
			<th class='w80'>状态</th>
			<th class='w110'>ip</th>
			<th class='w130'>时间</th>
		</tr>
	</thead>
	<tbody>
		<?php if(!empty($output['list']) && is_array($output['list'])){?>
		<?php foreach($output['list'] as $key => $value){?>
		<tr class='bd-line'>
			<td height='30'><?php echo $value['log_users_name'];?></td>
			<td class='tl'><?php echo $value['log_content'];?></td>
			<td><?php echo $value['log_state'] ? '成功' : '失败';?></td>
			<td><?php echo $value['log_ip'];?></td>
			<td><?php echo date('Y-m-d H:s', $value['log_time']);?></td>
		</tr>
		<?php }?>
		<?php }else{?>
		<tr>
			<td colspan='20' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span></div></td>
		</tr>
		<?php }?>
	</tbody>
	<?php if(!empty($output['list']) && is_array($output['list'])){?>
	<tfoot>
		<tr>
			<td colspan='20'><div class='pagination'><?=$output['page']?></div></td>
		</tr>
	</tfoot>
	<?php }?>
</table>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script type='text/javascript'>
$('#add_time_from').datetimepicker({
    lang:'ch',
	lang:'ch',
	timepicker:false,
	format:'Y-m-d',
	formatDate:'Y-m-d'
});

$('#add_time_to').datetimepicker({
    lang:'ch',
	lang:'ch',
	timepicker:false,
	format:'Y-m-d',
	formatDate:'Y-m-d'
});
</script>
