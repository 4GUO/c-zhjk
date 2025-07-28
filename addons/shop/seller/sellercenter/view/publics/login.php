<!DOCTYPE html>
<html lang='zh-cn'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title><?=$output['title']?></title>
		<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/seller/css/p_login.css'>
        <script type='text/javascript'>
			if (navigator.appName == 'Microsoft Internet Explorer') {
				if (navigator.userAgent.indexOf('MSIE 5.0') > 0 || navigator.userAgent.indexOf('MSIE 6.0') > 0 || navigator.userAgent.indexOf('MSIE 7.0') > 0) {
					alert('您使用的 IE 浏览器版本过低, 推荐使用 Chrome 浏览器或 IE8 及以上版本浏览器.');
				}
			}
        </script>
	</head>
    <body>
		<div class='wrapper page-login'>
			<div class='container'>
				<h1>商家管理平台</h1>
				<form class='form form-login form-horizontal form-validate' enctype='multipart/form-data' action='<?=_url('publics/login')?>' method='post'>
					<input type='hidden' name='is_api' value='1'>
					<input type='hidden' name='<?=$output['token_name']?>' value='<?=$output['token_value']?>' />
					<input type='hidden' name='form_submit' value='ok' />
					<input name='myhash' type='hidden' value='<?=getUrlhash()?>' />
					<input type='text' placeholder='请输入登录账号' name='Account'>
					<input type='password' placeholder='请输入登录密码' name='Password'>
					<input type='submit' name='submit' value='登录'>
					<br/>
					<div class='check-tips' style='color:red;'></div>
				</form>
			</div>
			<ul class='bg-bubbles'>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>
		<script src='<?=STATIC_URL?>/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript'>
			//表单提交
			function setTimeoutHandel() {
				$('.check-tips').fadeOut();
			}
			
			$ ('form').submit(function() {
				var self = $(this);
				$.post(self.attr('action'), self.serialize(), success, 'json');
				return false;
				function success(data){
					if (data.state == 200) {
						window.location.href = '<?=_url('index/index')?>';
					} else {
						self.find('.check-tips').text(data.msg);
						$('.check-tips').fadeIn();
						setTimeout('setTimeoutHandel()', 3000);
					}
				}
			});
		</script>
	</body>
</html>