<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<title><?=$output['title']?></title>
		<script>var STATICURL = '<?=STATIC_URL?>';</script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery-1.11.1.min.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/dialog/dialog.js' id='dialog_js' charset='utf-8'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery.validation.min.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/global.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/global.js'></script>
		<link href='<?=STATIC_URL?>/js/font-awesome/css/font-awesome.min.css' rel='stylesheet'/>
		<link href='<?=STATIC_URL?>/admin/css/global.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
		<link href='<?=STATIC_URL?>/admin/css/userscenter.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
		<script>
		    //var ajax_url = '<?=users_url('ajax/index')?>';
		</script>
	</head>

	<body>
	    <?php require $output['tpl_file'];?>
	</body>
</html>