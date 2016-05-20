<?php

class noddos {
	//防ddos代码
	//参数配置
	static $AUTH_INTERVAL=600;//下一次访问时间的时间间隔，单位：秒
	static $TOKEN_DEFAULT=100;//默认的令环牌数
	static $SERVICE_DENIAL = "SERVICE DENIAL";//认为是ddos攻击，终止服务前输出的信息
	static $COOKIE_PREFIX='v3_';//cookie前缀
	static $COOKIE_EXPIRE=3600;//cookie过期时间，单位为秒
	
	public function __construct() {
		
	}
	
	public function run(){
		if($this->defense_ddos($this->AUTH_INTERVAL,$this->TOKEN_DEFAULT,$this->COOKIE_PREFIX,$this->COOKIE_EXPIRE))
		{
			die($this->SERVICE_DENIAL);//认定是ddos攻击，退出程序
		}
	}
	public function defense_ddos($interval=60,$token_default=10,$cookie_prefix='v3',$cookie_expire=3600)
	{
		$now_time=time();//获取当前时间
		$next_auth_name=$cookie_prefix.'next_auth';//下一次访问时间，cookie变量名
		$token_name=$cookie_prefix.'token';//令环牌，cookie变量名
		$cookie_expires=$now_time+$cookie_expire;
		$next_time=$now_time+$interval;
		 
		//第一次访问或者cookie已经过期，重置令环牌和下一次访问时间
		if(empty($_COOKIE[$next_auth_name]))
		{
			$token=$token_default;
			setcookie($token_name,$token,$cookie_expires,'/');
			setcookie($next_auth_name,$next_time,$cookie_expires,'/');
			return false;
		}
		else if($now_time<$_COOKIE[$next_auth_name])//恶意访问
		{
			//如果令环牌大于0，扣令环牌，否则认定是ddos攻击
			if($_COOKIE[$token_name]>0)
			{
				$token=$_COOKIE[$token_name]-1;
				setcookie($token_name,$token,$cookie_expires,'/');
				return false;
			}
			else
			{
				return true;//认定是ddos攻击，返回true
			}
		}
		else
		{
			//正常访问，如果令环牌，小于默认值，则加一次令环牌
			if($_COOKIE[$token_name]<$token_default)
			{
				$token=$_COOKIE[$token_name]+1;
				setcookie($token_name,$token,$cookie_expires,'/');
			}
			setcookie($next_auth_name,$next_time,$cookie_expires,'/');
			return false;
		}
	}
}

?>