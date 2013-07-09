<?php
define('DEBUG', 'on');
define("WEBPATH", str_replace("\\","/", __DIR__));
if(!empty($_SERVER['SERVER_NAME']))
{
    define("WEBROOT", 'http://'.$_SERVER['SERVER_NAME']);
}
//应用程序的位置
define("APPSPATH", WEBPATH.'/apps');
define('HTML', WEBPATH.'/html');
define('HTML_URL_BASE','/html');
define('HTML_FILE_EXT','.html');

define("TABLE_PREFIX", 'st');
define("SITENAME", 'Swoole_PHP开发社区');
//define("TPL_DIR",WEBPATH.'/site/'.SITENAME.'/templates');
//模板目录

//上传文件的位置
define('UPLOAD_DIR','saestor://uploads');

//缓存系统
define('CACHE_URL','saememcache://localhost:11211');
//define('CACHE_URL', 'file://localhost#site_cache');
//define('SESSION_CACHE','memcache://192.168.11.26:11211');
//define('KDB_CACHE','memcache://192.168.11.26:11211');
//define('KDB_ROOT','cms,user');

//Login登录用户配置
define('LOGIN_TABLE','user_login');

if(get_cfg_var('env.name') == 'dev')
{
	require __DIR__.'/apps/dev_config.php'; 
}
require __DIR__.'/libs/lib_config.php';
require __DIR__.'/admin/func.php';
Swoole\Config::$debug = true;
//动态配置系统
$php->tpl->assign('_site_','/site/'.SITENAME);
$php->tpl->assign('_static_', $php->config['site']['static']);
$php->tpl->compile_dir = SAE_TMP_PATH;
$php->tpl->cache_dir = SAE_TMP_PATH;
//指定国际编码的方式
mb_internal_encoding('utf-8');
//$php->gzip();

function url_process_regx($path)
{
    $rewrite = Swoole::$php->config['rewrite'];
    $match = array();
    foreach($rewrite as $rule)
    {
        if(preg_match('#'.$rule['regx'].'#', $path, $match))
        {
            //合并到GET中
            if(isset($rule['get']))
            {
                $p = explode(',', $rule['get']);
                foreach($p as $k=>$v)
                {
                    $_GET[$v] = $match[$k+1];
                }
            }
            return $rule['mvc'];
        }
    }
    return false;
}

function url_process_mvc()
{
    $array = array('controller'=>'page', 'view'=>'index');
    if(!empty($_GET["c"])) $array['controller']=$_GET["c"];
    if(!empty($_GET["v"])) $array['view']=$_GET["v"];

    $uri = parse_url($_SERVER['REQUEST_URI']);
    if(empty($uri['path']) or $uri['path']=='/' or $uri['path']=='/index.php')
    {
        return $array;
    }
    elseif($mvc = url_process_regx($uri['path']))
    {
        return $mvc;
    }
    $request = explode('/', trim($uri['path'], '/'), 3);
    if(count($request) < 2)
    {
        return $array;
    }
    $array['controller']=$request[0];
    $array['view']=$request[1];
    if(is_numeric($request[2])) $_GET['id'] = $request[2];
    else
    {
        Swoole\Tool::$url_key_join = '-';
        Swoole\Tool::$url_param_join = '-';
        Swoole\Tool::$url_add_end = '.html';
        Swoole\Tool::$url_prefix = WEBROOT."/{$request[0]}/$request[1]/";
        Swoole\Tool::url_parse_into($request[2],$_GET);
    }
    return $array;
}

