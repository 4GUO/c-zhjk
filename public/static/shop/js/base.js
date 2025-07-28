(function($) {
	$.extend($, {
		scrollTransparent: function(e) {
			var t = {
				valve: '#header',
				scrollHeight: 50
			};
			var e = $.extend({}, t, e);

			function a() {
				$(window).scroll(function() {
					if ($(window).scrollTop() <= e.scrollHeight) {
						$(e.valve).addClass('transparent').removeClass('posf')
					} else {
						$(e.valve).addClass('posf').removeClass('transparent')
					}
				})
			}
			return this.each(function() {
				a()
			})()
		},
		areaSelected: function(options) {
			var defaults = {
				success: function(e) {}
			};
			var options = $.extend({}, defaults, options);
			var ASID = 0;
			var ASID_1 = 0;
			var ASID_2 = 0;
			var ASID_3 = 0;
			var ASNAME = '';
			var ASINFO = '';
			var ASDEEP = 1;
			var ASINIT = true;
			var myScrollArea;

			function _init() {
				if ($('#areaSelected').length > 0) {
					$('#areaSelected').remove()
				}
				var e = '<div id=\'areaSelected\'>' + '<div class=\'nctouch-full-mask left\'>' + '<div class=\'nctouch-full-mask-bg\'></div>' + '<div class=\'nctouch-full-mask-block\'>' + '<div class=\'header\'>' + '<div class=\'header-wrap\'>' + '<div class=\'header-l\'><a href=\'javascript:void(0);\'><i class=\'back\'></i></a></div>' + '<div class=\'header-title\'>' + '<h1>选择地区</h1>' + '</div>' + '<div class=\'header-r\'><a href=\'javascript:void(0);\'><i class=\'close\'></i></a></div>' + '</div>' + '</div>' + '<div class=\'nctouch-main-layout\'>' + '<div class=\'nctouch-single-nav\'>' + '<ul id=\'filtrate_ul\' class=\'area\'>' + '<li class=\'selected\'><a href=\'javascript:void(0);\'>一级地区</a></li>' + '<li><a href=\'javascript:void(0);\' >二级地区</a></li>' + '<li><a href=\'javascript:void(0);\' >三级地区</a></li>' + '</ul>' + '</div>' + '<div class=\'nctouch-main-layout-a\'><ul class=\'nctouch-default-list\'></ul></div>' + '</div>' + '</div>' + '</div>' + '</div>';
				$('body').append(e);
				_getAreaList();
				_bindEvent();
				_close()
			}

			function _getAreaList() {
				if(ASID == '' || typeof(ASID) == 'undefined'){
					ASID = 0;
				}
				getAjax(url('shop/area/area_list'), { area_id: ASID }, function(e) {
					if (e.data.area_list.length == 0) {
						_finish();
						return false
					}
					if (ASINIT) {
						ASINIT = false
					} else {
						ASDEEP++
					}
					$('#areaSelected').find('#filtrate_ul').find('li').eq(ASDEEP - 1).addClass('selected').siblings().removeClass('selected');
					var t = e.data;
					var a = '';
					for (var n = 0; n < t.area_list.length; n++) {
						a += '<li><a href=\'javascript:void(0);\' data-id=\'' + t.area_list[n].area_id + '\' data-name=\'' + t.area_list[n].area_name + '\'><h4>' + t.area_list[n].area_name + '</h4><span class=\'arrow-r\'></span> </a></li>'
					}
					$('#areaSelected').find('.nctouch-default-list').html(a);
					if (typeof myScrollArea == 'undefined') {
						if (typeof IScroll == 'undefined') {
							$.ajax({
								url: STATICURL + '/shop/js/iscroll.js',
								dataType: 'script',
								async: false
							})
						}
						myScrollArea = new IScroll('#areaSelected .nctouch-main-layout-a', {
							mouseWheel: true,
							click: true
						})
					} else {
						myScrollArea.refresh()
					}
				})
				return false
			}

			function _bindEvent() {
				$('#areaSelected').find('.nctouch-default-list').off('click', 'li > a');
				$('#areaSelected').find('.nctouch-default-list').on('click', 'li > a', function() {
					ASID = $(this).attr('data-id');
					eval('ASID_' + ASDEEP + '=$(this).attr(\'data-id\')');
					ASNAME = $(this).attr('data-name');
					ASINFO += ASNAME + ' ';
					var _li = $('#areaSelected').find('#filtrate_ul').find('li').eq(ASDEEP);
					_li.prev().find('a').attr({
						'data-id': ASID,
						'data-name': ASNAME
					}).html(ASNAME);
					if (ASDEEP == 3) {
						_finish();
						return false
					}
					_getAreaList()
				});
				$('#areaSelected').find('#filtrate_ul').off('click', 'li > a');
				$('#areaSelected').find('#filtrate_ul').on('click', 'li > a', function() {
					if ($(this).parent().index() >= $('#areaSelected').find('#filtrate_ul').find('.selected').index()) {
						return false
					}
					ASID = $(this).parent().prev().find('a').attr('data-id');
					ASNAME = $(this).parent().prev().find('a').attr('data-name');
					ASDEEP = $(this).parent().index();
					ASINFO = '';
					for (var e = 0; e < $('#areaSelected').find('#filtrate_ul').find('a').length; e++) {
						if (e < ASDEEP) {
							ASINFO += $('#areaSelected').find('#filtrate_ul').find('a').eq(e).attr('data-name') + ' '
						} else {
							var t = '';
							switch (e) {
							case 0:
								t = '一级地区';
								break;
							case 1:
								t = '二级地区';
								break;
							case 2:
								t = '三级地区';
								break
							}
							$('#areaSelected').find('#filtrate_ul').find('a').eq(e).html(t)
						}
					}
					_getAreaList()
				})
			}

			function _finish() {
				var e = {
					area_id: ASID,
					area_id_1: ASID_1,
					area_id_2: ASID_2,
					area_id_3: ASID_3,
					area_name: ASNAME,
					area_info: ASINFO
				};
				options.success.call('success', e);
				if (!ASINIT) {
					$('#areaSelected').find('.nctouch-full-mask').addClass('right').removeClass('left')
				}
				return false
			}

			function _close() {
				$('#areaSelected').find('.header-l').off('click', 'a');
				$('#areaSelected').find('.header-l').on('click', 'a', function() {
					$('#areaSelected').find('.nctouch-full-mask').addClass('right').removeClass('left');
				});
				return false
			}
			return this.each(function() {
				return _init()
			})()
		},
		animationLeft: function(e) {
			var t = {
				valve: '.animation-left',
				wrapper: '.nctouch-full-mask',
				scroll: ''
			};
			var e = $.extend({}, t, e);

			function a() {
				$(e.valve).click(function() {
					$(e.wrapper).removeClass('hide').removeClass('right').addClass('left');
					if (e.scroll != '') {
						if (typeof myScrollAnimationLeft == 'undefined') {
							if (typeof IScroll == 'undefined') {
								$.ajax({
									url: STATICURL + '/shop/js/iscroll.js',
									dataType: 'script',
									async: false
								})
							}
							myScrollAnimationLeft = new IScroll(e.scroll, {
								mouseWheel: true,
								click: true
							})
						} else {
							myScrollAnimationLeft.refresh()
						}
					}
				});
				$(e.wrapper).on('click', '.header-l > a', function() {
					$(e.wrapper).addClass('right').removeClass('left')
				})
			}
			return this.each(function() {
				a()
			})()
		},
		animationUp: function(e) {
			var t = {
				valve: '.animation-up',
				wrapper: '.nctouch-bottom-mask',
				scroll: '.nctouch-bottom-mask-rolling',
				start: function() {},
				close: function() {}
			};
			var e = $.extend({}, t, e);

			function a() {
				e.start.call('start');
				$(e.wrapper).removeClass('down').addClass('up');
				if (e.scroll != '') {
					if (typeof myScrollAnimationUp == 'undefined') {
						if (typeof IScroll == 'undefined') {
							$.ajax({
								url: STATICURL + '/shop/js/iscroll.js',
								dataType: 'script',
								async: false
							})
						}
						myScrollAnimationUp = new IScroll(e.scroll, {
							mouseWheel: true,
							click: true
						})
					} else {
						myScrollAnimationUp.refresh()
					}
				}
			}
			return this.each(function() {
				if (e.valve != '') {
					$(e.valve).on('click', function() {
						a()
					})
				} else {
					a()
				}
				$(e.wrapper).on('click', '.nctouch-bottom-mask-bg,.nctouch-bottom-mask-close', function() {
					$(e.wrapper).addClass('down').removeClass('up');
					e.close.call('close')
				})
			})()
		}
	})
})(Zepto);
function writeClear(e) {
	if (e.val().length > 0) {
		e.parent().addClass('write')
	} else {
		e.parent().removeClass('write')
	}
	btnCheck(e.parents('form'))
}
function btnCheck(e) {
	var t = true;
	e.find('input').each(function() {
		if ($(this).hasClass('no-follow')) {
			return
		}
		if ($(this).val().length == 0) {
			t = false
		}
	});
	if (t) {
		e.find('.btn').parent().addClass('ok')
	} else {
		e.find('.btn').parent().removeClass('ok')
	}
}
function $$(id) {
	return !id ? null : document.getElementById(id);
}
function getQueryString(e) {
	var t = new RegExp('(^|&)' + e + '=([^&]*)(&|$)');
	var a = window.location.search.substr(1).match(t);
	if (a != null) return a[2];
	return ''
}
function getQuery(url) {
	var theRequest = [];
	if (url.indexOf('?') != -1) {
		var str = url.split('?')[1];
		var strs = str.split('&');
		for (var i = 0; i < strs.length; i++) {
			if (strs[i].split('=')[0] && unescape(strs[i].split('=')[1])) {
				theRequest[i] = {
					'name': strs[i].split('=')[0],
					'value': unescape(strs[i].split('=')[1])
				}
			}
		}
	}
	return theRequest;
}
function isString(str){
    return (typeof str == 'string') && str.constructor == String;
} 
function isObject(obj) {
    return (typeof obj == 'object') && obj.constructor == Object;
}
function isArray(obj){
    return (typeof obj == 'object') && obj.constructor == Array;
} 
function in_array(needle, haystack) {
	if (typeof needle == 'string' || typeof needle == 'number') {
		for (var i in haystack) {
			if (haystack[i] == needle) {
				return true;
			}
		}
	}
	return false;
}
function getSign(url, data) {
	var querystring = '';
	if (data && data.sign) {
		return false;
	} else {
		if (url) {
			querystring = getQuery(url);
		}
		if (data) {
			var theRequest = [];
			for (let param in data) {
				if (param && data[param] !== '') {
					theRequest = theRequest.concat({
						'name': param,
						'value': data[param]
					})
				}
			}
			querystring = querystring.concat(theRequest);
		}
		//排序
		querystring = _.sortBy(querystring, 'name');
		//去重
		querystring = _.uniq(querystring, true, 'name');
		var urlData = '';
		for (let i = 0; i < querystring.length; i++) {
			if (querystring[i] && querystring[i].name && querystring[i].value !== '') {
				urlData += querystring[i].name + '=' + querystring[i].value;
				if (i < (querystring.length - 1)) {
					urlData += '&';
				}
			}
		}
		// 注释掉签名生成
		// token = token ? token : 'jjfujj';
		// sign = md5(urlData + token);
		// console.log(urlData + ':' + sign)
		// return sign;
		return 'disabled_sign'; // 返回固定值表示签名已禁用
	}
}
/* ajax提交*/
function postAjax(url, vars, callback, dataType) {
	if (isObject(vars) || isArray(vars)) {
		vars.is_api = 1;
		vars.i = uniacid;
		vars.client_type = 'wap';
		
		var paramarr = vars;
		var sign = getSign(url, paramarr);
		if (sign) {
			vars.sign = sign;
		}
	} else if (isString(vars)) {
		vars += '&is_api=1';
		vars += '&i=' + uniacid;
		var paramarr = [];
		var strs = vars.split('&');
		for (var i = 0; i < strs.length; i++) {
			if(strs[i].indexOf('=') != -1){
				if (strs[i].split('=')[0] && unescape(strs[i].split('=')[1])) {
					paramarr[strs[i].split('=')[0]] = unescape(strs[i].split('=')[1]);
				}
			}
		}
		var sign = getSign(url, paramarr);
		if (sign) {
			vars += '&sign=' + sign;
		}
	}
	if (!dataType) dataType = 'json';
	if(!callback) callback = {};
	var storage = getStorage(sign);
	if (storage !== null) {
		callback(storage);
	} else {
		return $.ajax({
			type : 'POST',
			url : url,
			data : vars,
			dataType : dataType,
			success : callback,
			error: function () {
				//alert('超时,或服务错误'); 
			}
		});
	}
}
/* ajax提交*/
function getAjax(url, vars, callback, dataType) {
	if(isObject(vars) || isArray(vars)) {
		vars.is_api = 1;
		vars.i = uniacid;
		vars.client_type = 'wap';
		
		var paramarr = vars;
		var sign = getSign(url, paramarr);
		if (sign) {
			vars.sign = sign;
		}
	} else if(isString(vars)) {
		vars += '&is_api=1';
		vars += '&i=' + uniacid;
		vars += '&client_type=wap';
		
		var paramarr = [];
		var strs = vars.split('&');
		for (var i = 0; i < strs.length; i++) {
			if(strs[i].indexOf('=') != -1){
				if (strs[i].split('=')[0] && unescape(strs[i].split('=')[1])) {
					paramarr[strs[i].split('=')[0]] = unescape(strs[i].split('=')[1]);
				}
			}
		}
		var sign = getSign(url, paramarr);
		if (sign) {
			vars += '&sign=' + sign;
		}
	}
	if (!dataType) dataType = 'json';
	if(!callback) callback = {};
	var storage = getStorage(sign);
	if (storage !== null) {
		callback(storage);
	} else {
		return $.ajax({
			type : 'GET',
			url : url,
			data : vars,
			dataType : dataType,
			success : callback,
			error : function () {
				//alert('超时,或服务错误'); 
			}
		});
	}
}

