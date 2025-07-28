<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<table class='css-default-table'>
	<tbody>
		<tr>
			<td class='w90 tr'><strong>活动名称：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['xianshi_name'];?></td>
			<td class='w90 tr'><strong>开始时间：</strong></td>
			<td class='w120 tl'><?php echo date('Y-m-d H:i', $output['xianshi_info']['start_time']);?></td>
			<td class='w90 tr'><strong>结束时间：</strong></td>
			<td class='w120 tl'><?php echo date('Y-m-d H:i', $output['xianshi_info']['end_time']);?></td>
			<td class='w90 tr'><strong>购买下限：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['lower_limit'];?></td>
			<td class='w90 tr'><strong>状态：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['xianshi_state_text'];?></td>
		</tr>
	</tbody>
</table>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w10'></th>
			<th class='w50'></th>
			<th class='tl'>商品名称</th>
			<th class='w90'>商品价格</th>
			<th class='w120'>折扣价格</th>
			<th class='w120'>折扣率</th>
		</tr>
	</thead>
	<tbody id='xianshi_goods_list'>
		<?php if (!empty($output['xianshi_goods_list'])) { ?>
		<?php foreach($output['xianshi_goods_list'] as $k => $v) { ?>
		<tr class='bd-line'>
			<td></td>
			<td><div class='pic-thumb'><img src='<?=$v['goods_image']?>' alt=''></div></td>
			<td class='tl'><dl class='goods-name'><dt><?=$v['goods_name']?></dt></dl></td>
			<td>￥<?=$v['goods_price']?></td>
			<td>￥<span><?=$v['xianshi_price']?></span></td>
			<td><span><?=number_format($v['xianshi_price'] / $v['goods_price'] * 10, 1)?></span></td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr id='xianshi_goods_list_norecord'>
			<td class='norecord' colspan='20'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无符合条件的数据记录</span></div></td>
		</tr>
		<?php } ?>
	</tbody>
</table>