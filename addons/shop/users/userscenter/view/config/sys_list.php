<style>
.goods-gallery .goodsList {
	padding:10px 0 10px 18px;
}
.goods-gallery .goodsList li {
	position: relative;
	z-index: 1;
	margin-right: 15px;
	margin-bottom: 10px;
	border: solid 1px #e7e7eb;
	text-align: center;
	width: 90px;
	height: 90px;
	cursor: pointer;
}
.goods-gallery .goodsList li .table {
	position: absolute;
	bottom: 0;
	left: 0;
	background: rgba(0,0,0,0.3);
	color: #ffffff;
	z-index: 2;
	width: 100%;
	height: 25px;
	line-height: 25px;
	font-size: 12px;
	white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    word-break: break-all;
}
.goods-gallery .goodsList li:hover{
	border: solid 1px #428bca;
}
.goods-gallery .goodsList li img {
	display: block;
	width: 100%;
	height: 100%;
}

.goods-gallery .articleList {
	padding:10px 0 10px 0px;
}
.goods-gallery .articleList li {
	position: relative;
	z-index: 1;
	margin: 0px;
	padding: 0 2% 0 2%;
	margin-bottom: 10px;
	border: solid 1px #e7e7eb;
	text-align: left;
	width: 96%;
	height: 35px;
	line-height: 35px;
	cursor: pointer;
	color: #333333;
	font-size: 14px;
}
.goods-gallery .articleList li:hover{
	border: solid 1px #428bca;
}


.search_btn {
    padding: 5px 10px;
    background: #F5F5F5;
    color: #777777;
}
.layui-tab-title {
    position: relative;
    left: 0;
    height: 40px;
    white-space: nowrap;
    font-size: 0;
    transition: all .2s;
    -webkit-transition: all .2s;
}
.layui-tab-title li {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    vertical-align: middle;
    font-size: 14px;
    transition: all .2s;
    -webkit-transition: all .2s;
    position: relative;
    line-height: 40px;
    min-width: 65px;
    padding: 0 15px;
    text-align: center;
    cursor: pointer;
}
.layui-tab-title .layui-this {
    color: #009688;
}
.layui-tab-title .layui-this:after {
    border: none;
    border-radius: 0;
    border-bottom: 2px solid #009688;
	position: absolute;
    left: 0;
    top: 0;
    content: '';
    width: 100%;
    height: 41px;
	box-sizing: border-box;
    pointer-events: none;
}

.layui-tab-content {
    padding: 10px;
}

.tab-content {
	display: none;
}

.tab-content .layui-form-item {
    padding: 9px 0;
    margin-bottom: 8px;
}
.tab-content .layui-form-item h4 {
    margin: 10px;
	font-size: 14px;
}
.tab-content .layui-form-item h4 i {
    padding-right: 10px;
}
.link-box a {
	display: inline-block;
	margin-right: 10px;
	padding: 5px 8px;
	border: solid 1px #e7e7eb;
	color: #333333;
	border-radius: 2px;
}
</style>
<div class='goods-gallery' id='DIALOGa'>
	<ul class='layui-tab-title'>
		<li class='layui-this' rel='shop'>商城</li>
		<li rel='goods'>商品</li>
		<li rel='article'>文章</li>
		<li rel='category'>分类</li>
		<!--<li rel='special'>专题</li>-->
	</ul>
	<div class='layui-tab-content'>
		<div class='tab-content' id='shop' style='display: block;'>
			<div class='layui-form-item'>
				<h4><i class='icon-file'></i>系统连接</h4>
			</div>
			<div class='link-box'>
				<a href='javascript:;' link='<?=uni_url('/pages/store/index')?>' class='select'>店铺列表</a>
				<a href='javascript:;' link='<?=uni_url('/pages/goods/class_list')?>' class='select'>分类</a>
			</div>
		</div>
		<div class='tab-content' id='goods'>
			<div class='nav'>
				<span class='l'>
					<input class='text w200' name='goods_name' value='' type='text'>&nbsp;&nbsp;<a href='javascript:;' class='search_btn'>搜索</a>
				</span>
			</div>
			<div class='listBox goodsList'></div>
			<div class='pagination'></div>
		</div>
		<div class='tab-content' id='article'>
			<div class='nav'>
				<span class='l'>
					<input class='text w200' name='article_title' value='' type='text'>&nbsp;&nbsp;<a href='javascript:;' class='search_btn'>搜索</a>
				</span>
			</div>
			<div class='listBox articleList'></div>
			<div class='pagination'></div>
		</div>
		<div class='tab-content' id='category'>
			<div class='listBox articleList'>
				<?php foreach($output['class_list'] as $val) { ?>
				<li class='select' link='<?=$val['link']?>'><?=$val['gc_name']?></li>
				<?php } ?>
			</div>
		</div>
		<div class='tab-content' id='special'>
			
		</div>
	</div>
