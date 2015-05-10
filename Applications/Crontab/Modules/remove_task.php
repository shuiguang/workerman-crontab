<?php
/**
 * 删除任务组，需要web用户对cron_dir进行读写权限
 */
function remove_task($file = '')
{
    $file = base64_decode(substr($file, 0 ,100));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
        $copy_file = $forbidden_dir.'/'.$file;
        $cur_file = $cron_dir.'/'.$file;
        $run_file = $run_dir.'/'.$file;
        if(file_exists($copy_file))
        {
            @unlink($copy_file);
        }
        if(file_exists($cur_file))
        {
            @unlink($cur_file);
        }
        if(file_exists($run_file))
        {
            @unlink($run_file);
        }
        if(!file_exists($copy_file))
        {
            log_mess('<font color="red">'.$file.'定时任务已经删除。</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('error');
        }
    }else{
        echo('error');
    }
    
}
