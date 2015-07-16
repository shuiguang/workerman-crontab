<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * remove task team
 * 停止指定任务组
 * @var string $file
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function stop_task($file = '')
{
    $file = base64_decode(substr($file, 0 ,1000));
    if(!empty($file))
    {
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
        $pid_dir = WEB_ROOT.'/'.basename(Crontab\Config::$pid_dir);
        $lock_dir = WEB_ROOT.'/'.basename(Crontab\Config::$lock_dir);
        $run_file = $run_dir.'/'.$file;
        //移除run_dir下文件
        if(file_exists($run_file))
        {
            @unlink($run_file);
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
        if(!file_exists($run_file))
        {
            log_mess('<font color="red">'.$file.'定时任务已停止</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('error');
        }
    }else{
        echo('error');
    }
}
