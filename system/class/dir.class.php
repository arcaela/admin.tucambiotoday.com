<?php
/*INICIO | Crear y eliminar directorios desde una clase, aplicando permisos de lectura y/o escritura*/
/*
Para hacer uso de esta clase es solo cuestion de iniciarla new dir(); luego hacer uso de setDir() para especificar el directorio que deseamos trabajar y 
durante toda la instancia de la clase vamos a utilizar ese directorio, si desemos modificarlo será solo cuestion de hacer llamado de setDir() nuevamente,
para crear el directorio llamamos class->create() y se creará con los permisos por defecto 0777 si deseamos modificarlos especificamos class->create(permisos).
Para eliminar el directorio debes enviar la llamada a class->remove().
Todas las peticiones modifican el status de la clase, dejando status en "success" o "error", en caso de retornar "error" puedes leer el mensaje del error en class->error;
*/
class dir{
	public static function createDir($path=false,$permissions=0777,$recursive=true) {
		return is_dir($path) ? $path : (
			mkdir($path, 0755, true) ? $path : $path
		);
	}
	public static function removeDir($dir) {
		if (is_array($dir)) {
			$dirs = array_values($dir);
			foreach ($dirs as $dir) {
				dir::removeDir($dir);
			}
		}
		else{
			foreach(scandir($dir) as $file) {
				if ('.' === $file || '..' === $file) {continue;}
				if (is_dir($dir."/".$file)){ dir::removeDir($dir."/".$file);}
				else {unlink($dir."/".$file);}
			}
			return rmdir($dir);
		}
	}
	public static function removeFile($file){
		if (is_array($file)) {
			$files = array_values($file);
			foreach ($files as $file) {
				dir::removeFile($file);
			}
		}
		else if(file_exists($file)){return (unlink($file)); }
	}
}
/*FIN | Crear y eliminar directorios desde una clase, aplicando permisos de lectura y/o escritura*/
