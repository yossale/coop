<?
class Coop_DutyReports extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "duty_reports";
		$this->editableColumns = array("coop_id", "report_week_number", "report_year", "report_content", "report_reset_day");
		$this->nameColumn = "report_week_number";
		$this->primaryColumn = "report_id";
		$this->deleteColumn = "report_deleted";
		$this->orderBy = "report_id DESC";
	}

	public function getAllReports($coop_id)
	{
		$sql = "SELECT * FROM duty_reports WHERE coop_id = " . (int)$coop_id;
		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;
	}
	
	public function editReport($id, $data)
	{
		return $this->edit($id, $data);
	}
	
	public function newReport($data)
	{
		return $this->add($data);
	}
	
	
	public function getThisWeekReport($coop_id)
	{
		$coop_coops = new Coop_Coops();
		$coop = $coop_coops->getCoop($coop_id);
		$reset_day = $coop['coop_last_reset_day'];
		
		
		$sql = "SELECT * FROM duty_reports 
				WHERE report_reset_day = '$reset_day' 
				AND coop_id = " . (int)$coop_id;
			
		 if (!$data = $this->adapter->fetchRow($sql))
		 {
		 	return false;
		 }
		 return $data;
	}

}