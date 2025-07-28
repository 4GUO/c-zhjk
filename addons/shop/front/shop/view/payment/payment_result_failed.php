<div style='font-size: 16px; color: #e33e33;line-height: 100px; text-align: center;'>支付未完成，页面跳转中...</div>
<script type='text/javascript'>
window.onload = function() {
	var order_type = getQueryString('attach');
	showError('Sorry，支付未完成或失败！', function(){
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