<?
class Coop_EmailMessages extends Awsome_DbTable
{	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "email_msgs";
		$this->editableColumns = array("coop_id", "email_from_name", "email_from_email", "email_subject", "email_body", "email_added_datetime");
		$this->nameColumn = "email_subject";
		$this->primaryColumn = "email_msg_id";
		$this->deleteColumn = "email_msg_deleted";
		$this->orderBy = "email_date_time";
	}
	
	public function getAllEmailMessages($coop_id)
	{		
		$sql = "SELECT * FROM email_msgs WHERE coop_id = " . (int)$coop_id;
		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;
	}
		
	public function getEmailMessage($id)
	{
		return $this->getOne($id);
	}
	
	public function addEmailMessage($coop_id, $data)
	{
		$data['coop_id'] = $coop_id;
		$data['email_added_datetime'] = $this->getCurrentDateTime();
		return $this->add($data);
	}
	
	public function setMessageAsSent($id)
	{
		$data = array("email_sent" => "1", "email_sent_datetime" => $this->getCurrentDateTime());
		return $this->update($id, $data);
	}

}