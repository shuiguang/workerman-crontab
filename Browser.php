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
include 'Applications/Crontab/Config/Config.php';
include 'Snoopy.class.php';
use Crontab\Config;
class CrontabBrowser
{
    // 执行目录
    public $run_dir = '';
    // 锁目录
    public $lock_dir = '';
    // 锁后缀
    public $lock_suffix = '';
    // 定时任务自动执行前缀
    public $auto_prefix =  '';
    // 定时任务文件名后缀
    public $cron_suffix = '';
    // 定时任务文件名(不含路径和前后缀)
    public $cron_file =  '';
    // 定时任务文件名(不含路径和前后缀)
    public $run_file =  '';
    // 系统php执行时间
    public $exec_time = '* * * * *';
    // 系统php执行用户
    public $exec_user = 'root';
    // 系统php路径，需要添加环境变量
    public $exec_home = 'php';
    // PHP_CLI执行传入参数名称
    public $opt_key = 'u';
    // PHP_CLI执行文件，默认为本文件路径
    public $exec_file =  '';
    // PHP_CLI执行传入参数的值：url为网址
    public $url = '';
    // PHP_CLI执行返回结束字符
    public $finish_str = 'finish';
    // 目标网址表单信息
    public $post_form = array();
    // 本地cookie伪造信息
    public $cookies = array();
    // 浏览器设置
    public $rawheaders = array();
    // 伪造浏览器信息
    public $agent = '';
    // 伪造来源页面
    public $referer = '';
    
    public function __construct($config = array())
    {
        $this->cron_dir = isset($config['cron_dir']) ? $config['cron_dir'] : Crontab\Config::$cron_dir;
        $this->run_dir = isset($config['run_dir']) ? $config['run_dir'] : Crontab\Config::$run_dir;
        $this->log_dir = isset($config['log_dir']) ? $config['log_dir'] : Crontab\Config::$log_dir;
        $this->lock_dir = isset($config['lock_dir']) ? $config['lock_dir'] : Crontab\Config::$lock_dir;
        $this->lock_suffix = isset($config['lock_suffix']) ? $config['lock_suffix'] : Crontab\Config::$lock_suffix;
        $this->auto_prefix = isset($config['auto_prefix']) ? $config['auto_prefix'] : Crontab\Config::$auto_prefix;
        $this->cron_suffix = isset($config['cron_suffix']) ? $config['cron_suffix'] : Crontab\Config::$cron_suffix;
        $this->run_file = isset($config['run_file']) ? $config['run_file'] : 'Browser';
        $this->cron_file = $this->cron_dir.'/'.$this->auto_prefix.$this->run_file.'.'.$this->cron_suffix;
        $this->run_file = $this->run_dir.'/'.$this->auto_prefix.$this->run_file.'.'.$this->cron_suffix;
        $this->exec_file = isset($config['exec_file']) ? $config['exec_file'] : str_replace('\\', '/', __FILE__);
        $this->finish_str = isset($config['finish_str']) ? $config['finish_str'] : 'finish';
        $this->post_form = isset($config['post_form']) ? $config['post_form'] : array();
        $this->cookies = isset($config['cookies']) ? $config['cookies'] : array();
        $this->rawheaders = isset($config['rawheaders']) ? $config['rawheaders'] : array();
        $this->agent = isset($config['agent']) ? $config['agent'] : 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36';
        $this->referer = isset($config['referer']) ? $config['referer'] : '';
        
        $params = getopt($this->opt_key.':');
        //默认从PHP_CLI获取
        if($params[$this->opt_key])
        {
            $this->url = urldecode($params[$this->opt_key]);
        }else{
            //默认传入起始请求地址
            $this->url = isset($config['url']) ? $config['url'] : '';
        }
        if($this->url)
        {
            //启动浏览器
            $this->startBrowser($this->url);
        }
    }
    
    /**
     * 启动浏览器
     * @param   string      $url 请求的网址
     * @return  null
     */
    public function startBrowser($url)
    {
        $cmd = $this->get_cmd($url);
        $pid_name = $this->get_pid_name($url);
        $prefix = basename($this->run_file);
        $lock_file = $this->lock_dir.'/'.$prefix.'.'.$pid_name.'.'.$this->lock_suffix;
        if(!file_exists($lock_file))
        {
            $this->lock_cron($url);
            //请求网址
            $response = $this->request_url($url);
            $next_url = $this->match_script_url($response);
            $this->log_mess('正在请求：'.$url.'，返回网址：'.$next_url.'，当前时间'.time());
            if(strpos($response, $this->finish_str) !== false || !$next_url)
            {
                $this->log_mess('采集结束'.$url.'，返回字符串：'.$this->finish_str);
				$this->add_cron('');
                die($this->finish_str);
            }else{
                $this->add_cron($next_url);
            }
        }else{
			$this->add_cron('');
            $this->log_mess('网址'.$url.'被锁定，锁定文件：'.basename($lock_file));
        }
    }
    
    /**
     * 获取php cmd命令
     * @param   array   $url为网址
     * @return  string
     */
    private function get_cmd($url)
    {
        $params = array(
            $this->opt_key => $url,
        );
        $cmd = $this->exec_home.' '.$this->exec_file;
        if($params && is_array($params))
        {
            foreach($params as $k => $v)
            {
                $cmd .= " -$k ".urlencode($v);
            }
        }
        return $cmd;
    }
    
