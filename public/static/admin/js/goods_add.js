$(function(){
    // 取消回车提交表单 
    $('input').keypress(function(e){
        var key = window.event ? e.keyCode : e.which;
        if (key.toString() == '13') {
			return false;
        }
    });
    
    // 计算折扣
    //$('input[name=g_price],input[name=g_marketprice]').change(function(){
     //   discountCalculator();
    //});
});
// 计算商品库存
function computeStock(){
    // 库存
    var _stock = 0;
    $('input[data_type=stock]').each(function(){
        if($(this).val() != ''){
            _stock += parseInt($(this).val());
        }
    });
    $('input[name=g_storage]').val(_stock);
}

// 计算商品销量
function computeSalenum(){
    // 库存
    var _salenum = 0;
    $('input[data_type=salenum]').each(function(){
        if($(this).val() != ''){
            _salenum += parseInt($(this).val());
        }
    });
    $('input[name=g_salenum]').val(_salenum);
}
// 计算成本价
function computeCostPrice(){
    // 计算最低价格
    var _price = 0;
	var _price_sign = false;
    $('input[data_type=costprice]').each(function(){
        if($(this).val() != '' && $(this)){
            if(!_price_sign){
                _price = parseFloat($(this).val());
                _price_sign = true;
            }else{
                _price = (parseFloat($(this).val())  > _price) ? _price : parseFloat($(this).val());
            }
        }
    });
    $('input[name=g_costprice]').val(number_format(_price, 2));
}
// 计算价格
function computePrice(_type){
    // 计算最低价格
    var _price = 0;var _price_sign = false;
    $('input[data_id=' + _type + ']').each(function(){
        if($(this).val() != '' && $(this)){
            if(!_price_sign){
                _price = parseFloat($(this).val());
                _price_sign = true;
            }else{
                _price = (parseFloat($(this).val())  > _price) ? _price : parseFloat($(this).val());
            }
        }
    });
    $('#g_' + _type).val(number_format(_price, 2));

    //discountCalculator();       // 计算折扣
}

// 计算折扣
function discountCalculator() {
	/*
    var _price = parseFloat($('input[name=g_price]').val());
    var _marketprice = parseFloat($('input[name=g_marketprice]').val());
    if((!isNaN(_price) && _price != 0) && (!isNaN(_marketprice) && _marketprice != 0)){
        var _discount = parseInt(_price/_marketprice*100);
        $('input[name=g_discount]').val(_discount);
    }
	*/
}