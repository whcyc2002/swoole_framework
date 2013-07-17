Swoole应用服务器
====================
框架
-----
PHP高级Web开发框架，内置应用服务器。提供统一注册树，数据库操作，模板，Cache，日志，队列，上传管理，用户管理等丰富的功能特性。
使用内置应用服务器，可节省每次请求代码来的额外消耗。连接池技术可以很好的帮助存储系统节省连接资源。

赞助Swoole开源项目
-----
捐赠地址：http://me.alipay.com/swoole

创建swoole.phar包
-----
```
php ./libs/code/phar.php
```

应用服务器
-----
需要安装swoole扩展。
```
git clone https://github.com/matyhtf/swoole.git
cd swoole
phpize
./configure
make
sudo make install
```
然后修改php.ini加入extension=swoole.so
```php
<?php
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
require __DIR__.'/libs/lib_config.php';
//require __DIR__'/phar://swoole.phar';
Swoole\Config::$debug = true;
$appserver = new Swoole\Network\Protocol\AppServer();
$appserver->loadSetting(__DIR__."/swoole.ini");
$server = new \Swoole\Network\Server('0.0.0.0', 8888);
$server->setProtocol($appserver);
$server->run(array('worker_num' => 4, 'max_request' => 1000));
```

```shell
php server.php
[2013-07-09 12:17:05]  Swoole. running. on 0.0.0.0:8888
```
压测数据
-----
```shell
ab -c 100 -n 100000 http://127.0.0.1:8888/hello/index/
This is ApacheBench, Version 2.3 <$Revision: 655654 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 10000 requests
Completed 20000 requests
Completed 30000 requests
Completed 40000 requests
Completed 50000 requests
Completed 60000 requests
Completed 70000 requests
Completed 80000 requests
Completed 90000 requests
Completed 100000 requests
Finished 100000 requests


Server Software:        Swoole
Server Hostname:        127.0.0.1
Server Port:            8888

Document Path:          /hello/index/
Document Length:        11 bytes

Concurrency Level:      100
Time taken for tests:   10.717 seconds
Complete requests:      100000
Failed requests:        0
Write errors:           0
Total transferred:      27500000 bytes
HTML transferred:       1100000 bytes
Requests per second:    9330.83 [#/sec] (mean)
Time per request:       10.717 [ms] (mean)
Time per request:       0.107 [ms] (mean, across all concurrent requests)
Transfer rate:          2505.84 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    1   1.0      1       9
Processing:     1   10   5.6      8      63
Waiting:        0    7   5.4      6      62
Total:          1   11   5.5      9      63

Percentage of the requests served within a certain time (ms)
  50%      9
  66%     11
  75%     12
  80%     13
  90%     17
  95%     22
  98%     28
  99%     32
 100%     63 (longest request)
```
