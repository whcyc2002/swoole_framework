<?php
/**
 * 文件上传类
 * 限制尺寸，压缩，生成缩略图，限制格式
 * @author Tianfeng.Han
 * @package Swoole
 * @subpackage SwooleSystem
 */
class Upload
{
    static $mimes;
    public $max_size=0;
    public $access = array('gif','jpg','jpeg','bmp','png'); //允许上传的类型
    public $name_type = ''; //md5,

    //上传文件的根目录
    public $base_dir;
    //指定子目录
    public $sub_dir;
    //子目录生成方法，可以使用randomkey，或者date
    public $shard_type = 'date';
    //子目录生成参数
    public $shard_argv;
    //文件命名法
    public $filename_type = 'randomkey';
    //检查是否存在同名的文件
    public $exist_check = true;
    //允许覆盖文件
    public $overwrite = true;
    
    /**
     * 限制上传文件的尺寸，如果超过尺寸，则压缩
     * @var unknown_type
     */
    public $max_width = 0; //如果为0的话不压缩
    public $max_height;
    public $max_qulitity = 80;

    /**
     * 产生缩略图
     * @var unknown_type
     */
    public $thumb_prefix = 'thumb_';
    public $thumb_dir; //最终上传的目录
    public $thumb_width = 0; //如果为0的话不生成缩略图
    public $thumb_height;
    public $thumb_qulitity = 100;

    public $error_msg;
    public $error_code;
    
    private $upload_dir = array(
    		'abs_upload_dir' => '',
    		'abs_thumb_dir' => '',

    		'abs_thumb_dir' => '',
    		'abs_upload_dir' => '',
    		
    		'thumb_filename' => '',
    		'upload_filename' => '',
    );
    
    const UPLOAD_DEFAULT = 0;
    const UPLOAD_SAE     = 1;
    
    static $upload_type = 0;
    static $storeDomain = 'static';
    
    private function codeToMessage($code)
    {
    	switch ($code) {
    		case UPLOAD_ERR_INI_SIZE:
    			$message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
    			break;
    		case UPLOAD_ERR_FORM_SIZE:
    			$message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
    			break;
    		case UPLOAD_ERR_PARTIAL:
    			$message = "The uploaded file was only partially uploaded";
    			break;
    		case UPLOAD_ERR_NO_FILE:
    			$message = "No file was uploaded";
    			break;
    		case UPLOAD_ERR_NO_TMP_DIR:
    			$message = "Missing a temporary folder";
    			break;
    		case UPLOAD_ERR_CANT_WRITE:
    			$message = "Failed to write file to disk";
    			break;
    		case UPLOAD_ERR_EXTENSION:
    			$message = "File upload stopped by extension";
    			break;
    
    		default:
    			$message = "Unknown upload error";
    			break;
    	}
    	return $message;
    }
    
    function uploadFile($dest_name, $tmp_name)
    {
    	switch(self::$upload_type)
    	{
    		case self::UPLOAD_SAE:
    			if(!class_exists('SaeStorage', false))
    			{
    				$this->error_code = -10;
    				$this->error_msg = 'SaeStorage not exists';
    				return false;
    			}
    			$s = new SaeStorage;
    			$ret = $s->upload($storeDomain, $dest_name, $tmp_name);
    			if(!$ret)
    			{
    				$this->error_msg = $s->errmsg();
    				$this->error_code = $s->errno();
    				return false;
    			}
    			break;
    		
    		default:
    			$ret = move_uploaded_file($tmp_name, $dest_name);
    			if(!$ret)
    			{
    				$this->error_msg = "move_uploaded_file fail!";
    				$this->error_code = -7;
    				return false;
    			}
    	}
    	return true;
    }

    function __construct($base_dir)
    {
        $this->base_dir = $base_dir;
        if(empty(self::$mimes))
        {
        	require LIBPATH.'/data/mimes.php';
        	self::$mimes = $mimes;
        }
    }
    
    function error_msg()
    {
        return $this->error_msg;
    }
    
    function set_access($access)
    {
        $this->access = $access;
    }
    
    function save_all()
    {
        if(!empty($_FILES))
		{
			foreach($_FILES as $k=>$f)
			{
				if(!empty($_FILES[$k]['type'])) $_POST[$k] = $this->save($k);
			}
		}
    }
    
