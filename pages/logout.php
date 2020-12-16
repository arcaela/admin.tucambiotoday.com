<?php
session_start();
session_destroy();
unset($_GLOBALS);
foreach ($_COOKIE as $key => $value) {
	if ($key!='update') {setcookie($key,false,(time()*(0-1)));}
}
header('Location: /login');