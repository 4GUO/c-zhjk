(function($) {
	$.fn.fxy_region = function(options) {
		var $region = $(this);
		var settings = $.extend({}, {
			area_id: 0,
			region_span_class: '_region_value',
			src: 'cache',
			show_deep: 0,
			btn_style_html: '',
			tip_type: ''			
		}, options);
		settings.islast = false;
		settings.selected_deep = 0;
		settings.last_text = '';
		this.each(function() {
			var $inputArea = $(this);
			if ($inputArea.val() === '') {
				initArea($inputArea)
			} else {
				var $region_span = $('<span id=\'_area_span\' class=\'' + settings.region_span_class + '\'>' + $inputArea.val() + '</span>');
				var $region_btn = $('<input type=\'button\' class=\'input-btn\' ' + settings.btn_style_html + ' value=\'编辑\' />');
				$inputArea.after($region_span);
				$region_span.after($region_btn);
				$region_btn.on('click', function() {
					$region_span.remove();
					$region_btn.remove();
					initArea($inputArea)
				});
				settings.islast = true
			}
			this.settings = settings;
			if ($inputArea.val() && /^\d+$/.test($inputArea.val())) {
				$.getJSON('/background.php?c=index&a=json_area_show&area_id=' + $inputArea.val() + '&callback=?', function(data) {
					$('#_area_span').html(data.text == null ? '无' : data.text)
				})
			}
		});

		function initArea($inputArea) {
			settings.$area = $('<select></select>');
			$inputArea.before(settings.$area);
			loadAreaArray(function() {
				loadArea(settings.$area, settings.area_id)
			})
		}
		function loadArea($area, area_id) {
			if ($area && nc_a[area_id].length > 0) {
				var areas = [];
				areas = nc_a[area_id];
				if (settings.tip_type && settings.last_text != '') {
					$area.append('<option value=\'\'>' + settings.last_text + '(*)</option>')
				} else {
					$area.append('<option value=\'\'>-请选择-</option>')
				}
				for (i = 0; i < areas.length; i++) {
					$area.append('<option value=\'' + areas[i][0] + '\'>' + areas[i][1] + '</option>')
				}
				settings.islast = false
			}
			$area.on('change', function() {
				var region_value = '',
					area_ids = [],
					selected_deep = 1;
				$(this).nextAll('select').remove();
				$region.parent().find('select').each(function() {
					if ($(this).find('option:selected').val() != '') {
						region_value += $(this).find('option:selected').text() + ' ';
						area_ids.push($(this).find('option:selected').val())
					}
				});
				settings.selected_deep = area_ids.length;
				settings.area_ids = area_ids.join(' ');
				$region.val(region_value);
				settings.area_id_1 = area_ids[0] ? area_ids[0] : '';
				settings.area_id_2 = area_ids[1] ? area_ids[1] : '';
				settings.area_id_3 = area_ids[2] ? area_ids[2] : '';
				settings.area_id_4 = area_ids[3] ? area_ids[3] : '';
				settings.last_text = $region.prevAll('select').find('option:selected').last().text();
				var area_id = settings.area_id = $(this).val();
				if ($('#_area_1').length > 0) $('#_area_1').val(settings.area_id_1);
				if ($('#_area_2').length > 0) $('#_area_2').val(settings.area_id_2);
				if ($('#_area_3').length > 0) $('#_area_3').val(settings.area_id_3);
				if ($('#_area_4').length > 0) $('#_area_4').val(settings.area_id_4);
				if ($('#_area').length > 0) $('#_area').val(settings.area_id);
				if ($('#_areas').length > 0) $('#_areas').val(settings.area_ids);
				if (settings.show_deep > 0 && $region.prevAll('select').size() == settings.show_deep) {
					settings.islast = true;
					if (typeof settings.last_click == 'function') {
						settings.last_click(area_id);
					}
					return
				}
				if (area_id > 0) {
					if (nc_a[area_id] && nc_a[area_id].length > 0) {
						var $newArea = $('<select></select>');
						$(this).after($newArea);
						loadArea($newArea, area_id);
						settings.islast = false
					} else {
						settings.islast = true;
						if (typeof settings.last_click == 'function') {
							settings.last_click(area_id);
						}
					}
				} else {
					settings.islast = false
				}
				if ($('#islast').length > 0) $('#islast').val('');
			})
		}
		function loadAreaArray(callback) {
			if (typeof nc_a === 'undefined') {
				$.getJSON('/background.php?c=index&a=json_area&src=' + settings.src + '&callback=?', function(data) {
					nc_a = data;
					callback()
				})
			} else {
				callback()
			}
		}
		if (typeof jQuery.validator != 'undefined') {
			jQuery.validator.addMethod('checklast', function(value, element) {
				return $(element).fetch('islast');
			}, '请将地区选择完整');
		}
	};
	$.fn.fetch = function(k) {
		var p;
		this.each(function() {
			if (this.settings) {
				p = eval('this.settings.' + k);
				return false
			}
		});
		return p
	}
})(jQuery);
function serialize(form) {
	var form = document.getElementById(form);
	var arr = {};
	for (var i = 0; i < form.elements.length; i++) {
		var feled = form.elements[i];
		switch (feled.type) {
		case undefined:
		case 'button':
		case 'file':
		case 'reset':
		case 'submit':
			break;
		case 'checkbox':
		case 'radio':
			if (!feled.checked) {
				break;
			}
		default:
			if (arr[feled.name]) {
				arr[feled.name] = arr[feled.name] + ',' + feled.value;
			} else {
				arr[feled.name] = feled.value;

			}
		}
	}
	return arr
} 
/* 显示Ajax表单 */
function ajax_form(id, title, url, width, height, model) {
	if (!width) width = 480;
	if (!height) height = 'auto';
	if (!model) model = 1;
	var d = DialogManager.create(id);
	d.setTitle(title);
	d.setContents('ajax', url);
	d.setWidth(width);
	d.setHeight(height);
	d.show('center', model);
	return d;
}
//显示一个内容为自定义HTML内容的消息

