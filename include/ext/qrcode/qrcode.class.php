<?php


class RQcode {
	
	public $PNG_TEMP_DIR = null;
	public $PNG_WEB_DIR = null;
	
	public function __construct() {
		$this->PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
		$this->PNG_WEB_DIR = 'temp/';
		
		include "qrlib.php";
		
		if (!file_exists($this->PNG_TEMP_DIR))
			mkdir($this->PNG_TEMP_DIR);
		
		
		$filename = $this->PNG_TEMP_DIR.'qrcode.png';
		
	}
	
	/**
	 * 生成QRCode
	 * @param unknown_type $data
	 * @param unknown_type $level L,M,Q,H
	 * @param unknown_type $size 1-10
	 */
	public function encode($data,$level,$size){
		
		$filename = $this->PNG_TEMP_DIR.'qr'.md5($data.'|'.$level.'|'.$size).'.png';
		
		QRcode::png($data, $filename, $level, $size, 2);
		
		return $this->PNG_WEB_DIR.basename($filename);
	}
	
}

?>