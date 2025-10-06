<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<form method='get' action='<?=users_url('distribute_award/lingshou_fenhong_send')?>'>
	<table class='search-form'>
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<th>时间</th>
				<td class='w300'>
					<input class='text w70' name='query_start_date' id='query_start_date' value='<?=input('query_start_date', '')?>' readonly='readonly' type='text' />
					<label class='add-on'><i class='icon-calendar'></i></label>
					&nbsp;–&nbsp;
					<input class='text w70' name='query_end_date' id='query_end_date' value='<?=input('query_end_date', '')?>' readonly='readonly' type='text' />
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
	<form id='form' method='post' target='_parent' action='<?=_url('distribute_award/lingshou_fenhong_send')?>'>
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
			<dt>分红总人数：</dt>
			<dd>
				<span class='highlight'><?=$output['total_fenshu']?></span>&nbsp;人
			</dd>
		</dl>
		<dl>
			<dt>分红条件：</dt>
			<dd>
				级别 ≥ <span class='highlight'><?=$output['fenhong_level']['level_name']?></span>，且邀请购买体验套餐人数 ≥ <span class='highlight'><?=$output['required_inviter_num']?></span>人
			</dd>
		</dl>
		<dl>
			<dt>发放范围：</dt>
			<dd>
				<input type='radio' name='range' value='0' checked>当前条件符合的人员</input>
				<input type='radio' name='range' value='1'>当前条件符合的人员+历史保存名单</input>
			</dd>
		</dl>
		<div class='bottom'>
			<label class='submit-border'> <a id='btn_send' class='submit' href='javascript:void(0);'>提交发放</a> </label>
			<label class='submit-border'> <a id='btn_save' class='submit' href='javascript:void(0);'>保存名单</a> </label>
		</div>
	
	</form>
</div>

<!-- 新增：详细人员列表 -->
<div class='css-form-default' style='margin-top: 20px;'>
	<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>
		<div class="switch-box" id="tabContainer">
			<a href='javascript:void(0);' onclick='switchTab("current")' class='btn <?=!input('tab') || input('tab') == 'current' ? 'btn-blue' : 'btn-gray'?>' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-users'></i> 符合分红条件的人员列表
			</a>
			<!-- 历史名单选项卡将通过JavaScript动态生成 -->
		</div>
		
		<div id='displayMode' style='display: flex; gap: 10px; align-items: center;'>
			<span style='font-size: 12px; color: #666;'>显示模式：</span>
			<a href='javascript:void(0);' onclick='switchDisplayMode(0)' class='btn <?=$output['show_detail'] == 0 ? 'btn-blue' : 'btn-gray'?>' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-bolt'></i> 快速模式
			</a>
			<a href='javascript:void(0);' onclick='switchDisplayMode(1)' class='btn <?=$output['show_detail'] == 1 ? 'btn-blue' : 'btn-gray'?>' style='padding: 5px 15px; font-size: 12px;'>
				<i class='icon-list'></i> 详细模式
			</a>
		</div>
	</div>
	
	<!-- 当前符合条件的人员列表选项卡 -->
	<div id='currentTab' class='tab-content' style='<?=!input('tab') || input('tab') == 'current' ? '' : 'display: none;'?>'>
		<?php if (!empty($output['detailed_gudong_list'])): ?>
			<table class='css-default-table' id='my_agent'>
				<thead>
					<tr>
						<th class='w60'>序号</th>
						<th class='w100'>用户ID</th>
						<th class='w150'>昵称</th>
						<th class='w120'>手机号</th>
						<th class='w100'>级别</th>
						<?php if ($output['show_detail']): ?>
						<th class='w120'>邀请体验人数</th>
						<?php endif; ?>
						<th class='w100'>状态</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($output['detailed_gudong_list'] as $index => $gudong): ?>
					<tr>
						<td class='tc'><?=$index + 1?></td>
						<td class='tc'><?=$gudong['uid']?></td>
						<td><?=htmlspecialchars($gudong['nickname'])?></td>
						<td><?=htmlspecialchars($gudong['mobile'])?></td>
						<td class='tc'><span class='label label-success'><?=htmlspecialchars($gudong['level_name'])?></span></td>
						<?php if ($output['show_detail']): ?>
						<td class='tc'>
							<span class='highlight'><?=$gudong['inviter_num']?></span> / <?=$gudong['required_num']?>
						</td>
						<?php endif; ?>
						<td class='tc'>
							<span class='label label-success'>符合条件</span>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class='alert alert-info' style='margin-top: 15px;'>
				<i class='icon-info-sign'></i> 当前时间段内没有符合分红条件的用户
			</div>
		<?php endif; ?>
	</div>
	
	<!-- 历史名单选项卡容器 -->
	<div id='historyTabsContainer'>
		<!-- 历史名单选项卡内容将通过JavaScript动态生成 -->
	</div>
