<style>
#pic_list{
	padding:10px 0 10px 18px;
}
#pic_list li{
	position:relative;
	z-index:1;
	margin-right:16px;
	margin-bottom:10px;
	border:solid 1px #E6E6E6;
}
#pic_list .delete{
	font-size: 20px;
	line-height: 16px;
	height: 16px;
	display: none;
	color: #428bca;
	position: absolute;
	bottom: 3px;
	right: 3px;
	cursor: pointer;
	z-index: 2;
}
#pic_list li:hover{
	border:solid 1px #428bca;
}
#pic_list li:hover .delete{
	display: block;
}
#pic_list .move{
	font-size: 20px;
	line-height: 16px;
	height: 16px;
	display: none;
	color: #428bca;
	position: absolute;
	bottom: 3px;
	left: 3px;
	cursor: pointer;
	z-index: 2;
}
#pic_list li:hover .move{
	display: block;
}

#move_to_class_locker {
	z-index: 1101 !important;
}
#fwin_move_to_class {
	z-index: 1102 !important;
}
</style>
<div class='goods-gallery'>
	<div class='nav'>
	    <span class='l'>
			<div class='css-upload-btn' style='margin:0;'> 
				<a href='javascript:void(0);'>
					<span>
						<input hidefocus='true' size='1' class='input-file' name='album_manger' id='album_manger' type='file' multiple='multiple' />
					</span>
					<p><i class='icon-upload-alt'></i>图片上传</p>
				</a> 
			</div>
		</span>
		<span class='r'>
			<select name='jumpMenu' id='jumpMenu'>
				<option value='0'>请选择分类</option>
				<?php foreach($output['class_list'] as $val) { ?>
				<option value='<?php echo $val['aclass_id']; ?>'><?php echo $val['aclass_name']; ?></option>
				<?php } ?>
			</select>
		</span>
	</div>
	<div id='pic_list'></div>
	<div class='pagination'></div>
</div>
<div id='thumbCategory' style='display: none;'>
	<div class='eject_con'>
		<form>
			<input type='hidden' name='form_submit' value='ok' />
			<input type='hidden' name='apic_id' value='0' />
			<dl>
				<dt>选择分类：</dt>
				<dd>
					<select name='aclass_id'>
						<option value='0'>请选择分类</option>
						<?php foreach($output['class_list'] as $val) { ?>
						<option value='<?php echo $val['aclass_id']; ?>'><?php echo $val['aclass_name']; ?></option>
						<?php } ?>
					</select>
				</dd>
			</dl>
			<div class='bottom'>
				<label class='submit-border'>
					<input type='button' class='submit' value='提交' />
				</label>
			</div>
		</form>
	</div>
</div>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.ui.widget.js' charset='utf-8'></script>
<script type='text/javascript' src='<?=STATIC_URL?>/js/fileupload/jquery.fileupload.js' charset='utf-8'></script>
<script>
var items = '<?=input('item', 'diy_mobile_body')?>';
if(items == 'diy_mobile_body'){
	var index = <?=input('index', '0')?>;
	var key = <?=input('key', '0')?>;
	// 替换图片
    replace_mobile_img = function(data){
		if(typeof(CUR_DIALOG) != 'undefined'){
			CUR_DIALOG.close();
		}
		value_edit('img', data, index, key);
    }
	function select_page(url, data){
		getAjax(url, data, function(e) {
			if (e.state == 200) {
				var list_data = e.data['list'];
				if(list_data.length > 0){
					var $html = '<ul class=\'list\'>';
					$.each(list_data, function(i){
						v = list_data[i];
						$html += '<li><a href=\'JavaScript:void(0);\' onclick=\'replace_mobile_img(\'' + v['apic_cover'] + '\');\'><img src=\'' + v['apic_cover'] + '\' title=\'' + v['apic_name'] + '\'/></a><div class=\'move icon-pencil\' rel=\'' + v['apic_id'] + '\' aclass_id=\'' + v['aclass_id'] + '\'></div><div class=\'delete icon-trash\' rel=\'' + v['apic_id'] + '\'></div></li>';
					})
					$html += '</ul>';
				}else{
					var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>相册中暂无图片</span></div>';
				}
				$('#pic_list').html($html);
				$('.pagination').html(e.data.page_html);
			}
		});
	}
}else{
	function insert_img(that){
		if(typeof(CUR_DIALOG) != 'undefined'){
			CUR_DIALOG.close();
		}
		var nctype = $(that).attr('nctype');
		var img_url = $(that).attr('img_url');
		if(items == 'editor'){
			var startOffset = <?=input('input_name', 'content')?>.cmd.range.startOffset;
			<?=input('input_name', 'content')?>.insertHtml('<img src=\''+ img_url + '\' alt=\''+ img_url + '\'>');
		}else{
			$('input[nctype=' + nctype + ']').val(img_url);
			$('img[nctype=' + nctype + ']').attr('src', img_url);
		}
	}
	function select_page(url, data){
		getAjax(url, data, function(e) {
			if (e.state == 200) {
				var list_data = e.data['list'];
				if(list_data.length > 0){
					var $html = '<ul class=\'list\'>';
					$.each(list_data, function(i){
						v = list_data[i];
						$html += '<li><a href=\'JavaScript:void(0);\' nctype=\'' + items + '\' img_url=\'' + v['apic_cover'] + '\' onclick=\'insert_img(this);\'><img src=\'' + v['apic_cover'] + '\' title=\'' + v['apic_name'] + '\'/></a><div class=\'move icon-pencil\' rel=\'' + v['apic_id'] + '\' aclass_id=\'' + v['aclass_id'] + '\'></div><div class=\'delete icon-trash\' rel=\'' + v['apic_id'] + '\'></div></li>';
					})
					$html += '</ul>';
				}else{
					var $html = '<div class=\'warning-option\'><i class=\'icon-warning-sign\'></i><span>相册中暂无图片</span></div>';
				}
				$('#pic_list').html($html);
				$('.pagination').html(e.data.page_html);
			}
		});
	}
}
	
