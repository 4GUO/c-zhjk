<?php defined('SAFE_CONST') or exit('Access Invalid!');?>

<div class='tabmenu'>
    <a href='javascript:void(0)' class='css-btn css-btn-green' id='refresh_data'><i class='icon-refresh'></i>刷新数据</a>
    <a href='javascript:void(0)' class='css-btn css-btn-purple' id='execute_synthesis' disabled><i class='icon-gift'></i>确认发放分红券</a>
</div>

<div class='css-default-table'>
    <div class='css-default-table-header'>
        <h3>手动发放分红券</h3>
        <p>检测系统中可合成的分红券，并提供手动发放功能</p>
    </div>
    
    <div class='css-default-table-content'>
        <div id='loading' class='loading-section'>
            <p><i class='icon-spinner icon-spin'></i> 正在加载数据...</p>
        </div>
        
        <div id='content' style='display:none;'>
            <div class='summary-section'>
                <div class='summary-item'>
                    <h4><i class='icon-star'></i> 联创级别</h4>
                    <p id='lianchuang_level'>-</p>
                </div>
                <div class='summary-item'>
                    <h4><i class='icon-gift'></i> 待合成数量</h4>
                    <p id='total_synthesis_count'>0</p>
                </div>
                <div class='summary-item'>
                    <h4><i class='icon-check'></i> 可发放状态</h4>
                    <p id='can_synthesis_status'>-</p>
                </div>
            </div>
            
            <div class='synthesis-list-section'>
                <h4><i class='icon-list'></i> 可合成详情</h4>
                <div id='synthesis_list'>
                    <!-- 动态加载合成列表 -->
                </div>
            </div>
        </div>
        
        <div id='no_data' style='display:none;' class='no-data-section'>
            <div class='no-data-content'>
                <i class='icon-info-sign'></i>
                <p>暂无可合成的分红券</p>
                <p class='hint'>当前没有满足合成条件的联创用户</p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 页面加载时获取数据
    loadSynthesisData();
    
    // 刷新数据按钮
    $('#refresh_data').click(function() {
        loadSynthesisData();
    });
    
    // 确认发放按钮
    $('#execute_synthesis').click(function() {
        if ($(this).prop('disabled')) {
            return;
        }
        
        if (!confirm('确定要发放分红券吗？此操作将消耗用户的激活提货券！')) {
            return;
        }
        
        executeSynthesis();
    });
    
    function loadSynthesisData() {
        var $loading = $('#loading');
        var $content = $('#content');
        var $noData = $('#no_data');
        var $executeBtn = $('#execute_synthesis');
        
        $loading.show();
        $content.hide();
        $noData.hide();
        $executeBtn.prop('disabled', true);
        
        $.ajax({
            url: '<?=users_url("distribute_award/manual_fenhongquan")?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $loading.hide();
                
                if (response.state == 200) {
                    var data = response.data;
                    
                    // 更新摘要信息
                    $('#lianchuang_level').text(data.lianchuang_level);
                    $('#total_synthesis_count').text(data.total_synthesis_count);
                    $('#can_synthesis_status').text(data.can_synthesis ? '可发放' : '不可发放');
                    
                    if (data.can_synthesis) {
                        $executeBtn.prop('disabled', false);
                        $content.show();
                        renderSynthesisList(data.synthesis_list);
                    } else {
                        $noData.show();
                    }
                } else {
                    alert('获取数据失败：' + response.msg);
                    $noData.show();
                }
            },
            error: function() {
                $loading.hide();
                alert('网络错误，请重试');
                $noData.show();
            }
        });
    }
    
    function renderSynthesisList(synthesisList) {
        var $container = $('#synthesis_list');
        var html = '';
        
        if (synthesisList.length === 0) {
            html = '<div class="no-synthesis">暂无可合成的分红券</div>';
        } else {
            synthesisList.forEach(function(item, index) {
                html += '<div class="synthesis-item">';
                html += '<div class="synthesis-header">';
                html += '<h5><i class="icon-user"></i> 联创用户：' + item.lianchuang_nickname + ' (' + item.lianchuang_mobile + ')</h5>';
                html += '<span class="synthesis-count">消耗 ' + item.consumed_tihuoquan_count + ' 张激活提货券</span>';
                html += '</div>';
                
                html += '<div class="consumed-users">';
                html += '<h6><i class="icon-users"></i> 消耗用户列表：</h6>';
                html += '<ul>';
                item.consumed_users.forEach(function(user) {
                    html += '<li>';
                    html += '<span class="user-name">' + (user.nickname || user.mobile) + '</span>';
                    html += '<span class="user-mobile">(' + user.mobile + ')</span>';
                    html += '<span class="user-tihuoquan">激活提货券：' + user.can_tihuoquan_num + ' 张</span>';
                    html += '</li>';
                });
                html += '</ul>';
                html += '</div>';
                html += '</div>';
            });
        }
        
        $container.html(html);
    }
    
    function executeSynthesis() {
        var $btn = $('#execute_synthesis');
        var $loading = $('#loading');
        
        $btn.prop('disabled', true).html('<i class="icon-spinner icon-spin"></i> 发放中...');
        $loading.show();
        
        $.ajax({
            url: '<?=users_url("distribute_award/execute_manual_fenhongquan")?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $loading.hide();
                
                if (response.state == 200) {
                    var data = response.data;
                    var message = '发放完成！\n';
                    message += '成功发放：' + data.synthesis_count + ' 张分红券\n';
                    message += '执行时间：' + data.execution_time + '\n';
                    
                    if (data.log_data && data.log_data.synthesis_logs) {
                        message += '\n发放详情：\n';
                        data.log_data.synthesis_logs.forEach(function(log) {
                            message += '联创用户ID: ' + log.lianchuang_uid + 
                                     ', 消耗用户: ' + log.consumed_users.join(',') + 
                                     ', 时间: ' + log.synthesis_time + '\n';
                        });
                    }
                    
                    alert(message);
                    
                    // 恢复按钮状态
                    $btn.prop('disabled', false).html('<i class="icon-gift"></i> 确认发放分红券');
                    
                    // 重新加载数据
                    loadSynthesisData();
                } else {
                    alert('发放失败：' + response.msg);
                    $btn.prop('disabled', false).html('<i class="icon-gift"></i> 确认发放分红券');
                }
            },
            error: function() {
                $loading.hide();
                alert('网络错误，请重试');
                $btn.prop('disabled', false).html('<i class="icon-gift"></i> 确认发放分红券');
            }
        });
    }
});
</script>

