<?php
/**
 * 从黑名单移除任务组
 */
function remove_blacklist($file = '')
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
        //如果以Crontab\Config::$auto_prefix开头则不删除，断点执行
        if(strpos($file, Crontab\Config::$auto_prefix) === 0)
        {
            
        }else{
            if(file_exists($run_file))
            {
                @unlink($run_file);
            }
        }
        if(!file_exists($copy_file))
        {
            log_mess('<font color="red">'.$file.'定时任务已从黑名单移出。</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('error');
        }
    }else{
        echo('error');
    }
}

