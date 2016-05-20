<?php
// 数据过滤函数库
/*
 * 功能：用来过滤字符串和字符串数组，防止被挂马和sql注入 参数$data，待过滤的字符串或字符串数组，
 * $force为true，忽略get_magic_quotes_gpc
 */
function in($data, $force = false) {
	if (is_string ( $data )) {
		$data = trim ( htmlspecialchars ( $data ) ); // 防止被挂马，跨站攻击
		if (($force == true) || (! get_magic_quotes_gpc ())) {
			$data = addslashes ( $data ); // 防止sql注入
		}
		return $data;
	} else if (is_array ( $data )) 	// 如果是数组采用递归过滤
	{
		foreach ( $data as $key => $value ) {
			$data [$key] = in ( $value, $force );
		}
		return $data;
	} else {
		return $data;
	}
}

// 用来还原字符串和字符串数组，把已经转义的字符还原回来
function out($data) {
	if (is_string ( $data )) {
		return $data = stripslashes ( $data );
	} else if (is_array ( $data )) 	// 如果是数组采用递归过滤
	{
		foreach ( $data as $key => $value ) {
			$data [$key] = out ( $value );
		}
		return $data;
	} else {
		return $data;
	}
}

// 文本输入
function text_in($str) {
	$str = strip_tags ( $str, '<br>' );
	$str = str_replace ( " ", "&nbsp;", $str );
	$str = str_replace ( "\n", "<br>", $str );
	if (! get_magic_quotes_gpc ()) {
		$str = addslashes ( $str );
	}
	return $str;
}

// 文本输出
function text_out($str) {
	$str = str_replace ( "&nbsp;", " ", $str );
	$str = str_replace ( "<br>", "\n", $str );
	$str = stripslashes ( $str );
	return $str;
}

// html代码输入
function html_in($str) {
	$search = array (
			"'<script[^>]*?>.*?</script>'si", // 去掉 javascript
			"'<iframe[^>]*?>.*?</iframe>'si"  // 去掉iframe
	);
	$replace = array (
			"",
			"" 
	);
	$str = @preg_replace ( $search, $replace, $str );
	$str = htmlspecialchars ( $str );
	if (! get_magic_quotes_gpc ()) {
		$str = addslashes ( $str );
	}
	return $str;
}

// html代码输出
function html_out($str) {
	if (function_exists ( 'htmlspecialchars_decode' ))
		$str = htmlspecialchars_decode ( $str );
	else
		$str = html_entity_decode ( $str );
	
	$str = stripslashes ( $str );
	return $str;
}

// 获取客户端IP地址
function get_client_ip() {
	if (getenv ( "HTTP_CLIENT_IP" ) && strcasecmp ( getenv ( "HTTP_CLIENT_IP" ), "unknown" ))
		$ip = getenv ( "HTTP_CLIENT_IP" );
	else if (getenv ( "HTTP_X_FORWARDED_FOR" ) && strcasecmp ( getenv ( "HTTP_X_FORWARDED_FOR" ), "unknown" ))
		$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
	else if (getenv ( "REMOTE_ADDR" ) && strcasecmp ( getenv ( "REMOTE_ADDR" ), "unknown" ))
		$ip = getenv ( "REMOTE_ADDR" );
	else if (isset ( $_SERVER ['REMOTE_ADDR'] ) && $_SERVER ['REMOTE_ADDR'] && strcasecmp ( $_SERVER ['REMOTE_ADDR'], "unknown" ))
		$ip = $_SERVER ['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return $ip;
}

// 中文字符串截取
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
	switch ($charset) {
		case 'utf-8' :
			$char_len = 3;
			break;
		case 'UTF8' :
			$char_len = 3;
			break;
		default :
			$char_len = 2;
	}
	// 小于指定长度，直接返回
	if (strlen ( $str ) <= ($length * $char_len)) {
		return $str;
	}
	if (function_exists ( "mb_substr" )) {
		$slice = mb_substr ( $str, $start, $length, $charset );
	} else if (function_exists ( 'iconv_substr' )) {
		$slice = iconv_substr ( $str, $start, $length, $charset );
	} else {
		$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all ( $re [$charset], $str, $match );
		$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	}
	if ($suffix)
		return $slice . "…";
	return $slice;
}

