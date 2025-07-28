<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>

</style>
<div class='wrapper_search'>
	<div class='alert'>
		<dl class='hover_tips_cont'>
			<dt id='commodityspan'> <span style='color: #F00;'>请选择商品分类</span> </dt>
			<dt id='commoditydt' style='display: none;' class='current_sort'></dt>
			<dd id='commoditydd'></dd>
		</dl>
	</div>
	<div class='wp_sort'>
		<div id='class_div' class='wp_sort_block'>
			<div class='sort_list'>
				<div class='wp_category_list'>
					<div id='class_div_1' class='category_list'>
						<ul>
							<?php foreach($output['gc_list'] as $gc_id=>$gc_name){?>
							<li class='' nctype='selClass' gcid='<?php echo $gc_id;?>' type='spec' deep='1'><a class='' href='javascript:void(0)'><i class='icon-double-angle-right'></i><?php echo $gc_name;?></a> </li>
							<?php }?>
							
						</ul>
					</div>
				</div>
			</div>
			<div class='sort_list'>
				<div class='wp_category_list blank'>
					<div id='class_div_2' class='category_list'>
						<ul>
						</ul>
					</div>
				</div>
			</div>
			<div class='sort_list sort_list_last'>
				<div class='wp_category_list blank'>
					<div id='class_div_3' class='category_list'>
						<ul>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<script src='<?php echo STATIC_URL;?>/js/perfect-scrollbar.min.js'></script>
<script type='text/javascript'>
var select_object;
// 选择商品分类
function selClass($this){
	select_object = $this;
	
    $('.wp_category_list').css('background', '');
	$('#commodityspan').hide();
    $('#commoditydt').show();
    $this.siblings('li').children('a').attr('class', '');
    $this.children('a').attr('class', 'classDivClick');
	var data_str = {};
    data_str.gcid = $this.attr('gcid');
	data_str.type = $this.attr('type');
	data_str.deep = $this.attr('deep');
    var gcid = data_str.gcid;
	var type = data_str.type;
	var deep = parseInt(data_str.deep) + 1;
	
	if(data_str.deep == 1){
		$('#commoditydd').html('<a href=\'javascript:void(0)\' nc_type=\'dialog\' dialog_width=\'480\' dialog_title=\'新增规格\' dialog_id=\'my_spec_add\' uri=\'<?=_url('shop_spec/add')?>?gc_id=' + gcid + '\' class=\'css-btn css-btn-green\'>新增规格</a>').show();
	} else if(data_str.deep == 2){
		$('#commoditydd').html('<a href=\'javascript:void(0)\' nc_type=\'dialog\' dialog_width=\'480\' dialog_title=\'新增规格值\' dialog_id=\'my_spec_value_add\' uri=\'<?=_url('shop_spec_value/add')?>?sp_id=' + gcid + '\' class=\'css-btn css-btn-green\'>新增规格值</a>&nbsp;&nbsp;<a href=\'javascript:void(0)\' nc_type=\'dialog\' dialog_width=\'480\' dialog_title=\'编辑规格\' dialog_id=\'my_spec_edit\' uri=\'<?=_url('shop_spec/edit')?>?id=' + gcid + '\' class=\'css-btn css-btn-green\'>编辑规格</a>&nbsp;&nbsp;<a href=\'javascript:void(0)\' uri=\'<?=_url('shop_spec/del')?>?id=' + gcid + '\' onclick=\'delete_spec(this)\' class=\'css-btn css-btn-green\'>删除规格</a>').show();
	} else{
		$('#commoditydd').html('<a href=\'javascript:void(0)\' nc_type=\'dialog\' dialog_width=\'480\' dialog_title=\'编辑规格值\' dialog_id=\'my_spec_value_edit\' uri=\'<?=_url('shop_spec_value/edit')?>?id=' + gcid + '\' class=\'css-btn css-btn-green\'>编辑规格值</a>&nbsp;&nbsp;<a href=\'javascript:void(0)\' uri=\'<?=_url('shop_spec_value/del')?>?id=' + gcid + '\'  onclick=\'delete_spec_value(this)\' class=\'css-btn css-btn-green\'>删除规格值</a>').show();
	}
	
	if(data_str.deep < 3){
		getAjax('<?=_url('shop_spec/ajax')?>', {gc_id : gcid, type : type}, function(data) {
			if (data.state == 200) {
				$('#class_div_' + deep).children('ul').html('').end()
					.parents('.wp_category_list:first').removeClass('blank')
					.parents('.sort_list:first').nextAll('div').children('div').addClass('blank').children('ul').html('');
				$.each(data.list, function(i, n){
					if(data_str.deep == 1){
						$('#class_div_' + deep).children('ul').append('<li gcid=\'' + n.spec_id + '\' deep=\'' + deep + '\' type=\'specvalue\'><a class=\'\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>'
							+ n.spec_name + '</a></li>')
							.find('li:last').click(function(){
								selClass($(this));
							});
					}else{
						$('#class_div_' + deep).children('ul').append('<li gcid=\'' + n.sp_value_id + '\' deep=\'' + deep + '\' type=\'specvalue\'><a class=\'\' href=\'javascript:void(0)\'><i class=\'icon-double-angle-right\'></i>'
							+ n.sp_value_name + '</a></li>')
							.find('li:last').click(function(){
								selClass($(this));
							});
					}
					
				});
			} else {
				$('#class_div_' + deep).children('ul').html('');
				$('#class_div_' + data_str.deep).parents('.sort_list:first').nextAll('div').children('div').addClass('blank').children('ul').html('');            
			}
			// 显示选中的分类
			//showCheckClass();
		});
	}
    
}

$(function(){
    //自定义滚定条
    $('#class_div_1').perfectScrollbar();
    $('#class_div_2').perfectScrollbar();
    $('#class_div_3').perfectScrollbar();

    // ajax选择分类
    $('li[nctype=selClass]').click(function(){
        selClass($(this));
    });
	
	$('#commoditydd').hide();
});
// 显示选中的分类
function showCheckClass(){	
    var str = '';
    $.each($('a[class=classDivClick]'), function(i) {
        str += $(this).text() + '<i class=\'icon-double-angle-right\'></i>';
    });
    str = str.substring(0, str.length - 39);
    $('#commoditydd').html(str);
}

function delete_spec(that){
	var url = $(that).attr('uri');
	ajax_get_confirm('确定删除?', url, function(e){
		if(e.state == 200){
			select_object.remove();
			if($('#class_div_2 ul').html() == ''){
				$('#class_div_2').parent().addClass('blank');
				$('#commoditydd').html('').hide();
			}
			$('#class_div_3 ul').html('');
			$('#class_div_3').parent().addClass('blank');
			if(typeof(CUR_DIALOG) != 'undefined'){
				CUR_DIALOG.close();
			}
		}
	});
}

function delete_spec_value(that){
	var url = $(that).attr('uri');
	ajax_get_confirm('确定删除?', url, function(e){
		if(e.state == 200){
			select_object.remove();
			if($('#class_div_3 ul').html() == ''){
				$('#class_div_3').parent().addClass('blank');
				$('#commoditydd').html('').hide();
			}
			if(typeof(CUR_DIALOG) != 'undefined'){
				CUR_DIALOG.close();
			}
		}
	});
}
</script>