<?php
ini_set('display_errors', true);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set("Israel");

$curr = str_replace("index.php", "", __FILE__);
define('APPLICATION_PATH', $curr . "../application");

$env = getenv('APPLICATION_ENV');
$env = (!empty($env)) ? $env : 'production';
define('APPLICATION_ENV', $env);

define('PUBLIC_PATH', '');

/** Zend_Application */
$library_path = $curr . '../../library/';
set_include_path($library_path . PATH_SEPARATOR . get_include_path());
require_once $library_path . 'Zend/Application.php';

require_once($curr . '../../vendor/autoload.php');

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

if (!defined('CRONJOB') || CRONJOB == false)
{
	$application->bootstrap()->run();
}
