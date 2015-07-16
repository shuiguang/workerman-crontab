<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * remove task team
 * 删除指定任务组,将会删除所有相关文件,需要web执行者对cron_dir进行写权限
 * @var string $file
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function remove_task($file = '')
{
    $file = base64_decode(substr($file, 0 ,1000));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
        $pid_dir = WEB_ROOT.'/'.basename(Crontab\Config::$pid_dir);
        $forbidden_file = $forbidden_dir.'/'.$file;
        $cron_file = $cron_dir.'/'.$file;
        $run_file = $run_dir.'/'.$file;
        //移除forbidden_dir下文件
        if(file_exists($forbidden_file))
        {
            @unlink($forbidden_file);
        }
        //移除rundir下文件
        if(file_exists($run_file))
        {
            @unlink($run_file);
        }
        //移除pid_dir下文件
        foreach(glob($pid_dir.'/'.$file.'*'.Crontab\Config::$pid_suffix) as $cur_file)
        {
            @unlink($cur_file);
        }
        //移除cron_dir下文件
        if(file_exists($cron_file))
        {
            @unlink($cron_file);
        }
        if(!file_exists($cron_file))
        {
            log_mess('<font color="red">'.$file.'定时任务已经删除</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            log_mess('<font color="red">'.$file.'定时任务无删除权限,请手动删除</font>', __FILE__, __LINE__);
            echo('error');
        }
    }else{
        echo('error');
    }
    
}
