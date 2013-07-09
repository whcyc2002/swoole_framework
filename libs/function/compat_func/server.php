<?php
function http_head($k,$v)
{
    Swoole::$php->response->send_head($k,$v);
}
function http_status($code)
{
    Swoole::$php->response->send_http_status($code);
}
function http_response($content)
{
    global $php;
    $php->response->body = $content;
    http_finish();
}
function http_redirect($url,$mode=301)
{
    Swoole::$php->response->send_http_status($mode);
    Swoole::$php->response->send_head('Location',$url);
}
function http_session()
{
    global $php;
    if(empty($_COOKIE[Session::$sess_name]))
    {
        $sess_id = uniqid(RandomKey::string(Session::$sess_size-13));
        $php->response->setcookie(Session::$sess_name,$sess_id,time()+$php->protocol->config['session']['cookie_life']);
    }
    else $sess_id = trim($_COOKIE[Session::$sess_name]);

    $session_cache = new Cache($php->protocol->config['session']['cache_url']);
    Session::$cache_life = $php->protocol->config['session']['session_life'];
    Session::$cache_prefix = Session::$sess_name;
    $sess = new Session($session_cache);
    $_SESSION = $php->request->session = $sess->load($sess_id);
    $php->session_open = true;
    $php->session = $sess;
}
function http_finish()
{
    Swoole::$php->request->finish = 1;
    throw new Exception;
}