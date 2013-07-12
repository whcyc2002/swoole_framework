<?php
namespace Swoole\Network\Protocol;
require_once LIBPATH . '/class/swoole/net/SwooleServer.class.php';
/**
 * HTTP Server
 * @author Tianfeng.Han
 * @link http://www.swoole.com/
 * @package Swoole
 * @subpackage net.protocol
 */
class HttpServer implements \Swoole_Server_Protocol
{
    public $server;
    public $config = array();
    protected $log;

    protected $mime_types;
    protected $static_dir;
    protected $static_ext;
    protected $dynamic_ext;
    protected $document_root;
    protected $deny_dir;

    protected $buffer = array();
    protected $buffer_maxlen = 65535; //最大POST尺寸，超过将写文件

    const SOFTWARE = "Swoole";

    function __construct($config = array())
    {
        define('SWOOLE_SERVER', true);
        \import_func('compat');
        $mimes = require(LIBPATH . '/data/mimes.php');
        $this->mime_types = array_flip($mimes);
        $this->config = $config;
    }

    function setLogger($log)
    {
        $this->log = $log;
    }

    function log($msg)
    {
        //$this->log->info($msg);
        echo '[' . date('Y-m-d H:i:s') . "]\t{$msg}\n";
    }

    function onStart($serv)
    {
        if (!defined('WEBROOT')) {
            define('WEBROOT', $this->config['server']['webroot']);
        }
        $this->log(self::SOFTWARE . ". running. on {$this->server->host}:{$this->server->port}");
    }

    function onShutdown($serv)
    {
        $this->log(self::SOFTWARE . " shutdown");
    }

    function onConnect($serv, $client_id, $from_id)
    {
        $this->log("client[#$client_id@$from_id] connect");
    }

    function onClose($serv, $client_id, $from_id)
    {
        $this->log("client[#$client_id@$from_id] close");
        unset($this->buffer[$client_id]);
    }

    function loadSetting($ini_file)
    {
        if (!is_file($ini_file)) exit("Swoole AppServer配置文件错误($ini_file)\n");
        $config = parse_ini_file($ini_file, true);
        /*--------------Server------------------*/
        if (empty($config['server']['webroot'])) {
            $config['server']['webroot'] = 'http://' . $this->server->host . ':' . $this->server->port;
        }
        /*--------------Session------------------*/
        if (empty($config['session']['cookie_life'])) $config['session']['cookie_life'] = 86400; //保存SESSION_ID的cookie存活时间
        if (empty($config['session']['session_life'])) $config['session']['session_life'] = 1800; //Session在Cache中的存活时间
        if (empty($config['session']['cache_url'])) $config['session']['cache_url'] = 'file://localhost#sess'; //Session在Cache中的存活时间
        /*--------------Apps------------------*/
        if (empty($config['apps']['url_route'])) $config['apps']['url_route'] = 'url_route_default';
        if (empty($config['apps']['auto_reload'])) $config['apps']['auto_reload'] = 0;
        if (empty($config['apps']['charset'])) $config['apps']['charset'] = 'utf-8';
        /*--------------Access------------------*/
        $this->deny_dir = array_flip(explode(',', $config['access']['deny_dir']));
        $this->static_dir = array_flip(explode(',', $config['access']['static_dir']));
        $this->static_ext = array_flip(explode(',', $config['access']['static_ext']));
        $this->dynamic_ext = array_flip(explode(',', $config['access']['dynamic_ext']));
        $this->document_root = $config['server']['document_root'];
        /*-----merge----*/
        if (!is_array($this->config)) {
            $this->config = array();
        }
        $this->config = array_merge($this->config, $config);

    }

    protected function checkData($client_id, $data)
    {
        if (!isset($this->buffer[$client_id])) {
            $this->buffer[$client_id] = $data;
        } else {
            $this->buffer[$client_id] .= $data;
        }
        //HTTP结束符
        if (substr($data, -4, 4) != "\r\n\r\n") {
            return false;
        }
    }

