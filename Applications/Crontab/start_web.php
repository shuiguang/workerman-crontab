<?php 
use \Workerman\Worker;
use \Workerman\WebServer;
use \Workerman\Autoloader;
use Bootstrap\CrontabWorker;
use Lib\CrontabParse;

// 自动加载类
require_once __DIR__ . '/../../Workerman/Autoloader.php';
require_once __DIR__.'/Bootstrap/CrontabWorker.php';
require_once __DIR__ .'/Config/Config.php';

// WebServer 进程
$webserver = new WebServer('http://0.0.0.0:5566');
$webserver->name = 'CrontabWeb';
$webserver->count = 1;
$webserver->addRoot('www.your_domain.com', __DIR__.'/Web');

// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
