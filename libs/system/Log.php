<?php
interface ILog
{
    /**
     * 写入日志
     * @param $type 类型
     * @param $msg  内容
     * @return unknown_type
     */
    function put($type,$msg);
}

class Log
{
    public $backends = array('DBLog','FileLog','PHPLog');
    public $backend;

    function __construct($params,$backend='FileLog')
    {
        if(!in_array($backend,$this->backends))
		{
			Error::info('Log backend Error',"Log backend <b>$backend</b> not no support!");
		}
		import('#log.'.$backend);
		$this->backend = new $backend($params);
    }
    function info($msg)
    {
    	$this->backend->put('INFO',$msg);
    }

    function error($msg)
    {
    	$this->backend->put('ERROR',$msg);
    }

    function warn($msg)
    {
        $this->backend->put('WARNNING',$msg);
    }

	function __call($method,$args=array())
	{
		return call_user_func_array(array($this->backend,$method),$args);
	}
}