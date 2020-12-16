<?php
function host($const=null){
	$const = (is_string($const)&&strlen(trim($const))>0) ? strtolower($const) : null;
	$h = new class{
		public function __construct($const='host'){
			try{
				return $this->$const;
			}
			catch(Exeption $e){return null;}
		}
		public function host(){return strtolower(trim($_SERVER['HTTP_HOST']));}
		public function referer(){return (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : $this->host(); }
		public function ip($need='ip'){
			$ip = (!empty($_SERVER['HTTP_CLIENT_IP'])) ? $_SERVER['HTTP_CLIENT_IP'] : ( (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : (!empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR']));
			return
				($need=='ip') ? trim($ip) : (
					($need=='type') ? ($ip=='127.0.0.1' ? 'local' : 'remote') :  (
						($need=='is_remote') ? ($ip!='127.0.0.1') : (
							($need=='is_local') ? ($ip=='127.0.0.1') : $ip
						)
					)
				);
		}
		public function type(){return $this->ip('type');}
		public function is_local(){return $this->ip('is_local');}
		public function is_remote(){return $this->ip('is_remote');}
		public function is_http($need='is_http'){
			$p = trim(strtolower((
				!empty($_SERVER['HTTPS']) ? 'https' : (
					!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : "http"
				)
			)));
			return ($need=='protocol') ? $p : (($need=='is_http'&&$p=='http')||($need=='is_https'&&$p=='https')||($need=='is_secure'&&$p=='https'));
		}
		public function is_https(){return $this->is_http('is_https');}
		public function is_secure(){return $this->is_http('is_secure');}
		public function protocol(){return $this->is_http('protocol');}
		public function url(){return trim($this->is_http('protocol').'://'.strtolower(trim($_SERVER['HTTP_HOST'])).'/'); }
		public function path(){return implode('/', array_slice(explode('/',$_SERVER['PHP_SELF']), 0, -1)).'/'; }
		public function root(){return $_SERVER['DOCUMENT_ROOT'];}
		public function array(){
			$obj = $this;
			$method = get_class_methods($this);
			$disabled = array(
				'__construct',
				'array',
				'Object',
				'Obj',
				'__get',
				'__invoke',
				'__call',
			);
			return array_filter(array_combine($method, array_map(function($method) use ($obj,$disabled){
				return in_array($method, $disabled) ? null : $obj->$method;
			}, $method)),function($method) use ($disabled){
				return (!in_array($method, $disabled));
			},ARRAY_FILTER_USE_KEY);
		}
		public function Object(){return (Object)$this->array();}
		public function Obj(){return $this->Object();}
		public function __get($var) {return $this->$var();}
		public function __invoke($fn) {return $this->$fn();}
		public function __call($fn,$arg) {return null;}
	};
	return is_null($const) ? $h->host : $h($const);
}
function array_sort(&$array=array(),$order='asc',$recursive=false,$index=null){
	switch ($order) {
		case 'desc':
		case 'za':
			$order = SORT_DESC;
			break;
		default:
			$order = SORT_ASC;
			break;
	}
	if (isset($index)) {
		$tmp_list = array();
		foreach ($array as $key => $row) {
			$tmp_list[$key] = $row[$index];
		}
		array_multisort($tmp_list, $order, $array);		
	}
	else if(is_array($array)){
		array_multisort($array, $order);
		foreach ($array as $key => $value) {
			if (is_array($value)) array_multisort($array[$key], $order);
		}
	}
	return $array;
}
function __pop($array=array(),$recursive=false){
	if (!empty($recursive)) {
		foreach ($array as $key => $value) {
			$array[$key] = (is_array($value)) ? __pop($value,true) : (($value!==''&&$value!=null) ? $value : 'derrrs');
			if($array[$key]=='derrrs') unset($array[$key]);
		}
	}
	return empty($recursive) ? array_filter($array, function($value){return ($value!==''&&$value!=null);}) : $array;
}
function compressFiles($paths=array()){
	if (!is_bool($paths)&&!empty($paths)) {
		$paths = __pop((is_array($paths)) ? $paths : explode(',', $paths),true);
		echo "\n	";
		foreach ($paths as $link) {
			$ext = implode('.', array_slice(explode('.', $link), -1));
			switch ($ext) {
				case 'js':
				case 'json':
					echo '<script src="'.$link.'"></script>';
					break;
				default:
					echo '<link rel="stylesheet" href="'.$link.'">';
					break;
			}
			echo "\n	";
		}
	}
}
function security($string=null,$expire='+10 years'){
	if (empty($string)) {
		$key = md5(strtotime("now")*rand(4,9999));
		$key = array(
			'key'=>$key,
			'part'=>array(
				preg_replace("(\D+)", "", $key),
				md5(preg_replace("([0-9]+)", "", $key)),
			),
			'expire'=>is_numeric($expire) ? $expire : strtotime($expire),
		);
		$cadena = substr(chunk_split($key['key'],4,'-'), 0, -1)
		. '_'.implode('+', $key['part'])
		. '_'.$key['expire'];
		return base64_encode($cadena);
	}
	$key = explode('_', base64_decode($string));
	if ((count($key)==3)) {
		$key = array(
			'key'=>preg_replace("(\W+)","",$key[0]),
			'part'=>explode('+', $key[1]),
			'expire'=>$key[2],
		);
		return (count($key['part'])==2&&(preg_replace("(\D+)", "", $key['key'])==preg_replace("(\D+)", "", $key['part'][0]))
				&&(md5(preg_replace("([0-9]+)", "", $key['key']))==$key['part'][1])
					&&(is_numeric($key['expire'])&&$key['expire']>=strtotime("now")));
	}
	return false;
}
function isEmail($correo) {return filter_var($correo, FILTER_VALIDATE_EMAIL); }
function fix_path($path) {
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_map('trim', array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen'));
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		}
		else {
			$absolutes[] = ($part=='https:'||$part=='http:') ? $part.DIRECTORY_SEPARATOR : $part;
		}
	}
	return str_replace(DIRECTORY_SEPARATOR, '/', implode(DIRECTORY_SEPARATOR, $absolutes));
}

function array_map_assoc(callable $f, array $a) {return array_column(array_map($f, array_keys($a), $a), 1, 0); }


function date_str($time=null,$compare=null,$format=false) {
	$time = empty($time) ? strtotime('now') : (is_string($time) ? strtotime($time) : $time);
	$compare = empty($compare) ? false : (is_string($compare) ? strtotime($compare) : $compare);
	$diff	 = !$compare ? (time()-$time) : ($time-$compare);
	$format = is_string($format) ? $format : false;
	$ng = ($diff<0);
	$diff	 = $ng ? ($diff*(0-1)) : $diff;
	$s = $diff;
	$date = array(
		's'	=> $s,
		'i'	=> intval($s / 60),
		'h'	=> intval($s / 3600),
		'd'	=> intval($s / 86400),
		'w'	=> intval($s / 604800),
		'm'	=> intval($s / 2620800),
		'y'	=> intval($s / 31449600),
	);
	$date = array_merge($date,array(
		'S' => $date['s']." segundo".($date['s']>1 ? 's' : ''),
		'I' => $date['i']." minuto".($date['i']>1 ? 's' : ''),
		'H' => $date['h']." hora".($date['h']>1 ? 's' : ''),
		'D' => $date['d']." dia".($date['d']>1 ? 's' : ''),
		'W' => $date['w']." semana".($date['w']>1 ? 's' : ''),
		'M' => $date['m']." mese".($date['m']>1 ? 's' : ''),
		'Y' => $date['y']." año".($date['y']>1 ? 's' : ''),
	));
	$datetime = preg_replace_callback("/\%\w+/",function($item) use ($date){
		$k = str_replace('%', '', $item[0]);
		return $date[$k];
	}, $format);
	if (!$format) {
		$datetime = "Hace un momento";
		$pre = $date['s']>30 ? (
			$ng ? 'Dentro de ' : 'Hace '
		) : '';
		$datetime = ($date['s']>30&&$date['s']<60) ? $date['S'] : $datetime;
		$datetime = ($date['s']>=60&&$date['s']<3600) ? $date['I'] : $datetime;
		$datetime = ($date['s']>=3600&&$date['s']<86400) ? $date['H'] : $datetime;
		$datetime = ($date['s']>=86400&&$date['s']<604800) ? $date['D'] : $datetime;
		$datetime = ($date['s']>=604800&&$date['s']<2620800) ? $date['W'] : $datetime;
		$datetime = ($date['s']>=2620800&&$date['s']<31449600) ? $date['M'] : $datetime;
		$datetime = ($date['s']>=31449600) ? $date['Y'] : $datetime;
		$datetime = $pre.$datetime;
	}
	return $datetime;
}

function varCache($var=null,$callback=null,$expire=null,$renobable=null){
	$renobable = is_bool($expire) ? $expire : (
		!is_null($renobable) ? $renobable : false
	);
	$expire = is_numeric($expire) ? $expire : 86400;
	$_SESSION['cacheades_vars_list'] = isset($_SESSION['cacheades_vars_list']) ? $_SESSION['cacheades_vars_list'] : array();
	if ((is_string($var)||is_numeric($var))) {
		$ex = isset($_SESSION['cacheades_vars_list'][$var]) ? $_SESSION['cacheades_vars_list'][$var] : false;
		$clear = ($ex&&($renobable||(($ex['created']+$ex['lifetime'])<=strtotime("now"))));
		if ($ex&&$clear) {unset($_SESSION['cacheades_vars_list'][$var]); }
		if (!is_null($callback)) {
			$_SESSION['cacheades_vars_list'][$var] = (!empty($ex)&&empty($clear)) ? $ex : array(
					'key'=>$var,
					'value'=>is_object($callback) ? $callback($var) : $callback,
					'created'=>strtotime("now"),
					'lifetime'=>$expire,
			);
		}
	}
	return $_SESSION['cacheades_vars_list'][$var]['value'];
}