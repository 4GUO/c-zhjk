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
	<form method='post' id='form' action='<?=users_url('config/distribute')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>必须通过邀请人才能成为会员：</dt>
				<dd>
					<label><input type='radio' name='member_inviter' value='1'<?php echo $output['config']['member_inviter'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='member_inviter' value='0'<?php echo $output['config']['member_inviter'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
					<p class='hint'>若关闭，则说明没有邀请人，用户成为不了会员，包括注册和微信自动授权</p>
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>分销商条件：</dt>
				<dd>
					<select name='dis_cometype'>
                    	<option value='1' <?php if($output['config']['dis_cometype'] == 1){?> selected='selected'<?php }?>>一次性消费</option>
                    	<option value='2' <?php if($output['config']['dis_cometype'] == 2){?> selected='selected'<?php }?>>购买指定商品</option>
                    	<option value='3' <?php if($output['config']['dis_cometype'] == 3){?> selected='selected'<?php }?>>购买任意商品</option>
                    </select>
					<span></span>
				</dd>
			</dl>
			<dl id='dis_cometype1'<?php if($output['config']['dis_cometype'] == 1) { ?> style='display: block;'<?php } else { ?> style='display: none;'<?php } ?>>
				<dt>一次性消费</dt>
				<dd>
					<input name='dis_come_money' class='text w60' value='<?=$output['config']['dis_come_money']?>' type='text' />&nbsp;元
					<span></span>
				</dd>
			</dl>
			<dl id='dis_cometype2'<?php if($output['config']['dis_cometype'] == 2) { ?> style='display: block;'<?php } else { ?> style='display: none;'<?php } ?>>
				<dt>选择商品</dt>
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
				<dt>商品分销模块开关：</dt>
				<dd>
					<label><input type='radio' name='distributor_open_goods' value='1'<?php echo $output['config']['distributor_open_goods'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='distributor_open_goods' value='0'<?php echo $output['config']['distributor_open_goods'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
					<p class='hint'>开启后，会员购物上级获得佣金</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>佣金名称：</dt>
				<dd>
					<input name='bonus_name_goods' class='text w400' value='<?=$output['config']['bonus_name_goods']?>' type='text' />
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>商品分销佣金级数：</dt>
				<dd>
					<select name='distributor_level_goods'>
					    <?php for($i=1;$i<=9;$i++){?>
						<option <?php if($output['config']['distributor_level_goods'] == $i){?> selected='selected'<?php }?> value='<?=$i?>'><?=$i?>级</option>
						<?php }?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt>商品自销开关：</dt>
				<dd>
					<label><input type='radio' name='distributor_self_goods' value='1'<?php echo $output['config']['distributor_self_goods'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='distributor_self_goods' value='0'<?php echo $output['config']['distributor_self_goods'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
					<p class='hint'><?=$output['config']['bonus_name_goods']?>自销开启后，会员购物自己获得佣金</p>
				</dd>
			</dl>
			<dl>
				<dt>联创绩效分红比例：</dt>
				<dd>
					<input name='yeji_fenhong_bili' class='text w60' value='<?=$output['config']['yeji_fenhong_bili']?>' type='text' />&nbsp;%
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>零售加权分红比例：</dt>
				<dd>
					<input name='fenhong_reward_bili' class='text w60' value='<?=$output['config']['fenhong_reward_bili']?>' type='text' />&nbsp;%
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>零售加权分红条件：</dt>
				<dd>
				    <div>邀请体验馆人数&nbsp;<input name='linshou_fenhong_inviter_num' class='text w60' value='<?=$output['config']['linshou_fenhong_inviter_num']?>' type='text' />&nbsp;人</div>
					<div>分红级别&nbsp;
    					<select name='linshou_fenhong_level_id'>
    					    <?php foreach($output['member_levels'] as $v) { ?>
                        	<option value='<?=$v['id']?>' <?php if($output['config']['linshou_fenhong_level_id'] == $v['id']){?> selected='selected'<?php }?>><?=$v['level_name']?>及以上</option>
                        	<?php } ?>
                        </select>
					</div>
					<span></span>
				</dd>
			</dl>
            <dl>
                <dt>复购见单条件：</dt>
                <dd>
                    <div>复购见单条件&nbsp;<input name='fgjdtj' class='text w60' value='<?=$output['config']['fgjdtj']?>' type='text' />&nbsp;单</div>
                    <span></span>
                </dd>
            </dl>
			<dl>
				<dt><i class='required'>*</i>产品佣金可提现时间段：</dt>
				<dd>
					<input name='tixian_day_start' id='tixian_day_start' class='text w60' value='<?=$output['config']['tixian_day_start']?>' type='text'> ~ <input name='tixian_day_end' id='tixian_day_end' class='text w60' value='<?=$output['config']['tixian_day_end']?>' type='text'> 号
					<p class='hint'>注：具体到日期，几号，0表示不限制</p>
				</dd>
			</dl>
			<dl>
				<dt>提现限制提示语：</dt>
				<dd>
					<textarea name='tixian_tip' class='textarea h60 w400'><?=$output['config']['tixian_tip']?></textarea>
					<span></span>
					<p class='hint'>注：产品分销佣金提现时间段限制提示语</p>
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
	$('select[name=dis_cometype]').change(function () {
		var dis_cometype = $(this).val();
		if (dis_cometype == 1) {
			$('#dis_cometype1').show();
			$('#dis_cometype2').hide();
			$('#dis_cometype3').hide();
		} else if (dis_cometype == 2) {
			$('#dis_cometype1').hide();
			$('#dis_cometype2').show();
			$('#dis_cometype3').hide();
		} else {
			$('#dis_cometype1').hide();
			$('#dis_cometype2').hide();
			$('#dis_cometype3').show();
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
