<script type='text/javascript'>
window.onload = function() {
	var order_type = getQueryString('attach');
	showError('支付操作完成！如果您的订单状态没有改变，请耐心等待支付网关的返回结果。', function(){
		if (order_type == 'p') {
			redirectTo('shop/user/pd_log');
		} else if (order_type == 'r') {
			redirectTo('shop/order/index');
		} else if (order_type == 'v') {
			redirectTo('shop/vr_order/index');
		} else {
			redirectTo('shop/user/index');
		}
	});	
}
</script>