$(function(){
	select_page('<?=users_url('album/pic_list_ajax')?>', {page: 1, item: items});
	$('#pic_list').on('click', '.delete', function(){
		var that = this;
		var apic_id = $(that).attr('rel');
		getAjax('<?=users_url('album/del_pic')?>', {apic_id: apic_id}, function(e) {
			if (e.state == 200) {
				$(that).parent('li').remove();
			} else {
				showError(e.msg);
			}
		});
	});
	$('#pic_list').on('click', '.move', function(){
		var that = this;
		var apic_id = $(that).attr('rel');
		$('input[name=apic_id]').val(apic_id);
		var aclass_id = $(that).attr('aclass_id');
		var _html = $('#thumbCategory').html();
		move_to_dialog = html_form('move_to_class', '转移相册', _html, 450, 1);
		$('#fwin_move_to_class select[name=aclass_id]').val(aclass_id);
	});
	$('body').on('click', '#fwin_move_to_class .submit', function() {
		var apic_id = $('#fwin_move_to_class input[name=apic_id]').val();
		var aclass_id = $('#fwin_move_to_class select[name=aclass_id]').val();
		postAjax('<?php echo _url('album/moveto');?>', {apic_id: apic_id, aclass_id: aclass_id}, function(e) {
			if (e.state == 200) {
				move_to_dialog.close();
			} else {
				showError(e.msg);
			}
		});
	})
	var gc_id = $('select[name=jumpMenu]').val();
	$('#jumpMenu').change(function() {
		gc_id = $(this).val();
		select_page('<?=users_url('album/pic_list_ajax')?>', {page: 1, item: items, gc_id: gc_id});
		$('#album_manger').fileupload({
            dataType: 'json',
            url: '<?=users_url('album/image_upload')?>',
            formData: {name : 'album_manger', aclass_id: gc_id},
            add: function (e, data) {
                data.submit();
            },
            done: function (e, data) {
                var param = data.result;
                if (param.state == 400) {
                    showError(param.msg);
                } else {
                    select_page('<?=users_url('album/pic_list_ajax')?>', {page: 1, item: items, gc_id: gc_id});
                }
            }
        });
	});
	$('#album_manger').fileupload({
        dataType: 'json',
        url: '<?=users_url('album/image_upload')?>',
        formData: {name : 'album_manger', aclass_id: gc_id},
        add: function (e, data) {
            data.submit();
        },
        done: function (e, data) {
            var param = data.result;
            if (param.state == 400) {
                showError(param.msg);
            } else {
                select_page('<?=users_url('album/pic_list_ajax')?>', {page: 1, item: items, gc_id: gc_id});
            }
        }
    });
	$('.pagination').on('click', '.select_page', function() {
		var base_url = $(this).attr('base_url');
		var param_str = $(this).attr('param_str');
		select_page(base_url, param_str);
	})
});
</script>
