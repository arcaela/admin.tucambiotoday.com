<?php
class fileManager{
	public $cog = false;
	function __construct(){
		$this->cog = array(
			'path'=>fix_path(__DIR__.'/upload/'),
			'rename'=>true,
		);
	}
	function path($path=false){
		$this->cog['path'] = is_string($path) ? fix_path($path) : $this->cog['path'];
	}


	public function add($file=false,$cog=array()){
		$cog = is_array($cog) ? $cog : array();
		$case = (is_string($file)&&filter_var($file,FILTER_VALIDATE_URL)) ? 'url' : false;
		$case = is_array($file)&&!empty($file['tmp_name']) ? 'upload' : $case;
		$case = is_string($file)&&strstr($file,'base64') ? 'base64' : $case;
		switch ($case) {
			case 'upload':
				for ($i=0; $i < count($file['tmp_name']); $i++) { 
					$this->files[] = array(
						'type'=>'upload',
						'name' => $file['name'][$i],
						'tmp_name' => $file['tmp_name'][$i],
						'size' => $file['size'][$i],
					);
				}
			break;
			default:
				$this->files[] = array_merge(array(
					'type'=>$case,
					'content'=>$file,
				),$cog);
				break;
		}
	}

	public static function mime(){
		if (empty($mime_array)) {
			include(__DIR__.'/../utilities/mimes-types.php');
		}
		return $mime_array;
	}




	public static function url_upload($file=null,$cog=array()){
		$file = array_merge(array(
			'path'=>fix_path(__DIR__.'/upload/'),
		), (is_array($cog) ? $cog : array()), array(
			'url'=>is_string($file) ? $file : false
		));
		$file['path'] = dir::createDir(fix_path($file['path']));
		$opt = new ajax($file['url']);
		$opt->cache(false);
		if ($opt->start()) {
			$name = $file['path'].DIRECTORY_SEPARATOR.md5($file['url']).'.'.fileManager::mime()['extensions'][$opt->mime][0];
			$fn = fopen($name, "a");
			if (!fputs($fn,$opt->body)) {
				$name = false;
				dir::removeFile($name);
			}
			fclose($fn);
		}
		return !empty($name) ? $name : false;
	}

	public static function base64_upload($file=null,$cog=array()){
		$file = array_merge(array(
			'path'=>fix_path(__DIR__.'/upload/'),
		), (is_array($cog) ? $cog : array()), array(
			'url'=>is_string($file) ? $file : false
		));
		$file['path'] = dir::createDir(fix_path($file['path']));
		$name = $file['path'].DIRECTORY_SEPARATOR.md5($file['url']).'.'.fileManager::mime()['extensions'][preg_replace("/data\:(\w+)(\/|\\+)(\w+)\;base64\,(.+)/", "$1$2$3", $file['url'])][0];
		$fn = fopen($name, "a");
		if (!fputs($fn,base64_decode(explode(',',$file['url'])[1]))) {
			$name = false;
			dir::removeFile($name);
		}
		fclose($fn);
		return !empty($name) ? $name : false;
	}


	public static function file_upload($file=null,$cog=array()){
		$file = (!is_array($file['tmp_name'])) ? array_map(function($v){
			return array($v);
		}, $file) : $file;
		$cog = array_merge(array(
			'path'=>__DIR__.'/upload/',
		),(is_array($cog) ? $cog : array()));
		$cog['path'] = dir::createDir(fix_path($cog['path']));
		$info = new finfo(FILEINFO_MIME);
		$files = array();
		for ($i=0; $i < count($file['tmp_name']); $i++) { 
			$name = fix_path($cog['path']).DIRECTORY_SEPARATOR.md5($file['tmp_name'][$i]).'.'.fileManager::mime()['extensions'][preg_replace("/(\w+)(\/|\\+)(\w+)\;(.+)/", "$1$2$3", $info->file($file['tmp_name'][$i]))][0];
			if(move_uploaded_file($file['tmp_name'][$i], $name)){
				$files['success'][] = $name;
			}
			else{
				$files['error'][] = $name;
			}
		}
		return empty($files['error']) ? $files : false;
	}

	public function upload($file=null){
		include(__DIR__.'/../utilities/mimes-types.php');
		if (isset($file)) {$this->add($file);}
		$this->response = array(
			'success'=>array(),
			'error'=>array(),
		);
		if (!empty($this->files)) {
			foreach ($this->files as $key => $file) {
				$file = array_merge($this->cog,$file);
				$file['path'] = dir::createDir(fix_path($file['path']));
				switch (strtolower($file['type'])) {
					case 'url':
						$opt = new ajax($file['content']);
						$opt->cache(false);
						if ($opt->start()) {
							$name = $file['path'].DIRECTORY_SEPARATOR.md5($file['content']).'.'.$mime_array['extensions'][$opt->mime][0];
							$fn = fopen($name, "a");
							if (fputs($fn,$opt->body)) {
								$this->response['success'][] = $name;
							}
							else{
								$this->response['error'][] = $name;
								dir::removeFile($name);
							}
							fclose($fn);
						}
					break;
					case 'base64':
						$name = $file['path'].DIRECTORY_SEPARATOR.md5($file['content']).'.'.$mime_array['extensions'][preg_replace("/data\:(\w+)(\/|\\+)(\w+)\;base64\,(.+)/", "$1$2$3", $file['content'])][0];
						$fn = fopen($name, "a");
						if (fputs($fn,base64_decode(explode(',',$file['content'])[1]))) {
							$this->response['success'][] = $name;
						}
						else{
							$this->response['error'][] = $name;
							dir::removeFile($name);
						}
					break;
					case 'upload':
						$info = new finfo(FILEINFO_MIME);
						$name = $file['path'].DIRECTORY_SEPARATOR.md5($file['tmp_name']).'.'.$mime_array['extensions'][preg_replace("/(\w+)(\/|\\+)(\w+)\;(.+)/", "$1$2$3", $info->file($file['tmp_name']))][0];
						$this->response[(move_uploaded_file($file['tmp_name'], $name)) ? 'success' : 'error'][] = implode(DIRECTORY_SEPARATOR, __pop(explode(host('root'), $name)));
						break;
				}
			}
		}
	}


	public static function read($content=''){return (!empty($content)&&is_file($content)) ? file_get_contents($content) : false; }




}