<?php
//加载核心的文件
require_once LIBPATH.'/system/Loader.php';
require_once LIBPATH.'/system/ModelLoader.php';
require_once LIBPATH.'/system/PluginLoader.php';
/**
 * Swoole系统核心类，外部使用全局变量$php引用
 * Swoole框架系统的核心类，提供一个swoole对象引用树和基础的调用功能
 * @package SwooleSystem
 * @author Tianfeng.Han
 * @subpackage base
 */
class Swoole
{
    //所有全局对象都改为动态延迟加载
    //如果希望启动加载,请使用Swoole::load()函数

    public $server;
    public $protocol;
    public $request;
    public $response;
    public $session;
    public $session_open = false;

    static public $app_root;
    static public $app_path;
    /**
     * 可使用的组件
     */
    static $autoload_libs = array(
    	'db' => true,  //数据库
    	'tpl' => true, //模板系统
    	'cache' => true, //缓存
    	'config' => true, //缓存
    	'event' => true, //异步事件
    	'log' => true, //日志
    	'kdb' => true, //key-value数据库
    	'upload' => true, //上传组件
    	'user' => true,   //用户验证组件
    );
    /**
     * Swoole类的实例
     * @var unknown_type
     */
    static public $php;
    public $pagecache;
    /**
     * 发生错误时的回调函数
     * @var unknown_type
     */
    public $error_callback;

    public $load;
    public $model;
    public $plugin;
    public $genv;
    public $env;
    
    private function __construct()
    {
        if(!defined('DEBUG')) define('DEBUG','off');
        if(DEBUG=='off') \error_reporting(0);
        else \error_reporting(E_ALL);
        $this->__init();
        $this->load = new Swoole\Loader($this);
        $this->model = new Swoole\ModelLoader($this);
        $this->plugin = new Swoole\PluginLoader($this);
    }
    static function getInstance()
    {
        if(!self::$php)
        {
            self::$php = new Swoole;
        }
        return self::$php;
    }
    private function __release()
    {
        if($this->db instanceof Database) $this->db->close();
        unset($this->tpl);
        unset($this->cache);
    }
    /**
     * 获取资源消耗
     * @return unknown_type
     */
    function runtime()
    {
        // 显示运行时间
        $return['time'] = number_format((microtime(true)-$this->env['runtime']['start']),4).'s';

        $startMem =  array_sum(explode(' ',$this->env['runtime']['mem']));
        $endMem   =  array_sum(explode(' ',memory_get_usage()));
        $return['memory'] = number_format(($endMem - $startMem)/1024).'kb';
        return $return;
    }
    /**
     * 压缩内容
     * @return unknown_type
     */
    function gzip()
    {
        //不要在文件中加入UTF-8 BOM头
        //ob_end_clean();
        ob_start("ob_gzhandler");
        #是否开启压缩
        if(function_exists('ob_gzhandler')) ob_start('ob_gzhandler');
        else ob_start();
    }
    /**
     * 初始化环境
     * @return unknown_type
     */
    private function __init()
    {
        #记录运行时间和内存占用情况
        $this->env['runtime']['start'] = microtime(true);
        $this->env['runtime']['mem'] = memory_get_usage();
        #捕获错误信息
        if(DEBUG=='on') set_error_handler('swoole_error_handler');
		
		#初始化App环境
		//为了兼容老的APPSPATH预定义常量方式
    	if(defined('APPSPATH'))
    	{
    		self::$app_root = str_replace(WEBPATH, '', APPSPATH);
    	}
    	//新版全部使用类静态变量 self::$app_root
    	elseif(empty(self::$app_root))
    	{
    		self::$app_root = "/apps";
    	}
    	self::$app_path = WEBPATH.self::$app_root;
    	$this->env['app_root'] = self::$app_root;
    }
    /**
     * 加载一个模块，并返回
     * @param $lib
     * @return object $lib
     */
    static function load($lib)
    {
    	$this->$lib = $this->load->loadLib($lib);
    	return $this->$lib;
    }
    /**
     * 自动导入模块
     * @return None
     */
    function autoload()
    {
        //$this->autoload_libs = array_flip(func_get_args());
        //历史遗留
    }
    function __get($lib_name)
    {
    	if(isset(self::$autoload_libs[$lib_name]) and empty($this->$lib_name))
    	{
    		$this->$lib_name = $this->load->loadLib($lib_name);
    	}
    	return $this->$lib_name;
    }
    /**
     * 运行MVC处理模型
     * @param $url_processor
     * @return None
     */
    function runMVC($url_processor)
    {
        $url_func = 'url_process_'.$url_processor;
        if(!function_exists($url_func))
        {
        	return Swoole\Error::info('MVC Error!',"Url Process function not found!<p>\nFunction:$url_func");
        }
        $mvc = $url_func($url_func);
        if(!preg_match('/^[a-z0-9_]+$/i', $mvc['controller']))
        {
        	return Swoole\Error::info('MVC Error!',"controller[{$mvc['controller']}] name incorrect.Regx: /^[a-z0-9_]+$/i");
        }
        if(!preg_match('/^[a-z0-9_]+$/i',$mvc['view']))
        {
        	return Swoole\Error::info('MVC Error!',"view[{$mvc['view']}] name incorrect.Regx: /^[a-z0-9_]+$/i");
        }
        if(isset($mvc['app']) and !preg_match('/^[a-z0-9_]+$/i',$mvc['app']))
        {
        	return Swoole\Error::info('MVC Error!',"app[{$mvc['app']}] name incorrect.Regx: /^[a-z0-9_]+$/i");
        }
		$this->env['mvc'] = $mvc;
		//支持app+controller+view三级映射
		if(isset($mvc['app']))
		{
			 $controller_path = self::$app_path."/{$mvc['app']}/controllers/{$mvc['controller']}.php";
		}
        else
        {
        	$controller_path = self::$app_path."/controllers/{$mvc['controller']}.php";
        }
        if(!is_file($controller_path))
        {
            header("HTTP/1.1 404 Not Found");
            Swoole\Error::info('MVC Error',"Controller <b>{$mvc['controller']}</b> not exist!");
        }
        else require_once($controller_path);
        if(!class_exists($mvc['controller']))
        {
            Swoole\Error::info('MVC Error',"Controller Class <b>{$mvc['controller']}</b> not exist!");
        }
        $controller = new $mvc['controller']($this);
        if(!is_callable(array($controller,$mvc['view'])))
        {
            header("HTTP/1.1 404 Not Found");
            Swoole\Error::info('MVC Error!'.$mvc['view'],"View <b>{$mvc['controller']}->{$mvc['view']}</b> Not Found!");
        }
        if(empty($mvc['param'])) $param = null;
        else $param = $mvc['param'];

        $method = $mvc['view'];
        $return = $controller->$method($param);

        if($controller->is_ajax)
        {
            header('Cache-Control: no-cache, must-revalidate');
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
            header('Content-type: application/json');
            echo \json_encode($return);
        }
        else $return;
    }

