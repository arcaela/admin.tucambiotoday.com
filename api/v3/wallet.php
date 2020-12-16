<?php
class wallet{
	public static function createKey($url=false){
		return (!empty($url)&&is_string($url)) ? (
			($md5=md5($url)) ? substr(chunk_split($md5,4,'-'), 0,-1) : rand()
		) : rand();
	}

	public static function is_key($key=false){
		$url = parse_url(host("referer"));
		$url = wallet::createKey(empty($url['host']) ? $url['path'] : $url['host']);
		$valids = array(
			'a7dd-238b-faa7-efbc-0e84-7127-390e-1d07',
			'2418-8e30-bcb9-86c1-ca21-bc1f-4bb0-654a',
		);
		return (is_string($key)&&in_array($key, $valids));
	}

}