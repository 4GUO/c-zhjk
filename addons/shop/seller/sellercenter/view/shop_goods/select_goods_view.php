<style>
#list{
	padding:10px 0 10px 18px;
}
#list li{
	position: relative;
	z-index: 1;
	margin-right: 16px;
	margin-bottom: 10px;
	border: solid 1px #E6E6E6;
	text-align: center;
	cursor: pointer;
	width: 100px;
	height: 100px;
}
#list li .title {
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
#list li .price {
	position: absolute;
	top: 0;
	left: 0;
	background: rgba(0,0,0,0.3);
	color: #ffffff;
	z-index: 2;
	width: 100%;
	height: 25px;
	line-height: 25px;
	font-size: 12px;
}
#list li:hover{
	border:solid 1px #428bca;
}
#list li img {
	display: block;
	width: 100%;
	height: 100%;
}
.search_btn {
	padding: 5px 10px;
	background: #F5F5F5;
	color: #777777;
}
</style>
<div class='goods-gallery'>
	<div id='list'></div>
	<div class='pagination'></div>
</div>
<script>
var has_special_goods = '<?=input('has_special_goods', 0, 'intval')?>';
var goods_name = '<?=input('goods_name', '')?>';
var input_name = '<?=input('input_name', '')?>';
var id = '<?=input('id', 0)?>';
function select_page(url, data){
	getAjax(url, data, function(e) {
		if (e.state == 200) {
			var list_data = e.data['list'];
			if (list_data.length > 0) {
				var $html = '<ul class=\'list\'>';
				$.each(list_data, function(i){
					v = list_data[i];
					$html += '<li nctype=\'' + input_name + '\' goods_commonid=\'' + v['goods_commonid'] + '\' goods_name=\'' + v['goods_name'] + '\' goods_image=\'' + v['goods_image'] + '\' goods_price=\'' + v['goods_price'] + '\'><img src=\'' + v['goods_image'] + '\' title=\'' + v['goods_name'] + '\'/>&nbsp;&nbsp;<div class=\'title\'>' + v['goods_name'] + '</div><div class=\'price\'>¥' + v['goods_price'] + '</div></li>';
				})
				$html += '</ul>';
			} else {
				var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>暂无上传商品</span></div>';
			}
			$('#list').html($html);
			$('.pagination').html(e.data.page_html);
		}
	});
}
	
$(function(){
	select_page('<?=_url('shop_goods/select_goods')?>', {page: 1, goods_name: goods_name, has_special_goods: has_special_goods});
	$('.search_btn').click(function() {
		select_page('<?=_url('shop_goods/select_goods')?>', {page: 1, goods_name: goods_name, has_special_goods: has_special_goods});
	});
});
</script>