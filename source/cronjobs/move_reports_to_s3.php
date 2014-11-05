<?
define('CRONJOB', true);
require_once("../public/index.php");
require_once(APPLICATION_PATH . '/models/Coop/S3.php');

header("Content-Type", "text/html; charset=windows-1255");

die('no longer in use');	

$adapter = Zend_Db_Table::getDefaultAdapter();
$adapter->query("SET NAMES utf8");


$config = Zend_Registry::get('config');
$s3 = new S3($config->s3->access_key, $config->s3->secret_key);

$json_reports_model = new Coop_JsonReports;
$reports = $json_reports_model->getAllReportsMetadata('weekly');
$list = array();

foreach ($reports as $report)
{	
	if ($report['error_with_s3'] == '1')
	{
		$report = $json_reports_model->getReportByID($report['id']);
					
		$array = $json_reports_model->deserialize($report['content']);
		$json = json_encode($array);
		
		$path = "{$report['coop_id']}/weekly-reports/{$report['date']}.json";
		$success = $s3->putObject($json, $config->s3->bucket_name, $path, S3::ACL_PUBLIC_READ_WRITE, array(), array('Content-Type' => 'text/json'));
		
		unset($report['content']);
		if ($success)
		{
			$adapter->query('update json_reports set sent_to_s3 = true, error_with_s3 = false where id = ' . $report['id']);
			echo "Successfully created report for " . print_R($report, true) . "\n";
		}
		else
		{
			$adapter->query('update json_reports set sent_to_s3 = true, error_with_s3 = true where id = ' . $report['id']);
			echo "Error - cannot create report for " . print_R($report, true) . "\n";
		}
		
	return;
	}
	
}

