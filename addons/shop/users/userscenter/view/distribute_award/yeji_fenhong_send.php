<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('distribute_award/yeji_fenhong_send')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>时间</th>
				<td class='w240'>
					<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					&nbsp;–&nbsp;
					<input id='query_end_date' class='text w70' name='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
				</td>
				<td class='tc w70'>
					<label class='submit-border'>
						<input class='submit' value='搜索' type='submit'>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<div class='css-form-default'>
	<form id='form' method='post' target='_parent' action='<?=_url('distribute_award/yeji_fenhong_send')?>'>
		<input type='hidden' id='form_submit' name='form_submit' value='ok'/>
		<input type='hidden' name='query_start_date' value='<?=input('query_start_date', '')?>' />
		<input type='hidden' name='query_end_date' value='<?=input('query_end_date', '')?>' />
		<dl>
			<dt><i class='required'>*</i>业绩：</dt>
			<dd>
				<input name='yeji' value='<?=$output['total_yeji'] ?? 0;?>' class='text w60' type='text' />
			</dd>
		</dl>
		<dl>
			<dt>分红券总量：</dt>
			<dd>
				<?=$output['total_fenhong_quan']?>&nbsp;
			</dd>
		</dl>
		<dl>
			<dt>分红比例：</dt>
			<dd>
				<?=$output['setting']['yeji_fenhong_bili']?>&nbsp;%
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'> <a id='btn_add' class='submit' href='javascript:void(0);'>提交发放</a> </label>
		</div>
	</form>
</div>
<link rel='stylesheet' type='text/css' href='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.css'/>
<script type='text/javascript' src='<?=STATIC_URL?>/js/datetimepicker/jquery.datetimepicker.js'></script>
<script>
$('#query_start_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});

$('#query_end_date').datetimepicker({
	lang:'ch',
	datepicker:true,
	format:'Y-m-d',
	formatDate:'Y-m-d',
	step:5
});
</script>
<script>
$(function() {
	var btn_check = true;
	$('#btn_add').click(function(e) {
		var msg = '您真的确定要发放吗？\n\n请确认！';
        if (confirm(msg) == true) {
            if (btn_check) {
    			ajax_form_post('form', function(e) {
    			    if (e.state == 200) {
    			        btn_check = false;
        				if (e.data.msg) {
        					showSucc(e.data.msg, function(){
        						if(typeof(e.data.url) != 'undefined'){
        							location.href = e.data.url;
        						}
        					});
        				}
        			} else {
        			    btn_check = true;
        				showError(e.msg);
        			}
    			});
    		}
    		btn_check = false;
            return true;
        } else {
            btn_check = true;
            return false;
        }
	});
});
</script>