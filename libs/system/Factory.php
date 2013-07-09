<?php
namespace Swoole;

class Factory
{
    private static $pool;
	static function __callStatic($func, $params)
	{
        $id = $params[0];
        if(empty(self::$pool[$id]))
        {
            $objectType = substr($func, 3);
            $class = '\\Swoole\\'.$objectType;
            $config = \Swoole::getInstance()->config[strtolower($objectType)][$id];
            self::$pool[$id] = $class::create($config);
        }
        return self::$pool[$id];
	}
}