<?php
import('#base.ProCsv');
/**
 * 键值关联数组转换字符串，字符串转换关联数组
 * @author Administrator
 *
 */
class ProArray
{
	static function str2array($str)
	{
		ProCsv::$col_sep = "\n";	
		return ProCsv::parse_line($str);
	}
	
	static function array2str($array)
	{
		ProCsv::$col_sep = "\n";
		return ProCsv::build_line($array);
	}
}
?>