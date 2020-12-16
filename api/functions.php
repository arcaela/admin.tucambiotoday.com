<?php
	include(__DIR__."/../system/autoload.php");

	function bypass($find=array(),$in=array()){
		return (count(array_diff($find,array_keys($in)))==0);
	}
	function error_helper($code=null){
		$list = array(
			'EMPTY_FIELDS'=>'Algun campo se encuentra vacio',

			'EMAIL_0'=>'Direccion de correo invalida',
			'EMAIL_ADDED'=>'E-mail ya registrado',
			'EMAIL_EXIST'=>'E-mail ya registrado',

			'ACCOUNT_404'=>'La cuenta solicitada no existe',
			
			'USER_0'=>'Formato invalido para nombre de usuario',
			'USER_404'=>'El usuario no existe',
			'USER_EXIST'=>'El usuario ya se encuentra registrado',
			'USER_ADDED'=>'Usuario registrado',

			'TOKEN_0'=>'Token de acceso errado',
			'PIN_0'=>'PIN de seguridad errado',
		);
		return !empty($list[$code]) ? $list[$code] : $code;
	}
	function decodeQueryError($string=false){
		if (is_string($string)) {
			$string = preg_replace("([^a-zA-Z0-9 .,;'\"_-])", "", $string);
			$algoritmos = array(
				array(
					'/^Entrada duplicada (\w+) para la clave (\w+)/i',
					'${1} ya esta registrado.',
				),
				array(
					'/^SQL_(\d+)/i',
					'Problema interno, codigo de error: #N${1}KPS-${1}',
				),
				array(
					'/^(\w+)_SQL_(\d+)/i',
					'Problema interno, codigo de error: #N${2}KPS-${1}',
				),
			);
			foreach ($algoritmos as $key) {
				$string = preg_replace($key[0], $key[1], $string);
			}
		}
		return $string;
	}
	function formValidation($fields=array()){
		$error = false;
		$fields = is_array($fields) ? $fields : array();
		$fields = array_combine(array_map('strtolower', array_keys($fields)), array_values($fields));
		foreach ($fields as $key => $value) {
			$value = !empty($value) ? (
				(strtolower($value)=='true') ? true : (
					(strtolower($value)=='false') ? false : $value
				)
			) : false;
			$error = empty($error) ? (
				($key=='email'&&!isEmail($value)) ? 'EMAIL_0' : (
					($key=='username'&&strlen(preg_replace("(\w+)", '', $value))>0) ? 'USER_0' : (
						($key=='access_token'&&(empty($value)||!security($value))) ? 'TOKEN_0' : (
							($key=='pin'&&strlen(preg_replace("(\D+)", "", $value))<6) ? 'PIN_0' : false
						)
					)
				)
			) : $error;
		}
		return $error;
	};
	function head($files=array()){
		$tags = json::decode(FileManager::read(COGS['cogs_folder'].'headers.json'))['tags'];
		foreach ($tags as $tag) {
			echo "\n	<{$tag['tag']} ". implode(' ', array_map(function ($k, $v) {
				return $k .'="'. htmlspecialchars($v) .'"';
			},array_keys($tag), $tag)).' />';
		}
		compressFiles($files);
	}
