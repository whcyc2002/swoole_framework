<?php
/**
 * 是对Linux Sysv系统消息队列的封装，单台服务器推荐使用
 * @author Tianfeng.Han
 */
class SysvQueue implements IQueue
{
	private $msgid;
	private $msgtype = 0;
	private $msg;
	
	function __construct($config)
	{
		if(!empty($config['msgid']))
		{
			$this->msgid = $config['msgid'];
		}
		else $this->msgid = ftok(__FILE__, __CLASS__);
		
		if(!empty($config['msgtype']))
		{
			$this->msgtype = $config['msgtype'];
		}
		
		$this->msg = msg_get_queue($this->msgid) ;
	}
	
	function pop()
	{
		$ret = msg_receive($this->msg, 0, $this->msgtype, 65525, $data);
		if($ret)
		{
			return $data;
		}
		return false;
	}
	
	function push($data)
	{
		return msg_send($this->msg, $this->msg_type, $data);
	}
}