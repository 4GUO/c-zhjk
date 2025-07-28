<style>
#list{
	padding:10px 0 10px 18px;
}
#list li{
	position:relative;
	z-index:1;
	margin-right:16px;
	margin-bottom:10px;
	border:solid 1px #E6E6E6;
	text-align: center;
}
#list li .name {
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
	<div class='nav'>
	    <span class='l'>
			<input class='text w100' name='goods_name' placeholder='商品名称' value='' type='text'>&nbsp;&nbsp;<a href='javascript:;' class='search_btn'>搜索</a>
		</span>
	</div>
	<div id='list'></div>
	<div class='pagination'></div>
</div>
<script>
var input_name = '<?=input('input_name', '')?>';
var id = '<?=input('id', 0)?>';
function insert_link(that){
	if(typeof(CUR_DIALOG) != 'undefined'){
		CUR_DIALOG.close();
	}
	var nctype = $(that).attr('nctype');
	var goods_commonid = $(that).attr('goods_commonid');
	var goods_image = $(that).attr('goods_image');
	var goods_name = $(that).attr('goods_name');
	if (input_name == 'selectall') {
		var can_tr = true;
		$('div[nctype=' + nctype + id + '] li').each(function() {
			if ($(this).attr('goods_commonid') == goods_commonid) {
				can_tr = false;
			}
		})
		if (can_tr) {
			var html = '<li class=\'goods_item\' goods_commonid=\'' + goods_commonid + '\' title=\'点击可删除\'><img class=\'goods_image\' src=\'' + goods_image + '\'/><p class=\'goods_name\'>' + goods_name + '</p><p class=\'goods_num\'>数量：<input class=\'text w60\' type=\'text\' value=\'\' name=\'goods_nums[' + goods_commonid + ']\' />&nbsp;件</p><input type=\'hidden\' value=\'' + goods_commonid + '\' name=\'goods_commonids[' + goods_commonid + ']\' /><a href=\'javascript:;\' onclick=\'del_link(this);\'><i class=\'icon-trash\'></i>删除</a></li>';
			$('div[nctype=' + nctype + id + ']').append(html);
		}
		$('div[nctype=' + nctype + id + ']').show();
	} else {
		$('input[nctype=' + nctype + id + ']').val(goods_commonid);
		$('img[nctype=' + nctype + id + ']').attr('src', goods_image);
		$('span[nctype=' + nctype + id + ']').html(goods_name).show();
		$('div[nctype=' + nctype + id + ']').show();
	}
}
function select_page(url, data){
	getAjax(url, data, function(e) {
		if (e.state == 200) {
			var list_data = e.data['list'];
			if (list_data.length > 0) {
				var $html = '<ul class=\'list\'>';
				$.each(list_data, function(i){
					v = list_data[i];
					$html += '<li nctype=\'' + input_name + '\' goods_commonid=\'' + v['goods_commonid'] + '\' goods_name=\'' + v['goods_name'] + '\' goods_image=\'' + v['goods_image'] + '\' onclick=\'insert_link(this);\'><img src=\'' + v['goods_image'] + '\' title=\'' + v['goods_name'] + '\'/>&nbsp;&nbsp;<div class=\'name\'>' + v['goods_name'] + '</div></li>';
				})
				$html += '</ul>';
			} else {
				var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>暂无上传数据</span></div>';
			}
			$('#list').html($html);
			$('.pagination').html(e.data.page_html);
		}
	});
}
	
$(function(){
	select_page('<?=users_url('shop_goods_taocan/selectGoods')?>', {page: 1});
	$('.search_btn').click(function() {
		select_page('<?=users_url('shop_goods_taocan/selectGoods')?>', {page: 1, goods_name: $('input[name=goods_name]').val()});
	});
});
</script>