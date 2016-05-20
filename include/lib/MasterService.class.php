<?php
/**
 * 后台管理服务根
 * @author Cxty
 *
 */
class MasterService  extends Service{

	public $user = '';
	public $pwd = '';
	public $iv = '';
	public $authorized = false;
	public $ClientIP = '';
	public $model;
	public $config;
	
	public function __construct($config)
	{
		parent::__construct();
		$this->config = $config;
		$this->user=$config['SOAP_SERVER_USER'];
		$this->pwd=$config['SOAP_SERVER_PWD'];
		$this->iv = $config['SOAP_SERVER_IV'];
		
		$this->ClientIP = Fun::GetIP();

		// 数据库模型初始化
		if (! isset ( $this->model )) {
			$this->model= new DBOModel ( $this->config ); // 实例化数据库模型类
		}
		
		if(!in_array($this->ClientIP,$config['SOAP_SERVER_CLIENTIP']) )
		{
			$this->authorized = false;
			return $this->Unauthorized_IP;
			//throw new SoapFault('Unauthorized IP', '443');
		}
	}
	/**
	 * 接口鉴权
	 * @param array $a
	 * @throws SoapFault
	 */
	public function Auth($a)
	{
		if($a->user === $this->user)
		{
			$this->authorized = true;
			return $this->_return(true, 'OK', null);
		}else{
			return $this->Unauthorized_User;
		}
	}
	/**
	 * 负责data加密
	 * @see Service::_return()
	 */
	public function _return( $state, $msg, $data)
	{
		if(isset($data))
		{
			return parent::_return($state, $msg,$this->_encrypt(json_encode(array('data'=>$data)),$this->pwd,$this->iv));
		}else{
			return parent::_return($state, $msg,$data);
		}
	}
	/**
	 * 负责解密data,还原客户端传来的参数
	 * @param unknown_type $data
	 */
	public function _value($data)
	{
		if(isset($data))
		{
			return json_decode(trim($this->_decrypt($data,$this->pwd,$this->iv)));
		}else{
			return $data;
		}
	}
	/**
	 * 记录系统日志
	 * @param int slActType 读取=0,添加=1,删除=2,修改=3
	 * @param string Mode 模块,表明
	 */
	public function AddSysLog($slActType=0,$Mode="",$slActParameter="",$slReCallValue="")
	{
		try{
			if ($this->model ) {
				$SysLog = $this->RequireClass ( 'SysLogInfo', $this->model, $this->config );
				
				$SysLog->Insert($slActType,urlencode($Mode."=>".$slActParameter),urlencode($slReCallValue),time());
			}
		}catch(Exception $ex){
			DBOError::write($ex);
		}
	}
	
}

?>