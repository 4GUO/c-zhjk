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
<form method='get' action='<?=users_url('distribute_award/index')?>'>
	<table class='search-form'>
		<tbody><tr>
			<td>&nbsp;</td>
			<th> 
				<select name='search_type'>
					<option value='nickname' <?php if(input('get.search_type', '') == 'nickname'){?> selected='selected'<?php }?>>买家昵称</option>
					<option value='truename' <?php if(input('get.search_type', '') == 'truename'){?> selected='selected'<?php }?>>买家姓名</option>
					<option value='mobile' <?php if(input('get.search_type', '') == 'mobile'){?> selected='selected'<?php }?>>买家手机</option>
					<option value='goods_name' <?php if(input('get.search_type', '') == 'goods_name'){?> selected='selected'<?php }?>>商品名称</option>
				</select>
			</th>
			<td class='w160'><input class='text w150' name='keyword' value='<?php echo input('get.keyword', '');?>' type='text'></td>
			<th>状态</th>
			<th> 
				<select name='status'>
					<option value='-1'>全部</option>
                    <option value='<?php echo ORDER_STATE_CANCEL;?>'<?php echo input('get.status', -1, 'intval') == ORDER_STATE_CANCEL ? ' selected' : '';?>>已取消</option>
                    <option value='<?php echo ORDER_STATE_NEW;?>'<?php echo input('get.status', -1, 'intval') == ORDER_STATE_NEW ? ' selected' : '';?>>未付款</option>
                    <option value='<?php echo ORDER_STATE_PAY;?>'<?php echo input('get.status', -1, 'intval') == ORDER_STATE_PAY ? ' selected' : '';?>>已付款</option>
                    <option value='<?php echo ORDER_STATE_SEND;?>'<?php echo input('get.status', -1, 'intval') == ORDER_STATE_SEND ? ' selected' : '';?>>已发货</option>
                    <option value='<?php echo ORDER_STATE_SUCCESS;?>'<?php echo input('get.status', -1, 'intval') == ORDER_STATE_SUCCESS ? ' selected' : '';?>>已完成</option>
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
			<th colspan='2'>买家信息</th>
			<th colspan='2'>商品信息</th>
			<th class='w80'>数量</th>
			<th>奖金明细</th>
			<th class='w100'>状态</th>			
			<th class='w150'>时间</th>
			<th class='w80'>操作</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($output['list'])) { ?>
		<?php foreach ($output['list'] as $key => $val) {?>
		<tr>
			<td><?php echo $val['record_id'] ?></td>
			<td width='70' height='90'><?php if(!empty($output['member_list'][$val['buyer_id']]['headimg'])) {?><img src='<?php echo $output['member_list'][$val['buyer_id']]['headimg'];?>' width='60' style='width: 60px; border-radius: 50%' /><?php }?></td>
			<td style='text-align: left; width: 200px;'>
				<p class='member_info'><span>昵称：</span><?php if(!empty($output['member_list'][$val['buyer_id']]['nickname'])) {?><?php echo $output['member_list'][$val['buyer_id']]['nickname'];?><?php }?></p>
				<p class='member_info'><span>姓名：</span><?php if(!empty($output['member_list'][$val['buyer_id']]['truename'])) {?><?php echo $output['member_list'][$val['buyer_id']]['truename'];?><?php }?></p>
				<p class='member_info'><span>手机号：</span><?php if(!empty($output['member_list'][$val['buyer_id']]['mobile'])) {?><?php echo $output['member_list'][$val['buyer_id']]['mobile'];?><?php }?></p>				
			</td>
			<td width='70'><?php if(!empty($output['goods_info'][$val['goods_id']]['goods_image'])) {?><img src='<?php echo $output['goods_info'][$val['goods_id']]['goods_image'];?>' width='60' style='width: 60px;' /><?php }?></td>
			<td style='text-align: left; width: 180px;'><?php echo empty($output['goods_info'][$val['goods_id']]['goods_name']) ? '' : $output['goods_info'][$val['goods_id']]['goods_name']?></td>
			<td><?=$val['goods_num']?> 件</td>
			<td>
				<?php if(!empty($output['award_list'][$val['record_id']])){?>
				<?php foreach($output['award_list'][$val['record_id']] as $award){?>
				<p class='member_info'><?php if(!empty($output['member_list'][$award['uid']]['nickname'])) {?><?php echo $output['member_list'][$award['uid']]['nickname'];?><?php }?>&nbsp;&nbsp;<font style='color:red;'>&yen;<?php echo $award['bonus'];?></font></p>
				<?php }?>
				<?php }?>
			</td>
			<td>
				<?php if($val['record_status'] == ORDER_STATE_CANCEL){?>
				<font style='padding:8px 15px; color:#FFF; background:#999; border-radius:5px;'>已取消</font>
				<?php }elseif($val['record_status'] == ORDER_STATE_NEW){?>
                <font style='padding:8px 15px; color:#FFF; background:#D9534F; border-radius:5px;'>未付款</font>
                <?php }elseif($val['record_status'] == ORDER_STATE_PAY){?>
                <font style='padding:8px 15px; color:#FFF; background:#5CB85C; border-radius:5px;'>已付款</font>
                <?php }elseif($val['record_status'] == ORDER_STATE_SEND){?>
                <font style='padding:8px 15px; color:#FFF; background:#F60; border-radius:5px;'>已发货</font>
                <?php }else{?>
                <font style='padding:8px 15px; color:#FFF; background:#E41F16; border-radius:5px;'>已完成</font>
                <?php }?>
			</td>			
			<td class='goods-time'><?=date('Y-m-d H:i:s', $val['record_addtime'])?></td>			
			<td class='css-table-handle'>
				<span>
					<a href='<?=users_url('shop_order/show_order', array('order_sn' => $val['order_sn']))?>' class='btn-blue'><i class='icon-eye-open'></i>
						<p>相关订单</p>
					</a>
				</span>
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
</script>