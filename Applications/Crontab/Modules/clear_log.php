<?php
/**
 * 清除运行日志
 */
function clear_log()
{
    $log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
    $log_path = $log_dir.'/'.date('Ymd').'.log';
    if(file_exists($log_path))
    {
        @unlink($log_path);
    }
    if(file_exists($log_path))
    {
        echo 'fail';
    }else{
        echo 'success';
    }
}

