<?php
class hello extends Swoole\Controller
{
    function index()
    {
        return "hello world";
    }
    function dbtest()
    {
        $res = $this->swoole->db->query("show tables");
        var_dump($res->fetchall());
    }
}
