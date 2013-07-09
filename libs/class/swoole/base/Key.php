<?php
/**
 * 基础类，用于处理key键值分段
 * 
 * @package Swoole
 * @subpackage Base
 * @author Tianfeng.Han
 *
 */
class Key
{
    public $key = '';
    public $keys = array();
    
    function __construct($key)
    {
        $this->key = $key;
        $this->keys = explode('_',$key);
    }
    
    /**
     * 获取根键
     * @return unknown_type
     */
    function root()
    {
        return $this->keys[0];
    }
    
    /**
     * 获取键名
     * @return unknown_type
     */
    function name()
    {
        if(isset($this->keys[1])) return $this->keys[1];
        return 'default';
    }
    
    /**
     * 获取键序
     * @return unknown_type
     */
    function id()
    {
        if(isset($this->keys[2])) return $this->keys[2];
        return 'default';
    }
}
?>