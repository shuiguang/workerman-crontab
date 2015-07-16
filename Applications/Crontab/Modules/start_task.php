<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * start task
 * 启动指定任务组
 * @var string $file
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function start_task($file = '')
{
    $file = base64_decode(substr($file, 0 ,1000));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        if(file_exists($forbidden_dir.'/'.$file))
        {
            log_mess('<font color="red">'.$file.'在黑名单无法启动</font>', __FILE__, __LINE__);
            echo('error');
        }
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
        $run_file = $run_dir.'/'.$file;
        //重置断点任务:如果以Crontab\Config::$auto_prefix开头则清除自动执行的锁文件
        $lock_dir = WEB_ROOT.'/'.basename(Crontab\Config::$lock_dir);
        if(strpos($file, Crontab\Config::$auto_prefix) === 0)
        {
            foreach(glob($lock_dir.'/'.$file.'.*') as $cur_file)
            {
                @unlink($cur_file);
            }
        }
        @copy($cron_dir.'/'.$file, $run_file);
        if(file_exists($run_file))
        {
            log_mess('<font color="green">'.$file.'定时任务已启动</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('error');
        }
    }else{
        echo('error');
    }
}
