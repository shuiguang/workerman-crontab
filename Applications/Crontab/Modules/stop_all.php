<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * remove task team
 * 停止所有任务组
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function stop_all()
{
    $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
    $pid_dir = WEB_ROOT.'/'.basename(Crontab\Config::$pid_dir);
    $lock_dir = WEB_ROOT.'/'.basename(Crontab\Config::$lock_dir);
    foreach(glob($run_dir.'/*') as $cur_file)
    {
        //断点任务不重置
        $file = basename($cur_file);
        if(strpos($file, Crontab\Config::$auto_prefix) === 0)
        {
            
        }else{
            @unlink($cur_file);
            if(!file_exists($cur_file))
            {
                if(strpos($cur_file, Crontab\Config::$cron_suffix) !== false)
                {
                    log_mess('<font color="green">'.basename($cur_file).'定时任务已停止</font>', __FILE__, __LINE__);
                }
            }else{
                echo('error');
            }
            //移除pid_dir下文件
            foreach(glob($pid_dir.'/'.$file.'*'.Crontab\Config::$pid_suffix) as $cur_file)
            {
                @unlink($cur_file);
            }
            //移除lock_dir下文件
            foreach(glob($lock_dir.'/'.$file.'*'.Crontab\Config::$lock_suffix) as $cur_file)
            {
                @unlink($cur_file);
            }
        }
    }
    echo('success');
}
