<?php
	
	class ajax{
		public $header = array(
			'CURLOPT_RETURNTRANSFER'=>1,
			'CURLOPT_FAILONERROR'=>true,
			'CURLOPT_SSL_VERIFYHOST'=>false,
			'CURLOPT_SSL_VERIFYPEER'=>false,
			'CURLOPT_FOLLOWLOCATION'=>true,
			'CURLOPT_TIMEOUT'=>30,
			'CURLOPT_HEADER'=>0,
		);
		private $method = 'get';
		private $cache = false;
		private $cache_time = '+3 minutes';
		public $url = false;


		function __construct($url=null) {
			$this->url = isset($url)&&is_string($url) ? $url : false;
		}
		public static function debugger($url=''){
			$url = explode('#', $url);
			$url = explode('?', $url[0]);
			$host = $url[0];
			$params = (count($url)>1) ? $url[1] : '';
			parse_str($params,$params);
			return array(
				'host'=>$host,
				'params'=>$params,
			);
		}

		public function headers($hd=false){
			$sp = array();
			if (is_array($hd)) {
				foreach ($hd as $key => $value) {$sp[strtoupper($key)] = $value;}
				$this->header = array_merge($this->header,$sp);
			}
			return $this;
		}

		public function method($method='get'){
			$this->method = strtolower($method);
			$this->header['CURLOPT_POST'] = ($this->method=='post');
		}
		public function params($params=null){
			$params = !empty($params) ? (
				is_string($params) ? $params : (
					is_array($params) ? http_build_query($params) : ''
				)
			) : (
				!empty($this->params) ? http_build_query($this->params) : ''
			);
			parse_str($params,$params);
			$this->params = !empty($this->params) ? array_merge($this->params,$params) : $params;
			return $this->params;
		}

		public function cache($cache=false,$cache_time='+3 minutes'){
			$this->cache = $cache;
			$this->cache_time = ($this->cache&&$cache_time) ? $cache_time : (
				!$this->cache ? false : '+3 minutes'
			);
		}

		public function start(){
			if (!empty($this->url)) {
				$debugger = $this->debugger($this->url);
				$this->params = ($this->method=='get') ? array_merge($debugger['params'],$this->params()) : $this->params();
				$this->header['CURLOPT_URL'] = $this->url = ($this->method=='get') ? $debugger['host'].'?'.http_build_query($this->params()) : $this->url;
				if ($this->method=='post') {
					$this->header['CURLOPT_POSTFIELDS'] = $this->params;
				}
				$this->urlKey = md5($this->url);
				if (($this->method=='get')&&(!empty($this->cache)&&!empty($this->cache_time))&&$response=cache_get($this->urlKey)){
					$this->mime = 'text/plain';
					$this->code = 200;
					$this->error = false;
					$this->body = $response;
				}
				else {
					foreach ($this->header as $key => $value) {
						$headers[constant($key)] = $value;
					}
					$handler = curl_init();
					curl_setopt_array($handler, $headers);
					if (!$data=curl_exec($handler)) {
						$this->mime = curl_getinfo($handler, CURLINFO_CONTENT_TYPE);
						$this->code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
						$this->error = curl_error($handler);
						$this->body = '';
					}
					else{
						$this->mime = curl_getinfo($handler, CURLINFO_CONTENT_TYPE);
						$this->code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
						$this->error = false;
						$this->body = $data;
						if (!empty($this->cache)&&!empty($this->cache_time)) {
							cache_add(md5($this->url),$this->body,$this->cache_time);
						}
					}
					curl_close($handler);					
				}
				return empty($this->error);
			}
			return false;
		}
	}