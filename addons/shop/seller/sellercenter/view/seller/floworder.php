<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w80'>ID</th>
			<!--<th>商品名称</th>-->
			<th class='w120'>订单金额</th>
			<th class='w120'>实收金额</th>
			<th class='w120'>状态</th>
			<th class='w160'>添加时间</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) { ?>
		<tr class='bd-line'>
			<td class='bdl'><?php echo $val['id'];?></td>
			<td class='bdl'>&yen; <?php echo $val['total_money'];?></td>
			<td class='bdl'>&yen; <?php echo $val['commiss_money'];?></td>
			<td class='bdl'>
				<?php if($val['state'] == 0) {?>
                <font style='padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;'>待结算</font>
                <?php }elseif($val['state'] == 1) {?>
                <font style='padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;'>已结算</font>
                <?php }elseif($val['state'] == 2) {?>
                <font style='padding:8px 15px; color:#FFF; background:#F60; border-radius:5px;'>已取消</font>
                <?php }?>
			</td>
			<td class='bdl'><?php echo date('Y-m-d H:i:s',$val['addtime']);?></td>
			<td class='bdl bdr'>
				 <?php if ($val['type'] == 1) { ?>
				<p><a href='<?=_url('shop_order/show_order', array('order_id' => $val['order_id']))?>' class='css-btn css-btn-green mt5' /><i class='icon-pencil'></i>相关订单</a></p>
				<?php } else if ($val['type'] == 2) { ?>
				<p><a href='<?=_url('shop_vr_order/show_order', array('order_id' => $val['order_id']))?>' class='css-btn css-btn-green mt5' /><i class='icon-pencil'></i>相关订单</a></p>
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