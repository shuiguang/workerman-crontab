<?php
/**
 * 开始所有定时任务组
 */
function start_all()
{
    $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
    $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
    foreach(glob($cron_dir.'/*'.Crontab\Config::$cron_suffix) as $cur_file)
    {
        $file = basename($cur_file);
        $run_file = $run_dir.'/'.$file;
        if(!file_exists($run_file))
        {
            //断点任务不重置
            if(strpos($file, Crontab\Config::$auto_prefix) === 0)
            {
                
            }else{
                @copy($cur_file, $run_file);
                if(strpos($cur_file, Crontab\Config::$cron_suffix) !== false)
                {
                    log_mess('<font color="green">'.basename($cur_file).'定时任务已启动</font>', __FILE__, __LINE__);
                }
            }
        }else{
            log_mess('<font color="red">'.basename($cur_file).'定时任务已启动</font>', __FILE__, __LINE__);
        }
    }
    echo('success');
}