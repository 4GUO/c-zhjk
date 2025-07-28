<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.dd {
		margin-top: 5px;
	}
	.selectUser {
		padding: 5px 10px;
		background: #F5F5F5;
		color: #777777;
	}
	.products_option {margin-bottom: 10px;}
	.products_option .search_div{margin-top:8px;}
	.products_option .search_div .button_search{padding:3px 6px; cursor:pointer}
	.products_option .select_items{margin-top:8px;}
	.products_option .select_items .button_add{height:30px; line-height:26px; width:45px; display:block; margin:30px 8px 0px; float:left; border: solid 1px #E6E6E6;}
	.products_option .select_items .products_show{height:100px; width:300px; display:block; border:1px #E6E6E6 solid; overflow:scroll; background:#FFF}
	.products_option .select_items .products_show p{height:24px; line-height:24px; width:95%; overflow:hidden; padding:0px; margin:0px auto; cursor:pointer}
	.products_option .select_items .products_show .p_cur{background:#39F;}
	.products_option .options_buttons{height:100px; width:80px; float:left; margin-left:8px}
	.products_option .options_buttons button{display:block; height:30px; line-height:26px; width:100%; text-align:center; cursor:pointer; margin:8px 0px 0px 0px; border: solid 1px #E6E6E6;}
	.search_cate select{width:120px}
</style>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/dis_public')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>模块开关：</dt>
				<dd>
					<label><input type='radio' name='public_open' value='1'<?php echo $output['config']['public_open'] == 1 ? ' checked=\'checked\'' : '';?> />开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='public_open' value='0'<?php echo $output['config']['public_open'] == 0 ? ' checked=\'checked\'' : '';?> />关闭</label>
					<p class='hint'>开启后，公排生效</p>
				</dd>
			</dl>
			<dl>
				<dt>递增形式：</dt>
				<dd>
					<select name='public_times'<?php echo $output['config']['public_status'] == 1 ? ' disabled=\'disabled\'' : ''?>>
					    <?php foreach($output['times'] as $k => $v){?>
						<option <?php if($output['config']['public_times'] == $k){?> selected='selected'<?php }?> value='<?=$k?>'><?=$v?></option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt>多点卡位：</dt>
				<dd>
					<label><input type='radio' name='public_multi' value='1'<?php echo $output['config']['public_multi'] == 1 ? ' checked=\'checked\'' : '';?> />开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='public_multi' value='0'<?php echo $output['config']['public_multi'] == 0 ? ' checked=\'checked\'' : '';?> />关闭</label>
					<p class='hint'>若开启，则说明分销商可以多次参与排位</p>
				</dd>
			</dl>
			<dl>
				<dt>卡位条件：</dt>
				<dd>
					<select name='public_cometype'>
                    	<option value='1' <?php if($output['config']['public_cometype'] == 1){?> selected='selected'<?php }?>>一次性消费</option>
                    	<option value='2' <?php if($output['config']['public_cometype'] == 2){?> selected='selected'<?php }?>>购买指定商品</option>
                    	<option value='3' <?php if($output['config']['public_cometype'] == 3){?> selected='selected'<?php }?>>购买任意商品</option>
                    </select>
					<span></span>
				</dd>
			</dl>
			<dl id='public_cometype1'<?php if($output['config']['public_cometype'] == 1) { ?> style='display: block;'<?php } else { ?> style='display: none;'<?php } ?>>
				<dt>一次性消费</dt>
				<dd>
					<input name='public_come_money' class='text w60' value='<?=$output['config']['public_come_money']?>' type='text' />&nbsp;元
					<span></span>
				</dd>
			</dl>
			<dl id='public_cometype2'<?php if($output['config']['public_cometype'] == 2) { ?> style='display: block;'<?php } else { ?> style='display: none;'<?php } ?>>
				<dt>选择卡位商品</dt>
				<dd>
					<div class='products_option'>
						<div class='select_items'>
							 <select size='10' class='select_product0' style='width:300px; height:100px; display:block; float:left'>
								<?php if (!empty($output['goods_list'])) { ?>
								<?php foreach ($output['goods_list'] as $v_r) { ?>
								<option value='<?php echo $v_r['goods_commonid'];?>'><?php echo $v_r['goods_name'];?></option>
								<?php } ?>
								<?php } ?>
							 </select>
							 <button type='button' class='button_add'> => </button>
							 <select size='10' class='select_product1' multiple style='width:300px; height:100px; display:block; float:left'>
								<?php if (!empty($output['goods_list_return'])) { ?>
								<?php foreach ($output['goods_list_return'] as $v_r) { ?>
								<option value='<?php echo $v_r['goods_commonid'];?>'><?php echo $v_r['goods_name'];?></option>
								<?php } ?>
								<?php } ?>
							 </select>
							 <input type='hidden' name='goods_ids' value='<?php echo empty($output['goods_ids']) ? '' : ',' . $output['goods_ids'] . ',';?>' />
						</div>
						   
						<div class='options_buttons'>
							<button type='button' class='button_remove'>移除</button>
							<button type='button' class='button_empty'>清空</button>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt>见点奖层数：</dt>
				<dd>
					<select name='public_bonus_level'>
					    <?php for ($i = 1; $i <= 15; $i++) {?>
						<option <?php if($output['config']['public_bonus_level'] == $i){?> selected='selected'<?php }?> value='<?=$i?>'><?=$i?>级</option>
						<?php } ?>
					</select>
					<p class='hint'>公排时获得见点奖层数</p>
				</dd>
			</dl>
			<dl>
				<dt>出局开关：</dt>
				<dd>
					<label><input type='radio' name='public_out_open' value='1'<?php echo $output['config']['public_out_open'] == 1 ? ' checked=\'checked\'' : '';?> />开启</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='public_out_open' value='0'<?php echo $output['config']['public_out_open'] == 0 ? ' checked=\'checked\'' : '';?> />关闭</label>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>出局层级：</dt>
				<dd>
					<select name='public_out_level'>
					    <?php for ($i = 1; $i <= 15; $i++) {?>
						<option <?php if($output['config']['public_out_level'] == $i){?> selected='selected'<?php }?> value='<?=$i?>'><?=$i?>级</option>
						<?php } ?>
					</select>
					<p class='hint'></p>
				</dd>
			</dl>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button'>
			</label>
		</div>
	</form>
</div>
<script>
$('.submit').click(function(e){
	ajax_form_post('form');
});
$(function(){
	$('select[name=public_cometype]').change(function () {
		var public_cometype = $(this).val();
		if (public_cometype == 1) {
			$('#public_cometype1').show();
			$('#public_cometype2').hide();
			$('#public_cometype3').hide();
		} else if (public_cometype == 2) {
			$('#public_cometype1').hide();
			$('#public_cometype2').show();
			$('#public_cometype3').hide();
		} else {
			$('#public_cometype1').hide();
			$('#public_cometype2').hide();
			$('#public_cometype3').show();
		}
	})
	$('.products_option .select_items .button_add').click(function() {
		var text = $(this).parent().children('.select_product0').find('option:selected').text();
		var value = $(this).parent().children('.select_product0').find('option:selected').val();
		if ($(this).parent().children('.select_product1').find('option:contains(' + text + ')').length == 0 && typeof(value) != 'undefined') {
			$(this).parent().children('.select_product1').append('<option value=\'' + value + '\'>' + text + '</option>');
		}
			
		var strids = $(this).parent().children('input').val();
		if (typeof(value)!='undefined') {
			if (strids == '') {
				$(this).parent().children('input').val(',' + value + ',');
			} else {
				strids = strids.replace(',' + value + ',',',');
				$(this).parent().children('input').val(strids + value + ',');
			}
		}
	});
		
	$('.products_option .options_buttons .button_remove').click(function(){//移除选项		
		var select_obj = $(this).parent().parent().children('.select_items').children('.select_product1').find('option:selected');
		var input_obj = $(this).parent().parent().children('.select_items').children('input');
		var strids = input_obj.val();
		select_obj.each(function() {
			$(this).remove();
			strids = strids.replace(',' + $(this).val() + ',',',');
		});
		if (strids == ',') {
			strids = '';
		}
		input_obj.val(strids);
	});
		
	$('.products_option .options_buttons .button_empty').click(function(){//清空选项
		 $(this).parent().parent().children('.select_items').children('.select_product1').empty();
		 $(this).parent().parent().children('.select_items').children('input').val('');
	});
})
</script>