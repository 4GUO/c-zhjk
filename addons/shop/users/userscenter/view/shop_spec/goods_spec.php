<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
	
	<?php if(!empty($output['spec_list']) && is_array($output['spec_list'])){?>
			<?php $i = '0';?>
			<?php foreach ($output['spec_list'] as $k => $val){?>
			<dl nc_type='spec_group_dl_<?php echo $i;?>' nctype='spec_group_dl' class='spec-bg' <?php if($k == '1'){?>spec_img='t'<?php }?>>
				<dt>
					<input name='sp_name[<?php echo $k;?>]' type='text' class='text w60 tip2 tr' readonly='readonly' style='border:none; backgound:none' title='' value='<?php if (isset($output['goods']['spec_name'][$k])) { echo $output['goods']['spec_name'][$k];} else {echo $val['sp_name'];}?>' maxlength='4' nctype='spec_name' id='<?php echo $k;?>' name='<?php echo $val['sp_name'];?>'/>
					
				</dt>
				<dd <?php if($k == '1'){?>nctype='sp_group_val'<?php }?>>
					<ul class='spec'>
						<?php if(is_array($val['value'])){?>
						<?php foreach ($val['value'] as $v) {?>
						<li><span nctype='input_checkbox'>
							<input type='checkbox' value='<?php echo $v['sp_value_name'];?>' nc_type='<?php echo $v['sp_value_id'];?>' <?php if($k == '1'){?>class='sp_val'<?php }?> name='sp_val[<?php echo $k;?>][<?php echo $v['sp_value_id']?>]'>
							</span><span nctype='pv_name'><?php echo $v['sp_value_name'];?></span></li>
						<?php }?>
						<?php }?>
						
					</ul>
					<?php if(!empty($output['edit_goods_sign']) && $k == '1'){?>
					<p class='hint'>添加或取消该规格时，提交后请编辑图片以确保商品图片能够准确显示。</p>
					<?php }?>
				</dd>
			</dl>
			<?php $i++;?>
			<?php }?>
			<?php }?>
			<dl nc_type='spec_dl' class='spec-bg' style='display:none; overflow: visible;'>
				<dt>属性设置</dt>
				<dd class='spec-dd'>
					<table border='0' cellpadding='0' cellspacing='0' class='spec_table'>
						<thead>
							<?php if(!empty($output['spec_list']) && is_array($output['spec_list'])){?>
							<?php foreach ($output['spec_list'] as $k => $val){?>
						    <th nctype='spec_name_<?php echo $k;?>'><?php if (isset($output['goods']['spec_name'][$k])) { echo $output['goods']['spec_name'][$k];} else {echo $val['sp_name'];}?></th>
							<?php }?>
							<?php }?>
							<th class='w90'><span class='red'>*</span>市场价
								<div class='batch'><i class='icon-edit' title='批量操作'></i>
									<div class='batch-input' style='display:none;'>
										<h6>批量设置价格：</h6>
										<a href='javascript:void(0)' class='close'>X</a>
										<input name='' type='text' class='text price' />
										<a href='javascript:void(0)' class='css-btn-mini' data-type='marketprice'>设置</a><span class='arrow'></span>
									</div>
								</div>
							</th>
							<th class='w90'><span class='red'>*</span>成本价
								<div class='batch'><i class='icon-edit' title='批量操作'></i>
									<div class='batch-input' style='display:none;'>
										<h6>批量设置价格：</h6>
										<a href='javascript:void(0)' class='close'>X</a>
										<input name='' type='text' class='text price' />
										<a href='javascript:void(0)' class='css-btn-mini' data-type='costprice'>设置</a><span class='arrow'></span>
									</div>
								</div>
							</th>
							<th class='w180'>
							    <span class='red'>*</span>商品价格
								<div class='batch'><i class='icon-edit' title='批量操作'></i>
									<div class='batch-input' style='display:none;'>
										<h6 style='text-align: center'>批量设置价格：</h6>
										<a href='javascript:void(0)' class='close'>X</a>
										<?php foreach($output['member_levels'] as $key => $level_info) {?>
										<div style='width:200px; margin-top:5px; color: #555; font-weight: normal; font-size: 12px;'><label style='display:block; float:left; width:100px; text-align:center;'><?php echo $level_info['level_name']?></label><input name='' data-id='price_<?php echo $level_info['id'];?>' class='text price' style='display:block; float:left; width: 60px; text-align: center; border-radius: 5px; margin:0px 0px 0px 5px; clear: none' type='text'><div class='clearfix'></div></div>
										<?php }?>
										<a href='javascript:void(0)' class='css-btn-mini' style='display: block; margin: 2px auto; width: 30px;' data-type='price'>设置</a><span class='arrow'></span>
									</div>
								</div>
							</th>
							<th class='w90'><span class='red'>*</span>库存
								<div class='batch'><i class='icon-edit' title='批量操作'></i>
									<div class='batch-input' style='display:none;'>
										<h6>批量设置库存：</h6>
										<a href='javascript:void(0)' class='close'>X</a>
										<input name='' type='text' class='text stock' />
										<a href='javascript:void(0)' class='css-btn-mini' data-type='stock'>设置</a><span class='arrow'></span></div>
								</div>
							</th>
							<th class='w90' style='display: none;'>销量
								<div class='batch'><i class='icon-edit' title='批量操作'></i>
									<div class='batch-input' style='display:none;'>
										<h6>批量设置销量：</h6>
										<a href='javascript:void(0)' class='close'>X</a>
										<input name='' type='text' class='text stock' />
										<a href='javascript:void(0)' class='css-btn-mini' data-type='salenum'>设置</a><span class='arrow'></span></div>
								</div>
							</th>
							<th class='w100'>商家货号</th>
						</thead>
						<tbody nc_type='spec_table'></tbody>
					</table>
					<p class='hint'>点击<i class='icon-edit'></i>可批量修改所在列的值。</p>
				</dd>
			</dl>
