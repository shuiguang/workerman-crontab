<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * read today's lastest log 
 * 读取今天最近的日志
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function read_log()
{
    $log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
    $log_path = $log_dir.'/'.date('Ymd').'.log';
    if(file_exists($log_path))
    {
        $log = FileLastLines($log_path, 19);
        echo nl2br(trim($log));
    }else{
        echo '';
    }
}
