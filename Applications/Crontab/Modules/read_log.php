<?php
/**
 * 读取日志信息
 * @return  string		读取的日志内容
 */
function read_log()
{
	$log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
	$log_path = $log_dir.'/'.date('Ymd').'.log';
	if(file_exists($log_path))
	{
		$log = FileLastLines($log_path, 20);
		echo nl2br(trim($log));
	}else{
		echo '';
	}
}
