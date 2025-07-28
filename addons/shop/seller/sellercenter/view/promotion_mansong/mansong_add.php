<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='alert alert-block'>
		<h4>说明：</h4>
		<ul>
			<li>满即送活动包括店铺所有商品，活动时间不能和已有活动重叠</li>
			<li>每个满即送活动最多可以设置3个价格级别，点击新增级别按钮可以增加新的级别，价格级别应该由低到高</li>
			<li>每个级别可以有减现金、送礼品2种促销方式，至少需要选择一种</li>
		</ul>
</div>
<div class='item-publish'>
	<form id='form' method='post' target='_parent' action='<?=_url('promotion_mansong/mansong_add')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<div class='css-form-goods'>
			<dl>
				<dt><i class='required'>*</i>活动名称：</dt>
				<dd>
					<input name='mansong_name' value='' class='text w200' type='text' />
					<p class='hint'>活动名称最多为25个字符</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>开始时间：</dt>
				<dd>
					<input class='text w100' name='start_time' id='start_time' value='' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					<p class='hint'>开始时间不能为空且不能早于<?=date('Y-m-d H:i', $output['start_time'])?></p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>结束时间：</dt>
				<dd>
					<input class='text w100' name='end_time' id='end_time' value='' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>满即送规则：</dt>
				<dd>
					<input type='hidden' id='mansong_rule_count' name='rule_count' />
					<ul id='mansong_rule_list' class='css-mansong-rule-list'></ul>
					<a href='javascript:void(0);' id='btn_add_rule' class='css-btn css-btn-acidblue'><i class='icon-plus-sign'></i>添加规则</a>
					<div id='div_add_rule' style='display:none;'>
						<div class='css-mansong-error'>
							<span id='mansong_price_error' style='display:none;'><i class='icon-exclamation-sign'></i>规则金额不能为空且必须为数字</span>
							<span id='mansong_discount_error' style='display:none;'><i class='icon-exclamation-sign'></i>满减金额必须小于规则金额</span>
						</div>
						<div class='css-mansong-rule'> <span>单笔订单满&nbsp;
							<input id='mansong_price' type='text' class='text w50' />
							<em class='add-on'><i class='icon-renminbi'></i></em>，</span> 
							<span>立减现金&nbsp;<input id='mansong_discount' type='text' class='text w50' />
							<em class='add-on'><i class='icon-renminbi'></i></em></span> 
							<span>，送礼品&nbsp;<a href='javascript:void(0);' id='btn_show_search_goods' class='css-btn'><i class='icon-gift'></i>选择礼品</a></span>
							<div id='mansong_goods_item' class='gift'></div>
							<div id='div_search_goods' class='div-goods-select mt10' style='display: none;'>
								<table class='search-form'>
									<tr>
										<th class='w150'> <strong>第一步：搜索店内商品</strong> </th>
										<td class='w160'><input id='search_goods_name' type='text w150' class='text' name='goods_name' value=''/></td>
										<td class='w70 tc'><a href='javascript:void(0);' id='btn_search_goods' class='css-btn'/><i class='icon-search'></i>搜索</a></td>
										<td class='w10'></td>
										<td><p class='hint'>不输入名称直接搜索将显示店内所有普通商品，特殊商品不能参加。</p></td>
									</tr>
								</table>
								<a id='btn_hide_search_goods' class='close' href='javascript:void(0);' style='color: #0579C6; right: 0;'>X</a>
								<div id='div_goods_search_result' class='search-result'></div>
							</div>
						</div>
						<div id='mansong_rule_error' style='display:none;'>请至少选择一种促销方式</div>
						<div class='mt10'> <a href='javascript:void(0);' id='btn_save_rule' class='css-btn css-btn-acidblue'><i class='icon-ok-circle'></i>确定规则设置</a> <a href='javascript:void(0);' id='btn_cancel_add_rule' class='css-btn css-btn-orange'><i class='icon-ban-circle'></i>取消</a></div>
					</div>
					<span class='error-message'></span>
					<p class='hint'>设置当单笔订单满足金额时（必填选项），减免金额（选填）或赠送的礼品（选填）；留空为不做减免金额处理。<br/>系统最多支持设置三组等级规则。</p>
				</dd>
			</dl>
			<dl>
				<dt>活动描述：</dt>
				<dd>
					<input name='remark' value='' class='text w200' type='text'>
					<p class='hint'>活动备注最多为100个字符</p>
				</dd>
			</dl>
		</div>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script id='mansong_rule_template' type='text/html'>
	<li nctype='mansong_rule_item'>
		<span>单笔订单满<strong><%= price %></strong>元， </span>
		<span>立减现金<strong><%= discount %></strong>元， </span>
		<%if (goods_id > 0) { %>
			<span>送礼品 <%== goods %></span>
		<% } %>
		<input type='hidden' name='mansong_rule[<%= key %>]' value='<%= price %>,<%= discount %>,<%= goods_id %>' />
		<a nctype='btn_del_mansong_rule' href='javascript:void(0);' class='css-btn-mini css-btn-red'><i class='icon-trash'></i>删除</a>
	</li>
