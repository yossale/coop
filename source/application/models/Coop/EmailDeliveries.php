<?
class Coop_EmailDeliveries extends Awsome_DbTable
{	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "email_deliveries";
		$this->editableColumns = array("email_msg_id", "email_delivery_address", "email_delivery_cc1", "email_delivery_cc2", "email_delivery_cc3", "email_delivery_name", "email_delivery_sent", "email_delivery_added_datetime", "email_delivery_sent_datetime", "user_id");
		$this->nameColumn = "email_delivery_address";
		$this->primaryColumn = "email_delivery_id";
		$this->deleteColumn = "email_delivery_deleted";
		$this->orderBy = "email_delivery_added_datetime";
	}
	
	public function getAllUnsentDeliveries($limit)
	{
		$sql = "SELECT * FROM email_msgs msgs, email_deliveries deliv WHERE deliv.email_msg_id = msgs.email_msg_id AND deliv.email_delivery_sent = 0 ORDER BY deliv.email_delivery_id LIMIT $limit";
		
		if (!$emails = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $emails;
	}
	
	
	public function replaceTags($text, $user)
	{
		$sql = "SELECT * FROM email_tags";
		$tags = $this->adapter->fetchAll($sql);
		foreach ($tags as $tag)
		{
			$text = str_replace("[" . stripslashes($tag['tag_name']) . "]", stripslashes($user[stripslashes($tag['tag_column'])]), $text);
		}
		return $text;
	}
	
	public function getAllDeliveries($email_msg_id)
	{		
		return $this->getAll("*", "email_msg_id = " . (int)$email_msg_id);
	}
		
	public function getDelivery($id)
	{
		return $this->getOne($id);
	}
	
	public function addDelivery($data)
	{
		return $this->add($data);
	}
		
	public function setDeliveryAsSent($id)
	{
		$sql = "UPDATE email_deliveries SET email_delivery_sent = 1, email_delivery_sent_datetime = '" . date("Y-m-d H:i:s") . "' WHERE email_delivery_id = " . $id;
		$this->adapter->query($sql);
	}
}