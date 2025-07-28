<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset='utf-8'>
    <meta name='applicable-device' content='pc'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge,chrome=1' />
    <meta http-equiv='Cache-Control' content='no-siteapp' />
    <meta name='applicable-device'content='mobile'>
    <meta name='viewport' content='width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no' />
    <title><?=$output['SEO']['title']?>-<?=$output['SEO']['site_title']?></title>
    <meta name='keywords' content='<?=$output['SEO']['keywords']?>' />
    <meta name='description' content='<?=$output['SEO']['description']?>' />
	<style>
		
	</style>
</head>
<body>

<style>
#all {
    width: 100%;
    padding-top: 100px;
    background: #ffffff;
}
.err-wrap {
    width: 100%;
    padding: 70px 0 60px 0;
    overflow: hidden;
    background: #fff;
}
.err-wrap .number {
    width: 285px;
    height: 112px;
    margin: 0 auto 10px;
    background: url(<?=STATIC_URL?>/images/404.png) no-repeat;
    background-size: contain;
}
.err-wrap .tit {
    margin: 0;
    font-size: 24px;
    color: #333;
    font-weight: 400;
    text-align: center;
    margin-top: 27px;
    display: block;
}
.err-wrap .proposal {
    color: #666;
    font-size: 16px;
    text-align: center;
    display: block;
    margin-top: 9px;
}
.err-wrap .buttons {
    text-align: center;
    margin-top: 30px;
    display: flex;
    justify-content: center;
}
.err-wrap .buttons a {
    display: inline-block;
    margin-right: 20px;
    color: rgba(102,102,102,1);
    font-size: 14px;
    font-weight: 400;
    max-width: 250px;
    height: 32px;
    line-height: 32px;
    border-radius: 32px;
    text-align: center;
    background: rgba(249,249,249,1);
    border: 1px solid rgba(238,238,238,1);
    box-sizing: border-box;
    padding: 0 10px;
}
</style>
<div id='all'>
    <div class='err-wrap'>
		<div class='number'></div>
		<span class='tit'><?=$output['tip_msg'] ?: '您正在查找的内容不存在或已被删除！'?></span>
		<span class='proposal'>建议去别的页面转转</span>
		<div class='buttons'>
			<a href='<?=_url('index/index/index')?>' class='action_post_web_data' id='home'><?=config('name')?>首页</a>
			<a href='javascript:history.back(-1)' class='action_post_web_data' id='back'>返回上一页</a>
		</div>
	</div>
</div>
</body>

</html>