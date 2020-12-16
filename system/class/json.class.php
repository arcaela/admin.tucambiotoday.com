<?php
class json{
	function __construct($string=false) {
		$this->string = $string;
	}

	public static function is_json($json=false,$partial=false){
		try{
			return is_string($json) ? json_decode($json,$partial) : false;
		}catch(Exception $e){return false;}
	}

	public static function encode($object=array(),$print=false,$pretty=true){
		if (!function_exists('clear')) {
			function clear($data=''){
				return is_array($data) ? array_map(function($t){
					return clear($t);
				}, $data) : (
					is_string($data) ? utf8_encode($data) : $data
				);
			}
		}
		$object = json_encode((Array)$object, (!empty($pretty) ? JSON_PRETTY_PRINT : false));
		switch ($print) {
			case true:
				print_r($object);
				break;
			default:
				return $object;
				break;
		}
	}
	
	public static function decode($json=false,$partial=true){
		try {
			$s = is_object($json) ? (Array)$json : (is_array($json) ? $json : json_decode($json,$partial));
			return empty($s) ? array() : $s;
		} catch (Exception $e) {return array();}
	}

}