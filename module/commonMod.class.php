<?php
// 公共模块
class commonMod {
	public $model; // 数据库模型对象
	public $tpl; // 模板对象
	public $config; // 全局配置
	static $global; // 静态变量，用来实现单例模式

	public $fun = null; // 公共函数
	public $_Lang = null; // 语言包
	
	
	public function __construct() {
		
		session_start (); // 开启session
		// 参数配置
		if (! isset ( self::$global ['config'] )) {
			global $config;
			self::$global ['config'] = $config;
		}
		$this->config = self::$global ['config']; // 配置
		
		$this->fun = new Fun ( $this->config );
		
		// 数据库模型初始化
		if (! isset ( self::$global ['model'] )) {
			self::$global ['model'] = new DBOModel ( $this->config ); // 实例化数据库模型类
		}
		$this->model = self::$global ['model']; // 数据库模型对象
		                              
		// 模板初始化
		if (! isset ( self::$global ['tpl'] )) {
			// self::$global ['tpl'] = new DBOTemplate ( $this->config );
			// //实例化模板类
			
			// 加载并实例化smarty类
			if (! (file_exists ( $this->config ['SMARTY_TEMPLATE_DIR'] ) && is_dir ( $this->config ['SMARTY_TEMPLATE_DIR'] ))) {
				mkdir ( $this->config ['SMARTY_TEMPLATE_DIR'], 0755, true );
			}
			if (! (file_exists ( $this->config ['SMARTY_COMPILE_DIR'] ) && is_dir ( $this->config ['SMARTY_COMPILE_DIR'] ))) {
				mkdir ( $this->config ['SMARTY_COMPILE_DIR'], 0755, true );
			}
			if (! (file_exists ( $this->config ['SMARTY_CACHE_DIR'] ) && is_dir ( $this->config ['SMARTY_CACHE_DIR'] ))) {
				mkdir ( $this->config ['SMARTY_CACHE_DIR'], 0755, true );
			}
			require_once (DBO_PATH . 'ext/smarty/Smarty.class.php');
			$smarty = new Smarty ();
			$smarty->debugging = $this->config ['SMARTY_DEBUGGING'];
			$smarty->caching = $this->config ['SMARTY_CACHING'];
			$smarty->cache_lifetime = $this->config ['SMARTY_CACHE_LIFETIME'];
			$smarty->template_dir = $this->config ['SMARTY_TEMPLATE_DIR'];
			$smarty->compile_dir = $this->config ['SMARTY_COMPILE_DIR'];
			$smarty->cache_dir = $this->config ['SMARTY_CACHE_DIR'];
			$smarty->left_delimiter = $this->config ['SMARTY_LEFT_DELIMITER'];
			$smarty->right_delimiter = $this->config ['SMARTY_RIGHT_DELIMITER'];
			
			self::$global ['tpl'] = $smarty;
		}
		$this->tpl = self::$global ['tpl']; // 模板类对象
		                                    
		// 初始化语言包
		Lang::init ();
		$this->_Lang = Lang::getPack ();
		$this->assign ( 'Lang', $this->_Lang ); // 页面调用语言包数组{$Lang.参数}
		$this->assign ( 'ThisLang', __LANG__ ); // 当前语言
		
		$this->assign ( 'pop_lang', json_encode ( $this->_Lang ['POP_txt'] ) ); // pop语言包
		$this->assign('UserInfoMenu', json_encode ( $this->_Lang ['UserInfoMenu']));//用户信息菜单语言包
		$this->assign ( 'upfiletool_lang', json_encode ( $this->_Lang ['UPFileTool_txt'] ) ); // 上传组件语言包
		$this->assign ( 'file_server', $this->config ['FILE_SERVER_GET'] ); // 文件服务器地址
		
		
	}
	/**
	 * 加载数据处理类,并返回类对象
	 *
	 * @param string $ClassName
	 * @param
	 *        	$model
	 * @param
	 *        	$config
	 * @return Object new ClassName
	 */
	public function RequireClass($ClassName, $model, $config, $model_mongo = null) {
		return DBOApp::RequireClass ( $ClassName, $model, $config, $model_mongo );
	}
	
	// 模板变量解析
	protected function assign($name, $value) {
		return $this->tpl->assign ( $name, $value );
	
	}
	// 模板输出
	protected function display($tpl = '') {
		// return $this->tpl->display ( $tpl );
		// 在模板中使用定义的常量,使用方式如{$__ROOT__} {$__APP__}
		$this->assign ( "__ROOT__", __ROOT__ );
		$this->assign ( "__APP__", __APP__ );
		$this->assign ( "__URL__", __URL__ );
		$this->assign ( "__PUBLIC__", __PUBLIC__ );
		
		// 实现不加参数时，自动加载相应的模板
		$tpl = empty ( $tpl ) ? $_GET ['_module'] . '/' . $_GET ['_action'] . $this->config ['TPL_TEMPLATE_SUFFIX'] : $tpl;
		return $this->tpl->display ( $tpl );
	}
	
	// 直接跳转
	protected function redirect($url) {
		header ( 'Location: ' . $url, false, 301 );
		exit ();
	}
	
