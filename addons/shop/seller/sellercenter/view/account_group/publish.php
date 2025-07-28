<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='item-publish'>
	<form method='post' id='form' action='<?=users_url('account_group/publish')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input type='hidden' name='id' value='<?php echo isset($output['info']['group_id']) ? $output['info']['group_id'] : 0;?>' />
		<div class='css-form-goods'>
			<h3 id='demo1'>基本信息</h3>
			<dl>
				<dt><i class='required'>*</i>组名称：</dt>
				<dd>
					<input name='group_name' class='text w400' value='<?=isset($output['info']['group_name']) ? $output['info']['group_name'] : ''?>' type='text' />
					<span></span>
					<p class='hint'>设定权限组名称，方便区分权限类型。</p>
				</dd>
			</dl>
			<h3 id='demo1'>分配权限</h3>
			<dl>
				<dt><i class='required'>*</i>权限：</dt>
				<dd>
					<div class='css-account-all'>
						<label for='btn_select_all'><input id='btn_select_all' name='btn_select_all' class='checkbox' type='checkbox' />全选</label>
						<span></span>
						<?php if(!empty($output['menu']) && is_array($output['menu'])) {?>
						<?php foreach($output['menu'] as $key => $value) {?>
					</div>
					<div class='css-account-container'>
						<h4>
							<label class='btn_select_module'><input class='checkbox' type='checkbox' /><?php echo $value['name'];?></label>
						</h4>
						<?php $submenu = $value['child'];?>
						<?php if(!empty($submenu) && is_array($submenu)) {?>
						<ul class='css-account-container-list'>
							<?php foreach($submenu as $submenu_value) {?>
							<li>
								<label><input class='checkbox' name='limits[]' value='<?php echo $submenu_value['act'] . '_' . $submenu_value['op'];?>' <?php if(!empty($output['limits'])) {if(in_array($submenu_value['act'] . '_' . $submenu_value['op'], $output['limits'])) { echo 'checked'; }}?> type='checkbox' /><?php echo $submenu_value['name'];?></label>
							</li>
							<?php } ?>
						</ul>
						<?php } ?>
						<?php } ?>
					</div>
					<?php } ?>
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
	$('#btn_select_all').on('click', function() {
        if($(this).prop('checked')) {
            $(this).parents('dd').find('input:checkbox').prop('checked', true);
        } else {
            $(this).parents('dd').find('input:checkbox').prop('checked', false);
        }
    });
    $('.btn_select_module').on('click', function() {
        if($('input:checkbox', this).prop('checked')) {
            $('input:checkbox', this).parents('.css-account-container').find('input:checkbox').prop('checked', true);
        } else {
            $('input:checkbox', this).parents('.css-account-container').find('input:checkbox').prop('checked', false);
        }
    });
});
</script>