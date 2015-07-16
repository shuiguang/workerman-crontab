<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * clean today's log
 * 清除今天记录的日志
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function clear_log()
{
    $log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
    $log_path = $log_dir.'/'.date('Ymd').'.log';
    if(file_exists($log_path))
    {
        @unlink($log_path);
    }
    if(file_exists($log_path))
    {
        echo 'fail';
    }else{
        echo 'success';
    }
}
