<?php 
use \Workerman\Worker;
use \Workerman\WebServer;
use \Workerman\Autoloader;
use Bootstrap\CrontabWorker;
use Lib\CrontabParse;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
require_once __DIR__.'/Bootstrap/CrontabWorker.php';
require_once __DIR__.'/Lib/CrontabParse.php';
require_once __DIR__ .'/Config/Config.php';

// Worker 进程
$worker = new CrontabWorker("tcp://0.0.0.0:3366");
// worker名称
$worker->name = 'CrontabWorker';
// bussinessWorker进程数量
$worker->count = 1;

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
