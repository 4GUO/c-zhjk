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
#my_agent .order_info{
	color:#666;
	font-size:12px;
}
</style>
<form method='get' action='<?=users_url('distribute_award/fgjdjl_index')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th> 
					<select name='search_type'>
						<option value='nickname' <?php if(input('get.search_type', '') == 'nickname'){?> selected='selected'<?php }?>>姓名</option>
						<option value='mobile' <?php if(input('get.search_type', '') == 'mobile'){?> selected='selected'<?php }?>>手机</option>
					</select>
				</th>
				<td class='w160'><input class='text w150' name='keyword' value='<?php echo input('get.keyword', '');?>' type='text'></td>
				<th>状态</th>
				<td class='w120'>
					<select name='detail_status'>
						<option value='-1' <?php if(input('get.detail_status', -1) == -1){?> selected='selected'<?php }?>>全部</option>
						<option value='10' <?php if(input('get.detail_status', -1) == 10){?> selected='selected'<?php }?>>已发放</option>
						<option value='20' <?php if(input('get.detail_status', -1) == 20){?> selected='selected'<?php }?>>已退回</option>
					</select>
				</td>
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
			<th colspan='2'>获奖人信息</th>
			<th colspan='2'>下单人信息</th>
			<th>订单信息</th>	
			<th>奖励金额</th>	
			<th class='w150'>时间</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td><?php echo $val['detail_id'] ?></td>
			<!-- 获奖人信息 -->
			<td width='70' height='90'><?php if(!empty($output['mapping_fans'][$val['uid']]['headimg'])) {?><img src='<?php echo $output['mapping_fans'][$val['uid']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 150px;'>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['mapping_fans'][$val['uid']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['uid']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['mapping_fans'][$val['uid']]['mobile'])) {?><?php echo $output['mapping_fans'][$val['uid']]['mobile'];?><?php }?></p>
			</td>
			<!-- 下单人信息 -->
			<td width='70' height='90'><?php if(!empty($output['mapping_from_users'][$val['from_uid']]['headimg'])) {?><img src='<?php echo $output['mapping_from_users'][$val['from_uid']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 150px;'>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['mapping_from_users'][$val['from_uid']]['nickname'])) {?><?php echo $output['mapping_from_users'][$val['from_uid']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['mapping_from_users'][$val['from_uid']]['mobile'])) {?><?php echo $output['mapping_from_users'][$val['from_uid']]['mobile'];?><?php }?></p>
			</td>
			<!-- 订单信息 -->
			<td style='text-align: left; width: 200px;'>
				<p class='order_info'><span>订单号：</span><?php echo $val['order_sn'];?></p>
				<p class='order_info'><span>订单金额：</span>￥<?php echo $val['order_amount'];?></p>
				<p class='order_info'><span>当前订单排序：</span><?php echo $val['user_orders'];?></p>
				<p class='order_info'><span>上级复购单数：</span><?php echo $val['parent_orders'];?></p>
				<p class='order_info'><span>描述：</span><?php echo $val['detail_desc'];?></p>
			</td>
			<!-- 奖励金额 -->
			<td>
				<font style='color:red; font-size:16px; font-weight:bold;'>&yen;<?php echo $val['detail_bonus'];?></font>
				<br>
				<?php if ($val['detail_status'] == 10) { ?>
					<span style='color:green;'>已发放</span>
				<?php } elseif ($val['detail_status'] == 20) { ?>
					<span style='color:red;'>已退回</span>
				<?php } ?>
			</td>		
			<td class='goods-time'><?php echo $val['detail_addtime'];?></td>
		</tr>
		<?php } ?>		
		<?php } else { ?>
		<tr>
			<td colspan='9' class='norecord'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无记录</span> </div></td>
		</tr>
		<?php } ?>
	</tbody>
	<?php if (!empty($output['list'])) { ?>
	<tfoot>
		<tr>
			<td colspan='9'><div class='pagination'><?=$output['page']?></div></td>
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