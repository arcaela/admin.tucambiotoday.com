<?php




class TimeZone{

	public static function offSet($from=false,$to=false){
		$local = new DateTime("now", new DateTimeZone(implode('/', array_map('ucfirst', __pop(explode('/', $from))))));
		$user = new DateTime("now", new DateTimeZone(implode('/', array_map('ucfirst', __pop(explode('/', $to))))));
		return ($user->getOffset()-$local->getOffset());
	}

	public static function byIp($ip=false){
		$ip = !$ip ? host("ip") : $ip;
		$ip = ($ip=='::1'||$ip=='127.0.0.1') ? "190.207.122.246" : $ip;
		$ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
		return json_decode($ipInfo)->timezone;
	}




}