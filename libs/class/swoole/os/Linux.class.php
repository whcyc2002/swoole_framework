<?php
/**
 * 此类封装了与系统相关的功能
 * @author tianfeng.han
 * @package Swoole
 * @subpackage os
 */
class Linux
{
	/**
	 * 创建子进程
	 * @return PID
	 */
	function create_process($func,$params)
	{
		$pid = pcntl_fork();
		if($pid>0) return $pid;
		elseif($pid<0) return false;
		else call_user_func_array($func,$params);
	}
	/**
	 *
	 * @param $signal
	 * @param $handle
	 * @return unknown_type
	 */
	function signal($signal,$handle)
	{
		return pcntl_signal($signal,$handle);
	}
	/**
	 * 时钟控制器
	 * @return unknown_type
	 */
	function alarm($second)
	{
		return pcntl_alarm($second);
	}
}