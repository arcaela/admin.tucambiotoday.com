<?php
/**

$response = array(
	'status'=>'error | success | 2fa',
	'message'=>'Hola mundo',
	'inputs'=>array(),
	'access_token'=>'BASE64',
	'timestamp',
);

 */
class response{
	public $status = false;
	public $message = false;
	public $inputs = false;
	public $timestamp = false;

	function __construct() {
		$this->timestamp = strtotime("now");
		return $this;
	}

	public function status($status=false){return $this->setstatus($status); }
	public function setStatus($status=false){
		$this->status = !empty($status)&&is_string($status) ? $status : (
			!empty($this->status) ? $this->status : 'error'
		);
		return $this;
	}



	public function message($message=false){return $this->setMessage($message); }
	public function setMessage($message=false){
		$this->setStatus(false);
		$defaults = array(
			'error'=>'ERROR',
			'success'=>false,
			'2fa'=>'TFA',
			'confirm'=>'CONTINUE',
			'default'=>false,
		);
		$this->message = is_string($message)&&!empty($message) ? $message : (
			!empty($this->message) ? $this->message : (
				isset($defaults[$this->status]) ? $defaults[$this->status] : $defaults['default']
			)
		);
		return $this;
	}
	
	public function inputs($inputs=array()){return $this->setInputs($inputs); }
	public function setInputs($inputs=array()){
		$this->inputs = is_array($inputs) ? $inputs : (
			!empty($this->inputs) ? $this->inputs : array()
		);
		return $this;
	}

	public function data($data=false){return $this->setData($data);}
	public function setData($data=false){
		$vars = (Array)get_object_vars($this);
		foreach ($vars as $key => $value){unset($this->$key); }
		foreach ($data as $key => $value){
			$this->$key = $value;
		}
		return $this;
	}


	public function __call($method,$argument){
		$this->$method = $argument[0];
	}
	
	public function __toString(){
		$vars = (Array)get_object_vars($this);
		return json::encode($vars);
	}
}