<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('voucher/index')?>'>商家代金券</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/pricelist')?>'>面额管理</a>
		</li>
		<li class='active'>
			<a href='javascript:;'>编辑代金券</a>
		</li>
		<li class='normal'>
			<a href='<?=users_url('voucher/config')?>'>设置</a>
		</li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('voucher/edit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' id='tid' name='tid' value='<?php echo isset($output['t_info']['voucher_t_id']) ? $output['t_info']['voucher_t_id'] : 0;?>'/>
		<div class='css-form-goods'>
			<dl>
				<dt>代金券名称：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_title']) ? $output['t_info']['voucher_t_title'] : '';?>
				</dd>
			</dl>

			<dl>
				<dt>领取方式：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_gettype_text']) ? $output['t_info']['voucher_t_gettype_text'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt><em class='pngFix'></em>有效期：</dt>
				<dd>
					<?php echo !empty($output['t_info']['voucher_t_end_date']) ? date('Y-m-d H:i', $output['t_info']['voucher_t_end_date']) : '';?>
				</dd>
			</dl>
			<dl>
				<dt>面额：</dt>
				<dd>
					<?php echo !empty($output['t_info']['voucher_t_price']) ? $output['t_info']['voucher_t_price'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt>可发放总数：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_total']) ? $output['t_info']['voucher_t_total'] : ''; ?>
				</dd>
			</dl>
			<dl id='eachlimit_dl'>
				<dt>每人限领：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_eachlimit']) ? $output['t_info']['voucher_t_eachlimit'] : ''; ?>
				</dd>
			</dl>
			<dl>
				<dt>消费金额：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_limit']) ? $output['t_info']['voucher_t_limit'] : '';?>
				</dd>
			</dl>
			<dl id='mgrade_dl'>
				<dt>会员级别：</dt>
				<dd>
					<?php echo isset($output['member_grade'][$output['t_info']['voucher_t_mgradelimit']]['level_name']) ? $output['member_grade'][$output['t_info']['voucher_t_mgradelimit']]['level_name'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt>代金券描述：</dt>
				<dd>
					<?php echo isset($output['t_info']['voucher_t_desc']) ? $output['t_info']['voucher_t_desc'] : '';?>
				</dd>
			</dl>
			<dl>
				<dt>代金券图片：</dt>
				<dd>
					<div class='css-goods-default-pic'>
						<div class='goodspic-uplaod'>
							<div class='upload-thumb'> <img nctype='voucher_t_customimg' src='<?=!empty($output['t_info']['voucher_t_customimg']) ? $output['t_info']['voucher_t_customimg'] : STATIC_URL . '/images/default_image.png'?>'> </div>
						</div>
					</div>
				</dd>
			</dl>
			<dl>
				<dt><em class='pngFix'></em>状态：</dt>
				<dd>
					<label><input type='radio' value='<?php echo $output['templatestate_arr']['usable'][0];?>' name='tstate' <?php echo !empty($output['t_info']['voucher_t_state']) && ($output['t_info']['voucher_t_state'] == $output['templatestate_arr']['usable'][0]) ? 'checked' : '';?> />&nbsp;<?php echo $output['templatestate_arr']['usable'][1];?></label>
					<label><input type='radio' value='<?php echo $output['templatestate_arr']['disabled'][0];?>' name='tstate' <?php echo !empty($output['t_info']['voucher_t_state']) && ($output['t_info']['voucher_t_state'] == $output['templatestate_arr']['disabled'][0]) ? 'checked' : '';?> />&nbsp;<?php echo $output['templatestate_arr']['disabled'][1];?></label>
				</dd>
			</dl>
			<dl>
				<dt><em class='pngFix'></em>已领取</dt>
				<dd><?php echo $output['t_info']['voucher_t_giveout'];?>&nbsp;张</dd>
			</dl>
			<dl>
				<dt><em class='pngFix'></em>已使用</dt>
				<dd><?php echo $output['t_info']['voucher_t_used'];?>&nbsp;张</dd>
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