<style>
.css-default-table-header {
    padding: 15px;
    background-color: #f5f5f5;
    border-bottom: 1px solid #ddd;
}

.css-default-table-header h3 {
    margin: 0 0 5px 0;
    color: #333;
}

.css-default-table-header p {
    margin: 0;
    color: #666;
}

.css-default-table-content {
    padding: 15px;
}

.loading-section {
    text-align: center;
    padding: 40px;
    color: #666;
}

.loading-section i {
    font-size: 24px;
    margin-right: 10px;
}

.summary-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.summary-item {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
}

.summary-item h4 {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 14px;
}

.summary-item p {
    margin: 0;
    color: #007bff;
    font-size: 18px;
    font-weight: bold;
}

.synthesis-list-section {
    margin-top: 20px;
}

.synthesis-list-section h4 {
    margin: 0 0 15px 0;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
}

.synthesis-item {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    overflow: hidden;
}

.synthesis-header {
    background-color: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.synthesis-header h5 {
    margin: 0;
    color: #333;
}

.synthesis-count {
    background-color: #007bff;
    color: white;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.consumed-users {
    padding: 15px;
}

.consumed-users h6 {
    margin: 0 0 10px 0;
    color: #495057;
}

.consumed-users ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.consumed-users li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f1f1f1;
}

.consumed-users li:last-child {
    border-bottom: none;
}

.user-name {
    font-weight: bold;
    color: #333;
}

.user-mobile {
    color: #666;
    font-size: 12px;
}

.user-tihuoquan {
    background-color: #28a745;
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
}

.no-data-section {
    text-align: center;
    padding: 60px 20px;
}

.no-data-content i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 15px;
}

.no-data-content p {
    margin: 5px 0;
    color: #666;
}

.no-data-content .hint {
    font-size: 12px;
    color: #999;
}

.no-synthesis {
    text-align: center;
    padding: 40px;
    color: #666;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.css-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
