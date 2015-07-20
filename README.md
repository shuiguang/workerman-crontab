# workerman-crontab-for-linux
workerman-crontab-for-linux
## 运行
(php>=5.3.3)
php start.php start -d(需要设置系统php环境变量)

PHP必须支持exec函数

Home page:[https://github.com/shuiguang/workerman-crontab](https://github.com/shuiguang/workerman-crontab)

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

