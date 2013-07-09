<?php
/**
 * 基本函数，全局对象$php的构造
 * @package SwooleSystem
 * @author 韩天峰
 */
define("LIBPATH",str_replace("\\","/", __DIR__));
if(PHP_OS=='WINNT') define("NL","\r\n");
else define("NL","\n");
define("BL","<br />".NL);

require_once LIBPATH.'/system/Loader.php';
/**
 * 注册顶层命名空间到自动载入器
 */
Swoole\Loader::setRootNS('Swoole', __DIR__.'/system');
spl_autoload_register('\\Swoole\\Loader::autoload');

/**
 * 产生类库的全局变量
 */
$php = Swoole::getInstance();

/**
 *函数的命名空间
 */
function import_func($space_name)
{
    if($space_name{0}=='@') $func_file = WEBPATH.'/class/'.substr($space_name,1).'.func.php';
    else $func_file = LIBPATH.'/function/'.$space_name.'.php';
    require_once($func_file);
}
/**
 * 生产一个model接口，模型在注册树上为单例
 * @param $model_name
 */
function createModel($model_name)
{
    global $php;
    return $php->model->$model_name;
}
/**
 * 传入一个数据库表，返回一个封装此表的Model接口
 * @param $table_name
 * @return unknown_type
 */
function table($table_name)
{
    global $php;
    if(isset($php->model->_models[$table_name])) return $php->model->$table_name;
    else
    {
        $model = new Model($php);
        $model->table = $table_name;
        $php->model->_models[$table_name] = $model;
        return $model;
    }
}
/**
 * 导入类库
 */
function import($lib_name)
{
    $file = str_replace('.','/',$lib_name);
    if($file{0}=='@') $lib_file = WEBPATH.'/class/'.substr($file,1).'.class.php';
    elseif($file{0}=='#') $lib_file = LIBPATH.'/class/swoole/'.substr($file,1).'.class.php';
    else $lib_file = LIBPATH.'/class/'.$file.".class.php";

    if(file_exists($lib_file))
    {
        require_once($lib_file);
        return true;
    }
    else
    {
        Swoole\Error::info("Import Error!","Class <b>$lib_file</b> not fountd!<br />\n $lib_name load fail!<br />\n");
        return false;
    }
}
/**
 * 工厂方法，产生一个类的对象
 * @param $name
 * @return unknown_type
 */
function create($name)
{
    import($name);
    $classinfo = explode('.',$name);
    $classname = $classinfo[-1];
    if(func_num_args()!=1)
    {
        $args=func_get_args();
        for($i=1;$i<count($args);$i++) $el[]='$args['.$i.']';
        $object=eval("return new $classname(".implode(",",$el).");");
        return $object;
    }
    else return new $classname;
}
/**
 * 开启会话
 * @return None
 */
function session()
{
    if(!defined('SESSION_CACHE'))
    {
        session_start();
        return true;
    }
    else
    {
        Session::$cache = new Cache(SESSION_CACHE);
        Session::initSess();
    }
}
/**
 * 导入插件
 * @param $plugin_name
 * @return None
 */
function loadPlugin($plugin_name)
{
    global $php;
    $php->plugin->load($plugin_name);
}
/**
 * 调试数据，终止程序的运行
 * @param $var
 * @return unknown_type
 */
function debug()
{
    echo '<pre>';
    $vars = func_get_args();
    foreach($vars as $var) var_dump($var);
    echo '</pre>';
    exit;
}
/**
 * 引发一个错误
 * @param $error_id
 * @param $stop
 * @return unknown_type
 */
function error($error_id,$stop=true)
{
    global $php;
    $error = new Error($error_id);
    if(isset($php->error_call[$error_id]))
    {
        call_user_func($php->error_call[$error_id],$error);
    }
    elseif($stop) exit($error);
    else echo $error;
}
/**
 * 错误信息输出处理
 */
function swoole_error_handler($errno, $errstr, $errfile, $errline)
{
    $level = 'Error';
    $info = '';

    switch ($errno)
    {
        case E_USER_ERROR:
            $level = 'User Error';
            break;
        case E_USER_WARNING:
            $level = 'Warnning';
            break;
        case E_USER_NOTICE:
            $level = 'Notice';
            break;
        default:
            return;
    }

    $title = 'Swoole '.$level;
    $info .= '<b>File:</b> '.$errfile."<br />\n";
    $info .= '<b>Line:</b> '.$errline."<br />\n";
    $info .= '<b>Info:</b> '.$errstr."<br />\n";
    $info .= '<b>Code:</b> '.$errno."<br />\n";
    echo Swoole\Error::info($title,$info);
}

