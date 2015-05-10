<?php
/**
 * Web展示有野
 */
function index()
{
    $port = $_SERVER['SERVER_PORT'];
    //模板输出
    include WEB_ROOT . '/Views/index.tpl.php';
}
