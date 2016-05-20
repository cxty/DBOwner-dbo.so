<?php
//cp异常和错误处理

 //如果还没有加载配置文件，则加载配置文件
if(!defined('is_load_DBOConfig'))
{
	require_once(dirname(__FILE__).'/DBOConfig.class.php');
}
if(DBOConfig::get('ERROR_HANDLE'))
{	
	/**
	 * 默认异常处理函数
	 */
	function dbo_exception_handler(Exception $e) {  
		throw new DBOError($e->getMessage(),$e->getCode(),$e->getFile(),$e->getLine());
	}
	/**
	 * 默认错误处理函数
	 */
	function dbo_error_handler($errorCode,$errorMessage,$errorFile,$errorLine) {  
		throw new DBOError($errorMessage,$errorCode,$errorFile,$errorLine);
	}
	
	set_exception_handler('dbo_exception_handler');//注册默认异常处理函数
	set_error_handler('dbo_error_handler',E_ALL ^ E_NOTICE);//注册默认错误处理函数
}	



/**
 * DBOError.class
 * 错误类
 */
class DBOError extends Exception{

    public $errorMessage='';
    public $errorFile='';
    public $errorLine=0;
    public $errorCode='';
	public $errorLevel='';
 	public $trace='';
 	public $config; //全局配置
 	static $global; //静态变量，用来实现单例模式
 	public $tpl; //模板对象
    /**
     * 构造函数
     * @param string $errorMessage 提示信息
     * @param int $errorCode 提示代号
     * @param string $errorFile 出错的文件名
     * @param int $errorLine 出错的行号
     */
    public function __construct($errorMessage,$errorCode=0,$errorFile='',$errorLine=0) 
	{
        parent::__construct($errorMessage,$errorCode);
        
        //参数配置
        if(!isset(self::$global['config'])){
        	global $config;
        	self::$global['config']=$config;
        }
        $this->config=self::$global['config'];//配置
        
        //模板初始化
        if (! isset ( self::$global ['tpl'] )) {
        	//self::$global ['tpl'] = new DBOTemplate ( $this->config ); //实例化模板类
        		
        	//加载并实例化smarty类
        	if (! (file_exists($this->config['SMARTY_TEMPLATE_DIR']) && is_dir($this->config['SMARTY_TEMPLATE_DIR'])) ) {
        		mkdir($this->config['SMARTY_TEMPLATE_DIR'], 0755, true);
        	}
        	if (! (file_exists($this->config['SMARTY_COMPILE_DIR']) && is_dir($this->config['SMARTY_COMPILE_DIR'])) ) {
        		mkdir($this->config['SMARTY_COMPILE_DIR'], 0755, true);
        	}
        	if (! (file_exists($this->config['SMARTY_CACHE_DIR']) && is_dir($this->config['SMARTY_CACHE_DIR'])) ) {
        		mkdir($this->config['SMARTY_CACHE_DIR'], 0755, true);
        	}
        	require_once(DBO_PATH . 'ext/smarty/Smarty.class.php');
        	$smarty                 =   new Smarty();
        	$smarty->debugging      =   $this->config['SMARTY_DEBUGGING'];
        	$smarty->caching        =   $this->config['SMARTY_CACHING'];
        	$smarty->cache_lifetime =   $this->config['SMARTY_CACHE_LIFETIME'];
        	$smarty->template_dir   =   $this->config['SMARTY_TEMPLATE_DIR'];
        	$smarty->compile_dir    =   $this->config['SMARTY_COMPILE_DIR'];
        	$smarty->cache_dir      =   $this->config['SMARTY_CACHE_DIR'];
        	$smarty->left_delimiter =   $this->config['SMARTY_LEFT_DELIMITER'];
        	$smarty->right_delimiter=   $this->config['SMARTY_RIGHT_DELIMITER'];
        
        	self::$global['tpl']    =   $smarty;
        }
        $this->tpl = self::$global ['tpl']; //模板类对象
        //初始化语言包
        Lang::init();
        $this->_Lang = Lang::getPack();
        $this->assign ( 'Lang',$this->_Lang);//页面调用语言包数组{$Lang.参数}
        $this->assign('ThisLang', __LANG__);//当前语言
        
        $this->assign('pop_lang',json_encode($this->_Lang['POP_txt']));//pop语言包
        
        $this->errorMessage=$errorMessage;
		$this->errorCode=$errorCode==0?$this->getCode():$errorCode;
        $this->errorFile=$errorFile==''?$this->getFile():$errorFile;
        $this->errorLine=$errorLine==0?$this->getLine():$errorLine;
		
      	$this->errorLevel=$this->getLevel();
 	    $this->trace=$this->trace();
        $this->showError();
    }
	//获取trace信息
	public function trace()
    {
        $trace = $this->getTrace();

        $traceInfo='';
        $time = date("Y-m-d H:i:s");
        foreach($trace as $t) {
            $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
            $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
           // $traceInfo .= implode(', ', $t['args']);
            $traceInfo .=")<br />\r\n";

        }
		return $traceInfo ;
    }
	//错误等级
	public function getLevel()
	{
	  $Level_array=array(	1=>'致命错误(E_ERROR)',
			2 =>'警告(E_WARNING)',
			4 =>'语法解析错误(E_PARSE)',  
			8 =>'提示(E_NOTICE)',  
			16 =>'E_CORE_ERROR',  
			32 =>'E_CORE_WARNING',  
			64 =>'编译错误(E_COMPILE_ERROR)', 
			128 =>'编译警告(E_COMPILE_WARNING)',  
			256 =>'致命错误(E_USER_ERROR)',  
			512 =>'警告(E_USER_WARNING)', 
			1024 =>'提示(E_USER_NOTICE)',  
			2047 =>'E_ALL', 
			2048 =>'E_STRICT'
		 );
		return isset($Level_array[$this->errorCode])?$Level_array[$this->errorCode]:$this->errorCode;
	}
	
