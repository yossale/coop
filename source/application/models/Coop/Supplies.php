<?
class Coop_Supplies extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "supplies";
		$this->editableColumns = array("product_id", "supply_date", "supply_amount");
		$this->primaryColumn = "supply_id";
		$this->deleteColumn = "supply_deleted";
		$this->orderBy = "supply_date DESC";
	}

	public function getAllSupplies($product_id)
	{
		return $this->getAll("*", "product_id = " . (int)$product_id);
	}	
		
	public function getSupply($id)
	{
		return $this->getOne($id);
	}
	
	public function addSupply($data)
	{
		return $this->add($data);
	}
	
	public function editSupply($id, $data)
	{
		return $this->edit($id, $data);
	}
	
	public function deleteSupply($id)
	{
		return $this->delete($id);
	}

}
