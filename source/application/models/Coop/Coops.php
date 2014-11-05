<?
class Coop_Coops extends Awsome_DbTable
{	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "coops";
		$this->editableColumns = array("coop_name", "coop_email", "coop_url", "coop_manager_user_id", "coop_is_open_now", "coop_default_openning_time", "coop_default_openning_time", "coop_close_day", "coop_close_time", "coop_open_day", "coop_open_time", "coop_reset_day", "coop_notes");
		$this->nameColumn = "coop_name";
		$this->primaryColumn = "coop_id";
		$this->deleteColumn = "coop_deleted";
		$this->orderBy = "coop_name";

        $this->logger = Zend_Registry::get('logger');
	}
	
	public function getAllCoops()
	{
		$sql = "SELECT coops.*, users.user_first_name, users.user_last_name FROM coops 
			LEFT JOIN users ON coops.coop_manager_user_id = users.user_id 
			WHERE coops.coop_deleted = 0 
			ORDER BY coops.coop_id";
		
		return $this->adapter->fetchAll($sql);
	}
		
        public function getCoopSafely($id)
        {
            try
            {
                 return $this->getCoop($id);
            }
            catch (Exception $e)
            {
                return null;
            }
        }
        
	public function getCoop($id)
	{
		return $this->getOne($id);
	}
	
	public function addCoop($data)
	{
		return $this->add($data);
	}
	
	public function editCoop($id, $data)
	{
            return $this->edit($id, $data);
	}
	
	public function deleteCoop($id)
	{
		return $this->delete($id);
	}
	
	
	public function resetCoop($coop_id)
	{
		$today = date("Y-m-d");
		$sql = "UPDATE coops SET `coop_last_reset_day` = '$today' WHERE coop_id = " . $coop_id;
		$this->adapter->query($sql);
	}

    public function closeAllCoops()
    {
        $coops = $this->getAllCoops();
        foreach ($coops as $coop)
        {
            $this->closeCoop($coop);
        }
    }

    public function closeCoop($coop)
    {
        $is_it_closing_day = $this->isItClosingDay($coop);
        if ($is_it_closing_day)
        {
            $is_it_closing_time = $this->isItClosingTime($coop);
            if ($is_it_closing_time)
            {
                $is_the_coop_open = $this->isTheCoopOpen($coop);
                if ($is_the_coop_open)
                {
                    $this->saveWeeklyReport($coop);
                    $this->flagCoopAsClosed($coop);
                    $this->logger->info("Closed Coop #{$coop['coop_id']}");
                    return;
                }
            }
        }
    }

    public function closeCoopWithoutCheck($coop) 
    {
        $this->saveWeeklyReport($coop);
        $this->flagCoopAsClosed($coop);
        $this->logger->info("Manually closed Coop #{$coop['coop_id']}");
    }

    private function isItClosingDay($coop)
    {
        $day_in_week = date('w');
        $is_it_closing_day = ($day_in_week == $coop['coop_close_day']);
        return $is_it_closing_day;
    }

    private function isItClosingTime($coop)
    {
        $current_hour = substr(date("H:i:s"), 0, 2);
        $coop_close_time = substr($coop['coop_close_time'], 0, 2);

        $is_it_closing_time = ($current_hour == $coop_close_time);
        return $is_it_closing_time;
    }

    private function isTheCoopOpen($coop)
    {
        $is_the_coop_open = ($coop['coop_is_open_now'] == '1');
        return $is_the_coop_open;
    }

    private function saveWeeklyReport($coop)
    {
        $this->logger->info("Begin saving weekly report for coop {$coop['coop_id']}...");

        $json_reports_model = new Coop_JsonReports;
        $json_reports_model->addWeeklyReportToCoop($coop['coop_id']); // throw exception if something goes wrong

        $this->logger->info("Successfully saved report for coop {$coop['coop_id']}");
    }

    private function flagCoopAsClosed($coop)
    {
        $this->editCoop($coop['coop_id'], array('coop_is_open_now' => 0));
    }
	
	public function getLastResetDay($coop_id)
	{
		$coop_orders = new Coop_Orders();		
		$reset_days = $coop_orders->getAllPossibleResetDays($coop_id);
		$list = array();
		foreach ($reset_days as $day)
		{
			if ($day['order_reset_day'] != '0000-00-00')
			{
				$list[] = $day['order_reset_day'];			
			}
		}
		if (!empty($list)) 
		{
			return $list[0];
		}
	}

}