function setStorage(key, value, ttl_ms) {
	ttl_ms = new Date().getTime() + ttl_ms * 1e3;
	var data = JSON.parse(localStorage.getItem(key));
	if (data !== null) {
		return true;
	}
	var data = {value: value, expirse: new Date(ttl_ms).getTime()};
	localStorage.setItem(key, JSON.stringify(data));
}

function getStorage(key) {
	var data = JSON.parse(localStorage.getItem(key));
	//console.log('getStorage', data);
	if (data !== null) {
		//debugger
		if (data.expirse != null && data.expirse < new Date().getTime()) {
			localStorage.removeItem(key);
		} else {
			try {
				var result = JSON.parse(data.value);
				return result;
			} catch(e) {
				return data.value;
			}
		}
	}
	return null;
}

function addCookie(e, t, a) {
	var n = e + '=' + escape(t) + '; path=/';
	if (a > 0) {
		var r = new Date;
		r.setTime(r.getTime() + a * 3600 * 1e3);
		n = n + ';expires=' + r.toGMTString();
	}
	document.cookie = n;
	setStorage(e, t, a);
}

function getCookie(e) {
	var t = document.cookie;
	var a = t.split('; ');
	var result = null;
	for (var n = 0; n < a.length; n++) {
		var r = a[n].split('=');
		if (r[0] == e) {
			result = unescape(r[1]);
		}
	}
	if (!result) {
		result = getStorage(e);
		console.log('result', result);
	}
	return result;
}

