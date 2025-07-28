<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('formguide_field/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='modelid' value='<?php echo input('modelid', 0, 'intval');?>' />
		<input type='hidden' name='id' value='<?php echo isset($output['info']['id']) ? $output['info']['id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>基本信息</h3>
			<dl>
				<dt><i class='required'>*</i>字段别名：</dt>
				<dd>
					<input name='title' class='text w400' value='<?=isset($output['info']['title']) ? $output['info']['title'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>标题名称长度至少3个字符，最长50个汉字</p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>字段名：</dt>
				<dd>
					<input name='name' class='text w400' value='<?=isset($output['info']['name']) ? $output['info']['name'] : ''?>' type='text'<?php if (!empty($output['info']['name'])) { ?> disabled='disabled' style='color: #999999;cursor: not-allowed;'<?php } ?> />
					<span></span>
					<p class='hint'>字母、数字组成，并且仅能字母开头</p>
				</dd>
			</dl>
			<dl>
				<dt>字段描述：</dt>
				<dd>
					<textarea name='remark' class='textarea h60 w400'><?=isset($output['info']['remark']) ? htmlspecialchars_decode($output['info']['remark']) : ''?></textarea>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>字段类型：</dt>
				<dd>
					<select name='type'>
                        <option value=''>选择类型</option>
                        <?php foreach($output['fieldType'] as $vo) { ?>
                        <option value='<?=$vo['name']?>' data-define='<?=$vo['default_define']?>' data-ifoption='<?=$vo['ifoption']?>' data-ifstring='<?=$vo['ifstring']?>' <?php if (isset($output['info']['type']) && $output['info']['type'] == $vo['name']) { ?> selected='selected'<?php } ?>><?=$vo['title']?></option>
                        <?php } ?>
                    </select>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>字段定义：</dt>
				<dd>
				    <input id='define' name='setting[define]' class='text w300' value='<?=isset($output['info']['setting']) ? $output['info']['setting']['define'] : ''?>' type='text' placeholder='字段定义' />
					<select id='fasttype'>
                        <option>快速选择</option>
                        <?php foreach ($output['fasttype'] as $v) { ?>
                        <option data-define='<?=$v['value']?>'><?=$v['label']?></option>
                        <?php } ?>
                    </select>
					<span></span>
				</dd>
			</dl>
			<dl>
				<dt>数据校验：</dt>
				<dd>
				    <input name='pattern' class='text w300' value='<?=isset($output['info']['pattern']) ? $output['info']['pattern'] : ''?>' type='text' placeholder='正则校验数据合法性，留空不校验' />
					<select id='pattern'>
                        <option>常用正则</option>
                        <option data-define='/^[0-9.-]+$/'>数字</option>
                        <option data-define='/^[0-9-]+$/'>整数</option>
                        <option data-define='/^[a-z]+$/i'>字母</option>
                        <option data-define='/^[0-9a-z]+$/i'>数字+字母</option>
                        <option data-define='/^[\x{4e00}-\x{9fa5}]+$/u'>中文</option>
                        <option data-define='/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/'>E-mail</option>
                        <option data-define='/^[0-9]{5,20}$/'>QQ</option>
                        <option data-define='/^http:\/\//'>超级链接</option>
                        <option data-define='/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/'>身份证</option>
                        <option data-define='/^(1)[0-9]{10}$/'>手机号码</option>
                        <option data-define='/^[0-9-]{6,13}$/'>电话号码</option>
                    </select>
					<span></span>
				</dd>
			</dl>
			<dl id='options'<?php if (!isset($output['info']['type']) || (isset($output['info']['type']) && empty($output['fieldType'][$output['info']['type']]['ifoption']))) { ?> style='display: none;'<?php } ?>>
				<dt>选项：</dt>
				<dd>
					<textarea name='setting[options]' class='textarea h60 w400' placeholder='值:描述
值:描述
值:描述
.....'><?=isset($output['info']['setting']) ? htmlspecialchars_decode($output['info']['setting']['options']) : ''?></textarea>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>字段默认值：</dt>
				<dd>
					<input name='setting[value]' class='text w200' value='<?=isset($output['info']['setting']) ? $output['info']['setting']['value'] : ''?>' type='text' placeholder='默认插入字段的值' />
					<span></span>
					<p class='hint'>字符串类型可以为空</p>
				</dd>
			</dl>
			<dl>
				<dt>提示信息：</dt>
				<dd>
					<input name='errortips' class='text w400' value='<?=isset($output['info']['errortips']) ? $output['info']['errortips'] : ''?>' type='text' placeholder='数据校验未通过的提示信息' />
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<?php if (!empty($output['modelInfo']) && $output['modelInfo']['type'] == 2) { ?>
			<dl>
				<dt>主表字段：</dt>
				<dd>
					<ul class='css-form-radio-list'>
						<li>
							<label><input name='ifsystem' value='1' <?php if(isset($output['info']['ifsystem']) && $output['info']['ifsystem'] == 1) { ?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='ifsystem' value='0' <?php if(empty($output['info']['ifsystem'])) { ?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<?php } else { ?>
			<input type='hidden' name='ifsystem' value='1' />
			<?php } ?>

			<dl>
				<dt>是否必填：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='ifrequire' value='1' <?php if(isset($output['info']['ifrequire']) && $output['info']['ifrequire'] == 1) { ?> checked='checked'<?php } ?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='ifrequire' value='0' <?php if(empty($output['info']['ifrequire'])) { ?> checked='checked'<?php } ?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>是否唯一：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='ifonly' value='1' <?php if(isset($output['info']['ifonly']) && $output['info']['ifonly'] == 1) { ?> checked='checked'<?php } ?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='ifonly' value='0' <?php if(empty($output['info']['ifonly'])) { ?> checked='checked'<?php } ?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'></p>
				</dd>
			</dl>
			<dl>
				<dt>显示：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='isadd' value='1' <?php if((isset($output['info']['isadd']) && $output['info']['isadd'] == 1) || !isset($output['info']['isadd'])) { ?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='isadd' value='0' <?php if(isset($output['info']['isadd']) && $output['info']['isadd'] == 0) { ?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'>选择“否”则以hidden型表单显示</p>
				</dd>
			</dl>
			<dl>
				<dt>允许搜索：</dt>
				<dd>
				    <ul class='css-form-radio-list'>
						<li>
							<label><input name='isindex' value='1' <?php if((isset($output['info']['isindex']) && $output['info']['isindex'] == 1) || !isset($output['info']['isindex'])) { ?> checked='checked'<?php }?> type='radio' />是</label>
						</li>
						<li>
							<label><input name='isindex' value='0' <?php if(isset($output['info']['isindex']) && $output['info']['isindex'] == 0) { ?> checked='checked'<?php }?> type='radio' />否</label>
						</li>
					</ul>
					<span></span>
					<p class='hint'>只有文本、数字、select类型支持搜索</p>
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
			<dl>
				<dt>排序：</dt>
				<dd>
					<input name='listorder' class='text w60' value='<?=isset($output['info']['listorder']) ? $output['info']['listorder'] : ''?>' type='text' />
					<span></span>
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
$(function () {
    $('select[name=type]').change(function () {
        var type = $(this).val();
        $('#define').val($(this).find('option:selected').attr('data-define'));
        var ifoption = $(this).find('option:selected').attr('data-ifoption');
        var ifstring = $(this).find('option:selected').attr('data-ifstring');
        if (ifoption == '1') {
            $('#options').show();
        } else {
            $('#options').hide();
        }
    })
    $('#fasttype').change(function () {
        $('#define').val($(this).find('option:selected').attr('data-define'));
    })
    $('#pattern').change(function () {
        $('input[name=pattern]').val($(this).find('option:selected').attr('data-define'));
    })
})
$('.submit').click(function(e){
	ajax_form_post('form');
});
</script>