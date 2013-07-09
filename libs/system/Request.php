<?php
class Request
{
    public $get;
    public $post;
    public $file;
    public $cookie;
    public $session;
    public $head;
    public $meta;

    public $status;

    /**
     * 将原始请求信息转换到PHP超全局变量中
     * @return unknown_type
     */
    function setGlobal()
    {
        if($this->get) $_GET = $this->get;
        if($this->post) $_POST = $this->post;
        if($this->file) $_FILES = $this->file;
        if($this->cookie) $_COOKIE = $this->cookie;
        $_REQUEST = array_merge($this->get,$this->post,$this->cookie);
        $_SERVER["HTTP_HOST"] = $this->head['Host'];
        $_SERVER["HTTP_USER_AGENT"] = $this->head['User-Agent'];
    }

    function unsetGlobal()
    {
        if($_GET) $_GET = array();
        if($_POST) $_POST = array();
        if($_FILES) $_FILES = array();
        if($_COOKIE) $_COOKIE = array();
        if($_SESSION) $_SESSION = array();
        $_REQUEST = array();
    }
}
