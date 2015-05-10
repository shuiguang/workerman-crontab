<?php
/**
 * 读取日志信息
 */
function read_log()
{
    $log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
    $log_path = $log_dir.'/'.date('Ymd').'.log';
    if(file_exists($log_path))
    {
        $log = FileLastLines($log_path, 19);
        echo nl2br(trim($log));
    }else{
        echo '';
    }
}
