<?php
$http_codes = array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    102 => 'Processing',
    103 => 'Checkpoint',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    207 => 'Multi-Status',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => 'Switch Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => 'I\'m a teapot',
    422 => 'Unprocessable Entity',
    423 => 'Locked',
    424 => 'Failed Dependency',
    425 => 'Unordered Collection',
    426 => 'Upgrade Required',
    449 => 'Retry With',
    450 => 'Blocked by Windows Parental Controls',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    506 => 'Variant Also Negotiates',
    507 => 'Insufficient Storage',
    509 => 'Bandwidth Limit Exceeded',
    510 => 'Not Extended'
);
?>
<!DOCTYPE html>
<html >
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $http_codes[$_GET['error']]; ?></title>
		<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900" rel="stylesheet">	
		<style>
		* {-webkit-box-sizing: border-box; box-sizing: border-box; } body {padding: 0; margin: 0; } #notfound {/*position: relative;height: 100vh;*/} #notfound .notfound {position: absolute; left: 50%; top: 50%; -webkit-transform: translate(-50%, -50%); -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); } .notfound {max-width: 410px; width: 100%; text-align: center; } .notfound .notfound-404 {height: 280px; position: relative; z-index: -1; } .notfound .notfound-404 h1 {font-family: 'Montserrat', sans-serif; font-size: 230px; margin: 0px; font-weight: 900; position: absolute; left: 50%; -webkit-transform: translateX(-50%); -ms-transform: translateX(-50%); transform: translateX(-50%);

background: #cedbe9; /* Old browsers */
background: -moz-linear-gradient(top, #cedbe9 0%, #aac5de 17%, #6199c7 50%, #3a84c3 51%, #419ad6 59%, #4bb8f0 71%, #3a8bc2 84%, #26558b 100%); /* FF3.6-15 */
background: -webkit-linear-gradient(top, #cedbe9 0%,#aac5de 17%,#6199c7 50%,#3a84c3 51%,#419ad6 59%,#4bb8f0 71%,#3a8bc2 84%,#26558b 100%); /* Chrome10-25,Safari5.1-6 */
background: linear-gradient(to bottom, #cedbe9 0%,#aac5de 17%,#6199c7 50%,#3a84c3 51%,#419ad6 59%,#4bb8f0 71%,#3a8bc2 84%,#26558b 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cedbe9', endColorstr='#26558b',GradientType=0 ); /* IE6-9 */

		-webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: cover; background-position: center; } .notfound h2 {font-family: 'Montserrat', sans-serif; color: #000; font-size: 24px; font-weight: 700; text-transform: uppercase; margin-top: 0; } .notfound p {font-family: 'Montserrat', sans-serif; color: #000; font-size: 14px; font-weight: 400; margin-bottom: 20px; margin-top: 0px; } .notfound a {font-family: 'Montserrat', sans-serif; font-size: 14px; text-decoration: none; text-transform: uppercase; background: #0046d5; display: inline-block; padding: 15px 30px; border-radius: 40px; color: #fff; font-weight: 700; -webkit-box-shadow: 0px 4px 15px -5px #0046d5; box-shadow: 0px 4px 15px -5px #0046d5; } @media only screen and (max-width: 767px) {.notfound .notfound-404 {height: 142px; } .notfound .notfound-404 h1 {font-size: 112px; } }
		</style>
	</head>
	<body>
		<div id="notfound">
			<div class="notfound">
				<div class="notfound-404">
					<h1>Oops!</h1>
				</div>
				<h2><?php echo $_GET['error'].' - '.$http_codes[$_GET['error']]; ?></h2>
				<a href="/">OK</a>
			</div>
		</div>
	</body>
</html>
