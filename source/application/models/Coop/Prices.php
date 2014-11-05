<?
class Coop_Prices extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "prices";
		$this->editableColumns = array("product_id", "price_amount", "price_date");
		$this->primaryColumn = "price_id";
		$this->orderBy = "price_id DESC";		
	}
	
	public function addPrice($product_id, $amount)
	{
		$data = array("product_id" => $product_id, "price_amount" => $amount, "price_date" => date("Y-m-d"));
		$id = $this->add($data);	
                
                // upate cached prices
                $sql = "update products 
                    set product_price = 
                        (select price_amount from prices 
                        where prices.product_id = products.product_id 
                        order by price_id desc limit 1) 
                    where product_id = " . (int)$product_id;
                $this->adapter->query($sql);
                
                return $id;
	}
	
	public function getPrices($coop_id, $reset_date) 
	{
		$coop_products = new Coop_Products;
		$products = $coop_products->getAllProducts(true);
		
		$ids = array();
		foreach ($products as $product)
		{
			$ids[] = $product['product_id'];
		}		
		$ids = implode(', ', $ids);		
		
		$sql = "SELECT * FROM prices
WHERE price_id IN
(SELECT max(prices.price_id) FROM prices
WHERE product_id in ($ids) AND price_date <= '$reset_date' GROUP BY product_id)";

		$prices = $this->adapter->fetchAll($sql);
		
		$prices_with_key = array();
		foreach ($prices as $price) {
			$prices_with_key[$price['product_id']] = $price;
		}
		
		return $prices_with_key;
	}
	
		
}
