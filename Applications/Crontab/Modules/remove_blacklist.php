<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * remove task team from blacklist
 * 将定时任务组从禁止目录中移除
 * @var string $file
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function remove_blacklist($file = '')
{
    $file = base64_decode(substr($file, 0 ,1000));
    if(!empty($file))
    {
        $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
        $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
        $forbidden_file = $forbidden_dir.'/'.$file;
        $cur_file = $cron_dir.'/'.$file;
        if(file_exists($forbidden_file))
        {
            @unlink($forbidden_file);
        }
        if(!file_exists($forbidden_file))
        {
            log_mess('<font color="red">'.$file.'定时任务已从黑名单移除</font>', __FILE__, __LINE__);
            echo('success');
        }else{
            echo('error');
        }
    }else{
        echo('error');
    }
}

