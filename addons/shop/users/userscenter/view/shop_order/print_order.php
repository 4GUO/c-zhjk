<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link href='<?=STATIC_URL?>/admin/css/global.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
<link href='<?=STATIC_URL?>/admin/css/userscenter.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
<style type='text/css'>
body {
	background: #FFF none;
}
</style>
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/print_order/jquery.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/print_order/common.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/print_order/jquery.poshytip.min.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/print_order/jquery.printarea.js' charset='utf-8'></script>
<title>打印--<?php echo $output['order_info']['store_name'];?>发货单</title>
</head>

<body>
<?php if (!empty($output['order_info'])){?>
<div class='print-layout'>
	<div class='print-btn' id='printbtn' title='选择喷墨或激光打印机<br/>根据下列纸张描述进行<br/>设置并打印发货单据'><i></i><a href='javascript:void(0);'>打印</a></div>
	<div class='a5-size'></div>
	<dl class='a5-tip'>
		<dt>
			<h1>A5</h1>
			<em>Size: 210mm x 148mm</em>
		</dt>
		<dd>当打印设置选择A5纸张、横向打印、无边距时每张A5打印纸可输出1页订单。</dd>
	</dl>
	<div class='a4-size'></div>
	<dl class='a4-tip'>
		<dt>
			<h1>A4</h1>
			<em>Size: 210mm x 297mm</em></dt>
		<dd>当打印设置选择A4纸张、竖向打印、无边距时每张A4打印纸可输出2页订单。</dd>
	</dl>
	<div class='print-page'>
		<div id='printarea'>
			<?php foreach ($output['goods_list'] as $item_k => $item_v){?>
			<div class='orderprint'>
				<div class='top'>
					<div class='full-title'><?php echo $output['order_info']['store_name'];?> 发货单</div>
				</div>
				<table class='buyer-info'>
					<tr>
						<td class='w200'>收货人：<?php echo $output['order_info']['extend_order_common']['reciver_name'];?></td>
						<td>电话：<?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan='3'>地址：<?php echo @$output['order_info']['extend_order_common']['reciver_info']['address'];?></td>
					</tr>
					<tr>
						<td>订单号：<?php echo $output['order_info']['order_sn'];?></td>
						<td>下单时间：<?php echo @date('Y-m-d',$output['order_info']['add_time']);?></td>
						<td><?php if (!empty($output['order_info']['shippin_code'])){?>
							<span>物流单号：<?php echo $output['order_info']['shipping_code'];?></span>
							<?php }?></td>
					</tr>
				</table>
				<table class='order-info'>
					<thead>
						<tr>
							<th class='w40'>序号</th>
							<th class='tl'>商品名称</th>
							<th class='w70 tl'>单价(元)</th>
							<th class='w50'>数量</th>
							<th class='w70 tl'>小计(元)</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($item_v as $k=>$v){?>
						<tr>
							<td><?php echo $k;?></td>
							<td class='tl'><?php echo $v['goods_name'];?></td>
							<td class='tl'><?php echo '&yen;'.$v['goods_price'];?></td>
							<td><?php echo $v['goods_num'];?></td>
							<td class='tl'><?php echo '&yen;'.$v['goods_all_price'];?></td>
						</tr>
						<?php }?>
						<tr>
							<th></th>
							<th colspan='2' class='tl'>合计</th>
							<th><?php echo $output['goods_all_num'];?></th>
							<th class='tl'><?php echo '&yen;'.$output['goods_total_price'];?></th>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan='10'><span>总计：<?php echo '&yen;'.$output['goods_total_price'];?></span><span>运费：<?php echo '&yen;'.$output['order_info']['shipping_fee'];?></span><span>优惠：<?php echo '&yen;'.$output['promotion_amount'];?></span><span>订单总额：<?php echo '&yen;'.$output['order_info']['order_amount'];?></span><span>店铺：<?php echo $output['order_info']['store_name'];?></span>
							</th>
						</tr>
					</tfoot>
				</table>
				
				<?php if (!empty($output['order_info']['extend_order_common']['order_message'])) { ?>
				<div class='user-remark'>
					<strong>用户备注：</strong><?php echo $output['order_info']['extend_order_common']['order_message']; ?>
				</div>
				<?php } ?>
				
				<div class='tc page'>第<?php echo $item_k;?>页/共<?php echo count($output['goods_list']);?>页</div>
			</div>
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>
</body>
<script>
$(function(){
	$('#printbtn').click(function(){
		$('#printarea').printArea();
	});
});

//打印提示
$('#printbtn').poshytip({
	className: 'tip-yellowsimple',
	showTimeout: 1,
	alignTo: 'target',
	alignX: 'center',
	alignY: 'bottom',
	offsetY: 5,
	allowTipHover: false
});
</script>
</html>