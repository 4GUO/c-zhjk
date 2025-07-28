<?php defined('SAFE_CONST') or exit('Access Invalid!');?> 
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<title>支付异常</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
<meta name='apple-mobile-web-app-capable' content='yes'>
<meta name='apple-mobile-web-app-status-bar-style' content='black'>
<meta name='format-detection' content='telephone=no'>
<style>
	.msg_box {
		text-align: center;
		margin-top: 100px;
		color: #333333;
	}
	.msg_box h1 {
		
	}
	.msg_box p {
		line-height: 30px;
	}
	.msg_box .msg {
		font-size: 16px;
		color: #e33e33;
	}
</style>
</head>
<body>
<div class='msg_box'>
	<h1>支付异常</h1>
	<p class='msg'><?php echo $output['msg']; ?></p>
	<p style='font-size: 12px;'>3秒钟后返回上一页…</p>
</div>
<script>
setTimeout(function() {
    history.go(-1);
}, 3000);
</script>
</body>
</html>