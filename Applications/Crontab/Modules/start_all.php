<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * start all tasks
 * 批量启动所有定时任务组,但对断点任务组和黑名单任务组无效
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
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
