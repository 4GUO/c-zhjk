<?php defined('SAFE_CONST') or exit('Access Invalid!');?>
<style type='text/css'>
.log-container {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.log-stats {
    background: #fff;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.log-stats .stat-item {
    display: inline-block;
    margin-right: 30px;
    font-size: 14px;
}
.log-stats .stat-item .number {
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
}
.log-content {
    background: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: 600px;
    overflow-y: auto;
}
.log-line {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
}
.log-line:last-child {
    border-bottom: none;
}
.log-line.success {
    color: #28a745;
    background-color: #f8fff9;
}
.log-line.fail {
    color: #dc3545;
    background-color: #fff8f8;
}
.log-line.info {
    color: #17a2b8;
    background-color: #f8fcff;
}
.log-timestamp {
    color: #666;
    font-weight: bold;
}
.log-user-id {
    color: #007bff;
    font-weight: bold;
}
.log-order-id {
    color: #6c757d;
}
.log-amount {
    color: #28a745;
    font-weight: bold;
}
.log-message {
    color: #333;
}
</style>

<div class="log-container">
    <div class="log-stats">
        <div class="stat-item">
            <span class="label">总记录数：</span>
            <span class="number"><?php echo isset($output['total_records']) ? $output['total_records'] : 0; ?></span>
        </div>
        <div class="stat-item">
            <span class="label">成功发放：</span>
            <span class="number success"><?php echo isset($output['success_count']) ? $output['success_count'] : 0; ?></span>
        </div>
        <div class="stat-item">
            <span class="label">发放失败：</span>
            <span class="number fail"><?php echo isset($output['fail_count']) ? $output['fail_count'] : 0; ?></span>
        </div>
        <div class="stat-item">
            <span class="label">成功率：</span>
            <span class="number"><?php 
                $total = isset($output['total_records']) ? $output['total_records'] : 0;
                $success = isset($output['success_count']) ? $output['success_count'] : 0;
                echo $total > 0 ? round(($success / $total) * 100, 2) : 0; 
            ?>%</span>
        </div>
    </div>
    
    <div class="log-content">
        <h3>复购见单奖励系统日志</h3>
        <p style="color: #666; margin-bottom: 20px;">显示最新的 <?php echo isset($output['log_lines']) ? count($output['log_lines']) : 0; ?> 条记录</p>
        
        <?php if(isset($output['log_lines']) && !empty($output['log_lines'])): ?>
            <?php foreach($output['log_lines'] as $line): ?>
                <?php
                $line_class = '';
                if(strpos($line, '成功发放复购见单奖励') !== false) {
                    $line_class = 'success';
                } elseif(strpos($line, '不发放奖励') !== false) {
                    $line_class = 'fail';
                } else {
                    $line_class = 'info';
                }
                
                // 解析日志内容
                $timestamp = '';
                $user_id = '';
                $order_id = '';
                $message = '';
                
                if(preg_match('/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $line, $matches)) {
                    $timestamp = $matches[1];
                }
                
                if(preg_match('/用户ID【(\d+)】/', $line, $matches)) {
                    $user_id = $matches[1];
                }
                
                if(preg_match('/订单号【([^】]+)】/', $line, $matches)) {
                    $order_id = $matches[1];
                }
                
                if(preg_match('/关键内容【(.+)】$/', $line, $matches)) {
                    $message = $matches[1];
                }
                ?>
                
                <div class="log-line <?php echo $line_class; ?>">
                    <div>
                        <span class="log-timestamp"><?php echo $timestamp; ?></span>
                        <?php if($user_id): ?>
                            <span class="log-user-id">用户ID: <?php echo $user_id; ?></span>
                        <?php endif; ?>
                        <?php if($order_id): ?>
                            <span class="log-order-id">订单号: <?php echo $order_id; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="log-message" style="margin-top: 5px;">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; color: #666; padding: 40px;">
                暂无日志记录
            </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // 自动滚动到顶部
    $('.log-content').scrollTop(0);
    
    // 添加刷新功能
    $('.log-stats').append('<div class="stat-item" style="float: right;"><button onclick="location.reload()" class="btn btn-primary btn-sm">刷新日志</button></div>');
});
</script> 