function html_form(id, title, _html, width, model) {
	if (!width) width = 480;
	if (!model) model = 1;
	var d = DialogManager.create(id);
	d.setTitle(title);
	d.setContents(_html);
	d.setWidth(width);
	d.show('center', model);
	return d;
}

function showSucc(msg, callback, pos, time, style, width, model) {
	if(typeof(CUR_DIALOG) != 'undefined'){
		CUR_DIALOG.close();
	}
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	if (!pos) pos = 'top';
	if (!time) time = 3;
	if (!style) style = 'new';
	if (!width) width = 400;
	if (!model) model = 1;
	if (msg !== '') {
		var d = DialogManager.create('success');
		d.setTitle('提示消息');
		d.setContents('message', {
			type: 'notice',
			text: msg,
			callback: callback,
			time: time,
			style: style
		});
		d.setWidth(width);
		d.show(pos, model);
		return d;
	}
}

function showError(msg, callback, pos, time, style, width, model) {
	if(typeof(CUR_DIALOG) != 'undefined'){
		//CUR_DIALOG.close();
	}
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	if (!pos) pos = 'top';
	if (!time) time = 2;
	if (!style) style = 'new';
	if (!width) width = 400;
	if (!model) model = 1;
	if (msg !== '') {
		var d = DialogManager.create('error');
		d.setTitle('提示消息');
		d.setContents('message', {
			type: 'warning',
			text: msg,
			callback: callback,
			time: time,
			style: style
		});
		d.setWidth(width);
		d.show(pos, model);
		return d;
	}
}

function ajax_get_confirm(msg, url, callback, width, model) {
	if(typeof(CUR_DIALOG) != 'undefined'){
		CUR_DIALOG.close();
	}
	if (!width) width = 400;
	if (!model) model = 1;
	if (msg != '') {
		var d = DialogManager.create('ajax_confirm');
		d.setTitle('提示消息');
		d.setContents('message', {
			type: 'confirm',
			text: msg,
			yes_button_name: '确定',
			no_button_name: '取消',
			onClickYes: function() {
				getAjax(url, {}, function(e) {
					if(!callback){
						if (e.state == 200) {
							location.href = e.data.url;
						} else {
							showError(e.msg);
						}
					}else{
						typeof(callback) == 'function' && callback(e);
					}
				});
			}
		});
		d.setWidth(width);
		d.show('center', model);
		return d;
	}
}