	//抛出错误信息，用于外部调用
     static public function show($message="")
    {
		throw new DBOError($message);
    }
	
	//获取ip地址，记录出错信息的时候，记录下ip信息
	static public function getIp()
	{
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		   $ip = getenv("HTTP_CLIENT_IP");
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		   $ip = getenv("HTTP_X_FORWARDED_FOR");
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		   $ip = getenv("REMOTE_ADDR");
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		   $ip = $_SERVER['REMOTE_ADDR'];
		else
		   $ip = "unknown";
		return($ip);
	}
	
	//记录错误信息
	static public function write($message)
	{		
		
		$log_path=DBOConfig::get('LOG_PATH');
		//检查日志记录目录是否存在
		 if(!is_dir($log_path)) 
		 {
			//创建日志记录目录
			@mkdir($log_path,0755);
		 }
		 $log_path= rtrim($log_path,"/")."/";
		 $time=date('Y-m-d H:i:s');
		 $ip=self::getIp();
		 $destination =$log_path .date("Y-m-d").".log";
		 //写入文件，记录错误信息
       	 @error_log("{$time} | {$ip} | {$_SERVER['PHP_SELF']} |{$message}\r\n", 3,$destination);
	}
	
	//输出错误信息
     public function showError()
    {
    	
		//如果开启了日志记录，则写入日志
		if(DBOConfig::get('LOG_ON'))
		{
			self::write($this->message);
		}
		$error_url=DBOConfig::get('ERROR_URL');
		//错误页面重定向
		if($error_url!='')
		{
		 echo '<script language="javascript">
			   if(self!=top)
			  {
				  parent.location.href="'.$error_url.'";
		      }
			  else
			  {
			 	 window.location.href="'.$error_url.'";
			  }
			  </script>';
			exit;
		}
		
		if(!defined('__APP__'))
		{	
			define('__APP__','/');
		}
		
		$this->assign('message', $this->message);
		$this->assign('errorCode', $this->errorCode);
		$this->assign('DEBUG', DBOConfig::get('DEBUG'));
		$this->assign('errorFile', $this->errorFile);
		$this->assign('errorLine', $this->errorLine);
		$this->assign('errorLevel', $this->errorLevel);
		$this->assign('trace', $this->trace);
		
		$this->display('error.html');
		exit;
    }
    //模板变量解析
    public function assign($name, $value) {
    	return $this->tpl->assign ( $name, $value );
    
    }
    //模板输出
    public function display($tpl = '') {
    	//return $this->tpl->display ( $tpl );
    	//在模板中使用定义的常量,使用方式如{$__ROOT__} {$__APP__}
    	$this->assign("__ROOT__",__ROOT__);
    	$this->assign("__APP__",__APP__);
    	$this->assign("__URL__",__URL__);
    	$this->assign("__PUBLIC__",__PUBLIC__);
    
    	//实现不加参数时，自动加载相应的模板
    	$tpl=empty($tpl)?$_GET['_module'].'/'.$_GET['_action'].$this->config['TPL_TEMPLATE_SUFFIX'] : $tpl;
    	return $this->tpl->display($tpl);
    }
    
    //直接跳转
    public function redirect($url) {
    	header ( 'location:' . $url, false, 301 );
    	exit ();
    }
}
?>