<?
class Coop_Settings extends Awsome_DbTable 
{
	const TABLE_NAME = "settings";
	const SETTING_NAME = "setting_name";
	const SETTING_VALUE = "setting_value";
	const OPEN_FOR_ORDERS = "OPEN_FOR_ORDERS";
	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = self::TABLE_NAME;
		$this->editableColumns = array(self::SETTING_NAME, self::SETTING_VALUE);
		$this->nameColumn = self::SETTING_NAME;
		$this->primaryColumn = self::SETTING_NAME;
		$this->deleteColumn = "";
		$this->orderBy = self::SETTING_NAME;
	}
	
	protected function setSetting($name, $value)
	{
		$data = array(self::SETTING_VALUE => $value);
		try {
			$this->getOne($name);
			$this->edit($name, $data);		
		}
		catch (Exception $e)
		{
			$data[self::SETTING_NAME] = $name;
			$this->add($data);
		}
	}
	
	protected function getSetting($name)
	{
		$data = $this->getOne($name, self::SETTING_VALUE);
		return $data[self::SETTING_VALUE];
	}
	
	public function setSiteOpenForOrders($is_open)
	{
		$this->setSetting(self::OPEN_FOR_ORDERS, (($is_open == "1") ? "1" : "0"));
	}
	
	public function getSiteOpenForOrders()
	{
		return $this->getSetting(self::OPEN_FOR_ORDERS );
	}
	
}