	// 出错之后跳转，后退到前一页
	protected function error($msg) {
		header ( "Content-type: text/html; charset=utf-8" );
		$msg = "alert('$msg');";
		echo "<script>$msg history.go(-1);</script>";
		exit ();
	}
	
	/*
	 * 功能:分页 $url，基准网址，若为空，将会自动获取，不建议设置为空 $total，信息总条数 $perpage，每页显示行数
	 * $pagebarnum，分页栏每页显示的页数 $mode，显示风格，参数可为整数1，2，3，4任意一个
	 */
	protected function page($url, $total, $perpage = 10, $pagebarnum = 5, $mode = 1) {
		$page = new page ();
		return $page->show ( $url, $total, $perpage, $pagebarnum, $mode );
	}
	
	/**
	 * addslashes() 别名函数,加强对数组类型(array)的数据处理
	 * 该函数并添加了对MSSQL 的转义字符异常的支持,但前提是SQL 的分界符为’ 即单引号
	 *
	 * @param
	 *        	string | array $string
	 * @param boolean $force
	 *        	是否强制转换转义字符
	 * @return string | array
	 */
	public function _addslashes($string, $force = 0) {
		global $db_type;
		if (! get_magic_quotes_gpc () || $force) {
			if (is_array ( $string )) {
				foreach ( $string as $key => $val ) {
					$string [$key] = $this->_addslashes ( $val, $force );
				}
			} else {
				$string = addslashes ( $string );
			}
		}
		return $string;
	}
	/**
	 * 取传来的参数值,防SQL注入
	 *
	 * @param unknown_type $key        	
	 * @param int $len        	
	 * @param unknown_type $def        	
	 */
	public function GetString($key, $len = 0, $def = null) {
		$_val = $_GET [$key] ? $_GET [$key] : $_POST [$key];
		if ($_val) {
			$_val = $this->_addslashes ( $_val );
			if ($len > 0) {
				return substr ( $_val, 0, $len );
			} else {
				return $_val;
			}
		} else if ($def) {
			return $def;
		} else {
			return null;
		}
	}
	public function GetStringNOaddslashes($key, $len = 0, $def = null) {
		$_val = $_GET [$key] ? $_GET [$key] : $_POST [$key];
		
		if ($_val) {
			if ($len > 0) {
				return substr ( $_val, 0, $len );
			} else {
				return $_val;
			}
		} else if ($def) {
			return $def;
		} else {
			return null;
		}
	}
	/**
	 * 向页面输出指定格式的字符串
	 *
	 * @param unknown_type $str        	
	 */
	public function Write($state = false, $msg = '', $data = array(), $format = 'json') {
		$format = $format?$format:'json';
		$_re = array (
				'state' => $state,
				'msg' => $msg,
				'data' => $data 
		);
		switch ($format) {
			case 'json' :
				echo json_encode ( $_re );

				break;
			case 'xml' :
				echo XML::Array2XML ( $_re );

				break;
		}
		exit;
	}
	/**
	 * 转换Ico文件代码为URL
	 * @param unknown_type $IcoFileCode
	 */
	public function IcoFileCode2Url($IcoFileCode,$Size){
		$re = '';
		if($IcoFileCode){
			$CodeArray = explode (',', $IcoFileCode);
			for($i=0;$i<count($CodeArray);$i++){
				$CodeArray[$i]=explode ('|', $CodeArray[$i]);
				if($CodeArray[$i][1]==$Size){
					$re = sprintf($this->config['FILE_SERVER_GET'],$CodeArray[$i][0],$CodeArray[$i][1]) ;
				}
			}
		}
		return $re;
	}
	/**
	 * 验证URL是否合法
	 * @param unknown $URL
	 * @return boolean
	 */
	function validateURL($url) {
		
		$url = trim($url);
		$up = parse_url($url);
		
		if (!$up || !$up['scheme'] || !$up['host']) {
			return false;
		}
		if (!( ($up['scheme'] == 'http') ||
		
				($up['scheme'] == 'https') ||
		
				($up['scheme'] == 'ftp')) ) {
		
			return false;
		
		}
		return true;
		//$url_quert = basename($url);
		
		//return ((strpos($url, "http://") === 0 || strpos($url, "https://") === 0 || strpos($url, "ftp://") === 0) && filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_QUERY_REQUIRED) !== false);
		/*
	if (filter_var($url, FILTER_VALIDATE_URL)) {
		   return true;
		} else {
		   return false;
		}*/
	}
	function urlConvert($url){
		$pathArr = array();
		$_m = parse_url($url);
		$path = $_m['path'];
		$pathSplit = explode('/', $path);
		 
		foreach ($pathSplit as $row){
			$pathArr[] = rawurlencode($row);
		}
		$urlNew = $_m['scheme']."://".$_m['host'].implode('/', $pathArr);
		return $urlNew;
	}
	
	/**
	 * 返回浏览器类型,判断手机类型
	 */
	public function is_mobile() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$mobile_agents = $this->config['MOBILE_AGENTS'];
		$is_mobile = false;
		foreach ($mobile_agents as $device) {
			if (stristr($user_agent, $device)) {
				$is_mobile = true;
				break;
			}
		}
		return $is_mobile;
	}
}
?>