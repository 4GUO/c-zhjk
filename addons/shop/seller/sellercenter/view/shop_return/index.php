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
.bdl{
	border-left: 1px solid #e6e6e6;
}
.bdr{
	border-right: 1px solid #e6e6e6;
}
</style>
<form method='get' action='<?=users_url('shop_return/index')?>'>
	<table class='search-form'>
		<tbody><tr>
			<td>&nbsp;</td>
			<th> 
				<select name='search_type'>
					<option value='nickname' <?php if(input('get.search_type', '') == 'nickname'){?> selected='selected'<?php }?>>昵称</option>
					<option value='truename' <?php if(input('get.search_type', '') == 'truename'){?> selected='selected'<?php }?>>姓名</option>
					<option value='mobile' <?php if(input('get.search_type', '') == 'mobile'){?> selected='selected'<?php }?>>手机</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?php echo input('get.keyword', '');?>' type='text'></td>
			<th class='w60'> 
				订单号
			</th>
			<td class='w160'><input class='text w150' name='order_sn' value='<?php echo input('get.order_sn', '');?>' type='text'></td>
			<th>状态</th>
				<td class='w80'>
					<select name='status'>
                        <option value='0'>全部</option>
                        <option value='1'<?php echo input('status', 0, 'intval') == 1 ? ' selected' : '';?>>处理中</option>
                        <option value='3'<?php echo input('status', 0, 'intval') == 3 ? ' selected' : '';?>>已退款</option>
                        <option value='4'<?php echo input('status', 0, 'intval') == 4 ? ' selected' : '';?>>已驳回</option>
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
	</tbody></table>
</form>
<table class='css-default-table' id='my_agent'>
	<thead>
		<tr>
			<th class='w100'>退款号</th>
			<th class='w100'>订单号</th>
			<th colspan='2'>买家信息</th>
			<th class='w80'>退款金额</th>
			<th class='w100'>退款理由</th>
			<th class='w100'>状态</th>
			<th class='w100'>时间</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr class='bd-line'>
			<td class='bdl'><?php echo $val['refund_sn'] ?></td>
			<td class='bdl'><?php echo $val['order_sn'] ?></td>
			<td class='bdl' width='70' height='90'><?php if(!empty($output['mapping_fans'][$val['buyer_id']]['headimg'])) {?><img src='<?php echo $output['mapping_fans'][$val['buyer_id']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 200px;'>
				<p class='member_info'><span>昵称：</span><?php if(!empty($output['mapping_fans'][$val['buyer_id']]['nickname'])) {?><?php echo $output['mapping_fans'][$val['buyer_id']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['member_list'][$val['buyer_id']]['truename'])) {?><?php echo $output['member_list'][$val['buyer_id']]['truename'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['member_list'][$val['buyer_id']]['mobile'])) {?><?php echo $output['member_list'][$val['buyer_id']]['mobile'];?><?php }?></p>				
			</td>
			<td class='orange bdl'>&yen; <?php echo $val['refund_amount'];?></td>
			<td class='bdl'><?php echo $val['buyer_message'];?></td>
			<td class='bdl'><?php echo $val['state_text'];?><?php if($val['refund_state']==4){?><br />(理由：<font style='color: #e33e33'><?php echo $val['admin_message'];?></font>)<?php }?></td>
			<td class='bdl goods-time'><?=date('Y-m-d H:i:s', $val['add_time'])?></td>
			<td class='bdl bdr' id='btns_<?php echo $val['refund_id']; ?>'>
				<p><a href='<?=users_url('shop_order/show_order', array('order_id' => $val['order_id']))?>' class='css-btn css-btn-orange mt5'><i class='icon-truck'></i>相关订单</a></p> 
				<?php if($val['refund_state']==1){?>				
                <p><a href='javascript:void(0)' class='css-btn css-btn-green mt5' nctype='pay_record' data-id='<?php echo $val['refund_id']; ?>' /><i class='icon-pencil'></i>在线退款</a></p>
				<p><a href='javascript:void(0)' class='css-btn css-btn-green mt5 delete' confirm='确定要退款?' url='<?=users_url('shop_return/pay_record', array('refund_id' => $val['refund_id'], 'refund_type' => 'offline'))?>' /><i class='icon-pencil'></i>线下退款</a></p>
                <p><a href='javascript:void(0)' class='css-btn css-btn-red mt5' nc_type='dialog' uri='<?=users_url('shop_return/reject', array('refund_id' => $val['refund_id']))?>' dialog_title='退款申请驳回' dialog_id='cancel_reocrd' dialog_width='400' id='reocrd<?php echo $val['refund_id']; ?>' /><i class='icon-remove-circle'></i>驳回申请</a></p>
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
	$('a[nctype=pay_record]').on('click', '', function(){
		if(num == 1){
			showError('已有任务在执行，请耐心等待');
		}
		num = 1;
		var rid = $(this).attr('data-id');
		$('#btns_' + rid).html('');
		getAjax('<?=users_url('shop_return/pay_record')?>', {refund_id: rid}, function(e){
			if(e.state == 400) {
				showError(e.msg, function() {
				    window.location.reload();
				});
			} else {
			    if (e.data.pay_url) {
			        location.href = e.data.pay_url;
			    } else {
			        showSucc(e.msg, function(){
    				    window.location.reload();
    				});
			    }
			}			
		});
	});
});
</script>