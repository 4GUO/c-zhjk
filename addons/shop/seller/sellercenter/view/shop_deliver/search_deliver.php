<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-flow-layout'>
	<div class='css-flow-container'>
		<div class='title'>
			<h3>物流详情</h3>
		</div>
		<div class='alert alert-block alert-info'>
			<ul>
				<li><strong>收货信息：</strong><?php echo $output['order_info']['extend_order_common']['reciver_name']?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?></li>
				<li><strong>买家留言：</strong><?php echo $output['order_info']['extend_order_common']['order_message']; ?></li>
				<?php if(!empty($output['daddress_info'])){?>
				<li><strong>发货信息：</strong><?php echo $output['daddress_info']['seller_name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['daddress_info']['telphone'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['daddress_info']['area_info'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['daddress_info']['address'];?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $output['daddress_info']['company'];?></li>
				<?php }?>
			</ul>
		</div>
		<div class='tabmenu'>
			<ul class='tab'>
				<li class='active'><a href='javascript:void(0);'>物流动态</a></li>
			</ul>
		</div>
		<?php if(!empty($output['shipping_express'])) { ?>
		<ul class='express-log' id='express_list'>
		<?php foreach($output['shipping_express'] as $log_info){?>
			<li><?php echo $log_info;?></li>
		<?php }?>
		</ul>
		<?php } else { ?>
		<ul class='express-log' id='express_list'>
			<li><?=$output['shipping_error']?></li>
		</ul>
		<?php } ?>
	</div>
	<div class='css-flow-item'>
		<div class='title'>订单信息</div>
		<div class='item-goods'>
			<?php if(is_array($output['order_info']['extend_order_goods']) && !empty($output['order_info']['extend_order_goods'])) { foreach($output['order_info']['extend_order_goods'] as $goods) { ?>
			<dl>
				<dt>
					<div class='css-goods-thumb-mini'><a href='javascript:void(0);'><img src='<?php echo $goods['goods_image']; ?>'/></a></div>
				</dt>
				<dd><a href='javascript:void(0);'><?php echo $goods['goods_name']; ?></a><span class='rmb-price'><i class='mr5'><?php echo $goods['goods_price']; ?></i>&nbsp;*&nbsp;<?php echo $goods['goods_num']; ?></span></dd>
				</dt>
			</dl>
			<?php } } ?>
		</div>
		<div class='item-order'>
			<dl>
				<dt>运费：</dt>
				<dd><?php echo $output['order_info']['shipping_fee'];?></dd>
			</dl>
			<dl>
				<dt>订单总额：</dt>
				<dd><strong>&yen;<?php echo $output['order_info']['order_amount'];?></strong></dd>
			</dl>
			<dl>
				<dt>订单编号：</dt>
				<dd><a href='javascript:void(0);'><?php echo $output['order_info']['order_sn']; ?></a><a href='javascript:void(0);' class='a'>更多<i class='icon-angle-down'></i>
					<div class='more'> <span class='arrow'></span>
						<ul>
							<li>支付方式：<span><?php echo $output['order_info']['payment_name']; ?>
								<?php if($output['order_info']['payment_code'] != 'offline' && !in_array($output['order_info']['order_state'],array(ORDER_STATE_CANCEL,ORDER_STATE_NEW))) { ?>
								(付款单号：<?php echo $output['order_info']['trade_no']; ?>)
								<?php } ?>
								</span> </li>
							<li>下单时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']);?></span></li>
							<?php if(intval($output['order_info']['payment_time'])) { ?>
							<li>付款时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['payment_time']); ?></span></li>
							<?php } ?>
							<?php if($output['order_info']['extend_order_common']['shipping_time']) { ?>
							<li>发货时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['extend_order_common']['shipping_time']); ?></span></li>
							<?php } ?>
							<?php if(intval($output['order_info']['finnshed_time'])) { ?>
							<li>完成时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['finnshed_time']); ?></span></li>
							<?php } ?>
						</ul>
					</div>
					</a></dd>
			</dl>
			<dl>
				<dt>物流单号：</dt>
				<dd><?php echo $output['order_info']['shipping_code']; ?><a href='<?php echo $output['e_url'];?>' class='a' target='_blank'><?php echo $output['e_name'];?></a></dd>
			</dl>
		</div>
	</div>
</div>
