<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.items_info{
	display:none
}
.items_info img{
	width:60px;
	border-radius: 5px;
}
.items_info strong{
	padding-left: 8px
}
</style>
<div class='tabmenu'>
	<ul class='tab pngFix'>
		<li class='normal'>
			<a href='<?=users_url('pd_log/index')?>'>余额明细</a>
		</li>
		<li class='active'>
			<a href='<?=users_url('pd_log/add')?>'>调节余额</a>
		</li>
	</ul>
</div>
<div class='item-publish'>
	<form method='post' id='member_form' action='<?=users_url('pd_log/add')?>'>
		<input name='form_submit' value='ok' type='hidden' />
		<input name='member_id' id='member_id' value='0' type='hidden' />
		<div class='css-form-goods'>			
			<dl>
				<dt><i class='required'>*</i>会员ID：</dt>
				<dd>
					<input name='member_name' id='member_name' class='text w80' value='' type='text' onchange='javascript:checkmember();' />
					<p id='tr_memberinfo'></p>
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>增减类型：</dt>
				<dd>
					<select id='operatetype' name='operatetype'>
						<option value='1'>增加</option>
						<option value='2'>减少</option>
						<option value='3'>冻结</option>
						<option value='4'>解冻</option>
					</select>				
				</dd>
			</dl>
			<dl>
				<dt><i class='required'>*</i>金额：</dt>
				<dd>
					<input name='pointsnum' id='pointsnum' class='text w80' value='' type='text' />					
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
	function checkmember(){
		$('#tr_memberinfo').html('').hide();
		var membername = $.trim($('#member_name').val());
		if(membername == ''){
			$('#member_id').val('0');
			showError('请填写会员ID');
			return false;
		}
		$.get('<?=users_url('pd_log/checkmember')?>', {'name':membername}, function(data){
			if (data){
				var msg= ' '+ data.name + ', 当前预存款数为' + data.available_predeposit+', 当前预存款冻结金额数为' + data.freeze_predeposit;
				$('#member_id').val(data.id);
				$('#tr_memberinfo').html(msg).show();
			}else{
				$('#member_name').val('');
				$('#member_id').val('0');
				showError('会员信息错误');
			}
		}, 'json');
	}
	
	$('#member_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
			pointsnum : {
				required : true
			}
        },
        messages : {
			pointsnum : {
				required : '请填写金额'
			}
        }
    });
    var submit_flag = true;
	$('.submit').click(function(e){
	    if (submit_flag == false) {
	        return true;
	    }
	    submit_flag = false;
		if($('#member_id').val() == 0){
			showError('请填写会员ID');
			submit_flag = true;
			return false;
		}
		if($('#member_form').valid()){
			ajax_form_post('member_form');
		};
	});
</script>