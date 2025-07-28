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
<title><?=isset($output['title']) ? $output['title'] : ''?></title>
<script> var site_url = '<?=SITE_URL?>';var STATICURL = '<?=STATIC_URL?>';</script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/underscore.js'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/md5.js'></script>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/css/weui.min.css?t=<?=time()?>'>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/shop/css/base.css?t=<?=time()?>'>
<script type='text/javascript' src='<?=STATIC_URL?>/shop/js/config.js'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/shop/js/zepto.min.js'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/shop/js/base.js?t=<?=time()?>'></script>
</head>
<body>
	<?php require $output['tpl_file'];?>
	<div id='footer'></div>
	<div class='js_dialog' id='alertDialog' style='opacity: 1;'>
		<div class='weui-mask'></div>
		<div class='weui-dialog'>
			<div class='weui-dialog__bd'>弹窗内容，告知当前状态、信息和解决方法，描述文字尽量控制在三行内</div>
			<div class='weui-dialog__ft'>
				<a href='javascript:;' class='weui-dialog__btn weui-dialog__btn_primary'>知道了</a>
			</div>
		</div>
	</div>
	<div class='js_dialog' id='confirmDialog' style='opacity: 1;'>
		<div class='weui-mask'></div>
		<div class='weui-dialog'>
			<div class='weui-dialog__hd'><strong class='weui-dialog__title'>系统提示</strong></div>
			<div class='weui-dialog__bd'>弹窗内容，告知当前状态、信息和解决方法，描述文字尽量控制在三行内</div>
			<div class='weui-dialog__ft'>
				<a href='javascript:;' class='weui-dialog__btn weui-dialog__btn_default'>取消</a>
				<a href='javascript:;' class='weui-dialog__btn weui-dialog__btn_primary'>知道了</a>
			</div>
		</div>
	</div>
	<div id='bottomBox' style='display: none;'>
        <div class='weui-mask' id='iosMask' style='opacity: 1;'></div>
        <div class='weui-actionsheet weui-actionsheet_toggle' id='boxBody'>
            
        </div>
    </div>
	<div id='toast' style='opacity: 0; display: none;'>
        <div class='weui-mask_transparent'></div>
        <div class='weui-toast'>
            <i class='weui-icon-success-no-circle weui-icon_toast'></i>
            <p class='weui-toast__content'>已完成</p>
        </div>
    </div>
	<div id='uploadLoading' style='opacity: 0; display: none;'>
        <div class='weui-mask_transparent'></div>
        <div class='weui-toast'>
            <i class='weui-loading weui-icon_toast'></i>
            <p class='weui-toast__content'>服务器正在响应</p>
        </div>
    </div>
	<div class='pre-loading' style='display: none;'>
		<div class='pre-block'>
			<div class='spinner'><i></i></div>
			数据努力读取中... 
		</div>
	</div>
	<script>
		function newRedirectTo (that) {
			var url = $(that).attr('uri');
			var canshu = $(that).attr('canshu') ? eval($(that).attr('canshu')) : {};
			console.log(canshu);
			redirectTo(url, canshu);
		}
	</script>
</body>
</html>