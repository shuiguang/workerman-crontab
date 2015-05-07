<?php
/**
 * 
 * 处理具体逻辑
 * 
 * @author walkor <walkor@workerman.net>
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
	//毫秒定时器间隔，默认精度为100ms，根据CPU运转压力适当调整
	private $interval = 0.1;
	//定时任务参照标准，默认与linux crontab分钟间隔一致，秒级定时标准需改写CrontabParse方法
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
				file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="green">['.date('Y-m-d H:i:s').']正在创建'.$dir.'['.__FILE__.':'.__LINE__.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
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
		date_default_timezone_set("PRC");
		//定时扫描任务，扫描间隔与执行间隔标准无关，而是用于同步crontab文件的修改，时间越短同步越快
		Timer::add($this->interval, array($this, 'startCrontab'));
    }
	
	/**
     * 启动定时任务扫描
     */
	public function startCrontab()
    {
		$mission = array();
		foreach(glob(Config::$run_dir.'/*'.Config::$cron_suffix) as $cur_file)
		{
			$file = basename($cur_file);
			$run_time = filemtime($cur_file);
			$cron_time = filemtime(Config::$cron_dir.'/'.$file);
			if($cron_time > $run_time)
			{
				@copy(Config::$cron_dir.'/'.$file, $cur_file);
				unset($this->cron_cache[$file]);
			}
			if(isset($this->cron_cache[$file]))
			{
				$command_arr = $this->cron_cache[$file];
			}else{
				$command_arr = explode("\n", file_get_contents($cur_file));
				$this->cron_cache[$file] = $command_arr;
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
						$mission['value'] = trim(preg_replace('/^\s+'.$user.'\s+/', '', $mission['value']));
					}
					if($this->start_time - \Lib\CrontabParse::parse($mission['cron_time'], $this->start_time) == 0)
					{
						//任务执行的时间，以分作为间隔，兼容linux的crontab机制
						$mission['exec_time'] = date($this->cron_standard, time());
						$pid_file = $this->get_pid_file($mission['value']);
						if(isset($this->tasks[$pid_file]))
						{
							//如果缓存中的执行时间与同名任务的执行时间的分钟相同则跳过
							if($this->tasks[$pid_file]['exec_time'] == $mission['exec_time'])
							{
								continue;
							}else
							{
								$this->tasks[$pid_file] = $mission;
								$this->runCrontab($mission['value'], $cur_file, $line);
							}
						}else{
							$this->tasks[$pid_file] = $mission;
							$this->runCrontab($mission['value'], $cur_file, $line);
						}
					}
				}
			}
		}
		$this->start_time++;
	}
		
	/**
	 * 获取PID的文件路径，用于启动子任务进程
	 * @param   string     $value 定时任务内容
	 * @return  null
	 */
	public function get_pid_file($value)
	{
		return Config::$pid_dir.'/'.md5($value).'.'.Config::$pid_suffix;
	}
		
	/**
	 * 获取PID的文件路径，用于停止子任务进程
	 * @param   string     $value 定时任务内容
	 * @return  null
	 */
	public function get_lock_file($value)
	{
		return Config::$lock_dir.'/'.md5($value).'.'.Config::$lock_suffix;
	}
	
	/**
     * 后台执行任务，执行的定时任务命令不能有重定向命令，如果有请通过增加php脚本重定向
     */
	public function runCrontab($value, $file='', $line='')
    {
		$lock_file = $this->get_lock_file($value);
		if(file_exists($lock_file))
		{
			//对于只需要执行一次的语句在$value执行完成之后根据$value计算$lock_file，写入磁盘后将会被系统轮循捕获后跳过执行
			//file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="red">['.date('Y-m-d H:i:s').']任务锁定，无法执行'.$value.'['.$file.':'.$line.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
		}else{
			file_put_contents(Config::$log_dir.'/'.date('Ymd').'.log', '<font color="green">['.date('Y-m-d H:i:s').']正在执行'.$value.'['.$file.':'.$line.']</font>'.PHP_EOL, FILE_APPEND|LOCK_EX);
			//构造一个php脚本作为容器，然后后台执行
			$pid_file = $this->get_pid_file($value);
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
				pclose(popen("php $pid_file> /dev/null &", 'r'));
			}
		}
	}
    
}
