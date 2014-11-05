<?
class Coop_OrderReportHistory extends Awsome_DbTable 
{
		
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "order_report_history";
		$this->editableColumns = array("coop_id", "reset_date", "history");
		$this->primaryColumn = "id";
	}

}