    function checkMimeType($name)
    {
    	//MIME格式
    	$mime = $_FILES[$name]['type'];
    	$filetype= self::mime_type($mime);
    	if($filetype==='bin') $filetype = self::file_ext($_FILES[$name]['name']);
    	if($filetype===false)
    	{
    		$this->error_msg = "File mime '$mime' unknown!";
    		$this->error_code = 1;
    		return false;
    	}
    	elseif(!empty($access))
    	{
    		if(!in_array($filetype, $this->access))
    		{
    			$this->error_msg = "File Type '$filetype' not allow upload!";
    			$this->error_code = 2;
    			return false;
    		}
    	}
    	return $filetype;
    }
    function getFilename($name, $filetype, $filename=null)
    {
    	switch(self::$upload_type)
    	{
    		case self::UPLOAD_SAE:
    			$this->upload_dir = $this->base_dir;
    			$this->thumb_dir = $this->base_dir;
    			return uniqid('pic_', false).mt_rand(1, 100).'.'.$filetype;
    		default:
    			break;
    	}
    	//最终相对目录
    	if(!empty($this->sub_dir)) $this->base_dir = $this->base_dir."/".$this->sub_dir;
    	//切分目录
    	if($this->shard_type=='randomkey')
    	{
    		if(empty($this->shard_argv)) $this->shard_argv = 8;
    		$up_dir = $this->base_dir."/".RandomKey::randmd5($this->shard_argv);
    	}
    	else
    	{
    		if(empty($this->shard_argv)) $this->shard_argv = 'Ym/d';
    		$up_dir = $this->base_dir."/".date($this->shard_argv);
    	}
    	//上传的最终绝对路径，如果不存在则创建目录
    	$path = WEBPATH.$up_dir;
    	if(!is_dir($path)) mkdir($path,0777,true);
    	 
    	//生成文件名
    	if($filename===null)
    	{
    		$filename=RandomKey::randtime();
    		//如果已存在此文件，不断随机直到产生一个不存在的文件名
    		while($this->exist_check and is_file($path.'/'.$filename.'.'.$filetype))
    		{
    			$filename = RandomKey::randtime();
    		}
    	}
    	elseif($this->overwrite===false and is_file($path.'/'.$filename.'.'.$filetype))
    	{
    		$this->error_msg = "File '$path/$filename.$filetype' is exists!";
    		$this->error_code = -8;
    		return false;
    	}
    	$this->upload_dir = $path;
    	if(empty($this->thumb_dir)) $this->thumb_dir = $up_dir;

    	$filename .= '.'.$filetype;
    	return $filename;
    }
    
    function save($name, $filename=null)
    {
        //检查请求中是否存在上传的文件
        if(empty($_FILES[$name]['type']))
        {
            $this->error_msg = "No upload file '$name'!";
    	    $this->error_code = -1;
    	    return false;
        }
        //上传失败
        if(isset($_FILES['file']['error']) and $_FILES['file']['error'] !== UPLOAD_ERR_OK)
        {
        	$this->error_msg = $this->codeToMessage($_FILES['file']['error']);
        	$this->error_code = -2;
        	return false;
        }
    	/**
    	 * 检测上传的MIME格式
    	 */
    	if(($filetype = $this->checkMimeType($name))===false)
    	{
    		return false;
    	}
    	/**
    	 * 取得文件名
    	 */
    	if($this->getFilename($name, $filetype, $filename)===false)
    	{
    		return false;
    	}
    	//检查文件大小
    	$filesize = filesize($_FILES[$name]['tmp_name']);
    	if($this->max_size>0 and $filesize>$this->max_size)
    	{
    	    $this->error_msg = "File size go beyond the max_size!";
    		$this->error_code = -4;
    		return false;
    	}
    	
    	//写入文件
    	if($this->uploadFile($this->upload_dir.'/'.$filename, $_FILES[$name]['tmp_name']))
    	{
    	    //产生缩略图
    	    if($this->thumb_width)
    	    {
    	        $thumb_file = $this->thumb_dir.'/'.$this->thumb_prefix.$filename;
    	        Image::thumbnail($save_filename,WEBPATH.$thumb_file,$this->thumb_width,$this->thumb_height,$this->thumb_qulitity, false);
    	        
    	        $return['thumb'] = $thumb_file;
    	    }
    	    //压缩图片
    	    if($this->max_width)
    	    {
    	        Image::thumbnail($save_filename, $save_filename, $this->max_width, $this->max_height, $this->max_qulitity, false);
    	    }
    		$return['name'] = "$up_dir/$filename";
    		$return['size'] = $filesize;
    		$return['type'] = $filetype;
    		return $return;
    	}
    	else
    	{
    		return false;
    	}
    }
    /**
     * 获取MIME对应的扩展名
     * @param $mime
     * @return unknown_type
     */
    static function mime_type($mime)
    {
    	if(isset(self::$mimes[$mime])) return self::$mimes[$mime];
    	else return false;
    }
    /**
     * 根据文件名获取扩展名
     * @param $file
     * @return unknown_type
     */
    static public function file_ext($file)
    {
    	return strtolower(trim(substr(strrchr($file, '.'), 1)));
    }
}