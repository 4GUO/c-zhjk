<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<a id='btn_show_goods_select' class='css-btn css-btn-green' href='javascript:;'>添加商品</a> 
</div>
<table class='css-default-table'>
	<tbody>
		<tr>
			<td class='w90 tr'><strong>活动名称：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['xianshi_name'];?></td>
			<td class='w90 tr'><strong>开始时间：</strong></td>
			<td class='w120 tl'><?php echo date('Y-m-d H:i', $output['xianshi_info']['start_time']);?></td>
			<td class='w90 tr'><strong>结束时间：</strong></td>
			<td class='w120 tl'><?php echo date('Y-m-d H:i', $output['xianshi_info']['end_time']);?></td>
			<td class='w90 tr'><strong>购买下限：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['lower_limit'];?></td>
			<td class='w90 tr'><strong>状态：</strong></td>
			<td class='w120 tl'><?php echo $output['xianshi_info']['xianshi_state_text'];?></td>
		</tr>
	</tbody>
</table>
<div class='alert'> <strong>说明：</strong>
	<ul>
		<li>1、限时折扣商品的时间段不能重叠</li>
		<li>2、点击添加商品按钮可以搜索并添加参加活动的商品，点击删除按钮可以删除该商品</li>
	</ul>
</div>
<!-- 商品搜索 -->
<div id='div_goods_select' class='div-goods-select' style='display: none;'>
	<table class='search-form'>
		<tr>
			<th class='w150'><strong>第一步：搜索店内商品</strong></th>
			<td class='w160'><input id='search_goods_name' type='text w150' class='text' name='goods_name' value=''/></td>
			<td class='w70 tc'><a href='javascript:void(0);' id='btn_search_goods' class='css-btn'/>搜索</a></td>
			<td class='w10'></td>
			<td><p class='hint'>不输入名称直接搜索将显示店内所有普通商品，特殊商品不能参加。</p></td>
		</tr>
	</table>
	<div id='div_goods_search_result' class='search-result' style='width: 90%;'></div>
	<a id='btn_hide_goods_select' class='close' href='javascript:void(0);' style='color: #0579C6; right: 0;'>X</a> 
</div>
<div id='dialog_add_xianshi_goods' style='display:none;'>
	<input id='dialog_goods_id' type='hidden' />
	<input id='dialog_input_goods_price' type='hidden' />
	<div class='selected-goods-info'>
		<div class='goods-thumb'><img id='dialog_goods_img' src='' alt=''></div>
		<dl class='goods-info'>
			<dt id='dialog_goods_name'></dt>
			<dd>销售价格：￥<span id='dialog_goods_price'></span></dd>
			<dd>折扣价格：
				<input id='dialog_xianshi_price' type='text' class='text w70' />
				<em class='add-on'><i class='icon-renminbi'></i></em>
			</dd>
		</dl>
	</div>
	<div class='eject_con'>
		<div class='bottom pt10 pb10'><a id='btn_submit' class='submit' href='javascript:void(0);'>提交</a></div>
	</div>
</div>
<table class='css-default-table'>
	<thead>
		<tr>
			<th class='w10'></th>
			<th class='w50'></th>
			<th class='tl'>商品名称</th>
			<th class='w90'>商品价格</th>
			<th class='w120'>折扣价格</th>
			<th class='w120'>折扣率</th>
			<th class='w120'>操作</th>
		</tr>
	</thead>
	<tbody id='xianshi_goods_list'>
		<tr id='xianshi_goods_list_norecord' style='display:none'>
			<td class='norecord' colspan='20'><div class='warning-option'><i class='icon-warning-sign'></i><span>暂无符合条件的数据记录</span></div></td>
		</tr>
	</tbody>
