<?php
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
require 'phar://swoole.phar';
Swoole\Config::$debug = true;

$appserver = new Swoole\Network\Protocol\AppServer();
$appserver->loadSetting(__DIR__."/swoole.ini");
$server = new \Swoole\Network\Server('0.0.0.0', 8888);
$server->setProtocol($appserver);
$server->run(2);