</script> 
<script id='mansong_goods_template' type='text/html'>
    <div nctype='mansong_goods' class='selected-mansong-goods'>
		<a href='<%=goods_url%>' title='<%=goods_name%>' class='goods-thumb' target='_blank'>
			<img src='<%=goods_image_url%>'/>
		</a>
		<input nctype='mansong_goods_id' type='hidden' value='<%=goods_id%>' />
    </div>
	<a nctype='btn_del_mansong_goods' href='javascript:void(0);' class='css-btn-mini css-btn-red'><i class='icon-trash'></i>删除已选择的礼品</a>
</script> 
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#start_time').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d H:i',
	formatDate: 'Y-m-d H:i',
	step: 5
});
$('#end_time').datetimepicker({
	lang: 'ch',
	datepicker: true,
	format: 'Y-m-d H:i',
	formatDate: 'Y-m-d H:i',
	step: 5
});
</script>
<script type='text/javascript'>
$(document).ready(function() {
    // 限时添加规则窗口
    $('#btn_add_rule').on('click', function() {
        $('#mansong_price').val('');
        $('#mansong_discount').val('');
        $('#mansong_goods_item').html('');
        $('#mansong_price_error').hide();
        $('#mansong_rule_error').hide();
        $('#div_add_rule').show();
        $('#btn_add_rule').hide();
    });

    // 规则保存
    $('#btn_save_rule').on('click', function() {
        var mansong = {};
		mansong.key = $('#mansong_rule_list').find('[nctype=mansong_rule_item]').length;
        mansong.price = Number($('#mansong_price').val());
        if(isNaN(mansong.price) || mansong.price <= 0) {
            $('#mansong_price_error').show();
            return false;
        } else {
            $('#mansong_price_error').hide();
        }
        mansong.discount = Number($('#mansong_discount').val());
        if(isNaN(mansong.discount) || mansong.discount < 0 || mansong.discount > mansong.price) {
            $('#mansong_discount_error').show();
            return false;
        } else {
            $('#mansong_discount_error').hide();
        }
        mansong.goods = $('#mansong_goods_item').find('[nctype=mansong_goods]').html();
        mansong.goods_id = Number($('#mansong_goods_item').find('[nctype=mansong_goods_id]').val());
        if(isNaN(mansong.goods_id)) {
            mansong.goods_id = 0;
        }
        if(mansong.discount == 0 && mansong.goods_id == 0) {
            $('#mansong_rule_error').show();
            return false;
        } else {
            $('#mansong_rule_error').hide();
        }
        var mansong_rule_item = template.render('mansong_rule_template', mansong);
        $('#mansong_rule_list').append(mansong_rule_item);
        close_div_add_rule();
    });

    // 删除已添加的规则
    $('#mansong_rule_list').on('click', '[nctype=btn_del_mansong_rule]', function() {
        $(this).parents('[nctype=mansong_rule_item]').remove();
        close_div_add_rule();
    });

    // 取消添加规则
    $('#btn_cancel_add_rule').on('click', function() {
        close_div_add_rule();
    });

    // 关闭规则添加窗口
    function close_div_add_rule() {
        var rule_count = $('#mansong_rule_list').find('[nctype=mansong_rule_item]').length;
        if( rule_count >= 3) {
            $('#btn_add_rule').hide();
        } else {
            $('#btn_add_rule').show();
        }
        $('#div_add_rule').hide();
        $('#mansong_rule_count').val(rule_count);
    }

    // 限时商品选择窗口
    $('#btn_show_search_goods').on('click', function() {
        $('#div_search_goods').show();
    });

    // 搜索商品
    $('#btn_search_goods').on('click', function() {
		var url = '<?php echo _url('shop_goods/select_goods_view');?>';
		url += '?' + $.param({goods_name: $('#search_goods_name').val(), 'has_special_goods': 0, input_name: 'btn_add_mansong_goods'});
        $('#div_goods_search_result').load(url);
    });

    // 关闭商品选择窗口
    $('#btn_hide_search_goods').on('click', function() {
        $('#div_search_goods').hide();
    });

    // 选择商品
    $('#div_goods_search_result').on('click', '[nctype=btn_add_mansong_goods]', function() {
        var goods = {};
        goods.goods_id = $(this).attr('goods_commonid');
        goods.goods_name = $(this).attr('goods_name');
        goods.goods_image_url = $(this).attr('goods_image');
        goods.goods_url = 'javascript:;';
        var mansong_goods_item = template.render('mansong_goods_template', goods);
        $('#mansong_goods_item').html(mansong_goods_item);
        $('#div_search_goods').hide();
    });

    // 删除以选的商品
    $('#mansong_goods_item').on('click', '[nctype=btn_del_mansong_goods]', function() {
        $('#mansong_goods_item').html('');
    });

});
</script>
<script type='text/javascript'>
$(function(){
    $('.submit').click(function(e){
		ajax_form_post('form');
	});
});
</script>