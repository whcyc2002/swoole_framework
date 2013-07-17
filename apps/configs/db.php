<?php
$db['master'] = array(
    'type'    => Swoole\Database::TYPE_MYSQL, //Database Driver，可以选择PdoDB , MySQL, MySQL2(MySQLi) , AdoDb(需要安装adodb插件)
    'host'    => "localhost",
    'port'    => 3306,
    'dbms'    => 'mysql',
    'engine'  => 'MyISAM',
    'user'    => "root",
    'passwd'  => "root",
    'name'    => "test",
    'charset' => "utf8",
    'setname' => true,
);
return $db;