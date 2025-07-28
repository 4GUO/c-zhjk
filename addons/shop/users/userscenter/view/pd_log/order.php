<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
#my_agent .member_info{
	height:20px;
	line-height:20px;
	color:#333;
}
#my_agent .member_info span{
	color:#999
}
</style>
<form method='get' action='<?=users_url('pd_log/order')?>'>
	<table class='search-form'>
		<tbody><tr>
			<td>&nbsp;</td>
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
	</tbody></table>
</form>
<table class='css-default-table' id='my_agent'>
	<thead>
		<tr>
			<th class='w60'>序号</th>
			<th colspan='2'>会员信息</th>
			<th class='w150'>时间</th>
			<th class='w120'>金额</th>
			<th>截图</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td><?php echo $val['pdr_id'] ?></td>
			<td width='70' height='90'><?php if(!empty($output['mapping_fans'][$val['pdr_member_id']]['headimg'])) {?><img src='<?php echo $output['mapping_fans'][$val['pdr_member_id']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 200px;'>
				<p class='member_info'><?php if(!empty($output['mapping_fans'][$val['pdr_member_id']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['pdr_member_id']]['nickname'];?><?php }?></p>			
			</td>
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['pdr_add_time'])?></td>
			<td><?php echo $val['pdr_amount'];?></td>
			<td>
				<?php foreach($val['imgs'] as $vv) { ?>
				<a href='<?=$vv?>' target='_blank'><img src='<?=$vv?>' style='width: 60px; display: inline-block; margin-left: 5px;'/></a>
				<?php } ?>
			</td>
			<td class='nscs-table-handle'>
				<?php if ($val['pdr_payment_state'] == 0) { ?>
				<span>
					<a href='javascript:void(0);' confirm='您确定要审核吗?' url='<?=users_url('pd_log/order_check', array('id' => $val['pdr_id']))?>' class='btn-blue delete'>
						<i class='icon-edit'></i>
						<p>审核</p>
					</a>
				</span> 
				<span>
					<a href='javascript:void(0);' confirm='您确定要删除吗?' url='<?=users_url('pd_log/order_del', array('id' => $val['pdr_id']))?>' class='btn-red delete'>
						<i class='icon-trash'></i>
						<p>删除</p>
					</a>
				</span>
				<?php } ?>
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
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/plugin/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/plugin/datetimepicker/jquery.datetimepicker.js'></script>
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