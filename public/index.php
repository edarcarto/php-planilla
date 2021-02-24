<?php
session_start();
define('PROJECT_PATH', dirname(__DIR__));
define('APP_PATH', PROJECT_PATH .'/app');
require_once realpath(__DIR__."/../app/init.php");
spl_autoload_register(function($class_name){
$file = PROJECT_PATH .'/'. str_replace('\\','/', $class_name).'.php';
	if(is_file($file)){
		include_once $file;
	}
});
$app = new App();