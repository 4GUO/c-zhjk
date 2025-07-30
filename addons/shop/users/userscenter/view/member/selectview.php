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
#list li .mobile {
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
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.search_btn:hover {
    background: #E6E6E6;
}
.nav {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 20px;
}
.nav .text {
    width: 70%;
    margin-right: 10px;
}
</style>
<div class='goods-gallery'>
    <div class='nav'>
        <input class='text w100' name='mobile_s' placeholder='手机号' value='' type='text'>
        <a href='javascript:;' class='search_btn'>搜索</a>
    </div>
    <div id='list'></div>
    <div class='pagination'></div>
</div>
<script>
var input_name = '<?=input('input_name', '')?>';
var id = '<?=input('id', 0)?>';
var ext_uid = '<?=input('uid', 0, 'intval')?>';
function insert_link(that){
	if(typeof(CUR_DIALOG) != 'undefined'){
		CUR_DIALOG.close();
	}
	var nctype = $(that).attr('nctype');
	var uid = $(that).attr('uid');
	var headimg = $(that).attr('headimg');
	var mobile = $(that).attr('mobile');
	var nickname = $(that).attr('nickname');
	if (input_name == 'selectall') {
		var can_tr = true;
		$('div[nctype=' + nctype+id + '] li').each(function() {
			if ($(this).attr('uid') == uid) {
				can_tr = false;
			}
		})
		if (can_tr) {
			var m_info = nickname;
			if (mobile) {
				m_info += '(' + mobile + ')';
			}
			var html = '<li uid=\'' + uid + '\' onclick=\'del_link(this);\' title=\'点击可删除\'>' + m_info + '<input type=\'hidden\' value=\'' + uid + '\' name=\'uids[' + uid + ']\' /></li>';
			$('div[nctype=' + nctype+id + ']').append(html);
		}
		$('div[nctype=' + nctype+id + ']').show();
	} else {
		$('input[nctype=' + nctype+id + ']').val(uid);
		$('img[nctype=' + nctype+id + ']').attr('src', headimg);
		var m_info = nickname;
		if (mobile) {
			m_info += '(' + mobile + ')';
		}
		$('span[nctype=' + nctype+id + ']').html(m_info).show();
		$('div[nctype=' + nctype+id + ']').show();
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
					$html += '<li nctype=\'' + input_name + '\' uid=\'' + v['uid'] + '\' mobile=\'' + v['mobile'] + '\' nickname=\'' + v['nickname'] + '\' headimg=\'' + v['headimg'] + '\' onclick=\'insert_link(this);\'><img src=\'' + v['headimg'] + '\' title=\'' + v['nickname'] + '\'/>&nbsp;&nbsp;<div class=\'mobile\'>' + v['nickname'] + '</div></li>';
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
	select_page('<?=users_url('member/selectUser')?>', {page: 1, uid: ext_uid});
	$('.search_btn').click(function() {
		select_page('<?=users_url('member/selectUser')?>', {page: 1, uid: ext_uid, mobile: $('input[name=mobile_s]').val()});
	});
	$('.pagination').on('click', '.select_page', function() {
		var base_url = $(this).attr('base_url');
		var param_str = $(this).attr('param_str');
		select_page(base_url, param_str);
	})
});
</script>
