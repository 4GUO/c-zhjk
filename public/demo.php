<?php
#step1：线下安全版本生成文件字典：文件路径 => md5(文件内容)
//$dir = dirname(str_replace('\\', '/', __DIR__)) . '/bf';//备份系统
//$local_md5_files = scanfiles($dir);
//file_put_contents('local_md5_files.php', '<?php return ' . var_export($local_md5_files, true) . ';');//字典结果写入本地文件
#step2：线上系统生成文件字典
$dir = dirname(str_replace('\\', '/', __DIR__));
//$online_md5_files = scanfiles($dir, array('php', 'css', 'js', 'jpg', 'gif', 'png', 'html'));
//file_put_contents('online_md5_files.php', '<?php return ' . var_export($online_md5_files, true) . ';');//字典结果写入本地文件
#step3：线上和线下字典比对
$online_md5_files = include_once('online_md5_files.php');
$local_md5_files = include_once('local_md5_files.php');
$local_md5_files = array_flip($local_md5_files);
foreach ($online_md5_files as $file => $md5) {
    if (isset($local_md5_files[$md5])) {
        unset($online_md5_files[$file]);
    }
}
#step4：输出校验结果
echo '<h1>异常文件：</h1></br>';
foreach ($online_md5_files as $file => $md5) {
    echo $dir . $file . '</br>';
}
/**
 * PHP 非递归实现查询该目录下所有文件
 * @param unknown $dir
 * @param array $exts 读取包含指定后缀的文件
 * @return multitype:|multitype:string
 */
function scanfiles($dir, $exts = array('php')) {
    $root_path = $dir;
    if (!is_dir($dir)) return array();
    // 兼容各操作系统
    $dir = rtrim(str_replace('\\', '/', $dir) , '/') . '/';
    // 栈，默认值为传入的目录
    $dirs = array(
        $dir
    );
    // 放置所有文件的容器
    $result = array();
    do {
        // 弹栈
        $dir = array_pop($dirs);
        // 扫描该目录
        $tmp = scandir($dir);
        foreach ($tmp as $f) {
            // 过滤. ..
            if ($f == '.' || $f == '..') continue;
            // 组合当前绝对路径
            $path = $dir . $f;
            // 如果是目录，压栈。
            if (is_dir($path)) {
                array_push($dirs, $path . '/');
            } else if (is_file($path)) { // 如果是文件，放入容器中
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                if (in_array($ext, $exts)) {
                    $relative_location = substr($path, strlen($root_path));
                    $result[$relative_location] = md5_file($path);
                }
            }
        }
    }
    while ($dirs); // 直到栈中没有目录
    return $result;
}