// 检查是否是正确的邮箱地址，是则返回true，否则返回false
function is_email($user_email) {
	$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
	if (strpos ( $user_email, '@' ) !== false && strpos ( $user_email, '.' ) !== false) {
		if (preg_match ( $chars, $user_email )) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
// 模块之间相互调用
function module($module) {
	static $module_obj = array ();
	static $config = array ();
	if (isset ( $module_obj [$module] )) {
		return $module_obj [$module];
	}
	if (! isset ( $config ['MODULE_PATH'] )) {
		$config ['MODULE_PATH'] = DBOConfig::get ( 'MODULE_PATH' );
		$config ['MODULE_SUFFIX'] = DBOConfig::get ( 'MODULE_SUFFIX' );
		$suffix_arr = explode ( '.', $config ['MODULE_SUFFIX'], 2 );
		$config ['MODULE_CLASS_SUFFIX'] = $suffix_arr [0];
	}
	if (file_exists ( $config ['MODULE_PATH'] . $module . $config ['MODULE_SUFFIX'] )) {
		require_once ($config ['MODULE_PATH'] . $module . $config ['MODULE_SUFFIX']); // 加载模型文件
		$classname = $module . $config ['MODULE_CLASS_SUFFIX'];
		if (class_exists ( $classname )) {
			return $module_obj [$module] = new $classname ();
		}
	}
	return false;
}

// 模型调用函数
function model($model) {
	static $model_obj = array ();
	static $config = array ();
	if (isset ( $model_obj [$model] )) {
		return $model_obj [$model];
	}
	if (! isset ( $config ['MODEL_PATH'] )) {
		$config ['MODEL_PATH'] = DBOConfig::get ( 'MODEL_PATH' );
		$config ['MODEL_SUFFIX'] = DBOConfig::get ( 'MODEL_SUFFIX' );
		$suffix_arr = explode ( '.', $config ['MODEL_SUFFIX'], 2 );
		$config ['MODEL_CLASS_SUFFIX'] = $suffix_arr [0];
	}
	if (file_exists ( $config ['MODEL_PATH'] . $model . $config ['MODEL_SUFFIX'] )) {
		require_once ($config ['MODEL_PATH'] . $model . $config ['MODEL_SUFFIX']); // 加载模型文件
		$classname = $model . $config ['MODEL_CLASS_SUFFIX'];
		if (class_exists ( $classname )) {
			return $model_obj [$model] = new $classname ();
		}
	}
	return false;
}
// 检查字符串是否是UTF8编码,是返回true,否则返回false
function is_utf8($string) {
	return preg_match ( '%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $string );
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from = 'gbk', $to = 'utf-8') {
	$from = strtoupper ( $from ) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper ( $to ) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper ( $from ) === strtoupper ( $to ) || empty ( $fContents ) || (is_scalar ( $fContents ) && ! is_string ( $fContents ))) {
		// 如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string ( $fContents )) {
		if (function_exists ( 'mb_convert_encoding' )) {
			return mb_convert_encoding ( $fContents, $to, $from );
		} elseif (function_exists ( 'iconv' )) {
			return iconv ( $from, $to, $fContents );
		} else {
			return $fContents;
		}
	} elseif (is_array ( $fContents )) {
		foreach ( $fContents as $key => $val ) {
			$_key = auto_charset ( $key, $from, $to );
			$fContents [$_key] = auto_charset ( $val, $from, $to );
			if ($key != $_key)
				unset ( $fContents [$key] );
		}
		return $fContents;
	} else {
		return $fContents;
	}
}

// 浏览器友好的变量输出
function dump($var, $exit = false) {
	ob_start ();
	var_dump ( $var );
	$output = ob_get_clean ();
	if (! extension_loaded ( 'xdebug' )) {
		$output = preg_replace ( "/\]\=\>\n(\s+)/m", "] => ", $output );
		$output = '<pre>' . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
	}
	echo $output;
	
	if ($exit) {
		exit (); // 终止程序
	} else {
		return;
	}
}

// 获取微秒时间，常用于计算程序的运行时间
function utime() {
	list ( $usec, $sec ) = explode ( " ", microtime () );
	return (( float ) $usec + ( float ) $sec);
}

// 生成唯一的值
function cp_uniqid() {
	return md5 ( uniqid ( rand (), true ) );
}
// 加密函数，可用cp_decode()函数解密，$data：待加密的字符串或数组；$key：密钥；$expire 过期时间
function cp_encode($data, $key = '', $expire = 0) {
	$string = serialize ( $data );
	$ckey_length = 4;
	$key = md5 ( $key );
	$keya = md5 ( substr ( $key, 0, 16 ) );
	$keyb = md5 ( substr ( $key, 16, 16 ) );
	$keyc = substr ( md5 ( microtime () ), - $ckey_length );
	
	$cryptkey = $keya . md5 ( $keya . $keyc );
	$key_length = strlen ( $cryptkey );
	
	$string = sprintf ( '%010d', $expire ? $expire + time () : 0 ) . substr ( md5 ( $string . $keyb ), 0, 16 ) . $string;
	$string_length = strlen ( $string );
	$result = '';
	$box = range ( 0, 255 );
	
	$rndkey = array ();
	for($i = 0; $i <= 255; $i ++) {
		$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
	}
	
	for($j = $i = 0; $i < 256; $i ++) {
		$j = ($j + $box [$i] + $rndkey [$i]) % 256;
		$tmp = $box [$i];
		$box [$i] = $box [$j];
		$box [$j] = $tmp;
	}
	
	for($a = $j = $i = 0; $i < $string_length; $i ++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box [$a]) % 256;
		$tmp = $box [$a];
		$box [$a] = $box [$j];
		$box [$j] = $tmp;
		$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
	}
	return $keyc . str_replace ( '=', '', base64_encode ( $result ) );
}
// cp_encode之后的解密函数，$string待解密的字符串，$key，密钥
function cp_decode($string, $key = '') {
	$ckey_length = 4;
	$key = md5 ( $key );
	$keya = md5 ( substr ( $key, 0, 16 ) );
	$keyb = md5 ( substr ( $key, 16, 16 ) );
	$keyc = substr ( $string, 0, $ckey_length );
	
	$cryptkey = $keya . md5 ( $keya . $keyc );
	$key_length = strlen ( $cryptkey );
	
	$string = base64_decode ( substr ( $string, $ckey_length ) );
	$string_length = strlen ( $string );
	
	$result = '';
	$box = range ( 0, 255 );
	
	$rndkey = array ();
	for($i = 0; $i <= 255; $i ++) {
		$rndkey [$i] = ord ( $cryptkey [$i % $key_length] );
	}
	
	for($j = $i = 0; $i < 256; $i ++) {
		$j = ($j + $box [$i] + $rndkey [$i]) % 256;
		$tmp = $box [$i];
		$box [$i] = $box [$j];
		$box [$j] = $tmp;
	}
	
	for($a = $j = $i = 0; $i < $string_length; $i ++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box [$a]) % 256;
		$tmp = $box [$a];
		$box [$a] = $box [$j];
		$box [$j] = $tmp;
		$result .= chr ( ord ( $string [$i] ) ^ ($box [($box [$a] + $box [$j]) % 256]) );
	}
	if ((substr ( $result, 0, 10 ) == 0 || substr ( $result, 0, 10 ) - time () > 0) && substr ( $result, 10, 16 ) == substr ( md5 ( substr ( $result, 26 ) . $keyb ), 0, 16 )) {
		return unserialize ( substr ( $result, 26 ) );
	} else {
		return '';
	}
}
// 遍历删除目录和目录下所有文件
function del_dir($dir) {
	if (! is_dir ( $dir )) {
		return false;
	}
	$handle = opendir ( $dir );
	while ( ($file = readdir ( $handle )) !== false ) {
		if ($file != "." && $file != "..") {
			is_dir ( "$dir/$file" ) ? del_dir ( "$dir/$file" ) : @unlink ( "$dir/$file" );
		}
	}
	if (readdir ( $handle ) == false) {
		closedir ( $handle );
		@rmdir ( $dir );
	}
}
// 如果json_encode没有定义，则定义json_encode函数，常用于返回ajax数据
if (! function_exists ( 'json_encode' )) {
	function format_json_value(&$value) {
		if (is_bool ( $value )) {
			$value = $value ? 'true' : 'false';
		} else if (is_int ( $value )) {
			$value = intval ( $value );
		} else if (is_float ( $value )) {
			$value = floatval ( $value );
		} else if (defined ( $value ) && $value === null) {
			$value = strval ( constant ( $value ) );
		} else if (is_string ( $value )) {
			$value = '"' . addslashes ( $value ) . '"';
		}
		return $value;
	}
	
	function json_encode($data) {
		if (is_object ( $data )) {
			// 对象转换成数组
			$data = get_object_vars ( $data );
		} else if (! is_array ( $data )) {
			// 普通格式直接输出
			return format_json_value ( $data );
		}
		// 判断是否关联数组
		if (empty ( $data ) || is_numeric ( implode ( '', array_keys ( $data ) ) )) {
			$assoc = false;
		} else {
			$assoc = true;
		}
		// 组装 Json字符串
		$json = $assoc ? '{' : '[';
		foreach ( $data as $key => $val ) {
			if (! is_null ( $val )) {
				if ($assoc) {
					$json .= "\"$key\":" . json_encode ( $val ) . ",";
				} else {
					$json .= json_encode ( $val ) . ",";
				}
			}
		}
		if (strlen ( $json ) > 1) { // 加上判断 防止空数组
			$json = substr ( $json, 0, - 1 );
		}
		$json .= $assoc ? '}' : ']';
		return $json;
	}
}
function json_to_array($json) {
	$arr = array ();
	foreach ( $json as $k => $w ) {
		if (is_object ( $w ))
			$arr [$k] = json_to_array ( $w ); // 判断类型是不是object
		else
			$arr [$k] = $w;
	}
	return $arr;
}

/**
 * 十进制数转换成其它进制
 * 可以转换成2-62任何进制
 *
 * @param integer $num
 * @param integer $to
 * @return string
 */
function dec_to($num, $to = 62) {
	if ($to == 10 || $to > 62 || $to < 2) {
		return $num;
	}
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$ret = '';
	do {
		$ret = $dict[bcmod($num, $to)] . $ret;
		$num = bcdiv($num, $to);
	} while ($num > 0);
	return $ret;
}

/**
 * 其它进制数转换成十进制数
 * 适用2-62的任何进制
 *
 * @param string $num
 * @param integer $from
 * @return number
 */
function dec_from($num, $from = 62) {
	if ($from == 10 || $from > 62 || $from < 2) {
		return $num;
	}
	$num = strval($num);
	$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen($num);
	$dec = 0;
	for($i = 0; $i < $len; $i++) {
		$pos = strpos($dict, $num[$i]);
		if ($pos >= $from) {
			continue; // 如果出现非法字符，会忽略掉。比如16进制中出现w、x、y、z等
		}
		$dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
	}
	return $dec;
}

// 直接跳转
function redirect($url) {
	header ( 'location:' . $url, false, 301 );
	exit ();
}
?>