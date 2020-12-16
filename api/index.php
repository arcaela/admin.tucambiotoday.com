<?php
include(__DIR__.'/functions.php');

header('content-type: application/json');
$is_api = strstr(host(), "api.");

$response = new response();
$parts = array_slice(__pop(explode('/', strtok(strtolower($_SERVER["REQUEST_URI"]),'?'))), ($is_api ? 0 : 1));
if (count($parts)<2||(!class_exists($parts[0])||!method_exists($parts[0], $parts[1].'_'.$_SERVER['REQUEST_METHOD']))) {
	echo $response->setMessage('API_EMPTY');
	exit();
}
$object = new $parts[0]($parts[1].'_'.$_SERVER['REQUEST_METHOD'],$response);
$object->response->message = error_helper($object->response->message);
echo $object->response;