</div>

<style>

.switch-box {
	
	max-width: 888px;
	display: flex;
	flex-direction: row;
	gap: 10px;
	overflow: hidden;
	overflow-x: scroll;
	white-space: nowrap;
	/* 超出宽度显示滚动条 */
	scrollbar-width: 3px;
	-ms-overflow-style: 3px;
	&::-webkit-scrollbar {
		display: none;
	}
	&:hover {
		overflow-x: auto;
	}

}
.switch-box h3 {
	width: auto;
	margin: 0;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	cursor: pointer;

}
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
.tab-content {
	margin-top: 15px;
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
	
	// 提交发放按钮 - 保持原有逻辑不变
	$('#btn_send').click(function(e) {
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
	
	// 保存名单按钮 - 新增功能，不影响原有分红逻辑
	$('#btn_save').click(function(e) {
		var msg = '确定要保存当前符合条件的人员名单吗？\n\n保存后可以在历史名单中查看和管理。';
        if (confirm(msg) == true) {
            if (btn_check) {
    			// 构建保存名单的请求数据
    			var saveData = {
    				'form_submit': 'ok',
    				'query_start_date': '<?=input('query_start_date', '')?>',
    				'query_end_date': '<?=input('query_end_date', '')?>'
    			};
    			
    			$.ajax({
    				url: '<?=_url('distribute_award/save_lingshou_fenhong_list')?>',
    				type: 'POST',
    				data: saveData,
    				dataType: 'json',
    				success: function(response) {
    					if (response.state == 200) {
    						showSucc(response.data.msg, function(){
    							// 保存成功后直接刷新当前页面
    							location.reload();
    						});
    					} else {
    						showError(response.msg);
    					}
    				},
    				error: function() {
    					showError('保存失败，请重试');
    				}
    			});
    		}
            return true;
        } else {
            return false;
        }
	});
	
	// 选项卡切换功能
	window.switchTab = function(tabName) {
		// 隐藏所有选项卡内容
		$('.tab-content').hide();
		
		// 重置所有选项卡按钮样式
		$('.switch-box .btn').removeClass('btn-blue').addClass('btn-gray');
		
		// 显示选中的选项卡
		if (tabName === 'current') {
			$('#currentTab').show();
			$('#displayMode').show();
			$('.switch-box .btn:first').removeClass('btn-gray').addClass('btn-blue');
		} else if (tabName.startsWith('history_')) {
			$('#displayMode').hide();
			$('#currentTab').hide();
			
			// 显示对应的历史选项卡内容
			$('#' + tabName).show();
			
			// 设置对应按钮为选中状态
			$('.switch-box .btn[onclick*="' + tabName + '"]').removeClass('btn-gray').addClass('btn-blue');
		}
		
		// 更新URL参数
		var url = new URL(window.location);
		url.searchParams.set('tab', tabName);
		window.history.replaceState({}, '', url);
	};
	
	// 显示模式切换功能
	window.switchDisplayMode = function(mode) {
		var url = new URL(window.location);
		url.searchParams.set('show_detail', mode);
		window.location.href = url.toString();
	};
	
	// 加载历史名单选项卡
	function loadHistoryTabs() {
		console.log('开始加载历史选项卡数据...');
		$.ajax({
			url: '<?=_url('distribute_award/get_history_tabs')?>',
			type: 'GET',
			dataType: 'json',
			success: function(response) {
				console.log('历史选项卡API响应:', response);
				if (response.state == 200) {
					generateHistoryTabs(response.data.tabs, response.data.data);
				} else {
					console.error('加载历史名单失败:', response.msg);
				}
			},
			error: function(xhr, status, error) {
				console.error('加载历史名单失败，请重试', xhr, status, error);
			}
		});
	}
	
	// 生成历史名单选项卡
	function generateHistoryTabs(tabs, data) {
		console.log('生成历史选项卡，tabs:', tabs, 'data:', data);
		
		var tabContainer = $('#tabContainer');
		var contentContainer = $('#historyTabsContainer');
		
		// 清空现有内容
		contentContainer.empty();
		
		// 检查是否有数据
		if (!tabs || tabs.length === 0) {
			console.log('没有历史选项卡数据');
			return;
		}
		
		// 生成选项卡按钮
		tabs.forEach(function(tab) {
			console.log('生成选项卡:', tab.id, tab.name);
			
			var tabButton = $('<a href="javascript:void(0);" onclick="switchTab(\'' + tab.id + '\')" class="btn btn-gray" style="padding: 5px 15px; font-size: 12px;">' +
				'<i class="icon-calendar"></i> ' + tab.name + ' (' + tab.count + ')' +
				'</a>');
			tabContainer.append(tabButton);
			
			// 生成选项卡内容
			var tabContent = $('<div id="' + tab.id + '" class="tab-content" style="display: none;"></div>');
			var contentHtml = generateTabContent(data[tab.id]);
			console.log('选项卡内容HTML长度:', contentHtml.length);
			tabContent.html(contentHtml);
			contentContainer.append(tabContent);
		});
	}
	
	// 生成选项卡内容HTML
	function generateTabContent(tabData) {
		console.log('生成选项卡内容，tabData:', tabData);
		
		if (!tabData) {
			console.log('tabData为空');
			return '<div class="alert alert-info" style="margin-top: 15px;"><i class="icon-info-sign"></i> 该日期暂无分红名单</div>';
		}
		
		if (!tabData.configs || !tabData.members) {
			console.log('tabData.configs或tabData.members为空', tabData.configs, tabData.members);
			return '<div class="alert alert-info" style="margin-top: 15px;"><i class="icon-info-sign"></i> 该日期暂无分红名单</div>';
		}
		
		if (tabData.configs.length === 0 && tabData.members.length === 0) {
			console.log('configs和members都为空');
			return '<div class="alert alert-info" style="margin-top: 15px;"><i class="icon-info-sign"></i> 该日期暂无分红名单</div>';
		}
		
		var html = '';
		
		// 第一个表格：分红配置信息
		if (tabData.configs && tabData.configs.length > 0) {
			html += '<div style="margin-bottom: 20px;">';
			html += '<h4 style="margin-bottom: 10px; color: #333;">分红配置信息</h4>';
			html += '<table class="css-default-table">';
			html += '<thead>';
			html += '<tr>';
			html += '<th class="w60">ID</th>';
			html += '<th class="w120">级别要求</th>';
			html += '<th class="w100">邀请人数要求</th>';
			html += '<th class="w80">总人数</th>';
			html += '<th class="w80">状态</th>';
			html += '<th class="w120">创建时间</th>';
			html += '<th class="w120">操作</th>';
			html += '</tr>';
			html += '</thead>';
			html += '<tbody>';
			
			tabData.configs.forEach(function(config) {
				html += '<tr>';
				html += '<td class="tc">' + config.id + '</td>';
				html += '<td class="tc">' + escapeHtml(config.level_name) + '</td>';
				html += '<td class="tc">' + config.yqgmtytcrs + '人</td>';
				html += '<td class="tc">' + config.tjrs + '人</td>';
				html += '<td class="tc">';
				html += '<span class="label ' + (config.stat == 1 ? 'label-success' : 'label-default') + '">' + config.stat_text + '</span>';
				html += '</td>';
				html += '<td class="tc">' + config.create_time_text + '</td>';
				html += '<td class="tc">';
				html += '<a href="javascript:void(0);" onclick="toggleStatus(' + config.id + ', ' + (config.stat == 1 ? 0 : 1) + ')" class="btn ' + (config.stat == 1 ? 'btn-warning' : 'btn-success') + '" style="padding: 2px 8px; font-size: 11px;">';
				html += (config.stat == 1 ? '禁用' : '启用');
				html += '</a>';
				html += '</td>';
				html += '</tr>';
			});
			
			html += '</tbody>';
			html += '</table>';
			html += '</div>';
		}
		
		// 第二个表格：详细名单
		if (tabData.members && tabData.members.length > 0) {
			html += '<div>';
			html += '<h4 style="margin-bottom: 10px; color: #333;">详细名单</h4>';
			html += '<table class="css-default-table">';
			html += '<thead>';
			html += '<tr>';
			html += '<th class="w60">序号</th>';
			html += '<th class="w100">用户ID</th>';
			html += '<th class="w150">昵称</th>';
			html += '<th class="w120">手机号</th>';
			html += '<th class="w100">级别</th>';
			html += '<th class="w100">邀请人数</th>';
			html += '<th class="w80">状态</th>';
			html += '<th class="w100">操作</th>';
			html += '</tr>';
			html += '</thead>';
			html += '<tbody>';
			
			tabData.members.forEach(function(member, index) {
				html += '<tr>';
				html += '<td class="tc">' + (index + 1) + '</td>';
				html += '<td class="tc">' + member.uid + '</td>';
				html += '<td>' + escapeHtml(member.nickname) + '</td>';
				html += '<td>' + escapeHtml(member.mobile) + '</td>';
				html += '<td class="tc"><span class="label label-success">' + escapeHtml(member.level_name) + '</span></td>';
				html += '<td class="tc">' + member.tjrs + '人</td>';
				html += '<td class="tc">';
				html += '<span class="label ' + (member.stat == 1 ? 'label-success' : 'label-default') + '">' + member.stat_text + '</span>';
				html += '</td>';
				html += '<td class="tc">';
				html += '<a href="javascript:void(0);" onclick="toggleUserStatus(' + member.detail_id + ', ' + (member.stat == 1 ? 0 : 1) + ')" class="btn ' + (member.stat == 1 ? 'btn-warning' : 'btn-success') + '" style="padding: 2px 8px; font-size: 11px;">';
				html += (member.stat == 1 ? '禁用' : '启用');
				html += '</a>';
				html += '</td>';
				html += '</tr>';
			});
			
			html += '</tbody>';
			html += '</table>';
			html += '</div>';
		} else {
			html += '<div class="alert alert-info" style="margin-top: 15px;">';
			html += '<i class="icon-info-sign"></i> 暂无详细名单数据';
			html += '</div>';
		}
		
		return html;
	}
	
	// HTML转义函数
	function escapeHtml(text) {
		var map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}
	
	// 切换分红配置状态
	window.toggleStatus = function(id, newStatus) {
		if (confirm('确定要' + (newStatus == 1 ? '启用' : '禁用') + '该分红配置吗？')) {
			$.ajax({
				url: '<?=_url('distribute_award/toggle_fenhong_status')?>',
				type: 'POST',
				data: {
					id: id,
					stat: newStatus
				},
				dataType: 'json',
				success: function(response) {
					if (response.state == 200) {
						// 只更新当前行的状态，不重新加载整个选项卡
						updateConfigStatus(id, newStatus);
					} else {
						alert('操作失败：' + response.msg);
					}
				},
				error: function() {
					alert('操作失败，请重试');
				}
			});
		}
	};
	
	// 更新配置状态显示
	function updateConfigStatus(id, newStatus) {
		// 找到对应的行
		var row = $('a[onclick*="toggleStatus(' + id + '"]').closest('tr');
		
		// 更新状态标签
		var statusCell = row.find('td').eq(4); // 状态列
		var statusText = newStatus == 1 ? '生效' : '不生效';
		var statusClass = newStatus == 1 ? 'label-success' : 'label-default';
		statusCell.html('<span class="label ' + statusClass + '">' + statusText + '</span>');
		
		// 更新操作按钮
		var actionCell = row.find('td').eq(6); // 操作列
		var buttonText = newStatus == 1 ? '禁用' : '启用';
		var buttonClass = newStatus == 1 ? 'btn-warning' : 'btn-success';
		var newStatusValue = newStatus == 1 ? 0 : 1;
		actionCell.html('<a href="javascript:void(0);" onclick="toggleStatus(' + id + ', ' + newStatusValue + ')" class="btn ' + buttonClass + '" style="padding: 2px 8px; font-size: 11px;">' + buttonText + '</a>');
	}
	
	// 切换用户状态
	window.toggleUserStatus = function(detailId, newStatus) {
		if (confirm('确定要' + (newStatus == 1 ? '启用' : '禁用') + '该用户吗？')) {
			$.ajax({
				url: '<?=_url('distribute_award/toggle_user_status')?>',
				type: 'POST',
				data: {
					id: detailId,
					stat: newStatus
				},
				dataType: 'json',
				success: function(response) {
					if (response.state == 200) {
						// 只更新当前行的状态，不重新加载整个选项卡
						updateUserStatus(detailId, newStatus);
					} else {
						alert('操作失败：' + response.msg);
					}
				},
				error: function() {
					alert('操作失败，请重试');
				}
			});
		}
	};
	
	// 更新用户状态显示
	function updateUserStatus(detailId, newStatus) {
		// 找到对应的行
		var row = $('a[onclick*="toggleUserStatus(' + detailId + '"]').closest('tr');
		
		// 更新状态标签
		var statusCell = row.find('td').eq(6); // 状态列
		var statusText = newStatus == 1 ? '启用' : '不启用';
		var statusClass = newStatus == 1 ? 'label-success' : 'label-default';
		statusCell.html('<span class="label ' + statusClass + '">' + statusText + '</span>');
		
		// 更新操作按钮
		var actionCell = row.find('td').eq(7); // 操作列
		var buttonText = newStatus == 1 ? '禁用' : '启用';
		var buttonClass = newStatus == 1 ? 'btn-warning' : 'btn-success';
		var newStatusValue = newStatus == 1 ? 0 : 1;
		actionCell.html('<a href="javascript:void(0);" onclick="toggleUserStatus(' + detailId + ', ' + newStatusValue + ')" class="btn ' + buttonClass + '" style="padding: 2px 8px; font-size: 11px;">' + buttonText + '</a>');
	}
	
	// 页面加载时自动加载历史选项卡
	$(document).ready(function() {
		loadHistoryTabs();
		
		// 如果URL中有历史选项卡参数，自动切换到对应选项卡
		var urlParams = new URLSearchParams(window.location.search);
		var currentTab = urlParams.get('tab');
		if (currentTab && currentTab.startsWith('history_')) {
			setTimeout(function() {
				switchTab(currentTab);
			}, 500); // 延迟执行，确保选项卡已生成
		}
	});
});
</script>