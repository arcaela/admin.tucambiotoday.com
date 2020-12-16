<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__.'/../phpmailer/autoload.php';

class Mail {
	private $mail = '';
	private $Subject = '';
	private $Body = '';

	function __construct($debugger=false,$ajustes=false) {
		$ajustes = (is_array($debugger)&&is_bool($ajustes)) ? $ajustes : (
			(is_bool($debugger)&&is_bool($ajustes)||!is_array($ajustes)) ? array() : $ajustes
		);
		$debugger = is_bool($debugger) ? $debugger : false;
		$this->settings = array_merge(array(
			"Host" => 'smtp.gmail.com',
			"SMTPAuth" => true,
			"Username" => 'arcaela99@gmail.com',
			"Password" => 'arcaelas123',
			"SMTPSecure" => 'tls',
			"Port" => 587,
		),$ajustes);
		$this->mail = new PHPMailer(true);
		$this->mail->SMTPDebug = false;
		$this->mail->isSMTP();
		$this->mail->Host = $this->settings['Host'];
		$this->mail->SMTPAuth = $this->settings['SMTPAuth'];
		$this->mail->Username = $this->settings['Username'];
		$this->mail->Password = $this->settings['Password'];
		$this->mail->SMTPSecure = $this->settings['SMTPSecure'];
		$this->mail->Port = $this->settings['Port'];
		$this->mail->setFrom('no-reply@ideadecoders.com', 'Bithub');
		$this->mail->AddReplyTo('no-reply@ideadecoders.com', 'Bithub');
		$this->mail->isHTML(true);
		return $this;
	}

	public function addTo($email=false,$name=false){
		$email = is_numeric(strpos($email,";")) ? __pop(explode(';', $email)) : $email;
		$name = (!is_array($email)&&!empty($name)&&is_string($name)) ? $name : '';
		$one = (!is_array($email));
		$email = __pop(is_array($email) ? $email : (
			is_string($email) ? explode(';', $email) : array()
		));
		foreach ($email as $email) {
			$this->mail->addAddress($email);
		}
		return $this;
	}

	public function send(){
		$this->mail->Subject = $this->titulo;
		$this->mail->Body = $this->mensaje;
		try {
			if($this->mail->send()){
				$d = true;
				$this->error = false;
			}
		}
		catch (phpmailerException $e) {
			$this->error =  $e->errorMessage();
		}
		catch (Exception $e) {
			$this->error =  $e->getMessage();
		}
		return !empty($d);
	}
}