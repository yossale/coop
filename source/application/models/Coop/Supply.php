<?
class Coop_Supply extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "supply";
		$this->editableColumns = array("product_id", "supply_date", "supply_amount",
                    "supply_comments", "supply_taken");
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
        
        public function getSupplyByProduct($product_id)
        {
            $sql = "SELECT * FROM supply WHERE product_id = '$product_id'"; 
		
            if (!$results = $this->adapter->fetchAll($sql))
            {
                    return false;
            }
            return $results;
        }
	
	public function addSupply($data)
	{
		$id = $this->add($data);
                $coop_products = new Coop_Products;
                $coop_products->editProduct($data['product_id'], array("product_in_supply" => "1"));
                return $id;
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