    /**
     * 接收到数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    function onReceive($serv, $client_id, $from_id, $data)
    {
        //检测request data完整性
        //请求不完整，继续等待
        if ($this->checkData($client_id, $data) === false) {
            return true;
        }
        //完整的请求
        $data = $this->buffer[$client_id];
        //解析请求
        $request = $this->request($data);
        if ($request === false) {
            $this->server->close($client_id);
            return false;
        }
        //处理请求，产生response对象
        $response = $this->onRequest($request);
        //发送response
        $this->response($client_id, $response);
        //回收内存
        unset($data);
        $request->unsetGlobal();
        unset($request);
        unset($response);
        //清空buffer
        $this->buffer[$client_id] = "";
        $this->server->close($client_id);
    }

    /**
     * 解析form_data格式文件
     * @param $part
     * @param $request
     * @param $cd
     * @return unknown_type
     */
    function parse_form_data($part, &$request, $cd)
    {
        $cd = '--' . str_replace('boundary=', '', $cd);
        $form = explode($cd, $part);
        foreach ($form as $f) {
            if ($f === '') continue;
            $parts = explode("\r\n\r\n", $f);
            $head = $this->parse_head(explode("\r\n", $parts[0]));
            if (!isset($head['Content-Disposition'])) continue;
            $meta = $this->parse_cookie($head['Content-Disposition']);
            if (!isset($meta['filename'])) {
                //checkbox
                if (substr($meta['name'], -2) === '[]') $request->post[substr($meta['name'], 0, -2)][] = trim($parts[1]);
                else $request->post[$meta['name']] = trim($parts[1]);
            } else {
                $file = trim($parts[1]);
                $tmp_file = tempnam('/tmp', 'sw');
                file_put_contents($tmp_file, $file);
                if (!isset($meta['name'])) $meta['name'] = 'file';
                $request->file[$meta['name']] = array('name' => $meta['filename'],
                    'type' => $head['Content-Type'],
                    'size' => strlen($file),
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => $tmp_file);
            }
        }
    }

    /**
     * 头部解析
     * @param $headerLines
     * @return unknown_type
     */
    function parse_head($headerLines)
    {
        $header = array();
        foreach ($headerLines as $k => $head) {
            $head = trim($head);
            if (empty($head)) continue;
            list($key, $value) = explode(':', $head);
            $header[trim($key)] = trim($value);
        }
        return $header;
    }

    /**
     * 解析Cookies
     * @param $cookies
     * @return unknown_type
     */
    function parse_cookie($cookies)
    {
        $_cookies = array();
        $blocks = explode(";", $cookies);
        foreach ($blocks as $cookie) {
            list ($key, $value) = explode("=", $cookie);
            $_cookies[trim($key)] = trim($value, "\r\n \t\"");
        }
        return $_cookies;
    }

    /**
     * 解析请求
     * @param $data
     * @return unknown_type
     */
    function request($data)
    {
        $parts = explode("\r\n\r\n", $data, 2);
        // parts[0] = HTTP头;
        // parts[1] = HTTP主体，GET请求没有body
        $headerLines = explode("\r\n", $parts[0]);
        $request = new \Request;
        // HTTP协议头,方法，路径，协议[RFC-2616 5.1]
        list($request->meta['method'], $request->meta['uri'], $request->meta['protocol']) = explode(' ', $headerLines[0], 3);
        //$this->log($headerLines[0]);
        //错误的HTTP请求
        if (empty($request->meta['method']) or empty($request->meta['uri']) or empty($request->meta['protocol'])) {
            return false;
        }
        unset($headerLines[0]);
        //解析Head
        $request->head = $this->parse_head($headerLines);
        $url_info = parse_url($request->meta['uri']);
        $request->meta['path'] = $url_info['path'];
        if (isset($url_info['fragment'])) $request->meta['fragment'] = $url_info['fragment'];
        if (isset($url_info['query'])) {
            parse_str($url_info['query'], $request->get);
        }
        //POST请求,有http body
        if ($request->meta['method'] === 'POST') {
            $cd = strstr($request->head['Content-Type'], 'boundary');
            if (isset($request->head['Content-Type']) and $cd !== false) $this->parse_form_data($parts[1], $request, $cd);
            else parse_str($parts[1], $request->post);
        }
        //解析Cookies
        if (!empty($request->head['Cookie'])) $request->cookie = $this->parse_cookie($request->head['Cookie']);
        return $request;
    }