</div>
<script>
$('.layui-tab-title li').click(function () {
	$('.layui-tab-title li').removeClass('layui-this');
	$(this).addClass('layui-this');
	var flag = $(this).attr('rel');
	$('.tab-content').hide();
	$('#' + flag).show();
})
function select_goods_page(url, data){
	getAjax(url, data, function(e) {
		if (e.state == 200) {
			var list_data = e.data['list'];
			if (list_data.length > 0) {
				var $html = '<ul class=\'list\'>';
				$.each(list_data, function(i) {
					v = list_data[i];
					$html += '<li link=\'' + v['link'] + '\' class=\'select\'><img src=\'' + v['goods_image'] + '\' title=\'' + v['goods_name'] + '\'/>&nbsp;&nbsp;<div class=\'table\'>' + v['goods_name'] + '</div></li>';
				})
				$html += '</ul>';
			} else {
				var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>暂无上传数据</span></div>';
			}
			$('#goods .listBox').html($html);
			$('#goods .pagination').html(e.data.page_html);
		}
	});
}
function select_article_page(url, data){
	getAjax(url, data, function(e) {
		if (e.state == 200) {
			var list_data = e.data['list'];
			if (list_data.length > 0) {
				var $html = '<ul class=\'list\'>';
				$.each(list_data, function(i) {
					v = list_data[i];
					$html += '<li link=\'' + v['link'] + '\' class=\'select\'>' + v['article_title'] + '</li>';
				})
				$html += '</ul>';
			} else {
				var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>暂无上传数据</span></div>';
			}
			$('#article .listBox').html($html);
			$('#article .pagination').html(e.data.page_html);
		}
	});
}	
$(function(){
	select_goods_page('<?=users_url('shop_goods/selectGoods')?>', {page: 1});
	$('.search_btn').click(function() {
		select_goods_page('<?=users_url('shop_goods/selectGoods')?>', {page: 1, goods_name: $('input[name=goods_name]').val()});
	});
	$('#goods .pagination').on('click', '.select_page', function() {
		var base_url = $(this).attr('base_url');
		var param_str = $(this).attr('param_str');
		select_goods_page(base_url, param_str);
	})
	select_article_page('<?=users_url('article/selectArticle')?>', {page: 1});
	$('.search_btn').click(function() {
		select_article_page('<?=users_url('article/selectArticle')?>', {page: 1, article_title: $('input[name=article_title]').val()});
	});
	$('#article .pagination').on('click', '.select_page', function() {
		var base_url = $(this).attr('base_url');
		var param_str = $(this).attr('param_str');
		select_article_page(base_url, param_str);
	})
});
var input_name = '<?=input('input_name', '')?>';
$('#DIALOGa').on('click', '.select', function() {
	if (typeof(CUR_DIALOG) != 'undefined') {
		CUR_DIALOG.close();
	}
	var link_url = $(this).attr('link');
	$('input[nctype=' + input_name + ']').val(link_url);
});
</script>
