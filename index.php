<?php


@date_default_timezone_set('PRC');//定义时区，校正时间为北京时间
//@set_magic_quotes_runtime(0);
if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
	preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);//获取客户端语言
	$lang = $matches[1];
}

define('ROOT_PATH',dirname(__FILE__));
define('DBO_CONF_PATH',dirname(__FILE__).'/conf/');//系统目录
define('DBO_PATH',dirname(__FILE__).'/include/');//系统目录
define('DBO_TEMPLATES_PATH',dirname(__FILE__).'/templates/');//模板目录

require(DBO_CONF_PATH.'/config.php');//加载配置
require(DBO_PATH.'core/DBOApp.class.php');//加载应用控制类

$app=new DBOApp($config);//实例化单一入口应用控制类

//$noddos = new noddos();//防ddos攻击
//$noddos->run();

//执行项目
$app->run();



?>