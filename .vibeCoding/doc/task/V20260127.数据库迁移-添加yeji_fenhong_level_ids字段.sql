-- 数据库迁移脚本
-- 为 fxy_config 表添加 yeji_fenhong_level_ids 字段
-- 用于存储允许绩效分红的级别ID列表（逗号分隔）

-- 检查字段是否存在，如果不存在则添加
-- 注意：根据实际数据库前缀调整表名（fxy_config 或 ims_config 等）

-- 方法1：直接执行（如果字段已存在会报错，可以忽略）
ALTER TABLE `ims_fxy_config`
ADD COLUMN `yeji_fenhong_level_ids` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '绩效分红级别ID列表（逗号分隔）'
AFTER `yeji_fenhong_bili`;

-- 方法2：先检查再添加（MySQL 5.7+ 支持）
-- 如果您的MySQL版本支持，可以使用以下方式检查字段是否存在
-- 但更简单的方式是直接执行上面的ALTER TABLE语句，如果字段已存在会报错，可以忽略

-- 验证字段是否添加成功
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_NAME = 'fxy_config' AND COLUMN_NAME = 'yeji_fenhong_level_ids';
