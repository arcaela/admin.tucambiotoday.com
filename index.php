<?php
	include ('./api/functions.php');
	define('LANG', "es");
	define('COGS', array(
		'pages_folder'=>__DIR__.'/pages/',
		'page_error'=>'error.php',
		'cogs_folder'=>__DIR__.'/cogs/',
	));
	/*Estudiamos el fichero que vamos a presentar*/
	$page = COGS['pages_folder'].(empty($_GET['page']) ? 'index.php' : (
		file_exists(COGS['pages_folder'].$_GET['page'].'.php') ? $_GET['page'].'.php' : 'error.php'
	));
	/*Creamos el objeto de renderizado*/
	function render($str='',$array=null){$array = is_array($array) ? $array : defined("RENDER") ? RENDER : array(); return str_replace(array_keys($array), array_values($array), $str); }
	define('RENDER', varCache('RENDER',function($key){
	return array_map_assoc(function ($k, $v) {
	   return [strtoupper('%{'.$k.'}%'), $v];
			}, array_merge(
			host('array'),
			json::decode(FileManager::read(COGS['cogs_folder'].'lang/'.LANG.'.json')),
			json::decode(FileManager::read(COGS['cogs_folder'].'business.json'))
		));
	},30));
	/*Preparamos el fichero que vamos a trabajar*/
	function depure($buffer=''){
		return preg_replace_callback("/\%\{?(\w+)\}?\%/", function($m){
			return strtoupper($m[0]);
		}, $buffer);
	}
	/* Funcion init se encargará de renderizar todo el buffer almacenado*/
	function init($html){return render(depure($html)); }
	if (!is_null($page)) {
		ob_start('init');
		include($page);
		ob_end_flush();
	}