<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author shuiguang
 * @copyright shuiguang
 * @link http://www.modulesoap.com/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Crontab;
class Config
{
    // cron任务目录
    public static $cron_dir = './Applications/Crontab/cron_dir';
    
    // run文件目录
    public static $run_dir = './Applications/Crontab/run_dir';
    
    // pid文件目录
    public static $pid_dir = './Applications/Crontab/pid_dir';
    
    // lock文件目录
    public static $lock_dir = './Applications/Crontab/lock_dir';
     
    // 黑名单目录
    public static $forbidden_dir = './Applications/Crontab/forbidden_dir';
    
    // 执行日志目录
    public static $log_dir = './Applications/Crontab/log_dir';
     
    // 执行的子任务进程后缀
    public static $pid_suffix = 'pid';
     
    // 停止的子任务进程后缀
    public static $lock_suffix = 'lock';
    
    // 机器人定时任务前缀，用于断点执行
    public static $auto_prefix = 'auto_';
    
    // 定时任务文件名后缀
    public static $cron_suffix = 'crontab';
    
    // 用于过滤定时任务中可能存在的用户名，当前用户会自动获取，最终执行用户为worker进程的用户
    public static $exec_user = array('root', 'www');
    
    // 管理员用户名，用户名密码都为空字符串时说明不用验证
    public static $adminName = '';
    
    // 管理员密码，用户名密码都为空字符串时说明不用验证
    public static $adminPassword = '';

}