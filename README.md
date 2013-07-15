Swoole应用服务器
====================
框架
-----
PHP高级Web开发框架，内置应用服务器。提供统一注册树，数据库操作，模板，Cache，日志，队列，上传管理，用户管理等丰富的功能特性。

创建swoole.phar包
-----
```bash
php ./libs/code/phar.php
```

应用服务器
-----
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
$server->run(array('worker_num' => 1, 'max_request' => 1000));
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
Time taken for tests:   17.226 seconds
Complete requests:      100000
Failed requests:        0
Write errors:           0
Total transferred:      27500000 bytes
HTML transferred:       1100000 bytes
Requests per second:    5805.06 [#/sec] (mean)
Time per request:       17.226 [ms] (mean)
Time per request:       0.172 [ms] (mean, across all concurrent requests)
Transfer rate:          1558.98 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.2      0       9
Processing:     4   17  31.7     15    1023
Waiting:        3   17  31.6     15    1017
Total:          4   17  31.8     16    1025

Percentage of the requests served within a certain time (ms)
  50%     16
  66%     16
  75%     17
  80%     17
  90%     19
  95%     20
  98%     23
  99%     25
 100%   1025 (longest request)
```
