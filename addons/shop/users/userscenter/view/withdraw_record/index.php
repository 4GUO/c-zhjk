<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.bdl{
	border-left: 1px solid #e6e6e6;
}
.bdr{
	border-right: 1px solid #e6e6e6;
}
</style>
<form method='get' action='<?=users_url('withdraw_record/index')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>会员昵称</th>
				<td class='w180'><input class='text w150' name='keyword' value='<?=input('keyword', '')?>' type='text' /></td>
				<th>提现方式</th>
				<td class='w100'>
                    <select name='code'>
                        <option value=''>全部</option>
                        <?php foreach($output['method_list'] as $item){?>
                        <option value='<?php echo $item['method_code'];?>'<?php echo input('code', '')==$item['method_code'] ? ' selected' : '';?>><?php echo $item['method_name'];?></option>
                        <?php }?>
                    </select>
                </td>
				<th>状态</th>
				<td class='w100'>
					<select name='status'>
                        <option value='0'>全部</option>
                        <option value='1'<?php echo input('status', 0, 'intval') == 1 ? ' selected' : '';?>>待处理</option>
                        <option value='2'<?php echo input('status', 0, 'intval') == 2 ? ' selected' : '';?>>已执行</option>
                        <option value='3'<?php echo input('status', 0, 'intval') == 3 ? ' selected' : '';?>>已驳回</option>
                    </select>
				</td>
				<th>提现时间</th>
				<td class='w240'>
					<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					&nbsp;–&nbsp;
					<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</td>
				<td class='w70 tc'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit' />
					</label>
				</td>
				<td class='tc w70'>
					<label class='submit-border'>
						<a class='submit' id='export' href='javascript:;'>导出</a>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<table class='css-default-table' id='withdraw_record'>
	<thead>
		<tr>
			<th class='tl' colspan='2'>会员信息</th>
			<th class='w80'>总额</th>
			<th class='w80'>手续费</th>
			<th class='w80'>转入余额</th>
			<th class='w80'>实转金额</th>
			<th class='w200'>提现方式</th>
			<th class='w100'>状态</th>
			<th class='w150'>提现时间</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line'>
			<td width='70' height='90' class='bdl'><?php if(!empty($output['member_list'][$val['uid']]['headimg'])) {?><img src='<?php echo $output['member_list'][$val['uid']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 200px;'>
				<p class='member_info'><span>昵称：</span><?php if(!empty($output['member_list'][$val['uid']]['nickname'])) {?><?php echo $output['member_list'][$val['uid']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['member_list'][$val['uid']]['truename'])) {?><?php echo $output['member_list'][$val['uid']]['truename'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['member_list'][$val['uid']]['mobile'])) {?><?php echo $output['member_list'][$val['uid']]['mobile'];?><?php }?></p>
			</td>
			<td class='bdl'>&yen; <?php echo $val['record_total'];?></td>
			<td class='bdl'>&yen; <?php echo $val['record_fee'];?></td>
			<td class='bdl'>&yen; <?php echo $val['record_yue'];?></td>
			<td class='bdl'>&yen; <?php echo $val['record_amount'];?></td>
			<td class='bdl'>
				<?php echo $val['method_title'];?>
				<?php if(!in_array($val['method_code'],array('wxzhuanzhang','wxhongbao','yue'))){?>
				<br /><?php echo $val['method_name']?><br /><?php echo $val['method_no']?>
				<?php if($val['method_code']!='alipay'){?>
				<br /><?php echo $val['method_bank']?><br /><?php echo $val['method_position']?>
				<?php }?>
				<?php }?>
			</td>
			<td class='bdl'>
				<?php if($val['record_status']==0){?>
                <font style='padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;'>待处理</font>
                <?php }elseif($val['record_status']==1){?>
                <font style='padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;'>已执行</font>
                <?php }elseif($val['record_status']==2){?>
                <font style='padding:8px 15px; color:#FFF; background:#F60; border-radius:5px;'>已驳回</font>
                <?php }?>
			</td>
			<td class='bdl'><?php echo date('Y-m-d H:i:s',$val['record_addtime']);?></td>
			<td class='bdl bdr' id='btns_<?php echo $val['record_id']; ?>'>
				<?php if($val['record_status']==0){?>
				<?php if($val['method_code']=='wxzhuanzhang'){?>
                <p><a href='javascript:void(0)' class='css-btn css-btn-green mt5' nctype='wxpay_record' data-id='<?php echo $val['record_id']; ?>' /><i class='icon-pencil'></i>微信转账</a></p>
                <?php }else{?>
                <p><a href='javascript:void(0)' confirm='确定处理该记录么?' url='<?=users_url('withdraw_record/deal', array('record_id' => $val['record_id']))?>' class='css-btn css-btn-green mt5 delete' /><i class='icon-pencil'></i>处理记录</a></p>
                <?php }?>
                <p><a href='javascript:void(0)' class='css-btn css-btn-red mt5' nc_type='dialog' uri='<?=users_url('withdraw_record/reject', array('record_id' => $val['record_id']))?>' dialog_title='提现申请驳回' dialog_id='cancel_reocrd' dialog_width='400' id='reocrd<?php echo $val['record_id']; ?>' /><i class='icon-remove-circle'></i>驳回申请</a></p>
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
var num = 0;
$(function(){
	$('a[nctype=wxpay_record]').on('click', '', function(){
		if(num == 1){
			showError('已有任务在执行，请耐心等待');
		}
		num = 1;
		var rid = $(this).attr('data-id');
		$('#btns_' + rid).html('');
		getAjax('<?=users_url('withdraw_record/pay_record')?>', {record_id: rid}, function(e){			
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
$('#export').click(function() {
	var url = '<?=users_url('withdraw_record/index')?>?<?php echo $_SERVER['QUERY_STRING'];?>&is_export=1';
	var totalpage = <?=$output['totalpage']?>;
	var i = 1;
	let t = setInterval(function () {
		url = url + '&export_page=' + i;
		location.href = url;
		if (i == totalpage) {
			clearInterval(t);
			return;
		}
		i++;
	}, 2000);
});
</script>