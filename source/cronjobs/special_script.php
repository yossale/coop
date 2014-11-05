<?
define('CRONJOB', true);
require_once("../public/index.php");

header("Content-Type", "text/html; charset=windows-1255");

$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");

$logger = Zend_Registry::get('logger');
$logger->info('Starting special script...');

ob_flush();

$coop_id = $_GET['coop_id'];
$coops_model = new Coop_Coops;
$coops_model->getCoop($coop_id); // just for validation

$reset_day = $_GET['reset_day'];

$json_reports_model = new Coop_JsonReports;
$json_reports_model->addWeeklyReportToCoop($coop_id, $reset_day);

$logger->info("Done special script");