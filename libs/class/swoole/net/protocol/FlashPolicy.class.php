<?php
class FlashPolicy implements Swoole_TCP_Server_Protocol
{
    public $default_port = 843;
    public $policy_xml = '<cross-domain-policy>
	<site-control permitted-cross-domain-policies="all"/>
	<allow-access-from domain="*" to-ports="1000-9999" />
</cross-domain-policy>';

    function onRecive($client_id,$data)
    {
        echo $data;
        $this->server->send($client_id,$this->policy_xml);
        $this->server->close($client_id);
    }

    function onStart()
    {

    }

    function onShutdown()
    {

    }
    function onClose($client_id)
    {

    }
    function onConnect($client_id)
    {

    }
}