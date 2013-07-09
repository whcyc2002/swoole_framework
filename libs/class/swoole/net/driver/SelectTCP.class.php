<?php
require_once LIBPATH.'/class/swoole/net/SwooleServer.class.php';
class SelectTCP extends SwooleServer implements Swoole_TCP_Server_Driver
{
    public $server_sock;
    public $server_socket_id;

    /**
     * 文件描述符
     * @var unknown_type
     */
    public $fds;
    //客户端数量
    public $client_num = 0;

    function __construct($host,$port,$timeout=30)
    {
        parent::__construct($host,$port,$timeout);
    }
    /**
     * 向client发送数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    function send($client_id,$data)
    {
        return $this->sendData($this->client_sock[$client_id], $data);
    }
    /**
     * 向所有client发送数据
     * @return unknown_type
     */
    function sendAll($client_id=null,$data)
    {
        foreach($this->client_sock as $k=>$sock)
        {
            if($client_id and $k==$client_id) continue;
            $this->sendData($sock,$data);
        }
    }

    function shutdown()
    {
        //关闭所有客户端
        foreach($this->client_sock as $k=>$sock)
        {
            sw_socket_close($sock,$this->client_event[$k]);
        }
        //关闭服务器端
        sw_socket_close($this->server_sock,$this->server_event);
        $this->protocol->onShutdown();
    }

    function close($client_id)
    {
        sw_socket_close($this->client_sock[$client_id]);
        $this->client_sock[$client_id] = null;
        $this->fds[$client_id] = null;
        unset($this->client_sock[$client_id],$this->fds[$client_id]);
        $this->protocol->onClose($client_id);
        $this->client_num--;
    }

    function server_loop()
    {
        while(true)
        {
            $read_fds = $this->fds;
            if(stream_select($read_fds , $write = null , $exp = null , null))
            {
                foreach($read_fds as $socket)
                {
                    $socket_id = (int)$socket;
                    if($socket_id == $this->server_socket_id)
                    {
                        if($client_socket_id = parent::accept())
                        {
                        	$this->fds[$client_socket_id] = $this->client_sock[$client_socket_id];
                        	$this->protocol->onConnect($client_socket_id);
                        }
                    }
                    else
                    {
                        $data = sw_fread_stream($socket,$this->buffer_size);
                        if($data !== false)
                        {
                            $this->protocol->onRecive($socket_id,$data);
                        }
                        else
                        {
                        	$this->close($socket_id);
                        }
                    }
                }
            }
        }
    }

    function run($num=1)
    {
        //初始化事件系统
        if(!($this->protocol instanceof Swoole_TCP_Server_Protocol))
        {
            return error(902);
        }
        //建立服务器端Socket
        $this->server_sock = $this->create("tcp://{$this->host}:{$this->port}");
        $this->server_socket_id = (int)$this->server_sock;
        $this->fds[$this->server_socket_id] = $this->server_sock;
        stream_set_blocking($this->server_sock , 0);
	    if(($num-1)>0) sw_spawn($num-1);
        $this->protocol->onStart();
        $this->server_loop();
    }
}
