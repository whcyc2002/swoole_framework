<?php
class hello extends Swoole\Controller
{
    function index()
    {
        return "hello world";
    }
}