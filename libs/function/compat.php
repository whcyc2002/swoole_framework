<?php
//运行于Server模式
if(defined('SWOOLE_SERVER'))
{
    require LIBPATH.'/function/compat_func/server.php';
    Swoole\Error::$stop = false;
}
else
{
    require LIBPATH.'/function/compat_func/apache.php';
}