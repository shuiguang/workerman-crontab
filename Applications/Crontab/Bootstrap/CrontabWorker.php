<?php
/**
 * 
 * 处理具体逻辑
 * 
 */
namespace Bootstrap;
use Workerman\Worker;
use Workerman\Lib\Timer;
use Crontab\Config;

class CrontabWorker extends Worker
{
	//记录启动的函数名
	protected $_onWorkerStart = null;
	//当前worker进程的用户名
    protected $current_user = null;
	//当前进程启动的时间
    private $start_time = 0;
	//定时任务缓存，避免频繁读取文件
	private $cron_cache = array();
	//实时任务数组
	private $tasks = array();
	//机器请求间隔，默认范围介于0.1-1之间，可根据CPU运转压力适当调整
	private $interval = 0.1;
	//定时任务参照标准，默认与linux crontab分钟间隔一致，秒级定时器标准需改写CrontabParse方法
	private $cron_standard = 'Y-m-d H:i';
	
	public function __construct($socket_name = '', $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
    }
	
	/**
     * 运行
     * @see Workerman.Worker::run()
    */
    public function run()
    {
		$this->_onWorkerStart = $this->onWorkerStart;
        $this->onWorkerStart = array($this, 'onWorkerStart');
        parent::run();
	}
	
    public function onWorkerStart()
    {
		//定时扫描任务并异步执行
		$this->start_time = time();
		//系统目录
		$sys_dir = array(
			'定时任务目录' => Config::$cron_dir, 
			'运行状态目录' => Config::$run_dir, 
			'执行进程目录' => Config::$pid_dir, 
			'停止进程目录' => Config::$lock_dir, 
			'禁止任务目录' => Config::$forbidden_dir, 
			'日志记录目录' => Config::$log_dir,
		);
		foreach($sys_dir as $dir)
		{
			if(!is_dir($dir))
			{
				mkdir($dir);
			}
		}
		//清除过期的pid文件
		foreach(glob(Config::$pid_dir.'/*.pid') as $file)
		{
			@unlink($file);
		}
		//获取当前workerman进程的用户名
		$this->current_user = array_merge(Config::$exec_user, array(get_current_user()));
		//设置系统时间
		date_default_timezone_set('PRC');
		//定时扫描任务，扫描间隔与执行间隔标准无关，而是用于同步crontab文件的修改，时间越短同步越快
		Timer::add($this->interval, array($this, 'startCrontab'));
    }
	
	/**
     * 启动定时任务扫描
     */
	public function startCrontab()
    {
		$mission = array();
		foreach(glob(Config::$cron_dir.'/*'.Config::$cron_suffix) as $cur_file)
		{
			$file = basename($cur_file);
			$run_file = Config::$run_dir.'/'.$file;
			$forbidden_file = Config::$forbidden_dir.'/'.$file;
			//判断是否可运行，使用加入黑名单功能可以强行停止定时任务
			if(file_exists($run_file) && !file_exists($forbidden_file))
			{
				//判断更新cache，当cron_dir更新用于手动编写cron_dir下的定时任务，run_dir下更新用于机器编写run_dir下的定时任务
				if(filemtime($cur_file) == filemtime($run_file))
				{
					if(isset($this->cron_cache[$file]['cmd']))
					{
						$command_arr = $this->cron_cache[$file]['cmd'];
					}else{
						$command_arr = explode("\n", file_get_contents($cur_file));
						$this->cron_cache[$file]['cmd'] = $command_arr;
					}
				}else{
					//如果没有对$run_file更新操作是不会执行以下语句的，建议将定时任务文件同时复制到$cron_dir和$run_dir中保证filemtime值相同以减少同步消耗
					if(strpos($file, Config::$auto_prefix) === 0)
					{
						//前缀是Config::$auto_prefix将会从run_dir中读取(机器自动更改autocrontab，不同步文件)
						$command_arr = explode("\n", file_get_contents($run_file));
						//机器预留功能：当run_dir中文件更新时不缓存到this->cron_cache[$file]['task']中
						$this->cron_cache[$file]['task'] = array();
					}else{
						//前缀不是Config::$auto_prefix将会从cron_dir中读取(手动更改crontab文件，自动同步文件)
						$this->rsync_file($cur_file, $run_file);
						$command_arr = explode("\n", file_get_contents($cur_file));
						//自动检测到文件更新后重新执行
					}
					$this->cron_cache[$file]['cmd'] = $command_arr;
				}
				$line = 0;
				foreach($command_arr as $command)
				{
					$line++;
					if(empty($command) || $command[0] == '#')
					{
						continue;
					}
					$command = trim($command);
					//分割定时时间和命令
					$part = explode(' ', $command);
					if(isset($part[5]))
					{
						$mission['cron_time'] = $part[0].' '.$part[1].' '.$part[2].' '.$part[3].' '.$part[4];
						$mission['value'] = str_replace($mission['cron_time'], '', $command);
						//去掉可能存在的执行用户字符
						foreach($this->current_user as $user)
						{
							$mission['value'] = trim(preg_replace('/^\s+'.$user.'\s+/', '', ' '.$mission['value'].' '));
						}
						if($this->start_time - \Lib\CrontabParse::parse($mission['cron_time'], $this->start_time) == 0)
						{
							//任务执行的时间，以分作为间隔，兼容linux的crontab机制
							$mission['exec_time'] = date($this->cron_standard, time());
							$pid_file = $this->get_pid_file($file, $mission['value']);
							if(isset($this->cron_cache[$file]['task'][$pid_file]))
							{
								//如果缓存中的执行时间与同名任务的执行时间的分钟相同则跳过
								if($this->cron_cache[$file]['task'][$pid_file]['exec_time'] == $mission['exec_time'])
								{
									continue;
								}else
								{
									$this->cron_cache[$file]['task'][$pid_file] = $mission;
									$this->runCrontab($mission['value'], $file, $line);
								}
							}else{
								$this->cron_cache[$file]['task'][$pid_file] = $mission;
								$this->runCrontab($mission['value'], $file, $line);
							}
						}
					}
				}
			}else{
				//从内存中清除
				if(isset($this->cron_cache[$file]))
				{
					unset($this->cron_cache[$file]);
				}
			}
		}
		$this->start_time++;
	}
		
