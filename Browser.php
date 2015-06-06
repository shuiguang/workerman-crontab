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
//引入CrontabBrowser类
include __DIR__ . '/CrontabBrowser.class.php';
//设置入口请求网址
$start_url = 'http://www.modulesoap.com/Jump.php';
//配置参数
$config = array(
    //如果如果需要提供表单信息
    'post_form' => array(
        'admin_name' => 'Browser',
        'admin_password' => 'Browser',
    ),
    //如果使用了SESSION验证，还可以设置cookie验证信息
    'cookies' => array(
        'PHPSESSID' => md5(__FILE__),
    ),
    //……
);
//实例化浏览器
$cb = new CrontabBrowser($config);
//启动浏览器
$cb -> startBrowser($start_url);
