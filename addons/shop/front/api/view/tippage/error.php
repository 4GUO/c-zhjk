<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!doctype html>
<html>
<head>
<meta charset='UTF-8'>
<meta http-equiv='X-UA-Compatible' content='IE=edge' />
<meta name='apple-mobile-web-app-capable' content='yes' />
<meta name='apple-touch-fullscreen' content='yes' />
<meta name='format-detection' content='telephone=no' />
<meta name='apple-mobile-web-app-status-bar-style' content='black' />
<meta name='format-detection' content='telephone=no' />
<meta name='msapplication-tap-highlight' content='no' />
<meta name='viewport' content='initial-scale=1,maximum-scale=1,minimum-scale=1' />
<title>系统提示</title>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/css/weui.min.css?t=<?=time()?>'>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/shop/css/base.css?t=<?=time()?>'>
<style>
	.container {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow: hidden;
	}
	.weui-btn {
		padding: 8px 24px;
	}
</style>
</head>
<body>
	<div class='container'>
		<div class='weui-msg'>
			<div class='weui-msg__icon-area'>
				<i class='weui-icon-warn weui-icon_msg'></i>
			</div>
			<div class='weui-msg__text-area'>
				<div class='weui-msg__title'>操作失败</div>
				<div class='weui-msg__desc'><?= $output['msg'];?></div>
			</div>
			<div class='weui-msg__opr-area'>
				<div class='weui-btn-area'>
					<a href='<?= front_url('index/index');?>' class='weui-btn weui-btn_default'>返回首页</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>