    function runAjax()
    {
        if(!preg_match('/^[a-z0-9_]+$/i',$_GET['method'])) return false;
        $method = 'ajax_'.$_GET['method'];

        if(!function_exists($method))
        {
            echo 'Error: Function not found!';
            exit;
        }
        header('Cache-Control: no-cache, must-revalidate');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-type: application/json');

        $data = call_user_func($method);
        if(DBCHARSET!='utf8')
        {
            $data = Swoole_tools::array_iconv(DBCHARSET , 'utf-8' , $data);
        }
        echo json_encode($data);
    }

    function runView($pagecache=false)
    {
        if($pagecache)
        {
            //echo '启用缓存';
            $cache = new Swoole_pageCache(3600);
            if($cache->isCached())
            {
                //echo '调用缓存';
                $cache->load();
            }
            else
            {
                //echo '没有缓存，正在建立缓存';
                $view = isset($_GET['view'])?$_GET['view']:'index';
                if(!preg_match('/^[a-z0-9_]+$/i',$view)) return false;
                foreach($_GET as $key=>$param)
                $this->tpl->assign($key,$param);
                $cache->create($this->tpl->fetch($view.'.html'));
                $this->tpl->display($view.'.html');
            }
        }
        else
        {
            //echo '不启用缓存';
            $view = isset($_GET['view'])?$_GET['view']:'index';
            foreach($_GET as $key=>$param)
            $this->tpl->assign($key,$param);
            $this->tpl->display($view.'.html');
        }
    }

    function runServer($ini_file='')
    {
        if(empty($ini_file)) $ini_file = WEBPATH.'/swoole.ini';
        import('#net.protocol.AppServer');
        $protocol = new AppServer($ini_file);
        global $argv;
        $server_conf = $protocol->config['server'];
        import('#net.driver.'.$server_conf['driver']);
        $server = new $server_conf['driver']($server_conf['host'],$argv[1],60);
        $this->server = $server;
        $this->protocol = $protocol;
        $server->setProtocol($protocol);
        $server->run($server_conf['processor_num']);
    }
}