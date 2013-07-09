<?php
/**
 * 会话控制类
 * 通过SwooleCache系统实现会话控制，可支持FileCache,DBCache,Memcache以及更多
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @package Login
 */
class Session
{
    // 类成员属性定义
    static $cache_prefix;
    static $cache;
    static $cache_life = 1800;

    public $sessID;
    static $sess_size = 32;
    static $sess_name = 'SESSID';
    static $sess_domain;

    /**
     * 构造函数
     * @param $cache Cache对象
     * @return NULL
     */
    public function __construct($cache)
    {
        self::$cache = $cache;
    }
    public function load($sessId)
    {
        $this->sessID = $sessId;
        $data = self::get($sessId);
        if($data) return unserialize($data);
        else return array();
    }
    public function save()
    {
        return self::set($this->sessID,serialize($_SESSION));
    }
    /**
     * 打开Session
     * @param   String  $pSavePath
     * @param   String  $pSessName
     * @return  Bool    TRUE/FALSE
     */
    static public function open($save_path='',$sess_name='')
    {
        self::$cache_prefix = $save_path.'_'.$sess_name;
        return true;
    }
    /**
     * 关闭Session
     * @param   NULL
     * @return  Bool    TRUE/FALSE
     */
    static public function close()
    {
        return true;
    }
    /**
     * 读取Session
     * @param   String  $sessId
     * @return  Bool    TRUE/FALSE
     */
    static public function get($sessId)
    {
        $session = self::$cache->get(self::$cache_prefix.'_'.$sessId);
        //先读数据，如果没有，就初始化一个
        if(!empty($session)) return $session;
        else return '';
    }
    /**
     * 设置Session的值
     * @param   String  $wSessId
     * @param   String  $wData
     * @return  Bool    true/FALSE
     */
    static public function set($sessId,$session='')
    {
        return self::$cache->set(self::$cache_prefix.'_'.$sessId,$session,self::$cache_life);
    }
    /**
     * 销毁Session
     * @param   String  $wSessId
     * @return  Bool    true/FALSE
     */
    static public function delete($wSessId = '')
    {
        return self::$cache->delete(self::$cache_prefix.'_'.$sessId);
    }
    /**
     * 内存回收
     * @param   NULL
     * @return  Bool    true/FALSE
     */
    static public function gc()
    {
        return true;
    }
    /**
     * 初始化Session，配置Session
     * @param   NULL
     * @return  Bool  true/FALSE
     */
    static function initSess()
    {
        //不使用 GET/POST 变量方式
        ini_set('session.use_trans_sid',0);
        //设置垃圾回收最大生存时间
        ini_set('session.gc_maxlifetime',self::$cache_life);
        //使用 COOKIE 保存 SESSION ID 的方式
        ini_set('session.use_cookies',1);
        ini_set('session.cookie_path','/');
        //多主机共享保存 SESSION ID 的 COOKIE
        ini_set('session.cookie_domain', self::$sess_domain);
        //将 session.save_handler 设置为 user，而不是默认的 files
        session_module_name('user');
        //定义 SESSION 各项操作所对应的方法名：
        session_set_save_handler(
                array('Session', 'open'),
                array('Session', 'close'),
                array('Session', 'get'),
                array('Session', 'set'),
                array('Session', 'delete'),
                array('Session', 'gc'));
        session_start();
        return true;
    }
}
