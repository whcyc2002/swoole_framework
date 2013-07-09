<?php
function http_head($k,$v)
{
    header($k.':'.$v);
}
function http_status($code)
{
    header('HTTP/1.1 '.Response::$HTTP_HEADERS[$code]);
}
function http_redirect($url,$mode=301)
{
    Swoole_client::redirect($url,$mode);
}
function http_session()
{
    session();
}
function http_finish()
{
    fastcgi_finish_request();
}