</table>
<div id='dialog_edit_xianshi_goods' style='display:none;'>
	<div class='eject_con'>
		<input id='dialog_xianshi_goods_id' type='hidden' />
		<dl>
			<dt>商品价格：</dt>
			<dd><span id='dialog_edit_goods_price'></dd>
		</dl>
		<dl>
			<dt>折扣价格：</dt>
			<dd>
				<input id='dialog_edit_xianshi_price' type='text' class='text w70' />
				<em class='add-on'><i class='icon-renminbi'></i></em>
			</dd>
		</dl>
		<div class='bottom pt10 pb10'><a id='btn_edit_xianshi_goods_submit' class='submit' href='javascript:void(0);'>提交</a></div>
	</div>
</div>
<script id='xianshi_goods_list_template' type='text/html'>
<tr class='bd-line'>
    <td></td>
    <td><div class='pic-thumb'><img src='<%= image_url %>' alt=''></div></td>
    <td class='tl'><dl class='goods-name'><dt><%= goods_name %></dt></dl></td>
    <td>￥<%= goods_price %></td>
    <td>￥<span nctype='xianshi_price'><%= xianshi_price %></span></td>
    <td><span nctype='xianshi_discount'><%= xianshi_discount %></span></td>
    <td class='nscs-table-handle'>
		<span>
			<a nctype='btn_edit_xianshi_goods' class='btn-blue' data-xianshi-goods-id='<%= xianshi_goods_id %>' data-goods-price='<%=goods_price%>' href='javascript:void(0);'><p>编辑</p></a>
		</span>
		<span>
			<a nctype='btn_del_xianshi_goods' class='btn-red' data-xianshi-goods-id='<%= xianshi_goods_id %>' href='javascript:void(0);'><p>删除</p></a>
		</span>
    </td>
</tr>
</script> 

