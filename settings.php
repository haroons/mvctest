<?php
	
define("DIRECT_ACCESS", 1);

define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));
define('BASE_DIR', str_replace('\\', '/', dirname(__FILE__)));

define('IMG_URL', BASE_URL.'/images');
define('IMG_DIR', BASE_DIR.'/images');

define('MODELS_URL', BASE_URL.'/models');
define('MODELS_DIR', BASE_DIR.'/models');

define('VEIWS_URL', BASE_URL.'/views');
define('VEIWS_DIR', BASE_DIR.'/views');

define('CONTROLLERS_URL', BASE_URL.'/controllers');
define('CONTROLLERS_DIR', BASE_DIR.'/controllers');

define('LIBS_URL', BASE_URL.'/libs');
define('LIBS_DIR', BASE_DIR.'/libs');

//Database credentials
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','123');
define('DB_NAME','mvctest');
define('DB_PREFIX','mt_');

//inlude liberary files
include_once(LIBS_DIR.'/Config.lib.php');
include_once(LIBS_DIR.'/Database.lib.php');
include_once(LIBS_DIR.'/Session.lib.php');

//global object definitions
$dbObj = new Database();
$cnfObj = new Config();
$sessObj = new Session();

$_SESSION['test'] = 'test';

echo $cnfObj->getValue('site_name');