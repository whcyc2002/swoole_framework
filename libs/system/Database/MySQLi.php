<?php
namespace Swoole\Database;
/**
 * MySQL数据库封装类
 * @package SwooleExtend
 * @author Tianfeng.Han
 *
 */
class MySQLi extends \mysqli implements \Swoole\IDatabase
{
    public $debug = false;
    public $conn = null;
    public $config;

    function __construct($db_config)
    {
        $this->config = $db_config;
    }

    function connect()
    {
        $db_config = &$this->config;
        parent::connect($db_config['host'],$db_config['user'],$db_config['password'],$db_config['dbname']);
        if(mysqli_connect_errno())  exit("Connect failed: %s\n".mysqli_connect_error());
        $this->set_charset($db_config['charset']);
    }
    /**
     * 执行一个SQL语句
     * @param $sql 执行的SQL语句
     */
    function query($sql)
    {
        parent::real_escape_string($sql);
        $res = parent::query($sql);
        if(!$res) echo Error::info("SQL Error",$this->error."<hr />$sql");
        return new MySQLiRecord($res);
    }
    /**
     * 返回上一个Insert语句的自增主键ID
     * @return $ID
     */
    function Insert_ID()
    {
        return $this->insert_id;
    }
}
class MySQLiRecord implements \Swoole\IDbRecord
{
    public $result;
    function __construct($result)
    {
        $this->result = $result;
    }

    function fetch()
    {
        return $this->result->fetch_assoc();
    }

    function fetchall()
    {
        $data = array();
        while($record = $this->result->fetch_assoc())
        {
            $data[] = $record;
        }
        return $data;
    }
    function free()
    {
        $this->result->free_result();
    }
}
?>