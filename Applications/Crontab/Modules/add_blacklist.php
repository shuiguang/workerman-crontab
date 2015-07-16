<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * add task team to blacklist
 * 将定时任务组加入禁止目录,$file为定时任务组的文件名通过base64加密传输
 * @var string $file
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function add_blacklist($file = '')
{
    $file = base64_decode(substr($file, 0 ,1000));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
        $pid_dir = WEB_ROOT.'/'.basename(Crontab\Config::$pid_dir);
        $lock_dir = WEB_ROOT.'/'.basename(Crontab\Config::$lock_dir);
        $forbidden_file = $forbidden_dir.'/'.$file;
        $cron_file = $cron_dir.'/'.$file;
        $run_file = $run_dir.'/'.$file;
        if(file_exists($cron_file))
        {
            @copy($cron_file, $forbidden_file);
        }
        //移除普通任务但不移除断点执行的任务
        if(strpos($file, Crontab\Config::$auto_prefix) === 0)
        {
            
        }else{
            if(file_exists($run_file))
            {
                @unlink($run_file);
            }
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
        if(file_exists($forbidden_file))
        {
            log_mess('<font color="red">'.$file.'定时任务组已加入黑名单</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('fail');
        }
    }else{
        echo('error');
    }
}
