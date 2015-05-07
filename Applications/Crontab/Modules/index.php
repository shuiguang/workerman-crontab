<?php
/**
 * 程序目录初始化
 * @param   array      $config 配置数组
 * @return  null
 */
function index()
{
	$port = $_SERVER['SERVER_PORT'];
	//模板输出
	include WEB_ROOT . '/Views/index.tpl.php';
}