<script type='text/javascript'>
$(function(){
    // 当前编辑对象，默认为空
	$edit_item = {};

	//现实商品搜索
	$('#btn_show_goods_select').on('click', function() {
		$('#div_goods_select').show();
	});

	//隐藏商品搜索
	$('#btn_hide_goods_select').on('click', function() {
		$('#div_goods_select').hide();
	});

	//搜索商品
	$('#btn_search_goods').on('click', function() {
		var url = '<?php echo _url('shop_goods/select_goods_view');?>';
		url += '?' + $.param({goods_name: $('#search_goods_name').val(), 'has_special_goods': 0, input_name: 'btn_add_xianshi_goods'});
		$('#div_goods_search_result').load(url);
	});

	//添加限时折扣商品弹出窗口
	$('#div_goods_search_result').on('click', '[nctype=btn_add_xianshi_goods]', function() {
		$('#dialog_goods_id').val($(this).attr('goods_commonid'));
		$('#dialog_goods_name').text($(this).attr('goods_name'));
		$('#dialog_goods_price').text($(this).attr('goods_price'));
		$('#dialog_input_goods_price').val($(this).attr('goods_price'));
		$('#dialog_goods_img').attr('src', $(this).attr('goods_image'));
		var _html = $('#dialog_add_xianshi_goods').html();
		add_dialog = html_form('add_xianshi_goods', '添加商品', _html, 450, 1);
		$('#dialog_xianshi_price').val('');
	});

	//添加限时折扣商品
	$('body').on('click', '#fwin_add_xianshi_goods #btn_submit', function() {
		var goods_id = $('#fwin_add_xianshi_goods #dialog_goods_id').val();
		var xianshi_id = <?php echo input('xianshi_id', 0, 'intval');?>;
		var goods_price = Number($('#fwin_add_xianshi_goods #dialog_input_goods_price').val());
		var xianshi_price = Number($('#fwin_add_xianshi_goods #dialog_xianshi_price').val());
		add_dialog.close();
		if(!isNaN(xianshi_price) && xianshi_price > 0 && xianshi_price < goods_price) {
			postAjax('<?php echo _url('promotion_xianshi/xianshi_goods_add');?>', {goods_id: goods_id, xianshi_id: xianshi_id, xianshi_price: xianshi_price}, function(e) {
				if (e.state == 200) {
					$('#xianshi_goods_list').prepend(template.render('xianshi_goods_list_template', e.data.xianshi_goods)).hide().fadeIn('slow');
					$('#xianshi_goods_list_norecord').hide();
				} else {
					showError(e.msg);
				}
			});
		} else {
			showError('折扣价格不能为空，且必须小于商品价格');
		}
	});

	//编辑限时活动商品
	$('#xianshi_goods_list').on('click', '[nctype=btn_edit_xianshi_goods]', function() {
		$edit_item = $(this).parents('tr.bd-line');
		var xianshi_goods_id = $(this).attr('data-xianshi-goods-id');
		var xianshi_price = $edit_item.find('[nctype=xianshi_price]').html();
		var goods_price = $(this).attr('data-goods-price');
		$('#dialog_xianshi_goods_id').val(xianshi_goods_id);
		$('#dialog_edit_goods_price').html(goods_price);
		//console.log(xianshi_price);
		$('#dialog_edit_xianshi_price').attr('value', xianshi_price);
		var _html = $('#dialog_edit_xianshi_goods').html();
		edit_dialog = html_form('edit_xianshi_goods', '修改价格', _html, 450, 1);
	});

	$('body').on('click', '#fwin_edit_xianshi_goods #btn_edit_xianshi_goods_submit', function() {
		var xianshi_goods_id = $('#fwin_edit_xianshi_goods #dialog_xianshi_goods_id').val();
		var xianshi_price = Number($('#fwin_edit_xianshi_goods #dialog_edit_xianshi_price').val());
		var goods_price = Number($('#fwin_edit_xianshi_goods #dialog_edit_goods_price').text());
		edit_dialog.close();
		if(!isNaN(xianshi_price) && xianshi_price > 0 && xianshi_price < goods_price) {
			postAjax('<?php echo _url('promotion_xianshi/xianshi_goods_price_edit');?>', {xianshi_goods_id: xianshi_goods_id, xianshi_price: xianshi_price}, function(e) {
				if (e.state == 200) {
					$edit_item.find('[nctype=xianshi_price]').text(e.data.xianshi_price);
					$edit_item.find('[nctype=xianshi_discount]').text(e.data.xianshi_discount);
					$('#dialog_edit_xianshi_goods').hide();
				} else {
					showError(e.msg);
				}
			});
		} else {
			showError('折扣价格不能为空，且必须小于商品价格');
		}
	});

	//删除限时活动商品
	$('#xianshi_goods_list').on('click', '[nctype=btn_del_xianshi_goods]', function() {
		var $this = $(this);
		get_confirm('确认删除？', function() {
			var xianshi_goods_id = $this.attr('data-xianshi-goods-id');
			postAjax('<?php echo _url('promotion_xianshi/xianshi_goods_delete');?>', {xianshi_goods_id: xianshi_goods_id}, function(e) {
				if (e.state == 200) {
					$this.parents('tr').hide('slow', function() {
						var xianshi_goods_count = $('#xianshi_goods_list').find('.bd-line:visible').length;
						if(xianshi_goods_count <= 0) {
							$('#xianshi_goods_list_norecord').show();
						}
					});
				} else {
					showError(e.msg);
				}
			});
		});
	});

	//渲染限时折扣商品列表
	xianshi_goods_array = $.parseJSON('<?php echo json_encode($output['xianshi_goods_list']);?>');
	if(xianshi_goods_array.length > 0) {
		var xianshi_goods_list = '';
		$.each(xianshi_goods_array, function(index, xianshi_goods) {
			xianshi_goods_list += template.render('xianshi_goods_list_template', xianshi_goods);
		});
		$('#xianshi_goods_list').prepend(xianshi_goods_list);
	} else {
		$('#xianshi_goods_list_norecord').show();
	}
});
</script>