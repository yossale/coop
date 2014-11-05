<?
use Mailgun\Mailgun;

class Coop_Products extends Awsome_DbTable 
{
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "products";
		$this->editableColumns = array("coop_id", "category_id", "product_name", "product_measure", "product_manufacturer", "product_description", "product_about", "product_image", "product_in_shortage", "product_coop_cost", "product_items_left");
		$this->nameColumn = "product_name";
		$this->primaryColumn = "product_id";
		$this->deleteColumn = "product_deleted";
		$this->orderBy = "product_name";
	}
	
	private function getProducts($coop_id, $also_in_shortage)
	{
            $sql = "SELECT p.*
                FROM products p
                WHERE p.coop_id = " . (int)$coop_id . " 
                AND p.product_deleted = 0";
            
		if (!$also_in_shortage)
		{
			$sql .= " AND p.product_in_shortage = 0";
		}
		$sql .= " ORDER BY p.product_name";

		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;		
	}
	
	public function getAllFruitsAndVegtebles($coop_id = 1)
	{
		if ($coop_id == 1)
		{
			$sql = "SELECT products.*, categories.category_name FROM products, product_categories as categories
					WHERE products.coop_id = 1 AND products.product_deleted = 0 AND products.category_id in (1, 2, 3, 7)
					AND products.category_id = categories.category_id
					ORDER BY category_name, product_name";			
		}
		else
		{
			$sql = "SELECT products.*, categories.category_name FROM products, product_categories as categories
					WHERE products.coop_id = $coop_id AND products.product_deleted = 0
					AND products.category_id = categories.category_id
					ORDER BY category_name, product_name";			
		}

		if (!$results = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $results;	
	}
	
	public function updateFruitsAndVegtebles($shortage, $prices)
	{
		$changes = $this->calcChanges($shortage, $prices);
		$this->sendUpdatesEmail($changes);
		$this->updateChangesInDB($changes);
	}

	private function calcChanges($shortage, $prices)
	{
		$products = $this->getAllFruitsAndVegtebles();
			
		$changes = array();
		
		foreach ($products as $product)
		{
			$id = $product['product_id'];
			$name = stripslashes($product['product_name']);
			
			$updates = array();
			
			$inShortage = $shortage[$id] == "on" ? 1 : 0;
			if ($inShortage != $product['product_in_shortage'])
			{
				$updates[] = Array('column' => "product_in_shortage", 'name' => 'האם במחסור',
									'newValue' => $inShortage, 'oldValue' => $product['product_in_shortage']);
			}
			
			if ($prices[$id] != $product['product_coop_cost'])
			{
				$updates[] = Array('column' => "product_coop_cost", 'name' => 'עלות לקואופ',
									'newValue' => $prices[$id], 'oldValue' => $product['product_coop_cost']);
									
				$new_price = $prices[$id] * 1.15;
				
				$updates[] = Array('column' => "product_price", 'name' => 'מחיר לחברי הקואופ',
									'newValue' => $new_price, 'oldValue' => $product['product_price']);
			}
			
			if (!empty($updates))
			{
				
				$changes[] = Array('productName' => $name, 'productID' => $id, 'updates' => $updates);				
			}
		}
		return $changes;
	}
	
	private function sendUpdatesEmail($changes)
	{
		$text = "<div dir=rtl><ul>";
		
		foreach ($changes as $name => $change)
		{
			$text .= "<li><b><u>{$change['productName']}</u></b></li>";
			foreach ($change['updates'] as $update)
			{			
				$text .= "<ul><li>{$update['name']} השתנה מ-" . $update["oldValue"] . " ל-" . $update["newValue"] . "</li></ul>";
			}
		}
		
		$config = Zend_Registry::get('config');
        $mailgun = $config->mailgun;

        $subject = "החלקאי עדכן מחירים";

        $to = 'Coop <' . $config->email_sender->from_email . '>';

        $mg = new Mailgun($mailgun->key);
        $mg->sendMessage($mailgun->domain, array(
            'from' => $mailgun->from,
            'to'      => $to,
            'subject' => $subject,
            'html'    => $text));

	}
	
	private function updateChangesInDB($changes)
	{
		foreach ($changes as $name => $change)
		{
			$dbUpdates = array();
			foreach ($change['updates'] as $update)
			{
				$dbUpdates[$update['column']] = $update['newValue'];
			}
			$this->editProduct($change['productID'], $dbUpdates);
		}	
	}
	
	public function getAllProducts($coop_id)
	{
		return $this->getProducts($coop_id, false);
	}
	
	public function getAllProductsInsideCategories($coop_id, $also_in_shortage)
	{
		$coop_categories = new Coop_Categories();				
		$products = $this->getProducts($coop_id, $also_in_shortage); 
		$categories = $coop_categories->getAllCategories($coop_id);

		if (!empty($categories) && !empty($products))
		{
			$results = $this->setAsChildOfArray($products, $categories, "category_id");
			return $results;		
		}
		return false;
	}
	
	public function getProduct($id)
	{
		$sql = "SELECT * FROM products WHERE product_id = $id";
		if (!$results = $this->adapter->fetchRow($sql))
		{
			return false;
		}
		return $results;
	}
	
	public function addProduct($coop_id, $data)
	{
		$data['coop_id'] = $coop_id;
		$id =  $this->add($data);
		
		$coop_prices = new Coop_Prices;
		$coop_prices->addPrice($id, $data['product_price']);
                return $id;
	}
	
	public function editProduct($id, $data)
	{		
		$product = $this->getProduct($id);
		if (!empty($data['product_price']) && 
                        $product['product_price'] != $data['product_price'])
		{
			$coop_prices = new Coop_Prices;
			$attampt = $coop_prices->addPrice($id, $data['product_price']);
		}
		$this->edit($id, $data);
	}
	
	public function deleteProduct($id)
	{
		return $this->delete($id);
	}
}