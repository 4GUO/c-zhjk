<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<form id='form' method='post' target='_parent' action='<?=_url('formguide_info/publish')?>'>
	    <input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='modelid' value='<?php echo input('modelid', 0, 'intval');?>' />
		<input type='hidden' name='id' value='<?=isset($output['info']['id']) ? $output['info']['id'] : 0?>' />
		<dl>
			<dt>备注：</dt>
			<dd>
				<textarea name='remark' class='textarea h60 w250'><?=isset($output['info']['remark']) ? htmlspecialchars_decode($output['info']['remark']) : ''?></textarea>
				<span></span>
			</dd>
		</dl>
		<dl>
			<dt>状态：</dt>
			<dd>
				<label><input type='radio' name='status' value='1'<?php if(isset($output['info']['status']) && $output['info']['status'] == 1) {?> checked='checked'<?php }?> />有效</label>&nbsp;
				<label><input type='radio' name='status' value='0'<?php if(empty($output['info']['status'])){?> checked='checked'<?php }?> />无效</label>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'>
				<input type='button' class='submit' value='提交' />
			</label>
		</div>
	</form>
</div>
<script type='text/javascript'>
$(function(){
	$('.submit').click(function(e){
		ajax_form_post('form');
	});
});
</script>