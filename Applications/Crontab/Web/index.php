<?php
/**
 * 
 * 路由控制
 * 
 */
require_once  __DIR__.'/_init.php';
date_default_timezone_set('PRC');
check_auth();
if(true)
{
    //从配置变量中获取配置信息
    $url_info = parse_url($_SERVER['REQUEST_URI']);
    
    if(isset($url_info['path']))
    {
        $path = $url_info['path'];
    }
    else
    {
        $path = '';
    }
    $tmp_arr = explode('/', $path);

    foreach($tmp_arr as $key => $value)
    {
        if(trim($value) == '')
        {
            unset($tmp_arr[$key]);
        }
    }
    $tmp_arr = array_values($tmp_arr);
    if(isset($tmp_arr[0]))
    {
        $action = array_shift($tmp_arr);
    }else 
    {
        $action = 'index';
    }
    
    //加载逻辑函数文件，仅仅调用当前模块
    if(!function_exists($action))
    {
        $php_file = WEB_ROOT.'/Modules/'.$action.'.php';
        if(file_exists($php_file))
        {
            require_once $php_file;
        }
    }
    //调用逻辑函数，由于无法在函数内使用global获取，因此注入$config到第一个参数，其他参数依次排列
    if(function_exists($action))
    {
        call_user_func_array($action, $tmp_arr);
    }else{
        return _header('Location: /'.(!empty($_GET) ? '?'.http_build_query($_GET) : ''));
    }
    
}else{
    return false;
}