function delCookie(e) {
	var t = new Date;
	t.setTime(t.getTime() - 1);
	var a = getCookie(e);
	if (a != null) {
		document.cookie = e + '=' + a + '; path=/;expires=' + t.toGMTString();
		localStorage.removeItem(e);
	}
}
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
function ajax_form_post(formid, callback) {
	var url = $$(formid).getAttribute('action');
	var data = serialize(formid);
	postAjax(url, data, function(e) {
		if(!callback){
			if (e.state == 200) {
				if (e.data.msg) {
					showSucc(e.data.msg, function(){
						if(typeof(e.data.url) != 'undefined'){
							redirectTo(e.data.url);
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
function showSucc(msg, callback, time) {
	if (!time) time = 2;
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	$('#alertDialog').css({'display': 'block'}).find('.weui-dialog__bd').html(msg);
	var showDialogST = setInterval(function(){
		--time;
		if (time <= 0) {
			if (typeof callback == 'function') callback();
			clearInterval(showDialogST);
		}
	}, 1000);
}
function showError(msg, callback, time) {
	if (!time) time = 3;
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	$('#alertDialog').css({'display': 'block'}).find('.weui-dialog__bd').html(msg);
	var showDialogST = setInterval(function(){
		--time;
		if (time <= 0) {
			if (typeof callback == 'function') callback();
			clearInterval(showDialogST);
		}
	}, 1000);
}
function showConfirm(msg, callback, cancelcallback) {
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	msg = msg.replace(p, '');
	$('#confirmDialog').css({'display': 'block'}).find('.weui-dialog__bd').html(msg);
	$('#confirmDialog .weui-dialog__btn_primary').click(function(){
		if (typeof callback == 'function') callback();
	})
	$('#confirmDialog .weui-dialog__btn_default').click(function(){
		if (typeof cancelcallback == 'function') cancelcallback();
	})
}
function showBottomBox(body_html, callback) {
	$('#bottomBox').css({'display': 'block'});
	$('#boxBody').html(body_html);
	if (typeof callback == 'function') callback();
}
function loadSeccode(myhash) {
	$('#codekey').val('');
	getAjax(url('shop/seccode/makecodekey'), {myhash: myhash}, function(e){
		$('#codekey').val(e.data.seccode_key);
		$('#codeimage').attr('src', url('shop/seccode/makecode', {i: uniacid, seccode: e.data.seccode, t: Math.random()}));
	});
}
function toast(msg, time, type, fontsize){
	if (!msg) msg = '已完成';
	if (!time) time = 3000;
	if (!type) type = 'success';
	if (!fontsize) fontsize = '14';
	if(type == 'success'){
		$('#toast .weui-icon_toast').removeClass('weui-icon-error-no-circle').addClass('weui-icon-success-no-circle');
	}else{
		$('#toast .weui-icon_toast').removeClass('weui-icon-success-no-circle').addClass('weui-icon-error-no-circle');
	}
	$('#toast').show().css({'opacity': 1}).find('.weui-toast__content').html('<front style=\'font-size: ' + fontsize + 'px;\'>' + msg + '</front>');
	var interval = setInterval(function(){
		$('#toast').hide().css({'opacity': 0}).find('.weui-toast__content').html('');
		clearInterval(interval);
	}, time);
}
function showLoading(msg, fontsize) {
	$('#uploadLoading').show().css({'opacity': 1}).find('.weui-toast__content').html('<front style=\'font-size: ' + fontsize + 'px;\'>' + msg + '</front>');
}
function hideLoading() {
	$('#uploadLoading').hide().css({'opacity': 0}).find('.weui-toast__content').html('');
}
function checkLogin() {
	var key = getCookie('key');
	if(!key){
		redirectTo('shop/publics/login');
	}
} 
$(function() {
	$('.weui-dialog__btn,.weui-dialog_close').click(function(){
		$(this).parents('.js_dialog').css({'display': 'none'});
	});
	$('#iosMask').click(function(){
		$(this).parents('#bottomBox').css({'display': 'none'});
	});
	$('#bottomBox').on('click', '.upload_canal', function(){
		$(this).parents('#bottomBox').css({'display': 'none'});
	});
	$('body').on('click', '.input-del', function(){
		$(this).parent().removeClass('write').find('input').val('');
		btnCheck($(this).parents('form'))
	});
	$('#header').on('click', '.back2', function(e) {
		history.go(-1);
	});
});
function url(action, querystring) {
	var start = action.indexOf('api/');
	if (start == 0) {
		var url = site_url + '/api.php?i=' + uniacid + '&oid=' + oid + '&';
	} else {
		var url = site_url + '/front.php?i=' + uniacid + '&oid=' + oid + '&';
	}
	if (action) {
		action = action.split('/');
		if (typeof (action[0]) != 'undefined') {
			url += 'g=' + action[0] + '&';
		}
		if (typeof (action[1]) != 'undefined') {
			url += 'c=' + action[1] + '&';
		}
		if (typeof (action[2]) != 'undefined') {
			url += 'a=' + action[2] + '&';
		}
		url += 'client_type=wap&';
	}
	if (querystring && typeof querystring === 'object') {
		for (let param in querystring) {
			if (param && querystring.hasOwnProperty(param) && querystring[param] !== '') {
				url += param + '=' + querystring[param] + '&';
			}
		}
	}
	return url;
}
function redirectTo(action, querystring) {
	location.href = url(action, querystring);
}
// 上传图片
function uploadImg(ele, options) {
	//console.log(ele.files);
	var percent = 0;
	var xhr = new XMLHttpRequest();
	var formData = new FormData();

	for(var i = 0, f; f = ele.files[i]; i++){
		formData.append('files', f);
	}

	//console.log('1',ele.files);
	//console.log('2',formData);
	
	// XHR2.0新增 上传进度监控
	xhr.upload.onprogress = function (event) {
		$('.upload_canal').click();
		showLoading('文件上传中，请勿离开', 12);
		//  console.log(event);
		percent = event.loaded / event.total * 100;
		//console.log(percent);
		if(percent == 100){
			hideLoading();
		}
	}
	//注册回调函数
	xhr.onreadystatechange = function (e) {
		if(xhr.readyState == 4){
			if(xhr.status == 200){
				eval('result = ' + xhr.responseText);
				options.onSuccess(result);
			}else {
				eval('result = ' + xhr.responseText);
				options.onFailure(result);
			}
		}
	}
	//注册回调函数
    //xhr.onload = function () {
		//console.log(xhr.responseText);
	//}
	xhr.open('POST', options.path, true);
	xhr.send(formData);	
}