<script type='text/javascript'>
// 按规格存储规格值数据
var spec_group_checked = [<?php for ($i=0; $i<$output['sign_i']; $i++){if($i+1 == $output['sign_i']){echo '\'\'';}else{echo '\'\',';}}?>];
var str = '';
var V = new Array();

<?php for ($i=0; $i<$output['sign_i']; $i++){?>
var spec_group_checked_<?php echo $i;?> = new Array();
<?php }?>

$(function(){
	// 修改规格名称
    $('dl[nctype=spec_group_dl]').on('click', 'input[type=checkbox]', function(){
        pv = $(this).parents('li').find('span[nctype=pv_name]');
        if(typeof(pv.find('input').val()) == 'undefined'){
            pv.html('<input type=\'text\' maxlength=\'20\' readonly=\'readonly\' class=\'text\' style=\'border:none; backgound:none\' value=\'' + pv.html() + '\' />');
        }else{
            pv.html(pv.find('input').val());
        }
    });
    
    $('span[nctype=pv_name] > input').live('change',function(){
        change_img_name($(this));       // 修改相关的颜色名称
        into_array();           // 将选中的规格放入数组
        goods_stock_set();      // 生成库存配置
    });
    
    // 修改规格名称
    $('input[nctype=spec_name]').change(function(){
		var data_str = {};
		data_str.id = $(this).attr('id');
		data_str.name = $(this).attr('name');
        if ($(this).val() == '') {
            $(this).val(data_str.name);
        }
        $('th[nctype=spec_name_' + data_str.id + ']').html($(this).val());
    });
    // 批量设置价格、库存、预警值
    $('.batch > .icon-edit').click(function(){
        $('.batch > .batch-input').hide();
        $(this).next().show();
    });
    $('.batch-input > .close').click(function(){
        $(this).parent().hide();
    });
    $('.batch-input > .css-btn-mini').click(function(){
        var _value = $(this).prev().val();
        var _type = $(this).attr('data-type');
        if (_type == 'price') {
			$($(this).parent().find('input')).each(function(){
				var _value = $(this).val();
				var _type = $(this).attr('data-id');
				_value = number_format(_value, 2);
				if (isNaN(_value)) {
					_value = 0;
				}
				$('input[data_id=' + _type + ']').val(_value);			
				$(this).val('');
				computePrice(_type);
			});
			$(this).parent().hide();
        } else {
            _value = parseInt(_value);
			if (isNaN(_value)) {
				_value = 0;
			}
			$('input[data_type=' + _type + ']').val(_value);
			$(this).prev().val('');			
			if (_type == 'stock') {
				computeStock();
			} else if (_type == 'salenum') {
				computeSalenum();
			} else if (_type == 'costprice') {
				computeCostPrice();
			}
			$(this).parent().hide();
        }
    });
	
	$('dl[nctype=spec_group_dl]').on('click', 'span[nctype=input_checkbox] > input[type=checkbox]',function(){
		into_array();
		goods_stock_set();
	});

	// 提交后不没有填写的价格或库存的库存配置设为默认价格和0
	// 库存配置隐藏式 里面的input加上disable属性
	$('input[type=submit]').click(function(){
		if($('dl[nc_type=spec_dl]').css('display') == 'none'){
			$('dl[nc_type=spec_dl]').find('input').attr('disabled','disabled');
		}
	});
	
});

