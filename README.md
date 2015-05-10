# workerman-crontab-for-linux
workerman-crontab-for-linux
## 运行
(php>=5.3.3)
php start.php start -d(需要设置系统php环境变量)

PHP必须支持exec函数

Home page:[http://www.modulesoap.com](http://www.modulesoap.com)

## 说明
此版本可用于linux下生产使用

## 建议
$webserver->user = 'www';	//建议使用权限较低的用户运行

$worker->user = 'www';		//建议使用权限较低的用户运行

使用以上设置后需要将以下目录和文件的权限分配给www用户

chown -R www:www ./Applications/Crontab/forbidden_dir/

chown -R www:www ./Applications/Crontab/lock_dir/

chown -R www:www ./Applications/Crontab/log_dir/

chown -R www:www ./Applications/Crontab/pid_dir/

chown -R www:www ./Applications/Crontab/run_dir/

chown -R www:www ./Applications/test.txt

./Applications/Crontab/cron_dir/ 该目录权限www用户只读不可写

## 移植
### windows到Linux（需要Linux的Workerman版本3.1.0及以上）
可以直接将Applications下的应用目录拷贝到Linux版本的Applications下直接运行

### Windows到Linux
拷贝Crontab到官方Applications/目录下即可
如果需要测试Browser.php案例，需要将Jump.php复制到http://IP地址/crontab/Jump.php并更改auto_Browser.crontab文件

### 与linux自带的crontab的异同

相同点：
兼容linux crontab语法，但是需要保证worker进程的执行者有权限执行exec命令

不同点：
提供了crontab多任务组，多个任务组同时运行。
分为一般任务和断点任务
一般任务：在cron_dir中编写定时任务命令组之后，然后复制到run_dir中即可加入定时任务扫描，时间到了之后会自动执行里面的任务，可通过web界面进行启动停止控制。
断点任务：在cron_dir中编写以auto_开头的定时任务命令组，此类任务无法可以通过在lock_dir目录中写入命令的lock文件进行控制，可通过web界面进行启动加入黑名单控制。


Browser.php案例：模拟浏览器javascript翻页跳转(以下A机器B机器可处于同一服务器或不同服务器)。
注意：CrontabWorker::$interval与浏览器的setInterval时间对应，0.1秒相当于最小间隔为100ms。

A机器提供业务网址：http://127.0.0.1/crontab/Jump.php，执行完成之后响应值为http://127.0.0.1/crontab/Jump.php?page=1并js跳转到该网址

B机器将php Browser.php -u http%3A%2F%2F127.0.0.1%2Fcrontab%2FJump.php记录在auto_Browser.crontab文件中等待定时任务进程扫描。

B机器定时任务扫描0.1秒后即可执行php Browser.php -u http%3A%2F%2F127.0.0.1%2Fcrontab%2FJump.php，该脚本中请求A机器上的服务后返回结果http://127.0.0.1/crontab/Jump.php?page=1。

B机器立即将crontab中的记录修改为下一个网址的定时任务，同时将已经执行过的网址加锁并清除定时任务。

B机器上的定时任务扫描器再经过0.1s扫描到auto_Browser.crontab文件中的更改，于是重新执行crontab任务在php Browser.php向A机器发起新的请求。

以此类推，直到B机器请求到预设的结束字符串后停止请求，并加锁所有的定时任务。
