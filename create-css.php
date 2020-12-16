<?php
	header("Content-Type: text/css");
	$folder = './system/assets/css/';
	$model = 'mobile.css';
	/*Nunca incluir el mobile.css en la lista de los archivos en $files*/
	$files = array(
		'tablet.css',
		'touch.css',
		'desktop.css',
		'widescreen.css',
		'fullhd.css',
		'basic.css'
	);
	if (is_file($folder.$model)) {
		$css = file_get_contents($folder.$model);
		$base = explode('.', $model)[0];
		foreach ($files as $name) {
			$path = $folder.$name;
			$device = explode('.', $name)[0];
			if ($name!=$model&&$name!='mobile.css') {
				if(is_file($path)) unlink($path);
				$fn = fopen($path, "a");
				$rp = $device=='basic' ? '' : '-'.$device;
				$cn = str_replace('-'.$base, $rp, $css);
				fputs($fn,$cn);
				fclose($fn);
			}
		}
	}