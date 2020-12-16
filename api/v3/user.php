<?php



class user{
	public $cache = true;
	public $error = false;
	public $msg = "ERROR_500";

	function __construct($call=false,$response=false){
		$this->response = is_object($response) ? $response : new response();
		$this->_VARS = strtolower($_SERVER['REQUEST_METHOD'])=='post' ? $_POST : $_GET;
		$error = formValidation($this->_VARS);
		if ($error) {$this->response->setMessage($error);}
		else {
			// $con = json::decode(FileManager::read(__DIR__."/../../cogs/db.json"))['default'];
			// $this->con = new MySQL($con['host'],$con['user'],$con['password'],$con['database']);
			$this->$call();
		}
	}


	public static function accounts($id=false){
		return [
			"salomon"=>[
				"token"=>'salomondiaz',
				"password"=>'123456789'
			]
		];
		return !$id ? $accounts : $accounts[$id];
	}



	public function register_get(){
		if (!bypass(array('token','password',),__pop($this->_VARS))) {$this->error = "EMPTY_FIELDS";}
		$keyLogin=null;
		$login = array_filter(self::accounts(),function($account,$key) use(&$keyLogin){
			$keyLogin = empty($keyLogin)&&(strtolower($this->_VARS['token'])==strtolower($account['token'])&&
				strtolower($this->_VARS['password'])==strtolower($account['password'])) ? $key : $keyLogin;
			return $keyLogin;
		},ARRAY_FILTER_USE_BOTH);
		if ($login) {
			$_SESSION['login_key'] = $keyLogin;
			$this->msg = "Exitoso.";
		}
		else{$this->error = "ACCOUNT_404";}
		$this->response->status(!$this->error ? "success" : "error");
		$this->response->message(!$this->error ? $this->msg : $this->error);
	}





}