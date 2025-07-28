<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<title><?=$output['title']?></title>
		<script>var STATICURL = '<?=STATIC_URL?>';</script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery-1.7.2.min.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/dialog/dialog.js' id='dialog_js' charset='utf-8'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/jquery.validation.min.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/js/global.js'></script>
		<script type='text/javascript' src='<?=STATIC_URL?>/admin/js/global.js'></script>
		<link href='<?=STATIC_URL?>/css/font-awesome/css/font-awesome.min.css' rel='stylesheet'/>
		<!--[if IE 7]>
		  <link rel='stylesheet' href='<?=STATIC_URL?>/css/font-awesome/css/font-awesome-ie7.min.css'>
		<![endif]-->
		<link href='<?=STATIC_URL?>/admin/css/global.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
		<link href='<?=STATIC_URL?>/admin/css/userscenter.css?t=<?=time()?>' rel='stylesheet' type='text/css' />
	</head>
	<body>
	    <div id='append_parent'></div>
        <div id='ajaxwaitid'></div>
		<?php if (!empty($output['error'])) { ?>
		<div class='store-closed'><i class='icon-warning-sign'></i>
			<dl>
				<dt>系统异常提醒</dt>
				<dd>原因：<?=$output['error'];?></dd>
				<dd>在此期间，如果您有异议或申诉请及时联系平台管理。</dd>
			</dl>
		</div>
		<?php }else{ ?>
		<div class='css-layout wrapper'>
			<div class='css-layout-right'>
				<div class='css-path'><i class='icon-desktop'></i>管理中心<i class='icon-angle-right'></i><?=$output['current_menu']['model_name'];?><i class='icon-angle-right'></i><?=isset($output['current_menu']['name']) ? $output['current_menu']['name'] : '';?></div>
				<div class='main-content' id='mainContent'>
					<?php require $output['tpl_file'];?>
				</div>
			</div>
		</div>
		<?php }?>
		<script>
			$(function() {
				$('.delete').click(function() {
					var msg = $(this).attr('confirm');
					var url = $(this).attr('url');
					ajax_get_confirm(msg, url);
				});
				$('.select_page').click(function() {
					var base_url = $(this).attr('base_url');
					var param_str = $(this).attr('param_str');
					select_page(base_url, param_str);
				})
			})
		</script>
	</body>
</html>