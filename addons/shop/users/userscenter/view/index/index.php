<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<title><?=$output['title']?></title>
		<script>var STATICURL = '<?=STATIC_URL?>';</script>
		<link rel='stylesheet' href='<?=STATIC_URL?>/layuiadmin/layui/css/layui.css' media='all'>
		<link rel='stylesheet' href='<?=STATIC_URL?>/layuiadmin/style/admin.css' media='all'>
		<link href='<?=STATIC_URL?>/css/iconfont-admin/iconfont.css?v=2.0.0' rel='stylesheet'>
		<style>
			.layui-layout-admin .layui-logo {
				padding: 0;
			}
			.wb-nav .icon{
				position: absolute;
				left: 20px;
				top: 50%;
				margin-top: -10px;
				line-height: 100%;
			} 
		</style>
	</head>

	<body class='layui-layout-body'>
		<div id='LAY_app'>
			<div class='layui-layout layui-layout-admin'>
				<div class='layui-header'>
					<!-- 头部区域 -->
					<ul class='layui-nav layui-layout-left'>
						<li class='layui-nav-item layadmin-flexible' lay-unselect>
							<a href='javascript:;' layadmin-event='flexible' title='侧边伸缩'>
								<i class='layui-icon layui-icon-shrink-right' id='LAY_app_flexible'></i>
							</a>
						</li>
						<li class='layui-nav-item' lay-unselect>
							<a href='javascript:;' layadmin-event='refresh' title='刷新'>
								<i class='layui-icon layui-icon-refresh-3'></i>
							</a>
						</li>
					</ul>
					<ul class='layui-nav layui-layout-right' lay-filter='layadmin-layout-right'>
						<!--<li class='layui-nav-item layui-hide-xs' lay-unselect>
							<a href='<?='/front.php'?>' target='_blank'>
								商城首页
							</a>
						</li>-->
						<li class='layui-nav-item layui-hide-xs' lay-unselect>
							<a href='<?='/store'?>' target='_blank'>
								商家后台
							</a>
						</li>
						<li class='layui-nav-item' lay-unselect>
							<a href='javascript:;'>
								<cite><?=input('session.username', '');?></cite>
							</a>
							<dl class='layui-nav-child' >
								<dd ><a href='javascript:;' lay-href='<?=_url('index/password');?>'>修改密码</a></dd>
								<dd ><a href='<?=_url('publics/logout');?>'>安全退出</a></dd>
							</dl>
						</li>
						<li class='layui-nav-item layui-hide-xs' lay-unselect>
							<a href='javascript:;' layadmin-event='#'>
								<!--<i class='layui-icon layui-icon-theme'></i>-->
							</a>
						</li>
					</ul>
				</div>
				<!-- 侧边菜单 -->
				<div class='layui-side layui-side-menu'>
					<div class='layui-side-scroll'>
						<div class='layui-logo' lay-href='<?=_url('index/analys');?>'>
							<?php if(empty($output['config']['login_logo'])) { ?>
								<img class='layui-circle' src='<?=STATIC_URL?>/images/default-pic.jpg' height='46px' width='46px'>
							<?php }else{ ?>
								<img class='layui-circle' src='<?=$output['config']['login_logo']?>' height='46px' width='46px'>
							<?php } ?>
						</div>
						<ul class='layui-nav layui-nav-tree' lay-shrink='all' id='LAY-system-side-menu' lay-filter='layadmin-system-side-menu'>
							<li data-name='0' class='wb-nav layui-nav-item <?php if ($output['current_menu']['model'] == 'index') { ?>layui-nav-itemed<?php } ?>'>
								<a href='javascript:;' lay-href='<?=_url('index/analys')?>' lay-tips='概况' lay-direction='2'>
									<i class='icon iconfont icon-index'></i>
									<cite>概况</cite>
								</a>
								<dl class='layui-nav-child'>
									<dd data-name='统计' >
										<a href='javascript:;' lay-href='<?=_url('index/analys')?>'>统计</a>
									</dd>
								</dl>
							</li>
							<?php 
								$i = 1; 
								if(!empty($output['menu']) && is_array($output['menu'])) {
								foreach($output['menu'] as $key => $menu_value) {
							?>
							<li data-name='<?=$i?>' class='wb-nav layui-nav-item <?php if ($i == 0) { ?>layui-nav-itemed<?php } ?>'>
								<a href='javascript:;' lay-href='<?=_url($menu_value['child'][key($menu_value['child'])]['act'] . '/' . $menu_value['child'][key($menu_value['child'])]['op'])?>' lay-tips='<?=$menu_value['name'];?>' lay-direction='2'>
									<i class='icon iconfont icon-<?=$menu_value['icon'];?>' style='font-size: <?=$menu_value['size'];?>'></i>
									<cite><?=$menu_value['name']?></cite>
								</a>
							  
								<?php if(!empty($menu_value['child']) && is_array($menu_value['child'])) { ?>
								<?php foreach($menu_value['child'] as $submenu_value) { ?>
								<dl class='layui-nav-child'>
									<dd data-name='<?=$submenu_value['name']?>' >
										<a href='javascript:;' lay-href='<?=_url($submenu_value['act'] . '/' . $submenu_value['op'])?>'><?=$submenu_value['name'];?></a>
									</dd>
								</dl>
								<?php } ?>
								<?php } ?>
							</li>
							<?php $i++; } ?>
							<?php } ?>
						</ul>
					</div>
				</div>
				<!-- 页面标签 -->
				<div class='layadmin-pagetabs' id='LAY_app_tabs'>
					<div class='layui-icon layadmin-tabs-control layui-icon-prev' layadmin-event='leftPage'></div>
					<div class='layui-icon layadmin-tabs-control layui-icon-next' layadmin-event='rightPage'></div>
					<div class='layui-icon layadmin-tabs-control layui-icon-down'>
						<ul class='layui-nav layadmin-tabs-select' lay-filter='layadmin-pagetabs-nav'>
							<li class='layui-nav-item' lay-unselect>
								<a href='javascript:;'></a>
								<dl class='layui-nav-child layui-anim-fadein'>
									<dd layadmin-event='closeThisTabs'><a href='javascript:;'>关闭当前标签页</a></dd>
									<dd layadmin-event='closeOtherTabs'><a href='javascript:;'>关闭其它标签页</a></dd>
									<dd layadmin-event='closeAllTabs'><a href='javascript:;'>关闭全部标签页</a></dd>
								</dl>
							</li>
						</ul>
					</div>
					<div class='layui-tab' lay-unauto lay-allowClose='true' lay-filter='layadmin-layout-tabs'>
						<ul class='layui-tab-title' id='LAY_app_tabsheader'>
							<li lay-id='<?=_url('index/analys');?>' lay-attr='<?=_url('index/analys');?>' class='layui-this'><i class='layui-icon layui-icon-home'></i></li>
						</ul>
					</div>
				</div>
				<!-- 主体内容 -->
				<div class='layui-body' id='LAY_app_body'>
					<div class='layadmin-tabsbody-item layui-show'>
						<iframe src='<?=_url('index/analys');?>' frameborder='0' class='layadmin-iframe'></iframe>
					</div>
				</div>
				<!-- 辅助元素，一般用于移动设备下遮罩 -->
				<div class='layadmin-body-shade' layadmin-event='shade'></div>
			</div>
		</div>
		<script src='<?=STATIC_URL?>/layuiadmin/layui/layui.js'></script>
		<script>
			layui.config({
				base: '<?=STATIC_URL?>/layuiadmin/' //静态资源所在路径
			}).extend({
				index: 'lib/index' //主入口模块
			}).use('index');
		</script>
	</body>
</html>