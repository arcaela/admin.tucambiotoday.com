<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);
include(__DIR__."/functions.php");
spl_autoload_register(function ($name) {
	$extensions = array(
		__DIR__."/class/$name.class.php",
		__DIR__."/class/moment/$name.php",
		__DIR__."/class_helper/$name.class.php",
		__DIR__."/../api/v3/$name.php",
	);
	foreach ($extensions as $file) {
		if (!class_exists($name)&&file_exists($file)) {
			include($file);
		}
	}
});

function cache_add($key=null,$content=false,$expire=false){
	if (is_string($key)) {
		$expire = $expire ? strtotime($expire) : strtotime("+3 minutes");
		$content = is_bool($content) ? (!$content ? '' : 1) : (
			is_string($content) ? $content : ''
		);
		$folder = $_SERVER['DOCUMENT_ROOT'].'/cache/';
		if (!is_dir($folder)) {mkdir($folder, 0777, 'R');}
		$file = "$folder$key.$expire";
		dir::removeFile(glob("$folder$key.*", GLOB_BRACE));
		$fn = fopen($file, "a");
		$exec = fputs($fn,$content);
		fclose($fn);
		return !empty($exec) ? $content : false;
	}
	return false;
}
function cache_get($key=null){
	if (is_string($key)) {
		$folder = $_SERVER['DOCUMENT_ROOT'].'/cache/';
		$find = glob("$folder$key.*", GLOB_BRACE);
		if (count($find)<=0) {return false;}
		$file = end($find);
		$expire = explode('.', $file);
		$expire = end($expire);
		if (((strtotime("now")-$expire)>0)) {
			dir::removeFile($find);
			return false;
		}
		else{
			return file_get_contents($file);
		}
	}
	return false;
}
function cache_remove($key=null){
	if (is_string($key)) {
		$folder = $_SERVER['DOCUMENT_ROOT'].'/cache/';
		return dir::removeFile(glob("$folder$key.*", GLOB_BRACE));
	}
}
function cache($url=null,$expire='+3 minutes',$force=false){
	if (is_string($url)) {
		$key = md5($url);
		if (!empty($force)) {cache_remove($key);}
		if ($content=cache_get($key)) {return $content;}
		else{
			$ajax = new ajax($url);
			if ($ajax->start()) {return cache_add($key,$ajax->body,$expire) ? cache_get($key) : false; }
		}
	}
	return false;
}


function debuggHTML($text, $tags = '', $invert = FALSE) {
	$tags = explode(',', $tags);
	for ($i=0; $i<count($tags); $i++) { 
		$text = preg_replace("#(<{$tags[$i]}(.*?)>(.*?)</{$tags[$i]}>)#is", '', $text);
	}
	return $text;
}