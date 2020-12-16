<?php
	set_time_limit(60);



	class localbitcoin {
		
	public $cache = true;
	public $error = false;
	public $msg = "ERROR_500";

	function __construct($call=false,$response=false){
		$this->response = is_object($response) ? $response : new response();
		$this->_VARS = strtolower($_SERVER['REQUEST_METHOD'])=='post' ? $_POST : $_GET;
		$error = formValidation($this->_VARS);
		if ($error) {$this->response->setMessage($error);}
		else {
			$this->$call();
		}
	}


	function build_data_files($boundary, $fields, $files){
		$data = '';
		$eol = "\r\n";
		$delimiter = '-------------' . $boundary;
		foreach ($fields as $name => $content) {
			$data .= "--" . $delimiter . $eol . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol . $content . $eol;
		}
		foreach ($files as $name => $content) {
			$data .= "--" . $delimiter . $eol . 'Content-Disposition: form-data; name="document"; filename="' . $name . '"' . $eol . 'Content-Type: image/png'.$eol . 'Content-Transfer-Encoding: binary'.$eol;
			$data .= $eol;
			$data .= $content . $eol;
		}
		$data .= "--" . $delimiter . "--".$eol;
		return $data;
	}


	function make_request($endpoint,$postdata = array(),$search = array(), $replace = array()) {
		$api_get 	= array('/api/ads/','/api/ad-get/{ad_id}/','/api/ad-get/','/api/payment_methods/','/api/payment_methods/{countrycode}/','/api/countrycodes/','/api/currencies/','/api/places/','/api/contact_messages/{contact_id}/','/api/contact_info/{contact_id}/','/api/contact_info/','/api/account_info/{username}','/api/dashboard/','/api/dashboard/released/','/api/dashboard/canceled/','/api/dashboard/closed/','/api/myself/','/api/notifications/','/api/real_name_verifiers/{username}/','/api/recent_messages/','/api/wallet/','/api/wallet-balance/','/api/wallet-addr/','/api/merchant/invoices/','/api/merchant/invoice/{invoice_id}/','/api/contact_message_attachment/22791801/139109770/');
		$api_post 	= array('/api/ad/{ad_id}/','/api/ad-create/','/api/ad-equation/{ad_id}/','/api/ad-delete/{ad_id}/','/api/feedback/{username}/','/api/contact_release/{contact_id}/','/api/contact_release_pin/{contact_id}/','/api/contact_mark_as_paid/{contact_id}/','/api/contact_message_post/{contact_id}/','/api/contact_dispute/{contact_id}/','/api/contact_cancel/{contact_id}/','/api/contact_fund/{contact_id}','/api/contact_mark_realname/{contact_id}/','/api/contact_mark_identified/{contact_id}/','/api/contact_create/{ad_id}/','/api/logout/','/api/notifications/mark_as_read/{notification_id}/','/api/pincode/','/api/wallet-send/','/api/wallet-send-pin/','/api/merchant/new_invoice/','/api/merchant/delete_invoice/{invoice_id}/');
		$api_public	= array('/buy-bitcoins-with-cash/{location_id}/{location_slug}/.json','/sell-bitcoins-for-cash/{location_id}/{location_slug}/.json','/buy-bitcoins-online/{countrycode:2}/{country_name}/{payment_method}/.json','/buy-bitcoins-online/{countrycode:2}/{country_name}/.json','/buy-bitcoins-online/{currency:3}/{payment_method}/.json','/buy-bitcoins-online/{currency:3}/.json','/buy-bitcoins-online/{payment_method}/.json','/buy-bitcoins-online/.json','/sell-bitcoins-online/{countrycode:2}/{country_name}/{payment_method}/.json','/sell-bitcoins-online/{countrycode:2}/{country_name}/.json','/sell-bitcoins-online/{currency:3}/{payment_method}/.json','/sell-bitcoins-online/{currency:3}/.json','/sell-bitcoins-online/{payment_method}/.json','/sell-bitcoins-online/.json','/bitcoinaverage/ticker-all-currencies/','/bitcoincharts/{currency}/trades.json','/bitcoincharts/{currency}/orderbook.json');	
		$nonce = $this->nonce();
		$ch = curl_init();
		$datas = '';
			if (in_array($endpoint,$api_post)) {
				if($postdata['document']) {
				//echo "Send document: {$postdata[document]}\n";
				$bn = basename($postdata['document']);
				//echo "BN=$bn\n";
				$fn[$bn] = file_get_contents($postdata['document']);
				$boundary = uniqid();
				$delimiter = '-------------' . $boundary;
				$datas = $this->build_data_files($boundary, $postdata, $fn);
				$headers[] = "Content-Type: multipart/form-data; boundary=$delimiter";
				} else {
				if (!empty($postdata))
					$datas = http_build_query($postdata,'','&');
				}
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
				$is_post = true;
			}
			elseif (in_array($endpoint,$api_get)) {
				if (!empty($postdata))
					$datas = http_build_query($postdata,'','&');
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				$is_get = true;
			}
			else {
				if (!empty($get))
					$datas = http_build_query($get,'','&');
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				$is_public = true;
			}	

		if(!empty($search)) {$endpoint = str_replace($search,$replace,$endpoint); }	
		$lbtkey = 'ee7e9f690caa3e226978040f17e655c1';
		$keysec = 'b7c736c348b49a916e7fe40d1e2ecd33c1d7f3f34c5b032c3f234524e3cc1f57';
		$headers[] = "Apiauth-Nonce: ".$nonce;
		$headers[] = "Apiauth-Key: $lbtkey";
		$API_AUTH_SIGNATURE = strtoupper(hash_hmac('sha256', $nonce.$lbtkey.$endpoint.$datas, $keysec));			
		$headers[] = "Apiauth-Signature: $API_AUTH_SIGNATURE";
		$test = new ajax("https://localbitcoins.com".$endpoint);
		$test->cache(false,'+0 minutes');
		$test->headers(array(
			'CURLOPT_SSL_VERIFYPEER'=> false,
			'CURLOPT_HTTPHEADER'=> $headers,
			'CURLOPT_RETURNTRANSFER'=> true,
		));
		if ($test->start()) {
			if (substr($endpoint,0,32) == '/api/contact_message_attachment/') {
				return base64_encode($test->body);
			}
			else {
				return json::decode($test->body);
			}
		}
		return array();
	}

	function nonce() {
		$mt = explode(' ', microtime());
		$API_AUTH_NONCE = $mt[1].substr($mt[0], 2, 6);
		return $API_AUTH_NONCE;	
	}






	function balance_get(){
		$exc = $this->make_request('/api/wallet-balance/',array(),array(),array());
		$this->response->status(!empty($exc['data']) ? 'success' : 'error');
		$this->response->message(!empty($exc['data']) ? 'OK' : 'error');
		$this->response->response($exc['data'] ?? 'Error');
	}






	}