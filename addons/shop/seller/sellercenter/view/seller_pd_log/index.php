<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
#my_agent .store_info{
	height:20px;
	line-height:20px;
	color:#333;
}
#my_agent .store_info span{
	color:#999
}
</style>
<form method='get' action='<?=users_url('seller_pd_log/index')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				
				<td>累计充值：<?=$output['total_chongzhi'];?>&nbsp;&nbsp;可用剩余：<?=$output['total_shengyu'];?></td>
				<th>时间</th>
				<td class='w240'>
					<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					&nbsp;–&nbsp;
					<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</td>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit'>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table' id='my_agent'>
	<thead>
		<tr>
			<th class='w60'>序号</th>
			<th class='w150'>时间</th>
			<th class='w120'>增\减金额</th>
			<th class='w120'>冻\解金额</th>
			<th class='w120'>当前余额</th>
			<th>描述</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td><?php echo $val['lg_id'] ?></td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['lg_add_time'])?></td>
			<td><?php echo $val['lg_av_amount'] == 0 ? '' : ($val['lg_av_amount'] < 0 ? $val['lg_av_amount'] : '+' . $val['lg_av_amount']);?></td>
			<td><?php echo $val['lg_freeze_amount'] == 0 ? '' : ($val['lg_freeze_amount'] < 0 ? $val['lg_freeze_amount'] : '+' . $val['lg_freeze_amount']);?></td>
			<td><?php echo $val['available_predeposit'];?></td>
			<td><?php echo $val['lg_desc'];?></td>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#query_start_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});

$('#query_end_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});
</script>