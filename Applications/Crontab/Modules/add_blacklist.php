<?php
/**
 * 拉黑任务组
 */
function add_blacklist($file = '')
{
    $file = base64_decode(substr($file, 0 ,100));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $copy_file = $forbidden_dir.'/'.$file;
        $cur_file = $cron_dir.'/'.$file;
        if(file_exists($cur_file))
        {
            @copy($cur_file, $copy_file);
        }
        if(file_exists($copy_file))
        {
            log_mess('<font color="red">'.$file.'定时任务组已加入黑名单。</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('fail');
        }
    }else{
        echo('error');
    }
}
