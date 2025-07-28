<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<link type='text/css' href='<?=STATIC_URL?>/admin/js/weixin/material.css' rel='stylesheet' />
<style type='text/css'>
#items_search{
	display: inline-block;
	height: 20px;
	padding: 4px 10px;
	border: 1px solid #CCC;
	background: #F5F5F5;
	color: #777;
}
</style>
<div class='goods-gallery'>
	<div class='nav'>
	    <span class='r'>
			<select name='material_type'>
				<option value=''>全部</option>
				<option value='1'>单图文</option>
				<option value='2'>多图文</option>
			</select>
			<a id='items_search' href='javascript:void(0);'>查询</a>
		</span>
	</div>
	<div id='items_list'></div>
	<div class='pagination'></div>
</div>
<script>
	function select_page(url, data){
		getAjax(url, data, function(e) {
			if (e.state == 200) {
				var list_data = e.data['list'];
				if (list_data.length > 0) {
					var $html = '<div class=\'material_dialog\'><div class=\'list\'>';
					$.each(list_data, function(i){
						var v = list_data[i];
						if (v.material_type == 2) {
							$html += '<div class=\'item multi\' id=\'select_' + v.material_id + '\'>'+
								'<div class=\'time\'>' + v.material_addtime + '</div>';
								for(var jj in v.material_content) {
									$html += '<div class=\'' + (jj > 0 ? 'list' : 'first') + '\'>'+
										'<div class=\'info\'>'+
											'<div class=\'img\'><img src=\'' + v.material_content[jj].ImgPath + '\' /></div>'+
											'<div class=\'title\'>' + v.material_content[jj].Title + '</div>'+
										'</div>'+
									'</div>';
								}
							$html += '<div class=\'mod_del\'>'+
									'<a href=\'Javascript:select_material(' + v.material_id + ',' + v.material_type + ');\'>[选择]</a>'+
								'</div>'+
							'</div>';
						} else {
							$html += '<div class=\'item one\' id=\'select_' + v.material_id + '\'>';
							for(var jj in v.material_content) {
								$html += '<div class=\'title\'>' + v.material_content[jj].Title + '</div>'+
									'<div>' + v.material_addtime + '</div>'+
									'<div class=\'img\'><img src=\'' + v.material_content[jj].ImgPath + '\' /></div>'+
									'<div class=\'txt\'>' + v.material_content[jj].TextContents + '</div>';
							}
							$html += '<div class=\'mod_del\'>'+
									'<a href=\'Javascript:select_material(' + v.material_id + ',' + v.material_type + ');\'>[选择]</a>'+
								'</div>'+
							'</div>';
						}
						if (i%4 == 3) {
							$html += '<div class=\'clear\'></div>';
						}
					})
					$html += '<div class=\'clear\'></div></div></div>';
				} else {
					var $html = '<div class=\'warning-option\'><i class=\'fa fa-warning\'></i><span>暂无信息</span></div>';
				}
				$('#items_list').html($html);
				$('.pagination').html(e.data.page_html);
			}
		});
	}
    $(function(){
		select_page('<?=users_url('wechat/material_list', array('is_ajax' => 1))?>', {page: 1});
		
		$('#items_list').on('click', 'ul li', function(){
			if(typeof(CUR_DIALOG) != 'undefined'){
				CUR_DIALOG.close();
			}
			$('#uid').attr('value', $(this).attr('data-uid'));
			$('#member_info').html($(this).html()).show();
		});
	});
	$('#items_search').click(function(){
		select_page('<?=users_url('wechat/material_list', array('is_ajax' => 1))?>', {page: 1, material_type: $('select[name=material_type]').val()});		
	});
	function select_material(id, type) {//素材选择
		if(type == 2){
			$('#material_confirm .list .item').removeClass('one');
			$('#material_confirm .list .item').addClass('multi');
		}else{
			$('#material_confirm .list .item').removeClass('multi');
			$('#material_confirm .list .item').addClass('one');
		}
		$('#material_confirm .list .item').html($('#select_' + id).html());
		$('#material_confirm .list .item .mod_del').hide();
		$('#material_confirm').show();
		$('#materialid').val(id);
		var fid=$('#firstmenu').attr('value');
    	var cid=$('#childmenu').attr('value');
		$('input[name=MaterialID\\['+fid+'\\]\\['+cid+'\\]]').val(id);
		DialogManager.close('sucai');
	}
</script>
