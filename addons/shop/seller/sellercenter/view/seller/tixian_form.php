<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=_url('seller/tixian_form_submit')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<div class='css-form-goods'>
			<dl>
				<dt>打款类型：</dt>
				<dd>
					<?php foreach ($output['withdraw_method_list'] as $key => $val) { ?>
					<label style='padding: 5px 10px;'><input name='supply_apply_type' value='<?=$val['method_code']?>' type='radio' />&nbsp;&nbsp;<?=$val['method_name']?></label>
					<?php } ?>
				</dd>
			</dl>
			<dl class='b wxzhuanzhang' style='display: none;'>
				<dt>绑定会员</dt>
				<dd>
					<?php if (empty($output['member_info'])) { ?>
						未绑定，请联系平台管理员绑定
					<?php } else { ?>
						<input type='hidden' name='weixin_account' value='<?=$output['member_info']['nickname']?>'/>
						<img src='<?=$output['member_info']['headimg']?>' width='35' height='35' style='vertical-align: middle; margin-right: 10px;' /><?=$output['member_info']['nickname']?>
					<?php } ?>
				</dd>
			</dl>
			<dl class='b alipay' style='display: none;'>
				<dt>支付宝账号</dt>
				<dd>
					<input name='alipay_account' class='text w400' value='' type='text' />
				</dd>
			</dl>
			<dl class='b bank' style='display: none;'>
				<dt>银行名称</dt>
				<dd>
					<input name='card_name' class='text w400' value='' type='text' />
				</dd>
			</dl>
			<dl class='b bank' style='display: none;'>
				<dt>银行卡账户</dt>
				<dd>
					<input name='card_account' class='text w400' value='' type='text' />
				</dd>
			</dl>
			<dl class='b bank' style='display: none;'>
				<dt>持卡人姓名</dt>
				<dd>
					<input name='card_username' class='text w400' value='' type='text' />
				</dd>
			</dl>
			<dl>
				<dt>提现金额：</dt>
				<dd>
					<input name='money' class='text w100' value='<?=$output['supply_commiss']['money']?>' type='text' />
					<span></span>
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
	$(function() {
		$('input[name=supply_apply_type]').change(function () {
			$('.b').hide();
			var supply_apply_type = $(this).val();
			$('.' + supply_apply_type).show();
		});
	})
</script>