    /**
     * 获取pid文件名不带后缀和路径
     * @param   array   $url为网址
     * @return  string
     */
    private function get_pid_name($url)
    {
        $cmd = $this->get_cmd($url);
        return md5($cmd);
    }
    
    /**
     * 写入lock文件，防止重复访问
     * @param   string      $url 请求的网址
     * @return  null
     */
    public function lock_cron($url)
    {
        $pid_name = $this->get_pid_name($url);
        $prefix = basename($this->run_file);
        $lock_file = $this->lock_dir.'/'.$prefix.'.'.$pid_name.'.'.$this->lock_suffix;
        file_put_contents($lock_file, '');
    }
    
    /**
     * 写入run_dir下文件，修改定时任务命令
     * @param   string      $url 请求的网址
     * @return  null
     */
    public function add_cron($url = '')
    {
		if($url)
		{
			$cmd = $this->get_cmd($url);
			if($this->exec_user)
			{
				$cron = $this->exec_time.' '.$this->exec_user.' ';
			}else{
				$cron = $this->exec_time.' ';
			}
			$cron .= ' '.$cmd;
		}else{
			$cron = '';
		}
        
        //获取最后一行内容加上#之后追加$cron
        if(file_exists($this->cron_file))
        {
            if(file_exists($this->run_file))
            {
                $contents = '';
                $arr = file($this->run_file);
                foreach($arr as $row)
                {
                    $row = trim($row);
                    if(strpos($row, '#') === 0)
                    {
                        $contents .= $row.PHP_EOL;
                    }else{
                        $contents .= '#'.$row.PHP_EOL;
                    }
                }
                $contents .= $cron ? $cron.PHP_EOL : '';
            }
            file_put_contents($this->run_file, $contents);
        }
    }
    
    /**
     * 从响应内容中匹配出跳转地址，优先匹配script标签
     * @param   string      $response 请求的响应字符串
     * @return  string
     */
    public function match_script_url($response)
    {
        $script_pattern = '/<script[^>]*?>(.*?)<\/script>/';
        preg_match($script_pattern, $response, $scripts);
        if(isset($scripts[1]))
        {
            $content = $scripts[1];
        }else{
            $content = $response;
        }
        $url_pattern = '/(((f|ht){1}tps?:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/';
        preg_match($url_pattern, $content, $urls);
        if(isset($urls[1]))
        {
            return $urls[1];
        }
    }
    
    /**
     * 记录请求信息
     * @param   string      $message 待记录的字符串
     * @return  string
     */
    public function log_mess($message)
    {
        file_put_contents($this->log_dir.'/'.date('Ymd').'.log', $message.PHP_EOL, FILE_APPEND|LOCK_EX);
    }
    
    /**
     * 使用Snoopy采集类
     * @param   string      $url 请求的网址
     * @return  string
     */
    public function request_url($url)
    {
        if(isset($this->cookies['cookie_path']))
        {
            $cookie_path = $this->cookies['cookie_path'];
        }else{
            $cookie_path = sys_get_temp_dir().'/'.md5(__FILE__).'.txt';
        }
        if(file_exists($cookie_path))
        {
            $cookies = $this->get_cookie($cookie_path);
        }else{
            $cookies = array();
        }
        $cookies = array_merge($this->cookies, $cookies);
        $snoopy = new Snoopy();
        $snoopy->rawheaders = $this->rawheaders;
        $snoopy->agent = $this->agent;
        $snoopy->referer = $this->referer;
        foreach($cookies as $key => $cookie)
        {
            $snoopy->cookies[$key] = $cookie;
        }
        $snoopy->submit($url, $this->post_form);
        $snoopy->setcookies();
        //修复Snoopy的cookie值带有空白的bug
        $cookies = array();
        foreach($snoopy->cookies as $key => $cookie)
        {
            $cookies[$key] = trim($cookie);
        }
        //cookie存盘
        $this->set_cookie($cookies, $cookie_path);
        return $snoopy->results;
    }
    
    /**
     * Snoopy采集类cookie存盘
     * @param   array       $cookies 浏览器返回的cookie数组
     * @param   string      $cookie_path 浏览器暂存的cookie文件路径
     * @return  string
     */
    public function set_cookie($cookies, $cookie_path)
    {
        file_put_contents($cookie_path, json_encode($cookies));
    }
    
    /**
     * 使用Snoopy采集类cookie读取
     * @param   string      $cookie_path 浏览器暂存的cookie文件路径
     * @return  string
     */
    public function get_cookie($cookie_path)
    {
        return json_decode(file_get_contents($cookie_path), true);
    }
}


//启动php浏览器
$start_url = 'http://127.0.0.1/crontab/Jump.php';

$config = array(
    'url' => $start_url,
    //如果需要传入post参数
    'post_form' => array(
        'admin_name' => 'Browser',
        'admin_password' => 'Browser',
    ),
    //如果使用了SESSION验证，还可以设置cookie验证信息
    'cookies' => array(
        'PHPSESSID' => md5(__FILE__),
    ),
);
//实例化浏览器
$cb = new CrontabBrowser($config);