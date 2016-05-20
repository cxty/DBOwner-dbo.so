<?php
// xml解析成数组
class Xml {
	public static function decode($xml) {
		$values = array ();
		$index = array ();
		$array = array ();
		$parser = xml_parser_create ( 'utf-8' );
		xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parse_into_struct ( $parser, $xml, $values, $index );
		xml_parser_free ( $parser );
		$i = 0;
		$name = $values [$i] ['tag'];
		$array [$name] = isset ( $values [$i] ['attributes'] ) ? $values [$i] ['attributes'] : '';
		$array [$name] = self::_struct_to_array ( $values, $i );
		return $array;
	}
	
	private static function _struct_to_array($values, &$i) {
		$child = array ();
		if (isset ( $values [$i] ['value'] ))
			array_push ( $child, $values [$i] ['value'] );
		
		while ( $i ++ < count ( $values ) ) {
			switch ($values [$i] ['type']) {
				case 'cdata' :
					array_push ( $child, $values [$i] ['value'] );
					break;
				
				case 'complete' :
					$name = $values [$i] ['tag'];
					if (! empty ( $name )) {
						$child [$name] = ($values [$i] ['value']) ? ($values [$i] ['value']) : '';
						if (isset ( $values [$i] ['attributes'] )) {
							$child [$name] = $values [$i] ['attributes'];
						}
					}
					break;
				
				case 'open' :
					$name = $values [$i] ['tag'];
					$size = isset ( $child [$name] ) ? sizeof ( $child [$name] ) : 0;
					$child [$name] [$size] = self::_struct_to_array ( $values, $i );
					break;
				
				case 'close' :
					return $child;
					break;
			}
		}
		return $child;
	}
	public static function Array2XML($array, $xslName = "") {
		
		$XMLString = '<?xml version="1.0" encoding="utf-8"?>';
		
		if ($xslName != "")
			
			$XMLString .= '<?xml-stylesheet type="text/xsl" href="' . $xslName . '"?>';
		
		$XMLString .= $this->make ( $array );
		
		return $XMLString;
	
	}
	/*
	 * 递归生成XML字串
	 */
	
	private static function make($array) 

	{
		
		$XMLString = '';
		
		$haveRightBracket = FALSE;
		
		if (isset ( $array ['elementName'] )) {
			
			$elementName = array_shift ( $array ); // 数组的第一个元素为XML元素名
		
		} else {
			
			$elementName = 'item'; // 如果没有指定则元素名为item
		
		}
		
		$XMLString .= '<' . $elementName . ' ';
		
		if (is_array ( $array )) {
			
			foreach ( $array as $paramKey => $nodeParam ) {
				
				if (! is_array ( $nodeParam )) {
					
					// 如果不是一个下级元素，那就是元素的参数
					
					$XMLString .= $paramKey . '="' . $nodeParam . '" ';
				
				} else {
					
					if (! $haveRightBracket) {
						
						$XMLString .= '>';
						
						$haveRightBracket = TRUE;
					
					}
					
					// 如果是下级元素，则追加元素
					
					$XMLString .= $this->make ( $nodeParam );
				
				}
			
			}
		
		}
		
		if (! $haveRightBracket) {
			
			$XMLString .= '>';
			
			$haveRightBracket = TRUE;
		
		}
		
		$XMLString .= '</' . $elementName . '>'; // 该元素处理结束
		
		return $XMLString;
	
	}
}
?>