// 将选中的规格放入数组
function into_array() {
	<?php for ($i = 0; $i < $output['sign_i']; $i++) { ?>
		spec_group_checked_<?php echo $i;?> = new Array();
		$('dl[nc_type=spec_group_dl_<?php echo $i;?>]').find('input[type=checkbox]:checked').each(function(){
			i = $(this).attr('nc_type');
			v = $(this).val();
			c = null;
			if ($(this).parents('dl:first').attr('spec_img') == 't') {
				c = 1;
			}
			spec_group_checked_<?php echo $i;?>[spec_group_checked_<?php echo $i;?>.length] = [v,i,c];
		});

		spec_group_checked[<?php echo $i;?>] = spec_group_checked_<?php echo $i;?>;

	<?php }?>
}

// 生成库存配置
function goods_stock_set() {
    //店铺价格 商品库存改为只读
    $('.g_price').attr('readonly','readonly').css('background','#E7E7E7 none');
	$('input[name=g_costprice]').attr('readonly','readonly').css('background','#E7E7E7 none');
    $('input[name=g_storage]').attr('readonly','readonly').css('background','#E7E7E7 none');
	$('input[name=g_salenum]').attr('readonly','readonly').css('background','#E7E7E7 none');
    $('dl[nc_type=spec_dl]').show();
    str = '<tr>';
    <?php recursionSpec(0, $output['sign_i'], $output['member_levels'], $output['member_levels_num']);?>
    if (str == '<tr>') {
        //  店铺价格 商品库存取消只读
        $('.g_price').removeAttr('readonly').css('background','');
		$('input[name=g_costprice]').removeAttr('readonly').css('background','');
        $('input[name=g_storage]').removeAttr('readonly').css('background','');
		$('input[name=g_salenum]').removeAttr('readonly').css('background','');
        $('dl[nc_type=spec_dl]').hide();
		$('tbody[nc_type=spec_table]').html('')
    } else {
        $('tbody[nc_type=spec_table]').empty().html(str)
            .find('input[nc_type]').each(function() {
                s = $(this).attr('nc_type');
                try{$(this).val(V[s]);}catch(ex){$(this).val('');};
                if ($(this).attr('data_type') == 'marketprice' && $(this).val() == '') {
                    $(this).val($('input[name=g_marketprice]').val());
                }
				if ($(this).attr('data_type') == 'costprice' && $(this).val() == '') {
                    $(this).val($('input[name=g_costprice]').val());
                }
                if ($(this).attr('data_type') == 'price' && $(this).val() == '') {
                    $(this).val($('.g_price').val());
                }
                if ($(this).attr('data_type') == 'stock' && $(this).val() == '') {
                    $(this).val('0');
                }
				if ($(this).attr('data_type') == 'salenum' && $(this).val() == '') {
                    $(this).val('0');
                }
            }).end()
			.find('input[data_type=costprice]').change(function() {
                computeCostPrice();    // 成本计算
            }).end()
            .find('input[data_type=stock]').change(function() {
                computeStock();    // 库存计算
            }).end()
			.find('input[data_type=salenum]').change(function() {
                computeSalenum();    // 销量计算
            }).end()
            .find('input[data_type=price]').change(function() {
				var _value = $(this).val();
				var _type = $(this).attr('data_id');				
				_value = number_format(_value, 2);
				if (isNaN(_value)) {
					_value = 0;
				}				
				computePrice(_type);   // 价格计算
            }).end()
            .find('input[nc_type]').change(function() {
                s = $(this).attr('nc_type');
                V[s] = $(this).val();
            });
    }
}

//修改相关的颜色名称
function change_img_name(Obj) {
	var S = Obj.parents('li').find('input[type=checkbox]');
	S.val(Obj.val());
	var V = $('tr[nctype=file_tr_' + S.attr('nc_type') + ']');
	V.find('span[nctype=pv_name]').html(Obj.val());
	V.find('input[type=file]').attr('name', Obj.val());
}

