<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='search-form'>	
	<tbody>
		<tr>
			<td>已提现金额：<?=$output['supply_commiss']['getmoney']?>  提现中金额：<?=$output['supply_commiss']['dongmoney']?>  可提现金额：<?=$output['supply_commiss']['money']?> &nbsp;<a href='<?=users_url('seller/tixian_form')?>' class='css-btn css-btn-green'>申请提现</a></td>
		</tr>
	</tbody>
</table>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w80'>ID</th>
			<th>账户信息</th>
			<th class='w120'>提现金额</th>
			<th class='w200'>状态</th>
			<th class='w250'>提现申请时间/处理时间</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line'>
			<td class='bdl'><?php echo $val['id'];?></td>
			<td class='bdl'>
				<?php echo '转账类型：' . $val['bankname'];?><br/>
				<?php if ($val['type'] == 'wxzhuanzhang') { ?>
					<?php echo '微信会员昵称：' . $val['bankaccount']; ?><br/>
				<?php } else { ?>
					<?php echo '账户：' . $val['bankaccount']; ?><br/>
				<?php } ?>
				<?php echo empty($val['bankusername']) ? '' : '持卡人姓名：' . $val['bankaccount'];?><br/>
			</td>
			<td class='bdl'>&yen; <?php echo $val['money'];?></td>
			<td class='bdl'>
				<?php if($val['state'] == 0) {?>
                <font style='padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;'>提现中</font>
                <?php }elseif($val['state'] == 1) {?>
                <font style='padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;'>提现成功，已打款</font>
                <?php }elseif($val['state'] == 2) {?>
                <font style='padding:8px 15px; color:#FFF; background:#F60; border-radius:5px;'>提现失败</font>
                <?php }?>
			</td>
			<td class='bdl'><?php echo date('Y-m-d H:i:s', $val['addtime']);?><br/><?php echo !empty($val['shentime']) ? date('Y-m-d H:i:s', $val['shentime']) : '';?></td>
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
$(function(){
	
});
</script>