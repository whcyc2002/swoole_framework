<?php
class SwooleServer
{
	public $protocol;
	public $host = '0.0.0.0';
	public $port;
	public $timeout;

	public $buffer_size = 8192;
	public $write_buffer_size = 2097152;
	public $server_block = 0; //0 block,1 noblock
	public $client_block = 0; //0 block,1 noblock
	//最大连接数
	public $max_connect=1000;
	//客户端socket列表
	public $client_sock;

	function __construct($host,$port,$timeout=30)
	{
		$this->host = $host;
		$this->port = $port;
		$this->timeout = $timeout;
	}
	/**
	 * 应用协议
	 * @return unknown_type
	 */
	function setProtocol($protocol)
	{
		$this->protocol = $protocol;
		$this->protocol->server = $this;
	}
	function accept()
	{
		$client_socket = stream_socket_accept($this->server_sock);
		$client_socket_id = (int)$client_socket;
		stream_set_blocking($client_socket,$this->client_block);
		$this->client_sock[$client_socket_id] = $client_socket;
		$this->client_num++;
		if($this->client_num > $this->max_connect)
		{
			sw_socket_close($client_socket);
			return false;
		}
		else
		{
			//设置写缓冲区
			stream_set_write_buffer($client_socket,$this->write_buffer_size);
			return $client_socket_id;
		}
	}
	function onError($errno,$errstr)
	{
		exit("$errstr ($errno)");
	}
	/**
	 * 创建一个Stream Server Socket
	 * @param $uri
	 * @return unknown_type
	 */
	function create($uri,$block=0)
	{
		//UDP
		if($uri{0}=='u') $socket = stream_socket_server($uri,$errno,$errstr,STREAM_SERVER_BIND);
		//TCP
		else $socket = stream_socket_server($uri,$errno,$errstr);

		if(!$socket) $this->onError($errno,$errstr);
		//设置socket为非堵塞或者阻塞
		stream_set_blocking($socket,$block);
		return $socket;
	}
	function create_socket($uri,$block=false)
	{
		$set = parse_url($uri);
		if($uri{0}=='u') $sock = socket_create(AF_INET, SOCK_DGRAM , SOL_UDP);
		else $sock = socket_create(AF_INET, SOCK_STREAM , SOL_TCP);

		if($block) socket_set_block($sock);
		else socket_set_nonblock($sock);
		socket_bind($sock,$set['host'],$set['port']);
		socket_listen($sock);
		return $socket;
	}
	function sendData($sock,$data)
	{
		return sw_fwrite_stream($sock,$data);
	}
	function log($log)
	{
		echo $log,NL;
	}
}
function sw_run($cmd)
{
	if(PHP_OS=='WINNT') pclose(popen("start /B ".$cmd,"r"));
	else exec($cmd." > /dev/null &");
}
function sw_gc_array($array)
{
	$new = array();
	foreach($array as $k=>$v)
	{
		$new[$k] = $v;
		unset($array[$k]);
	}
	unset($array);
	return $new;
}
/**
 * 关闭socket
 * @param $socket
 * @param $event
 * @return unknown_type
 */
function sw_socket_close($socket,$event=null)
{
	if($event)
	{
		event_del($event);
		event_free($event);
	}
	stream_socket_shutdown($socket,STREAM_SHUT_RDWR);
	fclose($socket);
}
function sw_fread_stream($fp,$length)
{
	$data = false;
	while($buf = fread($fp,$length))
	{
		$data .= $buf;
		if(strlen($buf)<$length) break;
	}
	return $data;
}
function sw_fwrite_stream($fp, $string)
{
	$length = strlen($string);
	for($written = 0; $written < $length; $written += $fwrite)
	{
		$fwrite = fwrite($fp, substr($string, $written));
		if($fwrite<=0 or $fwrite===false) return $written;
	}
	return $written;
}

function sw_spawn($num)
{
	if(!extension_loaded('pcntl')) return new Error("Require pcntl extension!");
	$pids = array();
	for($i=0;$i<$num;$i++)
	{
		$pid = pcntl_fork();
		if($pid) $pids[] = $pid;
		else break;
	}
	return $pids;
}
interface Swoole_TCP_Server_Driver
{
	function run($num=1);
	function send($client_id,$data);
	function close($client_id);
	function shutdown();
	function setProtocol($protocol);
}
interface Swoole_UDP_Server_Driver
{
	function run($num=1);
	function shutdown();
	function setProtocol($protocol);
}
interface Swoole_TCP_Server_Protocol
{
	function onStart();
	function onConnect($client_id);
	function onReceive($client_id, $data);
	function onClose($client_id);
	function onShutdown($server);
}

interface Swoole_UDP_Server_Protocol
{
	function onStart();
	function onData($peer,$data);
	function onShutdown();
}


interface Swoole_Server_Protocol
{
    function onStart($server);
    function onConnect($server, $client_id, $from_id);
    function onReceive($server,$client_id, $from_id, $data);
    function onClose($server, $client_id, $from_id);
    function onShutdown($server);
}