function get_confirm(msg, callback, width, model) {
	if(typeof(CUR_DIALOG) != 'undefined'){
		//CUR_DIALOG.close();
	}
	if (!width) width = 400;
	if (!model) model = 1;
	if (msg != '') {
		var d = DialogManager.create('confirm');
		d.setTitle('提示消息');
		d.setContents('message', {
			type: 'confirm',
			text: msg,
			yes_button_name: '确定',
			no_button_name: '取消',
			onClickYes: callback
		});
		d.setWidth(width);
		d.show('center', model);
		return d;
	}
}

function ajax_form_post(formid, callback) {
	var url = $$(formid).getAttribute('action');
	var data = serialize(formid);
	postAjax(url, data, function(e) {
		if(!callback){
			if (e.state == 200) {
				if (e.data.msg) {
					showSucc(e.data.msg, function(){
						if(typeof(e.data.url) != 'undefined'){
							location.href = e.data.url;
						}
					});
				}
			}else{
				showError(e.msg);
			}
		}else{
			typeof(callback) == 'function' && callback(e);
		}
	});
}
function loading(msg, style){
	if (!style) style = 'new';
	var width = 250;
	var model = 1;
	if (msg != '') {
		var d = DialogManager.create('loading');
		d.setTitle(null);
		d.setContents('loading', {
			text: msg,
			style: style
		});
		d.setWidth(width);
		d.show('center', model);
		return d;
	}
}
function loading_hide(){
	DialogManager.close('loading');
	return true;
}
//单独上传图片
function upload_file(file_btn_id, url, callback) {
	$('#' + file_btn_id).fileupload({
        dataType: 'json',
        url: url,
        formData: {name : file_btn_id},
        add: function (e, data) {
        	$('img[nctype=' + file_btn_id + ']').attr('src', DIALOGIMGDIR + 'loading.gif');
            data.submit();
        },
        done: function (e, data) {
            var param = data.result;
            if (param.state == 400) {
                showError(param.msg);
            } else {
				if(typeof(callback) == 'function'){
					callback(param.data);
				} else {
					$('input[nctype=' + file_btn_id + ']').val(param.data.file_url);
					$('img[nctype=' + file_btn_id + ']').attr('src', param.data.file_url);
				}
            }
        }
    });
}
//编辑器上传图片
function editor_upload_file(file_btn_id, url, callback){
	$('#' + file_btn_id + '_fileupload').fileupload({
        dataType: 'json',
        url: url,
        formData: {name : file_btn_id + '_fileupload'},
        add: function (e, data) {
        	$('img[nctype=' + file_btn_id + '_fileupload' + ']').attr('src', DIALOGIMGDIR + 'loading.gif');
            data.submit();
        },
        done: function (e, data) {
            var param = data.result;
            if (param.state == 400) {
                showError(param.msg);
            } else {
				if(typeof(callback) == 'function'){
					callback(param.data);
				}
            }
        }
    });
}
//单独上传文件
function upload_file2(file_btn_id, url){
	$('#' + file_btn_id).fileupload({
        dataType: 'json',
        url: url,
        formData: {name : file_btn_id},
        add: function (e, data) {
            data.submit();
        },
        done: function (e, data) {
            var param = data.result;
            if (param.state == 400) {
                showError(param.msg);
            } else {
                $('input[nctype=' + file_btn_id + ']').val(param.data.file_url);
                $('video[rel=' + file_btn_id + ']').attr('src', param.data.file_url);
            }
        }
    });
}

