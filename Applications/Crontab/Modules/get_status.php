<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * get all tasks info
 * 读取任务组状态以json信息返回给前端
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function get_status()
{
    //获取定时任务组总数和正在运行的任务组数量
    $list_file = array();
    $count_file = 0;
    $error_file = 0;
    $cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
    $run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
    $forbidden_dir = WEB_ROOT.'/'.basename(Crontab\Config::$forbidden_dir);
    foreach(glob($cron_dir.'/*'.Crontab\Config::$cron_suffix) as $cur_file)
    {
        $file = basename($cur_file);
        if(strpos($file, Crontab\Config::$auto_prefix) === 0)
        {
            $prefix = Crontab\Config::$auto_prefix;
        }else{
            $prefix = '';
        }
        $run_file = $run_dir.'/'.$file;
        if(file_exists($forbidden_dir.'/'.$file))
        {
            $black = true;
        }else{
            $black = false;
        }
        //获取每组任务第一行作为描述信息
        $description = '';
        $commands = file($cur_file);
        foreach($commands as $command)
        {
            if($command[0] == '#')
            {
                $description = str_replace('#', '', $command);
                break;
            }
        }
        if(file_exists($run_file) && !$black)
        {
            $list_file[] = array('file'=>$file, 'description'=>$description, 'running'=>true, 'black'=>$black, 'prefix'=>$prefix);
        }else{
            $list_file[] = array('file'=>$file, 'description'=>$description, 'running'=>false, 'black'=>$black, 'prefix'=>$prefix);
            $error_file++;
        }
        $count_file++;
    }
    $info = json_encode(array('list_file'=>$list_file, 'count_file'=>$count_file, 'error_file'=>$error_file));
    echo $info;
}
