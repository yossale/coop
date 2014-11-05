<?
define('CRONJOB', true);
require_once("../public/index.php");

header("Content-Type", "text/html; charset=windows-1255");

$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");

ob_start();

$coop_coops = new Coop_Coops();
$coop_coops->closeAllCoops();