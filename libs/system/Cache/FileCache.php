<?php
namespace Swoole\Cache;
/**
 * 文件缓存类，提供类似memcache的接口
 * 警告：此类仅用于测试，不作为生产环境的代码，请使用Key-Value缓存系列！
 * @author Tianfeng.Han
 * @package Swoole
 * @subpackage cache
 */
class FileCache implements \Swoole\ICache
{
	public $_vd=array();
	public $onchange=0;
	public $res;
	public $autosave = true;

	function __construct($config)
	{
	    if(isset($config['params']['file'])) $this->res = $config['params']['file'];
	    else $this->res = FILECACHE_DIR.'/'.$config['id'].'.php';
		if(is_file($this->res))
		{
		    require($this->res);
		    $this->_vd = $_vd;
		}
    }

    function set($name,$value,$timeout=0)
	{
		$this->_vd[$name]["value"]=$value;
		$this->_vd[$name]["timeout"]=$timeout;
		$this->_vd[$name]["mktime"]=time();
		$this->onchange=1;
		if($this->autosave) $this->save();
		return true;
    }

	function get($name)
	{
		if($this->exist($name)) return $this->_vd[$name]["value"];
		else return false;
	}

	function exist($name)
	{
		if(!isset($this->_vd[$name])) return false;
		elseif($this->_vd[$name]["timeout"]==0) return true;
		elseif(($this->_vd[$name]["mktime"]+$this->_vd[$name]["timeout"])<time())
		{
			$this->onchange=1;
			$this->delete($name);
			return false;
		}
		else return true;
	}

	function delete($name)
	{
		if(isset($this->_vd[$name])) unset($this->_vd[$name]);
		$this->onchange=1;
		$this->save();
	}

	function save()
	{
		if($this->onchange==1)
		{
		    file_put_contents($this->res,"<?php\n\$_vd=".var_export($this->_vd,true).';');
		}
	}

	function __destruct()
	{
		$this->save();
	}
}