//浮点数计算
function number_format(num, ext){
    if(ext < 0){
        return num;
    }
    num = Number(num);
    if(isNaN(num)){
        num = 0;
    }
    var _str = num.toString();
    var _arr = _str.split('.');
    var _int = _arr[0];
    var _flt = _arr[1];
    if(_str.indexOf('.') == -1){
        /* 找不到小数点，则添加 */
        if(ext == 0){
            return _str;
        }
        var _tmp = '';
        for(var i = 0; i < ext; i++){
            _tmp += '0';
        }
        _str = _str + '.' + _tmp;
    }else{
        if(_flt.length == ext){
            return _str;
        }
        /* 找得到小数点，则截取 */
        if(_flt.length > ext){
            _str = _str.substr(0, _str.length - (_flt.length - ext));
            if(ext == 0){
                _str = _int;
            }
        }else{
            for(var i = 0; i < ext - _flt.length; i++){
                _str += '0';
            }
        }
    }

    return _str;
}
var global_obj = {
	// 初始化控制面板
	init: function(){
		var that = this;
	},
	mbPannelInit: function(){
        $('#mobile_pannel')
            .find('.hand[nctype^=mp_]').show().end()
            .find('.module')
            .first().find('.hand[nctype=mp_up]').hide().end().end()
            .last().find('.hand[nctype=mp_down]').hide();
		$('.module').each(function(){
			$(this).attr('data_itemid', $(this).index());
		});
    },
	// 数据移动 
	// type 0上移  1下移
	mDataMove: function(index, type) {
		var that = this;
		_m_data = that.mDataGet();
		_data = _m_data.splice(index, 1);
		if (type) {
			index += 1;
		} else {
			index -= 1;
		}
		_m_data.splice(index, 0, _data[0]);
		return that.mDataSet(_m_data);
	},
	// 数据移除
	mDataRemove: function(index) {
		var that = this;
		_m_data = that.mDataGet();
		_m_data.splice(index, 1);     // 删除数据
		return that.mDataSet(_m_data);
	},
	// 替换数据
	mDataReplace: function (index, data){
		var that = this;
		_m_data = that.mDataGet();
		_m_data.splice(index, 1, data);
		return that.mDataSet(_m_data);
	},
	mDataInsert: function(data){// 插入数据
	    var that = this;
		_m_data = that.mDataGet();
		_m_data.push(data);
		return that.mDataSet(_m_data);
	},
	// 获取数据
	mDataGet: function (){
		_m_body = $('input[name=m_body]').val();
		if (_m_body == '' || _m_body == 'false') {
			var _m_data = new Array;
		} else {
			eval('var _m_data = ' + _m_body);
		}
		return _m_data;
	},
	// 设置数据
	mDataSet: function (data){
		_data = JSON.stringify(data);
		$('input[name=m_body]').val(_data);
		return true;
	},
	// 转码
    toTxt: function(str) {
        var RexStr = /\<|\>|\"|\'|\&|\\/g
        str = str.replace(RexStr, function(MatchStr) {
            switch (MatchStr) {
            case '<':
                return '';
                break;
            case '>':
                return '';
                break;
            case '"':
                return '';
                break;
            case '\'':
                return '';
                break;
            case '&':
                return '';
                break;
            case '\\':
                return '';
                break;
            default:
                break;
            }
        })
        return str;
    },
}
$(function() {
	global_obj.init();
    /* 全选 */
	$('.checkall').click(function() {
		var _self = this;
		$('.checkitem').each(function() {
			if (!this.disabled) {
				$(this).attr('checked', _self.checked);
			}
		});
		$('.checkall').attr('checked', this.checked);
	}); 
	/* 批量操作按钮 */
	$('a[nc_type=batchbutton]').click(function() { 
	    /* 是否有选择 */
		if ($('.checkitem:checked').length == 0) { //没有选择
			showError('请选择需要操作的记录');
			return false;
		}
		var _uri = $(this).attr('uri');
		var _name = $(this).attr('name');
		var handleResult = function(uri, name) {
				/* 获取选中的项 */
				var items = '';
				$('.checkitem:checked').each(function() {
					items += this.value + ',';
				});
				items = items.substr(0, (items.length - 1)); 
				/* 将选中的项通过GET方式提交给指定的URI */
				getAjax(uri + '?' + name + '=' + items, {}, function(e){
					if(e.state == 200){
						location.reload();
					}else{
						showError(e.msg);
					}
				});
				return false;
			}
		if ($(this).attr('confirm')) {
			get_confirm($(this).attr('confirm'), function(){
				handleResult(_uri, _name)
			});
			return false;
		}
	}); /* 弹窗 */
	$('body,div[nctype=mobile_pannel]').on('click', '[nc_type=dialog]', function() {
		var id = $(this).attr('dialog_id');
		var title = $(this).attr('dialog_title') ? $(this).attr('dialog_title') : '';
		var url = $(this).attr('uri');
		var width = $(this).attr('dialog_width');
		var height = $(this).attr('dialog_height');
		CUR_DIALOG = ajax_form(id, title, url, width, height, 0);
		CUR_DIALOG_PAGE = CUR_DIALOG;
		return false;
	});
});