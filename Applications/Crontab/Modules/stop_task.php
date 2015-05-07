<?php
/**
 * 停止定时任务
 * @param   string     $file 任务名称
 * @return  null
 */
function stop_task($file = '')
{
	$file = base64_decode(substr($file, 0 ,100));
    if(!empty($file))
	{
		$cron_dir = WEB_ROOT.'/'.basename(Crontab\Config::$cron_dir);
		$run_dir = WEB_ROOT.'/'.basename(Crontab\Config::$run_dir);
		$run_file = $run_dir.'/'.$file;
		if(file_exists($run_file))
		{
			@unlink($run_file);
		}
		if(!file_exists($run_file))
		{
			log_mess('<font color="red">'.$file.'定时任务已停止</font>', __FILE__, __LINE__);
			echo('success');
		}else{
			echo('error');
		}
	}else{
		echo('error');
	}
}