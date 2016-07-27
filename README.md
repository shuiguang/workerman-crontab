# workerman-crontab-for-linux
workerman-crontab-for-linux

## 运行环境要求
(php>=5.3.3)

PHP必须支持exec函数

## 启动守护进程
```sh
/usr/local/php/bin/php /www/workerman-crontab/start.php start -d
```

## 创建新的定时任务组
如果需要添加一组定时任务，组名为job1，那么可以在./Applications/Crontab/cron_dir/下创建job1.crontab，内容如下：
```sh
#案例1：每天22:00执行一次shell脚本
00 22 * * * www /www/cut-logs
#案例2：每分钟执行一次php脚本
* * * * * www /usr/local/php/bin/php /www/test.php
```

## 启动新添加的定时任务组
使用浏览器访问
http://您的IP地址:5566/
启动刚才添加的job1.crontab任务。

## Home page
[https://github.com/shuiguang/workerman-crontab](https://github.com/shuiguang/workerman-crontab)

## 说明
此版本可用于linux下生产使用

### Windows到Linux
拷贝Crontab到官方Applications/目录下即可

