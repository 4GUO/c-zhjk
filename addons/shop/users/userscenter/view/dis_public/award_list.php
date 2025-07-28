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
<form method='get' action='<?=users_url('dis_public/award_list')?>'>
	<table class='search-form'>
		<tbody><tr>
			<td>&nbsp;</td>
			<th>昵称</th>
			<td class='w160'><input class='text w120' name='membername' value='<?php echo input('get.membername', '');?>' type='text'></td>
			<th>奖励类型</th>
			<th> 
				<select name='type'>
					<option value=''>全部</option>
                    <option value='level'<?php echo input('get.type', '') == 'level' ? ' selected' : '';?>>见点奖</option>
                    <option value='invite'<?php echo input('get.type', '') == 'invite' ? ' selected' : '';?>>直推奖</option>
                    <option value='parent'<?php echo input('get.type', '') == 'parent' ? ' selected' : '';?>>懒人奖</option>
                    <option value='thinkful'<?php echo input('get.type', '') == 'thinkful' ? ' selected' : '';?>>感恩奖</option>
                </select>
			</th>
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
			<th class='w80'>类型</th>
			<th class='w150'>总额</th>
			<th>描述</th>		
			<th class='w150'>时间</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td><?php echo $val['record_id'] ?></td>
			<td width='40'><div class='pic-thumb'><img src='<?php echo $val['member_avatar'];?>' /></div></td>
			<td style='text-align: left'><?php echo $val['member_name'];?></td>
			<td><?php echo $output['public_award_title'][$val['detail_type']];?></td>
			<td style='color: #e33e33;'>&yen;<?php echo $val['detail_bonus'];?></td>
			<td><?php echo $val['detail_desc'];?></td>			
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['detail_addtime'])?></td>
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