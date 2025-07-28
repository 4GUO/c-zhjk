<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w30'></th>
			<th class='tl'>活动名称</th>
			<th class='w250'>开始时间&nbsp;-&nbsp;结束时间</th>
			<th class='w400'>活动内容</th>
			<th class='w110'>状态</th>
		</tr>
	</thead>
	<tbody>
		<tr class='bd-line'>
			<td></td>
			<td class='tl'>
				<dl class='goods-name'>
					<dt><?php echo $output['mansong_info']['mansong_name'];?></dt>
				</dl>
			</td>
			<td>
				<p><?php echo date('Y-m-d H:i', $output['mansong_info']['start_time']);?></p>
				<p>至</p>
				<p><?php echo date('Y-m-d H:i', $output['mansong_info']['end_time']);?></p>
			</td>
			<td>
				<ul class='css-mansong-rule-list'>
					<?php if(!empty($output['list']) && is_array($output['list'])) { ?>
					<?php foreach($output['list'] as $key => $val) { ?>
					<li> 
						单笔订单满<strong><?php echo $val['price'];?></strong>元，&nbsp;
						<?php if(!empty($val['discount'])) { ?>
						立减现金<strong><?php echo $val['discount'];?></strong>元，&nbsp;
						<?php } ?>
						<?php if(empty($val['goods_id'])) { ?>
						
						<?php } else { ?>
						送礼品<a href='<?php echo $val['goods_url'];?>' title='<?php echo $val['mansong_goods_name'];?>' class='goods-thumb'> <img src='<?php echo $val['goods_image'];?>'/> </a>
						<?php } ?>
					</li>
					<?php }?>
					<?php }?>
				</ul>
			</td>
			<td><?php echo $output['mansong_info']['mansong_state_text'];?></td>
		</tr>
	<tbody>
</table>
