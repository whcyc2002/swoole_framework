<?php
require(__DIR__.'/config.php');
//$php->db->debug = true;
//$php->tpl->debugging = true;
$appserver = new Swoole\Network\Protocol\AppServer();
$appserver->loadSetting(__DIR__."/swoole.ini");
$server = new \Swoole\Network\Server('0.0.0.0', 8888);
$server->setProtocol($appserver);
$server->run(2);
