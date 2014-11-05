<?
require_once(APPLICATION_PATH . '/models/Coop/S3.php');

class Coop_JsonReports extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "json_reports";
		$this->editableColumns = array("coop_id", "type", "date", "content");
		$this->primaryColumn = "id";
		
		$config = Zend_Registry::get('config');
		$this->s3 = new S3($config->s3->access_key, $config->s3->secret_key);
		$this->bucket_name = $config->s3->bucket_name;

        $this->logger = Zend_Registry::get('logger');
	}
	
	public function addWeeklyReportToCoop($coop_id, $last_reset_day = null)
	{
		$coop_model = new Coop_Coops;
		
		if ($last_reset_day == null)
		{
			$last_reset_day = $coop_model->getLastResetDay($coop_id);		
		}
		
		$weekly_reports_model = new Coop_WeeklyReports;		
		$weekly_report = $weekly_reports_model->getWeeklyReport($coop_id, $last_reset_day);
		
		$json = json_encode($weekly_report);
		
		return $this->saveReportToS3($coop_id, 'weekly-reports', $last_reset_day, $json);	
	}
	
	private function saveReportToS3($coop_id, $type, $date, $content)
	{
		$path = "$coop_id/weekly-reports/$date.json";

        $this->logger->info("Starting to save {$this->bucket_name}/$path on Amazon S3");

		$success = $this->s3->putObject($content, 
										$this->bucket_name, 
										$path, 
										S3::ACL_PUBLIC_READ_WRITE, 
										array(), 
										array('Content-Type' => 'text/json'));

        if ($success)
        {
            $this->logger->info("Successfully saved report {$this->bucket_name}/$path on Amazon S3");
        }
        else
        {
            throw new Exception("Could not save report $path on Amazon S3");
        }

		return $success;
	}
	
	public function getAllReportsMetadata($type)
	{		
		$sql = "SELECT coops.coop_id, reports.id, reports.sent_to_s3, reports.error_with_s3 FROM json_reports reports, coops 
				WHERE coops.coop_id = reports.coop_id 
				AND coops.coop_deleted = 0
				AND reports.type = '$type'";
 		return $this->adapter->fetchAll($sql); 
	}
	
	public function getReportByID($id)
	{
		return $this->getOne($id);
	}
	
	public function getAllPossibleDates($coop_id, $type)
	{
		$path = "$coop_id/$type/";
		$data = $this->s3->getBucket($this->bucket_name, $path);
		
		if (!empty($data))
		{
			$result = array();
			foreach ($data as $row)
			{
				$date = str_replace($path, '', $row['name']);
				$date = str_replace('.json', '', $date);
				
				$result[] = $date;
			}
			$result = array_reverse($result);
			return $result;
		}
	}

    public function getReport($coop_id, $type, $date)
    {
        $logger = Zend_Registry::get('logger');
        $path = "$coop_id/$type/$date.json";
        $logger->info('Checking for existing report on S3: ' . $this->bucket_name . '/' . $path);
        $report = $this->s3->getBucket($this->bucket_name, $path);

        if (empty($report))
        {
            $logger->info('Could not find the report on S3');
            return false;
        }
        return $report;
    }
	
	public function getReportContentAsArray($coop_id, $type, $date)
	{
		$path = "$coop_id/$type/$date.json";
		$data = $this->s3->getObject($this->bucket_name, $path);
		$array = json_decode($data->body, true);
		return $array;
	}
}
