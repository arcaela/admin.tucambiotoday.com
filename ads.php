<?php
	if (!defined("__DIR__")) {define("__DIR__", dirname(__FILE__)); }
	include (__DIR__.'/system/autoload.php');
	header('Content-type: text/json');
	$cacheTime = !empty($_GET['cached_expire']) ? $_GET['cached_expire'] : 3;
	$last_online = 180;/*20 minutos desde la ultima conexion*/

	if (!empty($_GET['price'])) {
		$price = 0;
		$exec = new ajax();
		$exec->cache(true,'+1 minutes');
		if($_GET['price']=='kraken'){
			$url = "https://api.cryptowat.ch/markets/kraken/btceur/price";
			$success = function($response){return floatval(json::decode($response)['result']['price']); };
		}
		else{
			$url = "http://coinstant.exchange/precio.php";
			$success = function($response){return floatval(preg_replace("/[^0-9.,]/", '', $response)); };
		}
		$exec->url = $url;
		if ($exec->start()) {
			$response = array('status'=>'success','price'=>$success($exec->body));
		}
		else{
			$response = array('status'=>'error','message'=>$exec->error);
		}
		if (!empty($_GET['html'])) {echo $response['price']; exit();}
		json::encode($response,true,false);
		exit();
	}

	else if (!empty($_GET['action'])) {
		$scheme = array(
			'action'=>trim(strtolower($_GET['action'])),
			'currency' => empty($_GET['currency']) ? false : $_GET['currency'],
			'page' => empty($_GET['page']) ? 1 : $_GET['page'],
		);
		if ($scheme['page']) {
			$ajax = new ajax(crArc::rrt("aHR0cHM6Ly9sb2NhbGJpdGNvaW5zLmNvbS8=")."{$scheme['action']}-bitcoins-online/{$scheme['currency']}/.json?page={$scheme['page']}");
			$ajax->cache(false,"+{$cacheTime} seconds");
			if ($ajax->start()&&($ajax=json::decode($ajax->body))&&!empty($ajax['data'])) {
				$scheme['next'] = !empty($ajax['pagination']['next']) ? intval(preg_replace("(\D+)", "", $ajax['pagination']['next'])) : false;
				$scheme['ads'] = array_map(function($ads) use($scheme,$last_online){
					$value_max_format = '∞';/*Simbolo para indicar que no tiene precio maximo*/
					/*Palabras que deben filtrarse*/
					$words_block = array(
						'bitmain',
						'estafa',
						'vehiculo',
						'devuel',
						'midinero',
						'bajen',
						'paypal',
                      'tesoro',
                      'petro',
                      'facebank',
                      'zelle',
                      'estafador',
                      'fondo comun',
                      'banplus',
                      'recarga',
					);
					$ads['data'] = array_merge($ads['data'],array(
						/* Basics */
						"page"=>$scheme['page'],
						"price" => floatval($ads['data']['temp_price']),
						"min_amount" => (is_bool($ads['data']['min_amount'])||is_null($ads['data']['min_amount'])) ? 0 : $ads['data']['min_amount'],
						"max_amount" => (is_bool($ads['data']['max_amount'])||is_null($ads['data']['max_amount'])) ? 9000000000000000000 : $ads['data']['max_amount'],
						"bank_filter" => strtolower(preg_replace("(\W+)","", $ads['data']['online_provider'].$ads['data']['bank_name'])),
						"last_seen" => date_str($ads['data']['profile']['last_online'],false),
						"last_seen_minutes" => round((strtotime('now')-strtotime($ads['data']['profile']['last_online']))/60),
						"demo_test"=>"function(ass){console.clear();console.log('On rendered: ',ass);}",
						/* Requires */
						"is_list" => (strlen($ads['data']['limit_to_fiat_amounts'])>0),
					));
					$ads['data']['max_amount'] = (!is_null($ads['data']['max_amount_available'])&&$ads['data']['max_amount_available']<$ads['data']['max_amount']) ? $ads['data']['max_amount_available'] : $ads['data']['max_amount'];
					/*Formats*/
					$ads['data'] = array_merge($ads['data'],array(
						"min_amount_format"=>number_format($ads['data']['min_amount'],2,',','.'),
						"max_amount_format"=>($ads['data']['max_amount']>=90000000000000000) ? $value_max_format : number_format($ads['data']['max_amount'],2,',','.'),
					));
					/* Filters */
					$ads['data']["filter-word"] = (count(array_filter($words_block,function($wr)use($ads){
							return strstr(strtolower($ads['data']['bank_filter']),$wr);
						}))>0
						||($ads['data']['last_seen_minutes']>=$last_online)
					) ? ' is-hidden ' : '';
					$ads = array_merge($ads['data'],$ads['data']['profile']);
					unset($ads['profile']);
					return $ads;
				}, $ajax['data']['ad_list']);
				json::encode($scheme,true,true);
			}
		}
	}
