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
use \Workerman\Worker;
use \Workerman\WebServer;
use \Workerman\Autoloader;
use Bootstrap\CrontabWorker;
use Lib\ParseCrontab;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
require_once __DIR__ . '/Bootstrap/CrontabWorker.php';
require_once __DIR__ . '/Lib/ParseCrontab.class.php';
require_once __DIR__ .'/Config/Config.php';

// Worker 进程
$worker = new CrontabWorker("tcp://0.0.0.0:3366");
// worker名称
$worker->name = 'CrontabWorker';
// bussinessWorker进程数量
$worker->count = 1;

// 如果不是在根目录启动,则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