    /**
     * 发送响应
     * @param $client_id
     * @param $response
     * @return unknown_type
     */
    function response($client_id, $response)
    {
        if (!isset($response->head['Date'])) $response->head['Date'] = gmdate("D, d M Y H:i:s T");
        if (!isset($response->head['Server'])) $response->head['Server'] = self::SOFTWARE;
        if (!isset($response->head['KeepAlive'])) $response->head['KeepAlive'] = 'off';
        if (!isset($response->head['Connection'])) $response->head['Connection'] = 'close';
        if (!isset($response->head['Content-Length'])) $response->head['Content-Length'] = strlen($response->body);

        $out = $response->head();
        $out .= $response->body;
        $this->server->send($client_id, $out);
    }

    function http_error($code, $response, $content = '')
    {
        $response->send_http_status($code);
        $response->head['Content-Type'] = 'text/html';
        $response->body = \Swoole\Error::info(\Response::$HTTP_HEADERS[$code], "<p>$content</p><hr><address>" . self::SOFTWARE . " at {$this->server->host} Port {$this->server->port}</address>");
    }

    /**
     * 处理请求
     * @param $request
     * @return unknown_type
     */
    function onRequest($request)
    {
        $response = new \Response;
        //请求路径
        if ($request->meta['path'][strlen($request->meta['path']) - 1] == '/') {
            $request->meta['path'] .= $this->config['request']['default_page'];
        }
        $path = explode('/', trim($request->meta['path'], '/'));
        //扩展名
        $ext_name = \Upload::file_ext($request->meta['path']);
        /* 检测是否拒绝访问 */
        if (isset($this->deny_dir[$path[0]])) {
            $this->http_error(403, $response, "服务器拒绝了您的访问({$request->meta['path']})");
        } /* 是否静态目录 */
        elseif (isset($this->static_dir[$path[0]]) or isset($this->static_ext[$ext_name])) {
            $this->process_static($request, $response);
        } /* 动态脚本 */
        elseif (isset($this->dynamic_ext[$ext_name]) or empty($ext_name)) {
            $this->process_dynamic($request, $response);
        } else {
            $this->http_error(403, $response);
        }
        return $response;
    }

    /**
     * 静态请求
     * @param $request
     * @param $response
     * @return unknown_type
     */
    function process_static($request, $response)
    {
        $path = $this->document_root . '/' . $request->meta['path'];
        if (is_file($path)) {
            $ext_name = \Upload::file_ext($request->meta['path']);
            $response->head['Content-Type'] = $this->mime_types[$ext_name];
            $response->body = file_get_contents($path);
        } else $this->http_error(404, $response, "文件不存在({$path})！");
    }

    /**
     * 动态请求
     * @param $request
     * @param $response
     * @return unknown_type
     */
    function process_dynamic($request, $response)
    {
        $path = $this->document_root . '/' . $request->meta['path'];
        if (is_file($path)) {
            $request->setGlobal();
            $response->head['Content-Type'] = 'text/html';
            ob_start();
            try {
                include $path;
            } catch (Exception $e) {
                $response->send_http_status(404);
                $response->body = $e->getMessage() . '!<br /><h1>' . self::SOFTWARE . '</h1>';
            }
            $response->body = ob_get_contents();
            ob_end_clean();
        } else $this->http_error(404, $response, "页面不存在({$request->meta['path']})！");
    }
}
