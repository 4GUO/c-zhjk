<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<div class='css-form-default'>
	<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>
		<h3>零售分红历史名单</h3>
		<div>
			<a href='<?=users_url('distribute_award/lingshou_fenhong_send')?>' class='btn btn-blue' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-arrow-left'></i> 返回分红发放
			</a>
		</div>
	</div>
	
	<!-- 搜索筛选 -->
	<form method='get' action='<?=users_url('distribute_award/lingshou_fenhong_list')?>'>
		<table class='search-form'>
			<tbody>
				<tr>
					<td>&nbsp;</td>
					<th>状态</th>
					<td class='w150'>
						<select name='stat' class='text w120'>
							<option value='-1'>全部状态</option>
							<option value='1' <?=$output['stat'] == 1 ? 'selected' : ''?>>生效</option>
							<option value='0' <?=$output['stat'] == 0 ? 'selected' : ''?>>不生效</option>
						</select>
					</td>
					<th>日期</th>
					<td class='w150'>
						<input class='text w120' name='date' value='<?=input('date', '')?>' placeholder='格式：202401' type='text' />
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
</div>

<div class='css-form-default' style='margin-top: 20px;'>
	<?php if (!empty($output['list'])): ?>
		<table class='css-default-table'>
			<thead>
				<tr>
					<th class='w60'>ID</th>
					<th class='w100'>日期</th>
					<th class='w120'>级别要求</th>
					<th class='w100'>邀请人数要求</th>
					<th class='w80'>总人数</th>
					<th class='w80'>状态</th>
					<th class='w120'>创建时间</th>
					<th class='w120'>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($output['list'] as $item): ?>
				<tr>
					<td class='tc'><?=$item['id']?></td>
					<td class='tc'><?=$item['date_text']?></td>
					<td class='tc'><?=htmlspecialchars($item['level_name'])?></td>
					<td class='tc'><?=$item['yqgmtytcrs']?>人</td>
					<td class='tc'><?=$item['tjrs']?>人</td>
					<td class='tc'>
						<span class='label <?=$item['stat'] == 1 ? 'label-success' : 'label-default'?>'><?=$item['stat_text']?></span>
					</td>
					<td class='tc'><?=$item['create_time_text']?></td>
					<td class='tc'>
						<a href='javascript:void(0);' onclick='toggleStatus(<?=$item['id']?>, <?=$item['stat'] == 1 ? 0 : 1?>)' class='btn <?=$item['stat'] == 1 ? 'btn-warning' : 'btn-success'?>' style='padding: 2px 8px; font-size: 11px;'>
							<?=$item['stat'] == 1 ? '禁用' : '启用'?>
						</a>
						<a href='javascript:void(0);' onclick='showMemberList(<?=$item['id']?>)' class='btn btn-info' style='padding: 2px 8px; font-size: 11px;'>
							查看名单
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
		<!-- 分页 -->
		<?php if ($output['page']): ?>
		<div class='pagination' style='margin-top: 20px; text-align: center;'>
			<?=$output['page']?>
		</div>
		<?php endif; ?>
		
	<?php else: ?>
		<div class='alert alert-info' style='margin-top: 15px;'>
			<i class='icon-info-sign'></i> 暂无历史分红名单
		</div>
	<?php endif; ?>
</div>

<!-- 名单详情弹窗 -->
<div id='memberListModal' style='display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;'>
	<div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 5px; padding: 20px; max-width: 80%; max-height: 80%; overflow: auto;'>
		<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;'>
			<h3>分红名单详情</h3>
			<button onclick='closeMemberListModal()' style='background: #f5f5f5; border: 1px solid #ddd; padding: 5px 10px; cursor: pointer;'>关闭</button>
		</div>
		<div id='memberListContent'>
			<!-- 名单内容将在这里显示 -->
		</div>
	</div>
</div>

