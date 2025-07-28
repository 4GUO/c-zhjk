<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='eject_con'>
	<div id='warning' class='alert alert-error'></div>
	<form id='record_form' method='post' action='<?=users_url('withdraw_record/reject')?>'>
	    <input type='hidden' name='form_submit' value='ok' />
		<input type='hidden' name='record_id' value='<?php echo $output['record_info']['record_id'];?>' />
		<dl>
			<dt><i class='required'>*</i>驳回理由：</dt>
			<dd>
				<textarea name='note' id='note' class='textarea h60 w200'></textarea>
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
    $('#record_form').validate({
        errorLabelContainer: $('#warning'),
        invalidHandler: function(form, validator) {
               $('#warning').show();
        },
    	onkeyup: false,
        rules : {
            note : {
                required : true
            }
        },
        messages : {
            note : {
                required : '<i class=\'icon-exclamation-sign\'></i>请填写驳回理由'
            }
        }
    });
	$('.submit').click(function(e){
		if($('#record_form').valid()){
			ajax_form_post('record_form');
		};
	});
});
</script>