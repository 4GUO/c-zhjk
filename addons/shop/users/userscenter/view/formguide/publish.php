<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('formguide/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['info']['id']) ? $output['info']['id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>基本信息</h3>
			<dl>
				<dt><i class='required'>*</i>表单标题：</dt>
				<dd>
					<input name='name' class='text w400' value='<?=isset($output['info']['name']) ? $output['info']['name'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>标题名称长度至少3个字符，最长50个汉字</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>表单键名：</dt>
				<dd>
					<input name='tablename' class='text w400' value='<?=isset($output['info']['tablename']) ? $output['info']['tablename'] : ''?>' type='text'<?php if (!empty($output['info']['tablename'])) { ?> disabled='disabled' style='color: #999999;cursor: not-allowed;'<?php } ?> />
					<span></span>
					<p class='hint'>由小写字母、数字组成，并且仅能小写字母开头</p>
				</dd>
			</dl>
			<dl>
				<dt>简介：</dt>
				<dd>
					<textarea name='description' class='textarea h60 w400'><?=isset($output['info']['description']) ? htmlspecialchars_decode($output['info']['description']) : ''?></textarea>
					<span></span>
				</dd>
			</dl>
			<h3 id='demo2'>安全设置项</h3>
			<dl>
				<dt>提交间隔：</dt>
				<dd>
					<input name='setting[interval]' class='text w100' value='<?=isset($output['info']['setting']) ? $output['info']['setting']['interval'] : 0?>' type='text' />
					<span></span>
					<p class='hint'>单位秒，0为不限</p>
				</dd>
			</dl>
			<dl>
				<dt>IP多次提交：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='setting[allowmultisubmit]' value='1' <?php if((isset($output['info']['setting']) && $output['info']['setting']['allowmultisubmit'] == 1) || !isset($output['info']['setting'])) { ?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='setting[allowmultisubmit]' value='0' <?php if(isset($output['info']['setting']) && $output['info']['setting']['allowmultisubmit'] == 0) { ?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>开启验证码：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='setting[isverify]' value='1' <?php if((isset($output['info']['setting']) && $output['info']['setting']['isverify'] == 1) || !isset($output['info']['setting'])) { ?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='setting[isverify]' value='0' <?php if(isset($output['info']['setting']) && $output['info']['setting']['isverify'] == 0) { ?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>状态：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='status' value='1' <?php if((isset($output['info']['status']) && $output['info']['status'] == 1) || !isset($output['info']['status'])) { ?> checked='checked'<?php }?> type='radio' />启用</label>
						</li>
						<li>
							<label><input name='status' value='0' <?php if(isset($output['info']['status']) && $output['info']['status'] == 0) { ?> checked='checked'<?php }?> type='radio' />禁用</label>
						</li>
					</ul>
					<p class='hint'>被禁用的表单无法提交数据</p>
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