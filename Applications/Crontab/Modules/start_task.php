<?php
/**
 * 开始定时任务
 * @param   string     $file 任务名称
 * @return  null
 */
function start_task($file = '')
{
	$file = base64_decode(substr($file, 0 ,100));
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