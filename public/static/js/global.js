/*
 * 为低版本IE添加placeholder效果
 *
 * 使用范例：
 * [html]
 * <input id="captcha" name="captcha" type="text" placeholder="验证码" value="" >
 * [javascrpt]
 * $("#captcha").fxy_placeholder();
 *
 * 生效后提交表单时，placeholder的内容会被提交到服务器，提交前需要把值清空
 * 范例：
 * $('[data-placeholder="placeholder"]').val("");
 * $("#form").submit();
 *
 */
(function($) {
    $.fn.fxy_placeholder = function() {
        var isPlaceholder = 'placeholder' in document.createElement('input');
        return this.each(function() {
            if(!isPlaceholder) {
                $el = $(this);
                $el.focus(function() {
                    if($el.attr('placeholder') === $el.val()) {
                        $el.val('');
                        $el.attr('data-placeholder', '');
                    }
                }).blur(function() {
                    if($el.val() === '') {
                        $el.val($el.attr('placeholder'));
                        $el.attr('data-placeholder', 'placeholder');
                    }
                }).blur();
            }
        });
    };
})(jQuery);

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
/* ajax提交*/
function postAjax(url, vars, callback, dataType) {
	if(isObject(vars) || isArray(vars)){
		vars.is_api = 1;
	}
	if(isString(vars)){
		vars += '&is_api=1';
	}
	if (!dataType) dataType = 'json';
	if(!callback) callback = {};
	return $.ajax({
		type : 'POST',
		url : url,
		data : vars,
		dataType : dataType,
		success : callback,
		error : function (XMLHttpRequest, textStatus, errorThrown) {
            showError(XMLHttpRequest.readyState + ':' + XMLHttpRequest.status + ':' + XMLHttpRequest.responseText);
        }
	});
}
/* ajax提交*/
function getAjax(url, vars, callback, dataType) {
	if(isObject(vars) || isArray(vars)){
		vars.is_api = 1;
	}
	if(isString(vars)){
		if(vars){
			vars += '&is_api=1';
		}else{
			vars += 'is_api=1';
		}
	}
	if (!dataType) dataType = 'json';
	if(!callback) callback = {};
	return $.ajax({
		type : 'GET',
		url : url,
		data : vars,
		dataType : dataType,
		success : callback,
		error : function (XMLHttpRequest, textStatus, errorThrown) {
            showError(XMLHttpRequest.readyState + ':' + XMLHttpRequest.status + ':' + XMLHttpRequest.responseText);
        }
	});
}
function addCookie(e, t, a) {
	var n = e + '=' + escape(t) + '; path=/';
	if (a > 0) {
		var r = new Date;
		r.setTime(r.getTime() + a * 3600 * 1e3);
		n = n + ';expires=' + r.toGMTString()
	}
	document.cookie = n
}

function getCookie(e) {
	var t = document.cookie;
	var a = t.split('; ');
	for (var n = 0; n < a.length; n++) {
		var r = a[n].split('=');
		if (r[0] == e) return unescape(r[1])
	}
	return null
}

function delCookie(e) {
	var t = new Date;
	t.setTime(t.getTime() - 1);
	var a = getCookie(e);
	if (a != null) document.cookie = e + '=' + a + '; path=/;expires=' + t.toGMTString()
}