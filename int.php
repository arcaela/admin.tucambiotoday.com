<?php
	include('./system/autoload.php');
	if (isset($_GET['task_day'])) {
		$key = 'cogs_system';
		echo empty($_GET['task_day']) ? cache_get($key) : cache_add($key,$_GET['task_day'],'+20 hour');
	}