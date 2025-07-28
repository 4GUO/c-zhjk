function initItemView(index, type){
	$('.module').each(function(){
		$(this).attr('data_itemid', $(this).index());
	});
	var _m_data = global_obj.mDataGet();
	var cur_data = _m_data[index];
	cur_data['index'] = index;
	global_obj.mDataReplace(index, cur_data);
	var html = template.render(type + '_tpl', cur_data);
	return html;
}
function initEditorBox(index, type){
	$('#mobile_pannel .module').removeClass('selectObj');
    $('.module[data_itemid=' + index + ']').addClass('selectObj');
	var _m_data = global_obj.mDataGet();
	var cur_data = _m_data[index];
	cur_data['index'] = index;
	global_obj.mDataReplace(index, cur_data);
	var html = template.render(type + '_tool', cur_data);
	$( '.explain' ).html(html);
	$('.bg_color').colorpicker({hideButton: true});
	$('.font_color').colorpicker({hideButton: true});
	$('.border_color').colorpicker({hideButton: true});
	//字体大小
	$('#slider_font_size').slider({
		range: 'min',
		value: $('input[name=font_size]').val(),
		min: 9,
		max: 30,
		slide: function( event, ui ) {
			var index = $(this).parents('.edit_box').attr('data_editid');
			var value = ui.value;
			style_edit('fontSize', value, index);
			$(this).parents('.form-group').find('.count span').html(value);
		}
	});
	//上下边距
	$('#slider_padding_top').slider({
		range: 'min',
		value: $('input[name=paddingtop]').val(),
		min: 0,
		max: 30,
		step: 0.5,
		slide: function( event, ui ) {
			var index = $(this).parents('.edit_box').attr('data_editid');
			var value = ui.value;
			style_edit('paddingTop', value, index);
			style_edit('paddingBottom', value, index);
			$(this).parents('.form-group').find('.count span').html(value);
		}
	});
	//左右边距
	$('#slider_padding_left').slider({
		range: 'min',
		value: $('input[name=paddingleft]').val(),
		min: 0,
		max: 30,
		step: 0.5,
		slide: function( event, ui ) {
			var index = $(this).parents('.edit_box').attr('data_editid');
			var value = ui.value;
			style_edit('paddingLeft', value, index);
			style_edit('paddingRight', value, index);
			$(this).parents('.form-group').find('.count span').html(value);
		}
	});
	console.log(cur_data);
}
$(function(){
	$( '.tool_box' ).accordion();
	$( '.tool_box ul li' ).draggable({
		appendTo: 'body',
		helper: 'clone',
		zIndex: 999,
		cursor: 'move', //拖动的时候鼠标样式
	});
	$( '#mobile_pannel' ).droppable({
		activeClass: 'ui_active_class',
		hoverClass: 'ui_hover_class',
		accept: ':not(.ui-sortable-helper)',
		drop: function( event, ui ) {
			var _data = new Object;
			var value = new Object;
			var type = ui.draggable.attr('rel');
			if(type == 'txt'){
				value = '文本区域';
			}
			if(type == 'img'){
				value[0] = new Object;
				value[0].img = STATICURL + '/images/default_image.png';
				value[0].link = '';
				value[0].title = '文字区域';
			}
			if(type == 'v2'){
				value[0] = value[1] = new Object;
				value[0].img = STATICURL + '/images/default_image.png';
				value[0].link = '';
				value[0].title = '文字区域';
				value[1].img = STATICURL + '/images/default_image.png';
				value[1].link = '';
				value[1].title = '文字区域';
			}
			if(type == 'v3' || type == 'v1_2' || type == 'v2_1'){
				value[0] = value[1] = value[2] = new Object;
				value[0].img = STATICURL + '/images/default_image.png';
				value[0].link = '';
				value[0].title = '文字区域';
				value[1].img = STATICURL + '/images/default_image.png';
				value[1].link = '';
				value[1].title = '文字区域';
				value[2].img = STATICURL + '/images/default_image.png';
				value[2].link = '';
				value[2].title = '文字区域';
			}
			if(type == 'v4'){
				value[0] = value[1] = value[2] = value[3] = new Object;
				value[0].img = STATICURL + '/images/default_image.png';
				value[0].link = '';
				value[0].title = '文字区域';
				value[1].img = STATICURL + '/images/default_image.png';
				value[1].link = '';
				value[1].title = '文字区域';
				value[2].img = STATICURL + '/images/default_image.png';
				value[2].link = '';
				value[2].title = '文字区域';
				value[3].img = STATICURL + '/images/default_image.png';
				value[3].link = '';
				value[3].title = '文字区域';
			}
			if(type == 'v5' || type == 'v1_4' || type == 'v4_1' || type == 'imgs'){
				value[0] = value[1] = value[2] = value[3] = value[4] = new Object;
				value[0].img = STATICURL + '/images/default_image.png';
				value[0].link = '';
				value[0].title = '文字区域';
				value[1].img = STATICURL + '/images/default_image.png';
				value[1].link = '';
				value[1].title = '文字区域';
				value[2].img = STATICURL + '/images/default_image.png';
				value[2].link = '';
				value[2].title = '文字区域';
				value[3].img = STATICURL + '/images/default_image.png';
				value[3].link = '';
				value[3].title = '文字区域';
				value[4].img = STATICURL + '/images/default_image.png';
				value[4].link = '';
				value[4].title = '文字区域';
			}
			var style = new Object;
			style['color'] = '#333333';
			style['backgroundColor'] = '#ffffff';
			style['borderColor'] = '';
			style['borderStyle'] = '';
			style['borderWidth'] = '';
			style['textAlign'] = 'left';
			style['fontSize'] = 14;
			style['paddingTop'] = 0;
			style['paddingBottom'] = 0;
			style['paddingLeft'] = 0;
			style['paddingRight'] = 0;
			_data.type = type;
			_data.value = value;
			_data.style = style;
			_rs = global_obj.mDataInsert(_data);
			if (!_rs) {
				return false;
			}
			var _parent = $( '<div class=\'module\'></div>' ).attr('nctype', type).appendTo( this );
			var index = _parent.index();
			var tpl = initItemView(index, type);
			_parent.html(tpl);
			global_obj.mbPannelInit();
			initEditorBox(index, type);
		}
	})
    // 选中
    $('#mobile_pannel').on('click', '.dragPart', function(){
		var _parents = $(this).parents('.module:first');
		var type = _parents.attr('nctype');
		var index = _parents.index();
		initEditorBox(index, type);
		global_obj.mbPannelInit();
    });
    // 上移
    $('#mobile_pannel').on('click', '[nctype=mp_up]', function(){
        var _parents = $(this).parents('.module:first');
        _rs = global_obj.mDataMove(_parents.index(), 0);
        if (!_rs) {
            return false;
        }
        var new_parents = _parents.clone().insertBefore(_parents.prev());
		_parents.remove();
		global_obj.mbPannelInit();
    });
    // 下移
    $('#mobile_pannel').on('click', '[nctype=mp_down]', function(){
        var _parents = $(this).parents('.module:first');		
        _rs = global_obj.mDataMove(_parents.index(), 1);		
        if (!_rs) {
            return false;
        }
        var new_parents = _parents.clone().insertAfter(_parents.next());
		_parents.remove();
		global_obj.mbPannelInit();
    });
    // 删除
    $('#mobile_pannel').on('click', '[nctype=mp_del]', function(){
        var _parents = $(this).parents('.module:first');
		var index = _parents.index();
        global_obj.mDataRemove(index);
        _parents.remove();
		$( '.explain div[data_editid=' + index + ']' ).hide();
		global_obj.mbPannelInit();
    });
	
	//右侧编辑栏
	//txt
	$('.explain').on('keyup', '.edit_box .input', function() {
		var value = $(this).val();
		var index = $(this).parents('.edit_box').attr('data_editid');
		var _m_data = global_obj.mDataGet();
		var cur_data = _m_data[index];
		var value = global_obj.toTxt(value.replace(/[\r\n]/g,''));
		cur_data['value'] = value;
		global_obj.mDataReplace(index, cur_data);
		$('.module[data_itemid=' + index + '] .dragPart').html(value);
	});
	//标题
	$('.explain').on('keyup', '.edit_box input[name=title]', function() {
		var value = $(this).val();
		var index = $(this).parents('.edit_box').attr('data_editid');
		var key =  $(this).parents('.item_box').attr('key');
		value_edit('title', value, index, key);
	});
	//链接
	$('.explain').on('keyup', '.edit_box input[name=link]', function() {
		var value = $(this).val();
		var index = $(this).parents('.edit_box').attr('data_editid');
		var key =  $(this).parents('.item_box').attr('key');
		value_edit('link', value, index, key);
	});
	//图片
	$('.explain').on('keyup', '.edit_box input[name=img]', function() {
		var value = $(this).val();
		var index = $(this).parents('.edit_box').attr('data_editid');
		var key =  $(this).parents('.item_box').attr('key');
		value_edit('img', value, index, key);
	});
	//改变背景颜色
	$('.explain').on('change.color', '.bg_color', function(event, color){
		var index = $(this).parents('.edit_box').attr('data_editid');
		style_edit('backgroundColor', color, index);
	});
	//改变字体颜色
	$('.explain').on('change.color', '.font_color', function(event, color){
		var index = $(this).parents('.edit_box').attr('data_editid');
		style_edit('color', color, index);
	});
	//改变边框颜色
	$('.explain').on('change.color', '.border_color', function(event, color){
		var index = $(this).parents('.edit_box').attr('data_editid');
		style_edit('borderColor', color, index);
	});
	//对齐方向
	$('.explain').on('click', '.edit_box .radio-inline', function(){
		var value = $('input[name=text_align]', this).val();
		var index = $(this).parents('.edit_box').attr('data_editid');
		style_edit('textAlign', value, index);
	});
});
function style_edit(name, value, index){
	var _m_data = global_obj.mDataGet();
	var cur_data = _m_data[index];
	cur_data['style'][name] = value;
	global_obj.mDataReplace(index, cur_data);
	var type = $('.module[data_itemid=' + index + ']').attr('nctype');
	if(name == 'color'){
		$('.module[data_itemid=' + index + '] .dragPart').css('color', value);
		$('.edit_box[data_editid=' + index + '] .font_color').css('color', value);
	}else if(name == 'backgroundColor'){
		$('.module[data_itemid=' + index + '] .dragPart').css('background-color', value);
		$('.edit_box[data_editid=' + index + '] .bg_color').css('background-color', value);
	}else if(name == 'borderColor'){
		$('.module[data_itemid=' + index + '] .dragPart').css('border-color', value);
	}else if(name == 'fontSize'){
		$('.module[data_itemid=' + index + '] .dragPart').css('font-size', value);
	}else if(name == 'borderStyle'){
		$('.module[data_itemid=' + index + '] .dragPart').css('border-style', value);
	}else if(name == 'borderWidth'){
		$('.module[data_itemid=' + index + '] .dragPart').css('border-width', value);
	}else if(name == 'textAlign'){
		$('.module[data_itemid=' + index + '] .dragPart').css('text-align', value);
	}else if(name == 'paddingTop'){
		$('.module[data_itemid=' + index + '] .dragPart').css({'padding-top': value, 'padding-bottom': value});
		$('.module[data_itemid=' + index + '] .imgObj').css({'padding-top': value, 'padding-bottom': value});
		$('.module[data_itemid=' + index + '] .wordObj').css({'padding-top': value, 'padding-bottom': value});
	}else if(name == 'paddingLeft'){
		$('.module[data_itemid=' + index + '] .dragPart').css({'padding-left': value, 'padding-right': value});
		$('.module[data_itemid=' + index + '] .imgObj').css({'padding-left': value, 'padding-right': value});
		$('.module[data_itemid=' + index + '] .wordObj').css({'padding-left': value, 'padding-right': value});
	}
}
function value_edit(name, value, index, key){
	var _m_data = global_obj.mDataGet();
	var cur_data = _m_data[index];
	var cur_data_item = cur_data['value'][key];
	cur_data_item[name] = value;
	cur_data['value'][key] = cur_data_item;
	global_obj.mDataReplace(index, cur_data);
	if(name == 'title'){
		$('.module[data_itemid=' + index + '] .dragPart .wordObj').eq(key).html(value);
		$('.edit_box[data_editid=' + index + '] input[name=title]').eq(key).val(value);
	}else if(name == 'img'){
		$('.module[data_itemid=' + index + '] .dragPart .imgObj').eq(key).html('<img src=\'' + value + '\'/>');
		$('.edit_box[data_editid=' + index + '] input[name=img]').eq(key).val(value);
		$('.edit_box[data_editid=' + index + '] .item-image img').eq(key).attr('src', value);
	}
}