<?php 
/**
 * 
 * 
 *  生成需要的js循环。递归调用	PHP
 * 
 *  形式参考 （ 2个规格）
 *  $('input[type=checkbox]').click(function(){
 *      str = '';
 *      for (var i=0; i<spec_group_checked[0].length; i++ ){
 *      td_1 = spec_group_checked[0][i];
 *          for (var j=0; j<spec_group_checked[1].length; j++){
 *              td_2 = spec_group_checked[1][j];
 *              str += '<tr><td>'+td_1[0]+'</td><td>'+td_2[0]+'</td><td><input type=\'text\' /></td><td><input type=\'text\' /></td><td><input type=\'text\' /></td>';
 *          }
 *      }
 *      $('table[class=spec_table] > tbody').empty().html(str);
 *  });
 */
function recursionSpec($len, $sign, $member_levels, $member_levels_num) {
    if ($len < $sign) {
        echo 'for (var i_' . $len . '=0; i_' . $len . '<spec_group_checked[' . $len . '].length; i_' . $len . '++){td_' . (intval($len) + 1) . ' = spec_group_checked[' . $len . '][i_' . $len . '];' . PHP_EOL;
        $len++;
        recursionSpec($len, $sign, $member_levels, $member_levels_num);
    } else {
        echo 'var tmp_spec_td = new Array();' . PHP_EOL;
        for ($i=0; $i < $len; $i++) {
            echo 'tmp_spec_td[' . ($i) . '] = td_' . ($i+1) . '[1];' . PHP_EOL;
        }
        echo 'tmp_spec_td.sort(function(a,b){return a-b});' . PHP_EOL;
        echo 'var spec_bunch = \'i_\';' . PHP_EOL;
        for ($i = 0; $i < $len; $i++) {
            echo 'spec_bunch += tmp_spec_td[' . ($i) . '];' . PHP_EOL;
        }
        echo <<<EOF
			str += '<input type=\'hidden\' name=\'spec[' + spec_bunch + '][goods_id]\' nc_type=\'' + spec_bunch + '|id\' value=\'\' />';
EOF;
        for ($i = 0; $i < $len; $i++) {
            echo 'if (td_' . ($i+1) . <<<EOF
			[2] != null) { str += '<input type=\'hidden\' name=\'spec['+spec_bunch+'][color]\' value=\''+td_
EOF;
			echo ($i+1); 

			echo <<<EOF
			[1]+'\' />';};
EOF;
			echo <<<EOF
            str +='<td style=\'height:
EOF;
			echo (36 * $member_levels_num);
			
			echo <<<EOF
			px;\'><input type=\'hidden\' name=\'spec['+spec_bunch+'][sp_value]['+td_
EOF;
			echo ($i+1);
			
			echo <<<EOF
			[1]+']\' value=\''+td_
EOF;
			echo ($i+1);
			
			echo <<<EOF
			[0]+'\' />'+td_
EOF;
			echo ($i+1);
			
			echo <<<EOF
			[0]+'</td>';
EOF;
		}
		echo <<<EOF
		str +='<td><input class=\'text price\' type=\'text\' name=\'spec['+spec_bunch+'][marketprice]\' data_type=\'marketprice\' nc_type=\''+spec_bunch+'|marketprice\' value=\'\' /><em class=\'add-on\'><i class=\'icon-renminbi\'></i></em></td>';
EOF;
		echo <<<EOF
		str +='<td><input class=\'text price\' type=\'text\' name=\'spec['+spec_bunch+'][costprice]\' data_type=\'costprice\' nc_type=\''+spec_bunch+'|costprice\' value=\'\' /><em class=\'add-on\'><i class=\'icon-renminbi\'></i></em></td>';
EOF;
			echo <<<EOF
			str +='<td>';
EOF;
			foreach ($member_levels as $level_info) {
				echo <<<EOF
				str += '<div><label style=\'display: block; width: 80px; height: 36px; line-height: 36px; text-align: center; float: left\'>'+
EOF;
				echo '\'' . $level_info['level_name'] . '\'+';
				echo <<<EOF
				'</label><input class=\'text price\' type=\'text\' name=\'spec['+spec_bunch+'][price]['+
EOF;
				echo '\'' . $level_info['id'] . '\'+';
				echo <<<EOF
				']\' data_type=\'price\' data_id=\'price_'+
EOF;
				echo '\'' . $level_info['id'] . '\'+';
				echo <<<EOF
				'\' nc_type=\''+spec_bunch+'|price_vip_'+
EOF;
				echo '\'' . $level_info['id'] . '\'+';
				echo <<<EOF
				'\' value=\'\' /><em class=\'add-on\'><i class=\'icon-renminbi\'></i></em><div style=\'clear: both\'></div></div>';
EOF;
			}
			echo <<<EOF
			str +='</td>';
EOF;
		echo <<<EOF
		str += '<td><input class=\'text stock\' type=\'text\' name=\'spec['+spec_bunch+'][stock]\' data_type=\'stock\' nc_type=\''+spec_bunch+'|stock\' value=\'\' /></td>'+
		'<td style=\'display: none;\'><input class=\'text stock\' type=\'text\' name=\'spec['+spec_bunch+'][salenum]\' data_type=\'salenum\' nc_type=\''+spec_bunch+'|salenum\' value=\'\' /></td>'+
		'<td><input class=\'text sku\' type=\'text\' name=\'spec['+spec_bunch+'][sku]\' nc_type=\''+spec_bunch+'|sku\' value=\'\' /></td></tr>';
EOF;
		for($i = 0; $i < $len; $i++){
            echo '}' . PHP_EOL;
        }
    }
}
?>
<?php if (!empty($output['sp_value']) && !empty($output['spec_checked']) && !empty($output['spec_list'])) { ?>
//  编辑商品时处理JS
$(function() {
	var E_SP = new Array();
	var E_SPV = new Array();
	<?php
	$string = '';
	foreach ($output['spec_checked'] as $v) {
		$string .= 'E_SP[' . $v['id'] . '] = \'' . $v['name'] . '\';';
	}
	echo $string;
	echo PHP_EOL;
	$string = '';
	foreach ($output['sp_value'] as $k => $v) {
		$string .= 'E_SPV[\'' . $k . '\'] = \'' . $v . '\';';
	}
	echo $string;
	?>
	V = E_SPV;
	$('dl[nc_type=spec_dl]').show();
	$('dl[nctype=spec_group_dl]').find('input[type=checkbox]').each(function(){
		//  店铺价格 商品库存改为只读
		$('.g_price').attr('readonly','readonly').css('background','#E7E7E7 none');
		$('input[name=g_costprice]').attr('readonly','readonly').css('background','#E7E7E7 none');
		$('input[name=g_storage]').attr('readonly','readonly').css('background','#E7E7E7 none');
		$('input[name=g_salenum]').attr('readonly','readonly').css('background','#E7E7E7 none');
		s = $(this).attr('nc_type');
		if (!(typeof(E_SP[s]) == 'undefined')){
			$(this).attr('checked',true);
			v = $(this).parents('li').find('span[nctype=pv_name]');
			if(E_SP[s] != ''){
				$(this).val(E_SP[s]);
				v.html('<input type=\'text\' maxlength=\'20\' readonly=\'readonly\' class=\'text\' style=\'border:none; backgound:none\' value=\'' + E_SP[s] + '\' />');
			}else{
				v.html('<input type=\'text\' maxlength=\'20\' readonly=\'readonly\' class=\'text\' style=\'border:none; backgound:none\' value=\'' + v.html() + '\' />');
			}
			//change_img_name($(this));			// 修改相关的颜色名称
		}
	});

    into_array();	// 将选中的规格放入数组
    str = '<tr>';
    <?php recursionSpec(0, $output['sign_i'], $output['member_levels'], $output['member_levels_num']);?>
    if (str == '<tr>') {
        $('dl[nc_type=spec_dl]').hide();
        $('.g_price').removeAttr('readonly').css('background','');
		$('input[name=g_costprice]').removeAttr('readonly').css('background','');
        $('input[name=g_storage]').removeAttr('readonly').css('background','');
		$('input[name=g_salenum]').removeAttr('readonly').css('background','');
    } else {
        $('tbody[nc_type=spec_table]').empty().html(str)
            .find('input[nc_type]').each(function(){
                s = $(this).attr('nc_type');
                try{$(this).val(E_SPV[s]);}catch(ex){$(this).val('');};
            }).end()
			.find('input[data_type=costprice]').change(function() {
                computeCostPrice();    // 成本计算
            }).end()
            .find('input[data_type=stock]').change(function(){
                computeStock();    // 库存计算
            }).end()
			.find('input[data_type=salenum]').change(function(){
                computeSalenum();   // 库存计算
            }).end()
            .find('input[data_type=price]').change(function(){
                var _value = $(this).val();
				var _type = $(this).attr('data_id');				
				_value = number_format(_value, 2);
				if (isNaN(_value)) {
					_value = 0;
				}				
				computePrice(_type);   // 价格计算
            }).end()
            .find('input[type=text]').change(function(){
                s = $(this).attr('nc_type');
                V[s] = $(this).val();
            });
    }
});
<?php } ?>
</script>