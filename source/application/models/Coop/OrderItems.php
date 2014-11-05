<?
class Coop_OrderItems extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "order_items";
		$this->editableColumns = array("order_id", "product_id", "item_amount");
		$this->nameColumn = "item_id";
		$this->primaryColumn = "item_id";
		$this->deleteColumn = "item_deleted";
		$this->orderBy = "item_id";
	}
	
	public function getAllItems()
	{
		return $this->getAll();
	}
	
	public function getItem($id)
	{
		return $this->getOne($id);
	}
	
	public function addItem($data)
	{
		return $this->add($data);
	}
	
	public function editItem($id, $data)
	{
		return $this->edit($id, $data);
	}
	
	public function deleteItem($id)
	{
		return $this->delete($id);
	}
	
	public function deleteAllItems($order_id)
	{
		$qry = $this->adapter->query("DELETE FROM order_items WHERE order_id = " . (int)$order_id);
	}
}