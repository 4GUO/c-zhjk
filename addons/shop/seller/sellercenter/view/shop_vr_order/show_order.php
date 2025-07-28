<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-oredr-show'>
	<div class='css-order-info'>
		<div class='css-order-details'>
			<div class='title'>订单信息</div>
			<div class='content'>
				<dl>
					<dt>买家留言：</dt>
					<dd><?php echo $output['order_info']['buyer_msg']; ?></dd>
				</dl>
				<dl class='line'>
					<dt>订单编号：</dt>
					<dd><?php echo $output['order_info']['order_sn']; ?><a href='javascript:void(0);'>更多<i class='icon-angle-down'></i>
						<div class='more'><span class='arrow'></span>
							<ul>
								<?php if($output['order_info']['payment_code']) { ?>
								<li>支付方式：<span><?php echo $output['order_info']['payment_code']; ?></span></li>
								<?php } ?>
								<li>下单时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['add_time']); ?></span></li>
								<?php if(intval($output['order_info']['payment_time'])) { ?>
								<li>支付时间：<span><?php echo date('Y-m-d H:i:s',$output['order_info']['payment_time']); ?></span></li>
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
					<time><?php echo date('Y-m-d H:i:s', $output['order_info']['order_cancel_day']);?></time>
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
				<li>1. 买家已使用“<?php echo orderPaymentName($output['order_info']['payment_code']);?>”方式成功对订单进行支付，支付单号 “<?php echo $output['order_info']['trade_no'];?>”。</li>
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
				<dd>已经完成。</dd>
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
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s', $output['order_info']['add_time']); ?></dd>
		</dl>
		<?php if ($output['order_info']['payment_code'] != 'offline') { ?>
		<dl class='<?php if(intval($output['order_info']['payment_time'])) echo 'current'; ?>'>
			<dt>支付订单</dt>
			<dd class='bg'> </dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s', $output['order_info']['payment_time']); ?></dd>
		</dl>
		<?php } ?>
		<dl class='<?php if(intval($output['order_info']['finnshed_time'])) { ?>current<?php } ?>'>
			<dt>订单完成</dt>
			<dd class='bg'> </dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s', $output['order_info']['finnshed_time']); ?></dd>
		</dl>
		<dl class='<?php if(!empty($output['order_info']['evaluation_state'])) { ?>current<?php } ?>'>
			<dt>评价</dt>
			<dd class='bg'></dd>
			<dd class='date' title=''><?php echo date('Y-m-d H:i:s', $output['order_info']['evaluation_time']); ?></dd>
		</dl>
		<div class='code-list tip' title='如列表过长超出显示区域时可滚动鼠标进行查看'><i class='arrow'></i>
			<h5>电子兑换码</h5>
			<div id='codeList'>
				<ul>
					<?php foreach($output['order_info']['extend_vr_order_code'] as $code_info){ ?>
					<li class='<?php echo $code_info['vr_state'] == 1 ? 'used' : null;?>'><strong><?php echo $code_info['vr_state'] == '0' ? ($code_info['vr_indate'] < TIMESTAMP ? $code_info['vr_code'] : encryptShow($code_info['vr_code'],7,12)) : $code_info['vr_code'];?></strong> <?php echo $code_info['vr_code_desc'];?> </li>
					<?php } ?>
				</ul>
			</div>
		</div>
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
				<tr class='bd-line'>
					<td>&nbsp;</td>
					<td class='w70'><div class='css-goods-thumb'><img src='<?=$output['order_info']['goods_image']?>' style='width:100%;' /></div></td>
					<td class='tl'>
						<dl class='goods-name'>
							<dt><?php echo $output['order_info']['goods_name']; ?></dt>
						</dl>
					</td>
					<td><?php echo $output['order_info']['goods_price']; ?></td>
					<td><?php echo $output['order_info']['goods_num']; ?></td>
					<td class='bdl bdr'>
						<?php echo $output['order_info']['state_desc']; ?>
						<!-- 取消订单 -->
						<?php if ($output['order_info']['if_cancel']) { ?>
						<p><a href='javascript:void(0)' style='color:#F30; text-decoration:underline;' nc_type='dialog' uri='<?=users_url('shop_vr_order/order_cancel', array('order_id' => $output['order_info']['order_id'], 'order_sn' => $output['order_info']['order_sn']))?>' dialog_title='取消订单' dialog_id='seller_order_cancel_order' dialog_width='400' id='order<?php echo $output['order_info']['order_id']; ?>_action_cancel' />取消订单</a></p>
						<?php } ?>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan='6'>
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
