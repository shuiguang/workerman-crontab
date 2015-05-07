<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Workerman\Protocols\Http;
/**
 * 检查是否登录
 */
function check_auth()
{
    // 如果配置中管理员用户名密码为空则说明不用验证
    if(Crontab\Config::$adminName == '' && Crontab\Config::$adminPassword == '')
    {
        return true;
    }
    // 进入验证流程
    _session_start();
    if(!isset($_SESSION['admin']))
    {
        if(!isset($_POST['admin_name']) || !isset($_POST['admin_password']))
        {
            include WEB_ROOT . '/Views/login.tpl.php';
            _exit();
        }
        else 
        {
            $admin_name = $_POST['admin_name'];
            $admin_password = $_POST['admin_password'];
            if($admin_name != Crontab\Config::$adminName || $admin_password != Crontab\Config::$adminPassword)
            {
                $msg = "用户名或者密码不正确";
                include WEB_ROOT . '/Views/login.tpl.php';
                _exit();
            }
            $_SESSION['admin'] = $admin_name;
        }
    }
    return true;
}

/**
 * 启动session，兼容fpm
 */
function _session_start()
{
    if(defined('WORKERMAN_ROOT_DIR'))
    {
        return Http::sessionStart();
    }
    return session_start();
}
/**
 * 退出
 * @param string $str
 */
function _exit($str = '')
{
    if(defined('WORKERMAN_ROOT_DIR'))
    {
        return Http::end($str);
    }
    return exit($str);
}
/**
 * 跳转
 * @param string $str
 */
function _header($content, $replace = true, $http_response_code = 0)
{
    if(!defined('WORKERMAN_ROOT_DIR'))
    {
        return header($content, $replace, $http_response_code);
    }
    return Http::header($content, $replace, $http_response_code);
}

/**
 * 获取PID的文件路径，用于kill掉某个子任务进程
 * @param   string     $value 定时任务内容
 * @return  null
 */
function get_pid_file($value)
{
	$pid_file = WEB_ROOT.'/'.basename(Crontab\Config::$pid_dir).'/'.md5($value).'.pid';
	return $pid_file;
}

/**
 * 工具函数，读取文件最后$n行
 * @param   string      $filename 文件的路径
 * @param   int      	$n 文件的行数
 * @return  null
 */
function FileLastLines($filename, $n = 1)
{
	if(!is_file($filename) || !$fp = fopen($filename,'r'))
	{
		return false;
	}
	$pos = -2;
	$eof = '';
	$lines = array();
    while($n>0)
	{
		$str = '';
		while($eof != "\n")
		{
			if(!fseek($fp,$pos,SEEK_END))
			{
				$eof = fgetc($fp);
				$pos--;
				$str = $eof.$str;
			}else{
				break;
			}
        }
		array_unshift($lines, $str);
		$eof = '';
		$n--;
	}
    return implode('', $lines);
}

/**
 * 工具函数，日志记录函数
 * @param   string     $string 记录字符串
 * @param   string     $pos 调用者的文件路径
 * @return  null
 */
function log_mess($string = '', $file = '', $pos = '')
{
	$log_dir = WEB_ROOT.'/'.basename(Crontab\Config::$log_dir);
	file_put_contents($log_dir.'/'.date('Ymd').'.log', '['.date('Y-m-d H:i:s').']'.$string.'['.$file.':'.$pos.']'.PHP_EOL, FILE_APPEND|LOCK_EX);
}
