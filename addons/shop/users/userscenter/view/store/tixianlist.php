<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w80'>ID</th>
			<th>账户信息</th>
			<th class='w120'>商家</th>
			<th class='w120'>提现金额</th>
			<th class='w120'>状态</th>
			<th class='w250'>提现申请时间</th>
			<th class='w120'>操作</th>
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
			<td class='bdl'><?php echo $output['store_list'][$val['store_id']]['name'];?></td>
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
			<td class='bdl'><?php echo date('Y-m-d H:i:s', $val['addtime']);?></td>
			<td class='bdl bdr' id='btns_<?php echo $val['id']; ?>'>
				<?php if($val['state'] == 0) { ?>
				<?php if($val['type'] == 'wxzhuanzhang') { ?>
                <p><a href='javascript:void(0)' class='css-btn css-btn-green mt5' nctype='wxpay_record' data-id='<?php echo $val['id']; ?>' /><i class='icon-pencil'></i>微信转账</a></p>
                <?php } else { ?>
                <p><a href='javascript:void(0)' confirm='确定处理该记录么?' url='<?=users_url('store/deal_tixian', array('id' => $val['id']))?>' class='css-btn css-btn-green mt5 delete' /><i class='icon-pencil'></i>处理记录</a></p>
                <?php } ?>
                <p><a href='javascript:void(0)' class='css-btn css-btn-red mt5' nc_type='dialog' uri='<?=users_url('store/reject_tixian', array('id' => $val['id']))?>' dialog_title='提现申请驳回' dialog_id='cancel_reocrd' dialog_width='400' id='reocrd<?php echo $val['id']; ?>' /><i class='icon-remove-circle'></i>驳回申请</a></p>
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
var num = 0;
$(function(){
	$('a[nctype=wxpay_record]').on('click', '', function(){
		if(num == 1){
			showError('已有任务在执行，请耐心等待');
		}
		num = 1;
		var rid = $(this).attr('data-id');
		$('#btns_' + rid).html('');
		getAjax('<?=users_url('store/pay_record')?>', {id: rid}, function(e){			
			if (e.state == 400) {
				showError(e.msg, function(){
					window.location.reload();
				});
			} else {
				showSucc(e.msg, function(){
					window.location.reload();
				});
			}			
		});
	});
});
</script>