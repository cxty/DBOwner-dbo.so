<?php
//ini_set('display_errors', true);
//error_reporting(E_ALL);
class indexMod extends commonMod {

	public function __construct() {
		parent::__construct ();

	}
	public function index() {
	//print_r($_SERVER);
		// 字符串
		$title = "";
		$_url_shortener = "";
		$_id = 0;
		$_format = $this->GetString("format");
		$_url = $this->GetStringNOaddslashes("url");
		$_shore_code = $this->GetString("key");

		$QRCode_leve = 'M';
		$QRCode_size = 4;
		
		$this->assign ( 'title', $title );
		
		
		// 系统是否有记录
		$_db = $this->RequireClass ( 'URLInfo', $this->model, $this->config );

		if (isset ( $_db )) {

			if(trim($_url)!=''){//URL缩短

				if($this->validateURL($_url)){
					
					$_url_code = md5(strtolower(trim($_url)));//URL转MD5

					$_url_info = $_db->Exist ( $_url_code );

					if($_url_info){
						$_id = $_url_info["URLID"];
					}else{
						
						//对参数进行处理
						$_c_url = explode('?',$_url);
						
						if(count($_c_url)>1)
						{
							parse_str($_c_url[1], $output);
							$_url = $_c_url[0].'?'.http_build_query($output);
						}
						
						$_id = $_db->Insert($_url_code,$_url,time (),0,0);
						//判断是否会存在模块冲突,存在冲突的删除后重新创建
						if(in_array(dec_to($_id),$this->config['NO_S_URL_MOD'])){
							$_db->Delete(array('URLID'=>$_id));
							$_id = $_db->Insert($_url_code,$_url,time (),0,0);
						}
					}

					$_url_shortener = sprintf($this->config['s_url'],dec_to($_id)) ;

					$_QRCODE_DIR = $this->config['QRCODE_DIR'].date("Ymd").'/';
					$_QRCODE_PATH = ROOT_PATH.$_QRCODE_DIR;
					
					if(!is_dir($_QRCODE_PATH)){
					    mkdir($_QRCODE_PATH, 0777, true);
					}
					
					//QRCode
					require_once (DBO_PATH . 'ext/qrcode/qrcode.class.php');
					$qrcode = new RQcode ();
					$qrcode->PNG_TEMP_DIR = $_QRCODE_PATH;
					$qrcode->PNG_WEB_DIR = $_QRCODE_DIR;

					$_QRCode = sprintf($this->config['qrcode_url'],$qrcode->encode($_url_shortener,$Code_leve,$QRCode_size));
					
					//API调用
					if($_format){
						parent::Write (true,"OK",array("s_url"=>$_url_shortener,"qrcode"=>$_QRCode),$_format);
					}else{

						$this->assign ( 's_url', $_url_shortener );

						$this->assign ( 'url', $_url );
						
						$this->assign( 'qrcode', $_QRCode);

						$this->display (); // 输出默认模板
					}
				}else{
					parent::Write (false,"URL Error!",array("s_url"=>$_url),$_format);
				}
			}else if(trim($_shore_code)!=""){//短地址转长地址
				$_id = dec_from($_shore_code);
				$_url_info = $_db->Get ( $_id );
				if($_url_info){
					$_url = $_url_info["URLStr"];
					$_uCount = (int)$_url_info["uCount"];
					//更新记录
					$_db->Update($_url_info["URLID"],
							$_url_info["uCode"],
							$_url_info["URLStr"],
							$_url_info["uAppendTime"],
							1+$_uCount,
							$_url_info["uState"]);
					//加统计分析
					$_db = $this->RequireClass ( 'ClientLogInfo', $this->model,  $this->config );
					$_db->Insert($_id,
							get_client_ip(),
							json_encode($_SERVER),
							time());
				}
				

				//API调用
				if($_format){
					parent::Write (true,"OK",array("l_url"=>$_url),$_format);
				}else{
					//跳转
					$this->redirect($_url);
				}
			}else{
				$this->display (); // 输出默认模板
			}

		}
		
		
	}
	
	/**
	 * 获取客户端ip
	 */
	function getClientIP() {
		$ip = "unknown";
		/*
		 * 访问时用localhost访问的，读出来的是“::1”是正常情况。
		* ：：1说明开启了ipv6支持,这是ipv6下的本地回环地址的表示。
		* 使用ip地址访问或者关闭ipv6支持都可以不显示这个。
		* */
	if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) {
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} else if (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
			$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER ['REMOTE_ADDR'];
		}
		return $ip;
	}
}
?>