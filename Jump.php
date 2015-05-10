<?php

$admin_name = 'Browser';
$admin_password = 'Browser';

//cookie验证机制：cookie
if(isset($_POST['admin_name']) && isset($_POST['admin_password']))
{
	if($_POST['admin_name'] == $admin_name && $_POST['admin_password'] == $admin_password)
	{
		setcookie('admin_name', $admin_name);
	}else{
		
		if(!isset($_COOKIE['admin_name']) || !$_COOKIE['admin_name'])
		{
			die('access deny');
		}
	}
}else{
	if(!isset($_COOKIE['admin_name']) || $_COOKIE['admin_name'])
	{
		die('access deny');
	}
}

//session验证机制：
session_start();
if(isset($_POST['admin_name']) && isset($_POST['admin_password']))
{
	if($_POST['admin_name'] == $admin_name && $_POST['admin_password'] == $admin_password)
	{
		$_SESSION['admin'] = $admin_name;
	}else{
		if(!isset($_SESSION['admin']) || !$_SESSION['admin'])
		{
			die('access deny');
		}
	}
}else{
	if(!isset($_SESSION['admin']) || $_SESSION['admin'])
	{
		die('access deny');
	}
}


sleep(1);
$start_url = 'http://www.modulesoap.com/Jump.php';

$params = array('key' => 'test');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$next_id = $id+1;

$params['id'] = $next_id;

$next_url = $start_url.'?'.http_build_query($params);

if($id == 10)
{
	$response = 'finish';
	
}else{

	$response = '<script type="text/javascript">setTimeout(function(){window.location.href="'.$next_url.'"}, 1000)</script>';

}

echo $response;

