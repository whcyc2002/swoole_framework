<?php
class Text
{
    static $number = array('〇','一','二','三','四','五','六','七','八','九');
    static function num2han($num_str)
    {
        return str_replace(range(0,9),self::$number,$num_str);
    }
}
?>