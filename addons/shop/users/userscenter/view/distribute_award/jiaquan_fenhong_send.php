<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('distribute_award/jiaquan_fenhong_send')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>时间</th>
				<td class='w300'>
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
	<form id='form' method='post' target='_parent' action='<?=_url('distribute_award/jiaquan_fenhong_send')?>'>
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
			<dt>联创总人数：</dt>
			<dd>
				<span class='highlight'><?=$output['total_fenshu']?></span>&nbsp;人
			</dd>
		</dl>
		<dl>
			<dt>分红条件：</dt>
			<dd>
				团队业绩 ≥ 级别要求，且累计分红 < 级别上限
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'> <a id='btn_add' class='submit' href='javascript:void(0);'>提交发放</a> </label>
		</div>
	</form>
</div>

<!-- 新增：详细人员列表 -->
<div class='css-form-default' style='margin-top: 20px;'>
	<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>
		<h3 style='color: #333; border-bottom: 2px solid #007cba; padding-bottom: 8px; margin: 0; padding-right: 20px;'>
			<i class='icon-users'></i> 符合分红条件的人员列表
		</h3>
		<div style='display: flex; gap: 10px; align-items: center;'>
			<span style='font-size: 12px; color: #666;'>显示模式：</span>
			<a href='<?=users_url('distribute_award/jiaquan_fenhong_send', array('query_start_date' => input('query_start_date', ''), 'query_end_date' => input('query_end_date', ''), 'show_detail' => 0))?>' class='btn <?=$output['show_detail'] == 0 ? 'btn-blue' : 'btn-gray'?>' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-bolt'></i> 快速模式
			</a>
			<a href='<?=users_url('distribute_award/jiaquan_fenhong_send', array('query_start_date' => input('query_start_date', ''), 'query_end_date' => input('query_end_date', ''), 'show_detail' => 1))?>' class='btn <?=$output['show_detail'] == 1 ? 'btn-blue' : 'btn-gray'?>' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-list'></i> 详细模式
			</a>
		</div>
	</div>
	
	<?php if (!empty($output['detailed_member_list'])): ?>
		<table class='css-default-table' id='my_agent'>
			<thead>
				<tr>
					<th class='w60'>序号</th>
					<th class='w100'>用户ID</th>
					<th class='w150'>昵称</th>
					<th class='w120'>手机号</th>
					<th class='w100'>级别</th>
					<?php if ($output['show_detail']): ?>
					<th class='w120'>团队业绩</th>
					<th class='w120'>累计分红</th>
					<?php endif; ?>
					<th class='w100'>分红比例</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($output['detailed_member_list'] as $index => $member): ?>
				<tr>
					<td class='tc'><?=$index + 1?></td>
					<td class='tc'><?=$member['uid']?></td>
					<td><?=htmlspecialchars($member['nickname'])?></td>
					<td><?=htmlspecialchars($member['mobile'])?></td>
					<td class='tc'><span class='label label-success'><?=htmlspecialchars($member['level_name'])?></span></td>
					<?php if ($output['show_detail']): ?>
					<td class='tc'>
						<span class='highlight'><?=number_format($member['yue_yeji'], 2)?></span> / <?=number_format($member['required_yue_yeji'], 2)?>
					</td>
					<td class='tc'>
						<span class='highlight'><?=number_format($member['total_bonus'], 2)?></span> / <?=number_format($member['required_total_bonus'], 2)?>
					</td>
					<?php endif; ?>
					<td class='tc'>
						<span class='label label-info'><?=$member['fenhong_bili']?>%</span>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>	
	<?php if (empty($output['detailed_member_list'])): ?>
		<div class='alert alert-info' style='margin-top: 15px;'>
			<i class='icon-info-sign'></i> 当前没有有分红资格的级别用户
		</div>
	<?php endif; ?>
</div>

<style>
.highlight {
	color: #007cba;
	font-weight: bold;
}
.label {
	padding: 3px 8px;
	border-radius: 3px;
	font-size: 12px;
}
.label-success {
	background-color: #5cb85c;
	color: white;
}
.label-info {
	background-color: #5bc0de;
	color: white;
}
.label-warning {
	background-color: #f0ad4e;
	color: white;
}
.alert {
	padding: 15px;
	border: 1px solid transparent;
	border-radius: 4px;
}
.alert-info {
	color: #31708f;
	background-color: #d9edf7;
	border-color: #bce8f1;
}
.summary-info {
	border-left: 4px solid #007cba;
}
.btn {
	display: inline-block;
	text-decoration: none;
	border-radius: 3px;
	border: 1px solid #ddd;
	transition: all 0.3s;
}
.btn-blue {
	background-color: #007cba;
	color: white;
	border-color: #007cba;
}
.btn-gray {
	background-color: #f5f5f5;
	color: #666;
	border-color: #ddd;
}
.btn:hover {
	opacity: 0.8;
}
</style>

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