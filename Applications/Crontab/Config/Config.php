<?php
/**
 * This file is part of workerman-crontab.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author shuiguang
 * @link https://github.com/shuiguang/workerman-crontab
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Crontab;
class Config
{
    // 定时任务目录
    public static $cron_dir = '';
    
    // 运行状态目录
    public static $run_dir = '';
    
    // 执行进程目录
    public static $pid_dir = '';
    
    // 停止进程目录
    public static $lock_dir = '';
    
    // 禁止任务目录
    public static $forbidden_dir = '';
    
    // 日志记录目录
    public static $log_dir = '';
     
    // 执行的子任务进程后缀
    public static $pid_suffix = 'pid';
     
    // 停止的子任务进程后缀
    public static $lock_suffix = 'lock';
    
    // 机器人定时任务前缀,用于断点执行
    public static $auto_prefix = 'auto_';
    
    // 定时任务文件名后缀
    public static $cron_suffix = 'crontab';
    
    // php程序所在完整路径,例如/usr/local/php/bin/php,设置完整路径才能开机自动启
    public static $exec_path = 'php';
    
    // 用于过滤定时任务中可能存在的用户名,当前用户会自动获取,最终执行用户为worker进程的用户
    public static $exec_user = array('root', 'www');
    
    // 管理员用户名,用户名密码都为空字符串时说明不用验证
    public static $adminName = '';
    
    // 管理员密码,用户名密码都为空字符串时说明不用验证
    public static $adminPassword = '';

}
// 建议使用绝对路径直接覆盖上面的参数配置
Config::$cron_dir = __DIR__ . '/../cron_dir';
Config::$run_dir = __DIR__ . '/../run_dir';
Config::$pid_dir = __DIR__ . '/../pid_dir';
Config::$lock_dir = __DIR__ . '/../lock_dir';
Config::$forbidden_dir = __DIR__ . '/../forbidden_dir';
Config::$log_dir = __DIR__ . '/../log_dir';
