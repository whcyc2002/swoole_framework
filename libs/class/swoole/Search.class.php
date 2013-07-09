<?php
class Search
{
	public $db;
	public $index_table='search_index';
	
	static $encode = 'base64_encode';	
	static $autodis = true;
	const DES_LEN = 128;
	
	function __construct($db)
	{
		$this->db = $db;
	}
	
	function search($keyword,$app='')
	{
		$_keyword = self::splitword($keyword);
		if(empty($app))	$app_where = "and app={$app}";
		else $app_where = '';
		
		$sql = "select aid,title,description,url,MATCH(s_index) AGAINST('$_keyword' IN BOOLEAN MODE ) AS score
		FROM $this->index_table
		WHERE MATCH( s_index ) AGAINST('$_keyword' IN BOOLEAN MODE ) $app_where
		ORDER BY score DESC
		LIMIT 0,5";
		
		//"select id,savedata from $this->index_table where MATCH(s_index) AGAINST ('$_keyword')"; 
		
		$res = $this->db->query($sql);
		$records = $res->fetchall();
		foreach($records as &$record) $record = unserialize($record['savedata']);
		return $records;
	}
	
	function addIndex($data,$id,$app)
	{	
		$content = '';
		if(isset($data['description']))	$description = $data['description'];
		elseif(isset($data['intro'])) $description = $data['intro'];
		elseif(isset($data['content'])) $description = mb_substr($data['content'],0,self::DES_LEN,DBCHARSET);
		
		foreach($data as $key=>$value)
		{
			if(empty($value)) continue;
			$content.=self::splitword($value).'.';
		}
		$this->db->insert(array('aid'=>$id,
								'app'=>$app,
								'title'=>$data['title'],
								'description'=>$description,
								's_index'=>$content,
								'updatetime'=>time()),$this->index_table);
		return true;
	}
	
	static function splitword($string)
	{
		global $php;
		$php->plugin->require_plugin('pscws');
        $cws = $php->cws;
		$cws->set_ignore_mark(false);
		$cws->set_autodis(self::$autodis);
		$cws->set_debug(false);
		
		$string = str_replace('。','.',$string);
		
		$result = $cws->segment($string);
		$content = '';
		
		foreach($result as $str)
		{
			if($str!='.')
			{
				if(self::$encode=='base64_encode')
					$content.= base64_encode($str).' ';
				elseif(self::$encode=='areacode')
					$content.= word2code($str).' ';
			}
		}
		return $content;
	}
}
?>