<?php
/*
INICIO | Realizar peticiones a otros servidores evadiendo las conexiones SSL para evitar el bloqueo por Allow-Origin
La clase getURL está diseñada para trabajar de la siguiente manera
$var = new getURL();
$var = $var->setUrl() Es indispensable indicar la url a trabajar.
$var = $var->setHeader(); (Opcional) Especificas los headers que quieres enviar, tales como timeout, ssl etc
$var = $var->call(); llamas la funcion de set para hacer la peticion a la url indicada, el resultado te será añadido a la variable body.
$var->body; Contiene el resultado de la llamada al servidor.
$var->code retornara el codigo de respuesta HTTP ya sea 500 400 404 301 etc.
*/
class getURL{
	public $header = array(
		'CURLOPT_RETURNTRANSFER'=>1,
		'CURLOPT_FAILONERROR'=>true,
		'CURLOPT_SSL_VERIFYHOST'=>false,
		'CURLOPT_SSL_VERIFYPEER'=>false,
		'CURLOPT_FOLLOWLOCATION'=>true,
		'CURLOPT_TIMEOUT'=>30,
		'CURLOPT_HEADER'=>0,
	);
	public $url = '';
	public $response = '';
	public $code = 0;
	public $body = false;
	
	function __construct($url=false){
		$this->url = $url;
		return $this;
	}
	public function setUrl($url=false){
		$this->url = $url;
		return $this;
	}
	public function setHeader($header=array()){
		$header = (!empty($header)&&is_array($header)) ? $header : array();
		$this->header = array_merge($this->header, $header);
		return $this;
	}
	public function call(){
		if (!empty($this->url)) {
			$headers = array(
				CURLOPT_URL=>$this->url,
				CURLOPT_POST=>true,
			);
			foreach ($this->header as $key => $value) {
				$value = (is_numeric($value)) ? floatval($value) : $value;
				$value = (empty($value)) ? false : $value;
				$headers[constant($key)] = $value;
			}
			$handler = curl_init();
			curl_setopt_array($handler, $headers);
			if (!$data=curl_exec($handler)) {
				$this->body = curl_error($handler);
				$this->info = curl_getinfo($handler);
				$this->mime = curl_getinfo($handler, CURLINFO_CONTENT_TYPE);
				$this->code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
			}
			else{
				$this->body = $data;
				$this->info = curl_getinfo($handler, CURLINFO_HEADER_SIZE);
				$this->mime = curl_getinfo($handler, CURLINFO_CONTENT_TYPE);
				$this->code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
			}
			curl_close($handler);
			return $this;
		}
	}
}
/*FIN | Realizar peticiones a otros servidores evadiendo las conexiones SSL para evitar el bloqueo por Allow-Origin*/
