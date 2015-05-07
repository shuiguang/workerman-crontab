# workerman-crontab-for-linux
workerman-crontab-for-linux
## 运行
(php>=5.3.3)
php start.php start -d(需要设置系统php环境变量)

支持exec函数

Home page:[http://www.modulesoap.com](http://www.modulesoap.com)

## 说明
此版本可用于linux下生产使用

## 建议
$webserver->user = 'www';	//建议使用权限较低的用户运行

$worker->user = 'www';		//建议使用权限较低的用户运行

使用以上设置后需要将以下目录和文件的权限分配给www用户

chown -R www:www workerman-crontab/Applications/Crontab/forbidden_dir/

chown -R www:www workerman-crontab/Applications/Crontab/lock_dir/

chown -R www:www workerman-crontab/Applications/Crontab/log_dir/

chown -R www:www workerman-crontab/Applications/Crontab/pid_dir/

chown -R www:www workerman-crontab/Applications/Crontab/run_dir/

chown -R www:www workerman-crontab/test.txt

workerman-crontab/Applications/Crontab/cron_dir/该目录权限www用户只读不可写

## 移植
### windows到Linux（需要Linux的Workerman版本3.1.0及以上）
可以直接将Applications下的应用目录拷贝到Linux版本的Applications下直接运行

### Windows到Linux
拷贝Crontab到官方Applications/目录下即可

### 与linux自带的crontab的异同

相同点：

兼容linux crontab语法，但是需要保证worker进程的执行者有权限执行命令

不同点：

提供了crontab多任务组，多个crontab文件同时运行。

任务组提供web界面进行启动管理，提供REST接口访问，提供简单的用户验证。

模拟浏览器翻页跳转：

注意：CrontabWorker::$interval与浏览器的setInterval时间对应，0.1秒相当于最小间隔为100ms。以下A机器B机器可处于同一服务器或不同服务器。

A机器提供业务网址：http://index.php?page=1，执行完成之后响应值为http://index.php?page=2，以此类推

B机器将php request.php -url urlencode('http://index.php?page=1')记录在crontab文件中等待定时任务进程扫描

B机器定时任务扫描0.1秒即可执行php request.php访问A机器，返回结果http://index.php?page=2，B机器立即将crontab中的记录修改为php request.php -url urlencode('http://index.php?page=2')

同时B机器上的定时任务扫描器再经过0.1s扫描到crontab文件中的更改，于是重新执行crontab任务向php request.php发起新的请求

B机器如果接收到finish等结束字符标记时将会将crontab文件复制到forbidden_dir目录中终止定时任务

任务组中的子任务以pid文件运行，以lock文件进行次数控制，配合soapApi客户端回调到本机对命令的执行次数进行控制。

多任务并发控制：

A机器提供多条任务(每条任务可独立设置执行时间，执行次数)，使用soapApi客户端将数据发送到B机器的soapApi服务端。

B机器分析多条任务数据并将其存储作为一组定时任务存储，B机器需要运行定时任务扫描进程。同时B机器需要编写soapApi服务端监控子任务的pid文件和lock文件的模块。

B机器定时任务扫描到第n条定时任务，异步执行。任务的性质分为以下2类：

第一类：任务只需执行一次，异步执行代码存在于B机器，执行完成之后可以在本地生成lock文件加锁(相对于linux crontab设置超长间隔时间更简单)防止再次执行

第二类：任务执行多次，执行频率由linux crontab规则决定。

第一类适用于A机器发送异步任务，然后A机器使用soapApi客户端对B机器soapApi服务端pid和lock模块进行查询状态。

第二类适用于A机器发起定时任务，由于B机器上的子任务的进程永远运行，A机器只能获取运行状态，而不能获取锁状态。