	/**
	 * 校验同步文件，不建议每次都copy文件，使用特殊同步工具或手动复制保证filemtime可以减少消耗
	 * @param   string     $from 源文件名称
	 * @param   string     $to 目标文件名称
	 * @return  null
	 */
	public function rsync_file($from, $to)
	{
		@copy($from, $from);
	}
			
	/**
	 * 获取PID的文件路径，用于启动子任务进程
	 * @param   string     $group 分组任务组名
	 * @param   string     $value 定时任务内容
	 * @return  null
	 */
	public function get_pid_file($group, $value)
	{
		//$group前缀命令空间，命名不能含有.Config::$cron_suffix.字符
		return Config::$pid_dir.'/'.$group.'.'.md5($value).'.'.Config::$pid_suffix;
	}
		
	/**
	 * 获取PID的文件路径，用于停止子任务进程
	 * @param   string     $group 分组任务组名
	 * @param   string     $value 定时任务内容
	 * @return  null
	 */
	public function get_lock_file($group, $value)
	{
		//$group前缀命令空间，命名不能含有.Config::$cron_suffix.字符
		return Config::$lock_dir.'/'.$group.'.'.md5($value).'.'.Config::$lock_suffix;
	}
	
	/**
     * 后台执行任务，执行的定时任务命令不能有重定向命令，如果有请通过增加php脚本重定向
     */
	public function runCrontab($value, $file='', $line='')
    {
		$lock_file = $this->get_lock_file($file, $value);
		if(file_exists($lock_file))
		{
			//对于只需要执行一次的语句在$value执行完成之后根据$value计算$lock_file，写入磁盘后将会被系统轮循捕获后跳过执行
			//file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="red">['.date('Y-m-d H:i:s').']任务锁定，无法执行'.$value.'['.$file.':'.$line.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
		}else{
			file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="green">['.date('Y-m-d H:i:s').']正在执行'.$value.'['.$file.':'.$line.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
			//构造一个php脚本作为容器，然后后台执行
			$pid_file = $this->get_pid_file($file, $value);
			$contents = '<?php
$value =
<<<EOF
'.$value.'
EOF;
exec($value);';
			if(!file_exists($pid_file))
			{
				file_put_contents($pid_file, $contents);
			}
			if(strtoupper(substr(PHP_OS,0,3)) == 'WIN')
			{
				pclose(popen('start /B php '.$pid_file, 'r'));
			}else
			{
				pclose(popen('php '.$pid_file.'> /dev/null &', 'r'));
			}
		}
	}
    
}