<style>
.label {
	padding: 3px 8px;
	border-radius: 3px;
	font-size: 12px;
}
.label-success {
	background-color: #5cb85c;
	color: white;
}
.label-default {
	background-color: #f5f5f5;
	color: #666;
}
.btn {
	display: inline-block;
	text-decoration: none;
	border-radius: 3px;
	border: 1px solid #ddd;
	transition: all 0.3s;
	margin: 0 2px;
}
.btn-success {
	background-color: #5cb85c;
	color: white;
	border-color: #5cb85c;
}
.btn-warning {
	background-color: #f0ad4e;
	color: white;
	border-color: #f0ad4e;
}
.btn-info {
	background-color: #5bc0de;
	color: white;
	border-color: #5bc0de;
}
.btn-blue {
	background-color: #007cba;
	color: white;
	border-color: #007cba;
}
.btn:hover {
	opacity: 0.8;
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
</style>

<script>
// 切换状态
function toggleStatus(id, newStatus) {
	var action = newStatus == 1 ? '启用' : '禁用';
	var msg = '确定要' + action + '这个分红配置吗？';
	
	if (confirm(msg)) {
		$.ajax({
			url: '<?=_url('distribute_award/toggle_fenhong_status')?>',
			type: 'POST',
			data: {
				'id': id,
				'stat': newStatus
			},
			dataType: 'json',
			success: function(response) {
				if (response.state == 200) {
					showSucc(response.data.msg, function(){
						location.reload();
					});
				} else {
					showError(response.msg);
				}
			},
			error: function() {
				showError('操作失败，请重试');
			}
		});
	}
}

// 显示名单详情
function showMemberList(hisId) {
	$.ajax({
		url: '<?=_url('distribute_award/get_member_list')?>',
		type: 'POST',
		data: {
			'his_id': hisId
		},
		dataType: 'json',
		success: function(response) {
			if (response.state == 200) {
				var html = '<table class="css-default-table" style="width: 100%;">';
				html += '<thead><tr><th>用户ID</th><th>昵称</th><th>手机号</th><th>级别</th><th>邀请人数</th><th>状态</th><th>操作</th></tr></thead>';
				html += '<tbody>';
				
				if (response.data.member_list && response.data.member_list.length > 0) {
					response.data.member_list.forEach(function(member) {
						html += '<tr>';
						html += '<td class="tc">' + member.uid + '</td>';
						html += '<td>' + member.nickname + '</td>';
						html += '<td>' + member.mobile + '</td>';
						html += '<td class="tc">' + member.level_name + '</td>';
						html += '<td class="tc">' + member.tjrs + '</td>';
						html += '<td class="tc"><span class="label ' + (member.stat == 1 ? 'label-success' : 'label-default') + '">' + member.stat_text + '</span></td>';
						html += '<td class="tc"><a href="javascript:void(0);" onclick="toggleUserStatus(' + member.id + ', ' + (member.stat == 1 ? 0 : 1) + ')" class="btn ' + (member.stat == 1 ? 'btn-warning' : 'btn-success') + '" style="padding: 2px 8px; font-size: 11px;">' + (member.stat == 1 ? '禁用' : '启用') + '</a></td>';
						html += '</tr>';
					});
				} else {
					html += '<tr><td colspan="7" class="tc">暂无名单数据</td></tr>';
				}
				
				html += '</tbody></table>';
				$('#memberListContent').html(html);
				$('#memberListModal').show();
			} else {
				showError(response.msg);
			}
		},
		error: function() {
			showError('获取名单失败，请重试');
		}
	});
}

// 关闭名单弹窗
function closeMemberListModal() {
	$('#memberListModal').hide();
}

// 切换用户状态
function toggleUserStatus(id, newStatus) {
	var action = newStatus == 1 ? '启用' : '禁用';
	var msg = '确定要' + action + '这个用户吗？';
	
	if (confirm(msg)) {
		$.ajax({
			url: '<?=_url('distribute_award/toggle_user_status')?>',
			type: 'POST',
			data: {
				'id': id,
				'stat': newStatus
			},
			dataType: 'json',
			success: function(response) {
				if (response.state == 200) {
					showSucc(response.data.msg, function(){
						// 重新加载名单
						var hisId = $('#memberListContent').data('his-id');
						if (hisId) {
							showMemberList(hisId);
						}
					});
				} else {
					showError(response.msg);
				}
			},
			error: function() {
				showError('操作失败，请重试');
			}
		});
	}
}
</script>
