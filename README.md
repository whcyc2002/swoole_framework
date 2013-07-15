Swoole应用服务器
====================
框架
-----
PHP高级Web开发框架，内置应用服务器。提供统一注册树，数据库操作，模板，Cache，日志，队列，上传管理，用户管理等丰富的功能特性。

创建swoole.phar包
-----
php ./libs/code/phar.php

应用服务器
-----
```php
<?php
require 'phar://swoole.phar';
//$php->db->debug = true;
//$php->tpl->debugging = true;
$appserver = new Swoole\Network\Protocol\AppServer();
$appserver->loadSetting(__DIR__."/swoole.ini");
$server = new \Swoole\Network\Server('0.0.0.0', 8888);
$server->setProtocol($appserver);
$server->run(2);
```

```shell
php server.php
[2013-07-09 12:17:05]  Swoole. running. on 0.0.0.0:8888
```
