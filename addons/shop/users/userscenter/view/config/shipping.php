<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='active'><a href='javascript:;'>综合设置</a></li>
		<li class='normal'><a href='<?=users_url('config/shipping_transport')?>'>区域运费</a></li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('config/shipping')?>'>
		<input name='form_submit' value='ok' type='hidden' />		
		<div class='css-form-goods'>
			<dl>
				<dt>统一运费：</dt>
				<dd>
					<input name='freight_in' class='text w60' value='<?=$output['config']['freight_in']?>' type='text' /> 元
					<p class='hint'>该设置项仅做记录，具体运费在编辑商品时设置</p>
				</dd>
			</dl>
			<dl>
				<dt>运费统一到所有商品：</dt>
				<dd>
					<label><input type='radio' name='freight_in_all' value='1'<?php echo $output['config']['freight_in_all'] == 1 ? ' checked=\'checked\'' : '';?> />是</label>&nbsp;&nbsp;&nbsp;&nbsp;
					<label><input type='radio' name='freight_in_all' value='0'<?php echo $output['config']['freight_in_all'] == 0 ? ' checked=\'checked\'' : '';?> />否</label>
					<p class='hint'>开启后，系统自动将所有商品的运费统一设置</p>
				</dd>
			</dl>
			<dl>
				<dt>免运费条件：</dt>
				<dd>
					订单总额到达 <input name='freight_infree' class='text w60' value='<?=$output['config']['freight_infree']?>' type='text' /> 元
				</dd>
			</dl>
			<dl style='display: none;'>
				<dt>国外运费基数：</dt>
				<dd>
                	<?php foreach($output['member_levels'] as $level_info){?>
                	<div style='padding-bottom:8px;'>
                        <i style='line-height: 28px; background-color: #E6E6E6; vertical-align: top; display: block; width: 80px; text-align: center; padding:0px 8px; height: 28px; border: 1px solid #CCC; border-right:none; margin:0 0 0 -5px; float:left'><?php echo $level_info['level_name'];?></i><input name='freight_out[<?php echo $level_info['id'];?>]' value='<?php echo empty($output['freight_out'][$level_info['id']]) ? 0 : $output['freight_out'][$level_info['id']]; ?>' type='text'  class='text w60' style='display: block; float:left'/><span></span>
						<div style='clear: both'></div>
                    </div>
                    <?php }?>
					<p class='hint'>不同会员级别不同基数。国外运费=该值*商品总重量</p>
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
</script>