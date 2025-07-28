<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-oredr-show'>
	<div class='css-order-info'>
		<div class='css-order-details'>
			<div class='title'>订单信息</div>
			<div class='content'>
				<dl>
					<dt>收  货  人：</dt>
					<dd><?php echo $output['order_info']['extend_order_common']['reciver_name'];?>&nbsp; <?php echo $output['order_info']['extend_order_common']['reciver_info']['tel_phone'];?>&nbsp; <?php echo $output['order_info']['extend_order_common']['reciver_info']['address'];?><?php echo !empty($output['order_info']['extend_order_common']['reciver_info']['dlyp']) ? '[自提服务站]' : '';?></dd>
				</dl>
				<dl style='display: none'>
					<dt>发&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;票：</dt>
					<dd>
						<?php foreach ((array)$output['order_info']['extend_order_common']['invoice_info'] as $key => $value){?>
						<span><?php echo $key;?> (<strong><?php echo $value;?></strong>)</span>
						<?php } ?>
					</dd>
				</dl>
				<dl>
					<dt>买家留言：</dt>
					<dd><?php echo $output['order_info']['extend_order_common']['order_message']; ?></dd>
				</dl>
				<dl class='line'>
					<dt>订单编号：</dt>
					<dd><?php echo $output['order_info']['order_sn']; ?><a href='javascript:void(0);'>更多<i class='icon-angle-down'></i>
						<div class='more'><span class='arrow'></span>
							<ul>
								<?php if($output['order_info']['payment_code']) { ?>
								<li>支付方式：<span><?php echo $output['order_info']['payment_code']; ?>
									<?php if($output['order_info']['payment_code'] != 'offline' && !in_array($output['order_info']['order_state'],array(ORDER_STATE_CANCEL,ORDER_STATE_NEW))) { ?>
									(付款单号：<?php echo $output['order_info']['pay_sn']; ?>)
									<?php } ?>
									</span></li>
								<?php } ?>
								<li>下单时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']); ?></span></li>
								<?php if(intval($output['order_info']['payment_time'])) { ?>
								<li>支付时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['payment_time']); ?></span></li>
								<?php } ?>
								<?php if($output['order_info']['extend_order_common']['shipping_time']) { ?>
								<li>发货时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['extend_order_common']['shipping_time']); ?></span></li>
								<li>发货备注：<span><?php echo $output['order_info']['extend_order_common']['deliver_explain']; ?></span></li>
								<?php } ?>
								<?php if(intval($output['order_info']['finnshed_time'])) { ?>
								<li>完成时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['finnshed_time']); ?></span></li>
								<?php } ?>
							</ul>
						</div>
						</a></dd>
				</dl>
				<dl>
					<dt></dt>
					<dd></dd>
				</dl>
			</div>
		</div>
		<?php if ($output['order_info']['order_state'] == ORDER_STATE_CANCEL) { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-off orange'></i>订单状态：</dt>
				<dd>交易关闭</dd>
			</dl>
			<ul>
				<li><?php echo $output['order_info']['close_info']['log_role'];?> 于 <?php echo date('Y-m-d H:i:s',$output['order_info']['close_info']['log_time']);?> <?php echo $output['order_info']['close_info']['log_msg'];?></li>
			</ul>
		</div>
		<?php } ?>
		<?php if ($output['order_info']['order_state'] == ORDER_STATE_NEW) { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-ok-circle green'></i>订单状态：</dt>
				<dd>订单已经提交，等待买家付款</dd>
			</dl>
			<ul>
				<li>1. 买家尚未对该订单进行支付。</li>
				<li>2. 如果买家未对该笔订单进行支付操作，系统将于
					<time><?php echo date('Y-m-d H:i:s',$output['order_info']['order_cancel_day']);?></time>
					自动关闭该订单。</li>
			</ul>
		</div>
		<?php } ?>
		<?php if ($output['order_info']['order_state'] == ORDER_STATE_PAY) { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-ok-circle green'></i>订单状态：</dt>
				<dd>
					<?php if ($output['order_info']['payment_code'] == 'offline') { ?>
					订单已提交，等待发货
					<?php } else { ?>
					已支付成功
					<?php } ?>
				</dd>
			</dl>
			<ul>
				<?php if ($output['order_info']['payment_code'] == 'offline') { ?>
				<li>1. 买家已经选择货到付款方式下单成功。</li>
				<li>2. 订单已提交商家进行备货发货准备。</li>
				<?php } else { ?>
				<li>1. 买家已使用“<?php echo orderPaymentName($output['order_info']['payment_code']);?>”方式成功对订单进行支付，支付单号 “<?php echo $output['order_info']['trade_no'];?>”。</li>
				<li>2. 订单已提交商家进行备货发货准备。</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
		<?php if ($output['order_info']['order_state'] == ORDER_STATE_SEND) { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-ok-circle green'></i>订单状态：</dt>
				<dd>已发货</dd>
			</dl>
			<ul>
				<li>1. 商品已发出；
					<?php if ($output['order_info']['shipping_code'] != '') { ?>
					物流公司：<?php echo $output['order_info']['express_info']['e_name']?>；单号：<?php echo $output['order_info']['shipping_code'];?>。
					<?php if ($output['order_info']['if_deliver']) { ?>
					查看 <a href='<?=users_url('shop_deliver/search_deliver', array('order_sn' => $output['order_info']['order_sn']))?>' class='blue'>“物流跟踪”</a> 情况。
					<?php } ?>
					<?php } else { ?>
					无需要物流。
					<?php } ?>
				</li>
				<li>2. 如果买家没有及时进行收货，系统将于
					<time><?php echo date('Y-m-d H:i:s',$output['order_info']['order_confirm_day']);?></time>
					自动完成“确认收货”，完成交易。</li>
			</ul>
		</div>
		<?php } ?>
		<?php if ($output['order_info']['order_state'] == ORDER_STATE_SUCCESS) { ?>
		<?php if (!empty($output['order_info']['evaluation_state'])) { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-ok-circle green'></i>订单状态：</dt>
				<dd>评价完成。</dd>
			</dl>
			<ul>
				<li>买家已对该笔订单进行了商品及交易评价。</li>
			</ul>
		</div>
		<?php } else { ?>
		<div class='css-order-condition'>
			<dl>
				<dt><i class='icon-ok-circle green'></i>订单状态：</dt>
				<dd>已经收货。</dd>
			</dl>
			<ul>
				<li>1. 交易已完成，买家可以对购买的商品及服务进行评价。</li>
				<li>2. 评价后的情况会在商品详细页面中显示，以供其它会员在购买时参考。</li>
			</ul>
		</div>
		<?php } ?>
		<?php } ?>
		<div class='css-order-condition'>
		<?php if ($output['order_info']['trade_no']) { ?>
		(付款单号：<?php echo $output['order_info']['trade_no']; ?>)
		<?php } ?>
		</div>
	</div>
	<?php if ($output['order_info']['order_state'] != ORDER_STATE_CANCEL) { ?>
	<div id='order-step' class='css-order-step'>
		<dl class='step-first <?php if ($output['order_info']['order_state'] != ORDER_STATE_CANCEL) echo 'current';?>'>
			<dt>提交订单</dt>
			<dd class='bg'></dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']); ?></dd>
		</dl>
		<?php if ($output['order_info']['payment_code'] != 'offline') { ?>
		<dl class='<?php if(intval($output['order_info']['payment_time'])) echo 'current'; ?>'>
			<dt>支付订单</dt>
			<dd class='bg'> </dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s',$output['order_info']['payment_time']); ?></dd>
		</dl>
		<?php } ?>
		<dl class='<?php if($output['order_info']['extend_order_common']['shipping_time']) echo 'current'; ?>'>
			<dt>商家发货</dt>
			<dd class='bg'> </dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s',$output['order_info']['extend_order_common']['shipping_time']); ?></dd>
		</dl>
		<dl class='<?php if(intval($output['order_info']['finnshed_time'])) { ?>current<?php } ?>'>
			<dt>确认收货</dt>
			<dd class='bg'> </dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s',$output['order_info']['finnshed_time']); ?></dd>
		</dl>
		<dl class='<?php if(!empty($output['order_info']['evaluation_state'])) { ?>current<?php } ?>'>
			<dt>评价</dt>
			<dd class='bg'></dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s',$output['order_info']['evaluation_time']); ?></dd>
		</dl>
	</div>
	<?php } ?>
	<div class='css-order-contnet'>
		<table class='css-default-table order'>
			<thead>
				<tr>
					<th class='w10'>&nbsp;</th>
					<th colspan='2'>商品名称</th>
					<th class='w120'>单价(元)</th>
					<th class='w60'>数量</th>
					<th>交易操作</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($output['order_info']['shipping_code'])) { ?>
				<tr>
					<th colspan='5' style='border-right: 0;'>
						<div class='order-deliver'>
							<span>物流公司： <a target='_blank' href='<?php echo $output['order_info']['express_info']['e_url'];?>'><?php echo $output['order_info']['express_info']['e_name'];?></a></span>
							<span>物流单号： <?php echo $output['order_info']['shipping_code']; ?></span>
						</div>
					</th>
					<th style=' border-left: 0;'><?php if(!empty($output['daddress_info'])) { ?>
						<dl class='daddress-info'>
							<dt>发&nbsp;&nbsp;货&nbsp;&nbsp;人：</dt>
							<dd><?php echo $output['daddress_info']['seller_name']; ?><a href='javascript:void(0);'>更多<i class='icon-angle-down'></i>
								<div class='more'><span class='arrow'></span>
									<ul>
										<li>公&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;司：<span><?php echo $output['daddress_info']['company'];?></span></li>
										<li>联系电话：<span><?php echo $output['daddress_info']['telphone'];?></span></li>
										<li>发货地址：<span><?php echo $output['daddress_info']['area_info'];?>&nbsp;<?php echo $output['daddress_info']['address'];?></span></li>
									</ul>
								</div>
								</a></dd>
						</dl>
						<?php } ?>
					</th>
				</tr>
				<?php } ?>
				<?php $i = 0;?>
				<?php foreach($output['order_info']['extend_order_goods'] as $k => $goods) { ?>
				<?php $i++;?>
				<tr class='bd-line'>
					<td>&nbsp;</td>
					<td class='w70'><div class='css-goods-thumb'><img src='<?=$goods['goods_image']?>' style='max-width:100%; max-height: 100%;' /></div></td>
					<td class='tl'><dl class='goods-name'>
							<dt><?php echo $goods['goods_name']; ?></dt>
							<dd>
								<?php if (!empty($output['refund_all']) && is_array($output['refund_all'])) {?>
								退款单号：<a target='_blank' href='index.php?act=store_refund&op=view&refund_id=<?php echo $output['refund_all']['refund_id'];?>'><?php echo $output['refund_all']['refund_sn'];?></a>
								<?php }else if(isset($goods['extend_refund']['refund_type']) && $goods['extend_refund']['refund_type'] == 1) {?>
								退款单号：<a target='_blank' href='index.php?act=store_refund&op=view&refund_id=<?php echo $goods['extend_refund']['refund_id'];?>'><?php echo $goods['extend_refund']['refund_sn'];?></a></dd>
							<?php }else if(isset($goods['extend_refund']['refund_type']) && $goods['extend_refund']['refund_type'] == 2) {?>
							退货单号：<a target='_blank' href='index.php?act=store_return&op=view&return_id=<?php echo $goods['extend_refund']['refund_id'];?>'><?php echo $goods['extend_refund']['refund_sn'];?></a>
							</dd>
							<?php } ?>
						</dl></td>
					<td>
						<?php echo $goods['goods_price']; ?>
						<p class='green'>
							<?php if (!empty($output['refund_all']) && is_array($output['refund_all']) && $output['refund_all']['admin_time'] > 0) {?>
							<?php echo $goods['goods_pay_price'];?><span>退</span>
							<?php } elseif (isset($goods['extend_refund']['admin_time']) && $goods['extend_refund']['admin_time'] > 0) { ?>
							<?php echo $goods['extend_refund']['refund_amount'];?><span>退</span>
							<?php } ?>
						</p>
					</td>
					<td><?php echo $goods['goods_num']; ?></td>
					<?php if (($output['order_info']['goods_count'] > 1 && $k ==0) || ($output['order_info']['goods_count'] == 1)){?>
					<td class='bdl bdr' rowspan='<?php echo $output['order_info']['goods_count'];?>'>
						<?php if ($output['order_info']['if_lock']) { ?>
						<p><?php if(empty($output['refund_state'])){?>退款中<?php } else {?>退款<?php echo $output['refund_state'];?><?php }?></p>
						<?php } else {?>
						<?php echo $output['order_info']['state_desc']; ?>
						<?php }?>
						
						<!-- 修改价格 -->
						
						<?php if ($output['order_info']['if_modify_price']) { ?>
						<p><a href='javascript:void(0)' class='css-btn' uri='<?=users_url('shop_order/shipping_price', array('order_id' => $output['order_info']['order_id'], 'order_sn' => $output['order_info']['order_sn']))?>' dialog_width='480' dialog_title='修改运费' nc_type='dialog'  dialog_id='seller_order_adjust_fee' id='order<?php echo $output['order_info']['order_id']; ?>_action_adjust_fee' />修改运费</a></p>
						<?php }?>
						
						<!-- 取消订单 -->
						
						<?php if ($output['order_info']['if_cancel']) { ?>
						<p><a href='javascript:void(0)' style='color:#F30; text-decoration:underline;' nc_type='dialog' uri='<?=users_url('shop_order/order_cancel', array('order_id' => $output['order_info']['order_id'], 'order_sn' => $output['order_info']['order_sn']))?>' dialog_title='取消订单' dialog_id='seller_order_cancel_order' dialog_width='400' id='order<?php echo $output['order_info']['order_id']; ?>_action_cancel' />取消订单</a></p>
						<?php } ?>
						
						<!-- 发货 -->
						
						<?php if ($output['order_info']['if_send']) { ?>
						<p><a class='css-btn' href='<?=users_url('shop_deliver/send', array('order_id' => $output['order_info']['order_id']))?>'/><i class='icon-truck'></i>发货</a></p>
						<?php } ?>
						
						<?php if ($output['order_info']['order_state'] == ORDER_STATE_SEND) { ?>
						<p><a href='javascript:void(0)' class='css-btn-mini css-btn-orange ml5' uri='<?=users_url('shop_deliver/delay_receive', array('order_id' => $output['order_info']['order_id']))?>' dialog_width='480' dialog_title='延迟收货' nc_type='dialog' dialog_id='seller_order_delay_receive' id='order<?php echo $output['order_info']['order_id'];?>_action_delay_receive' /><i class='icon-time'></i></i>延迟收货</a></p>
						<?php } ?>
					</td>
					<?php } ?>
					<!-- E 合并TD --> 
				</tr>
				
				<!-- S 赠品列表 -->
				<?php if (!empty($output['order_info']['extend_order_common']['zengpin_list']) && $i == count($output['order_info']['extend_order_goods'])) { ?>
				<tr class='bd-line'>
					<td>&nbsp;</td>
					<td colspan='6' class='tl'><div class='css-goods-gift'>赠品：
							<ul>
								<?php foreach($output['order_info']['extend_order_common']['zengpin_list'] as $zengpin_info) {?>
								<li><a title='赠品：<?php echo $zengpin_info['goods_name'];?> * <?php echo $zengpin_info['goods_num'];?>' href='javascript:;'><img src='<?php echo $zengpin_info['goods_image']; ?>' /></a></li>
								<?php } ?>
							</ul>
						</div></td>
				</tr>
				<?php } ?>
				<!-- E 赠品列表 -->
				
				<?php } ?>
			</tbody>
			<tfoot>
				<?php if(!empty($output['order_info']['extend_order_common']['promotion_info']) || !empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
				<tr>
					<td colspan='20'><dl class='css-store-sales'>
							<dt>其它信息：</dt>
							<?php if(!empty($output['order_info']['extend_order_common']['promotion_info'])){ ?>
							<dd><?php echo $output['order_info']['extend_order_common']['promotion_info'];?></dd>
							<?php } ?>
							<?php if(!empty($output['order_info']['extend_order_common']['voucher_code'])){ ?>
							<dd>使用了面额为 <?php echo $output['order_info']['extend_order_common']['voucher_price'];?> 元的代金券，编码：<?php echo $output['order_info']['extend_order_common']['voucher_code'];?></span></dd>
							<?php } ?>
						</dl></td>
				</tr>
				<?php } ?>
				<tr>
					<td colspan='20'>
						<dl class='freight'>
							<dd>
								<?php if(!empty($output['order_info']['shipping_fee']) && $output['order_info']['shipping_fee'] != '0.00'){ ?>
								运费: <span>&yen;<?php echo $output['order_info']['shipping_fee']; ?></span>
								<?php }else{?>
								不需运费
								<?php }?>
								<?php if(!empty($output['order_info']['refund_amount'])) { ?>
								(退款金额：&yen;<?php echo $output['order_info']['refund_amount'];?>)
								<?php } ?>
							</dd>
						</dl>
						<dl class='sum'>
							<dt>订单金额：</dt>
							<dd><em><?php echo $output['order_info']['order_amount']; ?></em>元</dd>
						</dl>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
