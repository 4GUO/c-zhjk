<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style>
	.tbl-attach {
		padding: 5px 0;
	}
	.tr {
		text-align: center !important;
	}
</style>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'><a href='<?=users_url('config/shipping')?>'>综合设置</a></li>
		<li class='active'><a href='<?=users_url('config/shipping_transport')?>'>区域运费</a></li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/shipping_transport_add')?>'>
		<input type='hidden' name='transport_id' value='<?php echo isset($output['transport']) ? $output['transport']['id'] : 0;?>' />
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>区域名称：</dt>
				<dd>
					<input name='title' class='text' value='<?=isset($output['transport']) ? $output['transport']['title'] : ''?>' type='text' />
					<p class='hint'>备注用</p>
				</dd>
			</dl>
			<dl>
				<dt>详细设置：</dt>
				<dd class='trans-line'> </dd>
			</dl>
		</div>
		<div class='bottom tc hr32'>
			<label class='submit-border'>
				<input class='submit' value='提交' type='button'>
			</label>
		</div>
	</form>
</div>
<div class='ks-ext-mask' style='position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 999; display:none'></div>
<div id='dialog_areas' class='dialog-areas' style='display:none'>
	<div class='ks-contentbox'>
		<div class='title'>选择区域<a class='ks-ext-close' href='javascript:void(0)'>X</a></div>
		<form method='post'>
			<ul id='J_CityList'>
				<?php $i = 1; $areas = $output['areas']; foreach ($areas['region'] as $region => $provinceIds) { ?>
				<li<?php if ($i % 2 == 0) echo ' class=\'even\''; ?>>
					<dl class='css-region'>
						<dt class='css-region-title'> <span>
							<input type='checkbox' id='J_Group_<?php echo $i; ?>' class='J_Group' value=''/>
							<label for='J_Group_<?php echo $i; ?>'><?php echo $region; ?></label>
							</span> </dt>
						<dd class='css-province-list'>
							<?php foreach ($provinceIds as $provinceId) { ?>
							<div class='css-province'><span class='css-province-tab'>
								<input type='checkbox' class='J_Province' id='J_Province_<?php echo $provinceId; ?>' value='<?php echo $provinceId; ?>'/>
								<label for='J_Province_<?php echo $provinceId; ?>'><?php echo $areas['name'][$provinceId]; ?></label>
								<span class='check_num'/> </span><i class='icon-angle-down trigger'></i>
								<div class='css-citys-sub'>
									<?php foreach ($areas['children'][$provinceId] as $cityId) { ?>
									<span class='areas'>
									<input type='checkbox' class='J_City' id='J_City_<?php echo $cityId; ?>' value='<?php echo $cityId; ?>'/>
									<label for='J_City_<?php echo $cityId; ?>'><?php echo $areas['name'][$cityId]; ?></label>
									</span>
									<?php } ?>
									<p class='tr hr8'><a href='javascript:void(0);' class='css-btn-mini css-btn-orange close_button'>关闭</a></p>
								</div>
								</span> </div>
							<?php } ?>
						</dd>
					</dl>
				</li>
				<?php $i++; } ?>
			</ul>
			<div class='bottom'> <a href='javascript:void(0);' class='J_Submit css-btn css-btn-green'>确定</a> <a href='javascript:void(0);' class='J_Cancel css-btn'>取消</a> </div>
		</form>
	</div>
</div>
<div id='dialog_batch' class='dialog-batch' style='z-index: 9999; display:none'>
	<div class='ks-contentbox'>
		<div class='title'>批量操作<a class='ks-ext-close' href='javascript:void(0)'>X</a></div>
		<form method='post'>
			<div class='batch'>默认运费:
				<input class='w30 mr5 text' type='text' maxlength='4' autocomplete='off' data-field='start' value='1' name='express_start' />
				件内，
				<input class='w60 text' type='text' maxlength='6' autocomplete='off' value='0.00' name='express_postage' data-field='postage' />
				<em class='add-on'> <i class='icon-renminbi'></i> </em>，每增加
				<input class='w30 mr5 text' type='text' maxlength='4' autocomplete='off' value='1' data-field='plus' name='express_plus'>
				件，增加运费
				<input class='w60 text' type='text' maxlength='6' autocomplete='off' value='0.00' data-field='postageplus' name='express_postageplus' />
				<em class='add-on'> <i class='icon-renminbi'></i> </em></div>
			<div class='J_DefaultMessage'></div>
			<div class='bottom'> <a href='javascript:void(0);' class='J_SubmitPL css-btn css-btn-green'>确定</a> <a href='javascript:void(0);' class='J_Cancel css-btn'>取消</a> </div>
		</form>
	</div>
</div>
<script src='<?=STATIC_URL?>/admin/js/transport.js'></script> 
<script>
$(function(){
	$('.trans-line').append(TransTpl.replace(/TRANSTYPE/g, 'kd'));
	$('.tbl-except').append(RuleHead);
	<?php if (!empty($output['extend']) && is_array($output['extend'])) { ?>
	<?php foreach ($output['extend'] as $value) { ?>
		<?php if (isset($value['is_default']) && $value['is_default'] == 1) { ?>
			var cur_tr = $('.tbl-except').prev();
			$(cur_tr).find('input[data-field=start]').val('<?php echo $value['snum'];?>');
			$(cur_tr).find('input[data-field=postage]').val('<?php echo $value['sprice'];?>');
			$(cur_tr).find('input[data-field=plus]').val('<?php echo $value['xnum'];?>');
			$(cur_tr).find('input[data-field=postageplus]').val('<?php echo $value['xprice'];?>');
		<?php } else { ?>
			StartNum += 1;
			cell = RuleCell.replace(/CurNum/g, StartNum);
			cell = cell.replace(/TRANSTYPE/g, 'kd');
			$('.tbl-except').find('table').append(cell);
			$('.tbl-attach').find('.J_ToggleBatch').css('display', '').html('批量操作');
			var cur_tr = $('.tbl-except').find('table').find('tr:last');
			$(cur_tr).find('.area-group>p').html('<?php echo $value['area_name'];?>');
			$(cur_tr).find('input[type=hidden]').val('<?php echo trim($value['area_id'],',');?>|||<?php echo $value['area_name'];?>');
			$(cur_tr).find('input[data-field=start]').val('<?php echo isset($value['snum']) ? $value['snum'] : 0;?>');
			$(cur_tr).find('input[data-field=postage]').val('<?php echo $value['sprice'];?>');
			$(cur_tr).find('input[data-field=plus]').val('<?php echo isset($value['xnum']) ? $value['xnum'] : 0;?>');
			$(cur_tr).find('input[data-field=postageplus]').val('<?php echo isset($value['xprice']) ? $value['xprice'] : 0;?>');
		<?php } ?>
	<?php } ?>
	<?php } ?>
});
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>