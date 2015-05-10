<?php
/**
 * 读取任务组状态
 */
function get_status()
{
    //获取定时任务总数和正常运行数
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
        //获取描述信息
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
