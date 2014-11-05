<?
class Coop_Orders extends Awsome_DbTable 
{
	const STATUS_UNPAYED = "unpayed";
	const STATUS_PAYED = "payed";
	
	public function __construct()
	{
		parent::__construct();
		$this->tableName = "orders";
		$this->editableColumns = array("order_date", "user_id", "order_last_edit", "order_status", "order_reset_day");
		$this->nameColumn = "order_id";
		$this->primaryColumn = "order_id";
		$this->deleteColumn = "order_deleted";
		$this->orderBy = "order_id DESC";
	}

    public function getAllOrders($coop_id)
    {
        $sql = "SELECT * FROM orders WHERE coop_id = " . (int)$coop_id;
        if (!$results = $this->adapter->fetchAll($sql))
        {
            return false;
        }
        return $results;
    }

    public function getUserOrders($user_id)
    {
        $sql = "SELECT * FROM orders WHERE user_id = " . (int)$user_id;
        if (!$results = $this->adapter->fetchAll($sql))
        {
            return false;
        }
        return $results;
    }

    public function getAllPossibleResetDays($coop_id)
	{
		$sql = "select orders.order_reset_day from orders, users 
				where orders.user_id = users.user_id 
				and users.coop_id = " . $coop_id  . " 
				group by orders.order_reset_day
				order by orders.order_reset_day desc";
 		return $this->adapter->fetchAll($sql); 
	}
	
	public function countWeeklyOrdersForProduct($coop_id, $product_id, $reset_day = "")
	{
		if ($reset_day == "")
		{
			$coop_coops = new Coop_Coops();
			$coop = $coop_coops->getCoop($coop_id);
			$reset_day = $coop['coop_last_reset_day'];			
		}
  
		$sql = "select sum(oi.item_amount) as total 
				from orders o, order_items oi 
				where o.order_id = oi.order_id 
				and o.order_reset_day = '$reset_day'
				and oi.product_id = $product_id 
				group by oi.product_id";
				
		$row = $this->adapter->fetchRow($sql);
		return $row['total'];
	}
	
	public function getCurrentOrder($user_id)
	{
            $coop_users = new Coop_Users();
            
            $user = $coop_users->getUserOrNull($user_id);
			
			if (!$user) {
				return;
			}

            $coop_coops = new Coop_Coops();
            $coop = $coop_coops->getCoop($user['coop_id']);
            $reset_day = $coop['coop_last_reset_day'];


            $sql = "SELECT * FROM orders 
                            WHERE order_deleted = 0 
                            AND user_id = " . (int)$user_id . " 
                            AND order_reset_day = '$reset_day'";

            if (!$order = $this->adapter->fetchRow($sql))
            {
                    return false;
            }
            
            return $order;
	}
        
        public function getUsersLastOrder($user_id)
        {
            $order = $this->getCurrentOrder($user_id);
            if (!$order)
            {
                return false;
            }

            $items = $this->getItems($order['order_id']);
            $categories = array();
            foreach ($items as $item)
            {
                $category_id = $item['category_id'];
                if (!isset($categories[$category_id]))
                {
                    $categories[$category_id] = array("category_name" => $item['category_name'],
                                                        "products" => array());

                }
                $categories[$category_id]['products'][$item['product_id']] = 
                        array("name" => $item['product_name'],
                            "price" => $item['product_price'],
                            "measure" => $item['product_measure'],
                            "amount" => $item['item_amount'],
                            "manufacturer" => $item['product_manufacturer'],
                            "description" => $item['product_description']);
            }
            
            return $categories;
        }

        public function createCurrentOrder($user_id)
	{
		$coop_users = new Coop_Users();
		$user = $coop_users->getUser($user_id);
		
		$coop_coops = new Coop_Coops();
		$coop = $coop_coops->getCoop($user['coop_id']);
		$reset_day = $coop['coop_last_reset_day'];
		
		
		$order_data = array();
		$order_data['order_deleted'] = 0;
		$order_data['order_date'] = date("Y-m-d");
		$order_data['user_id'] = $user_id;
		$order_data['order_last_edit'] = date("Y-m-d H:i:s");
		$order_data['order_status'] = self::STATUS_UNPAYED;
		$order_data['order_reset_day'] = $reset_day;
		
		$pk = $this->addOrder($order_data);
		return $pk;
	}
	
	public function getUserPreviousOrders($user_id)
	{
		$sql = "SELECT * FROM orders 
				WHERE order_deleted = 0 
				AND user_id = " . (int)$user_id . " 
				AND order_date < STR_TO_DATE(concat(year(curdate()), week(curdate()), ' Sunday'), '%X%V %W') 
				ORDER BY order_id DESC";
		if (!$order = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $order;		
	}
	
	public function getAllThisWeekOrders($coop_id)
	{
		$coop_coops = new Coop_Coops();
		$coop = $coop_coops->getCoop($coop_id);
		$reset_day = $coop['coop_last_reset_day'];
		
                $sql = "SELECT u.*, o.*
                        FROM users u, orders o, order_items oi, products p
                        WHERE oi.order_id = o.order_id 
                        AND o.user_id = u.user_id 
                        AND oi.product_id = p.product_id 
                        AND order_deleted = 0 
                        AND order_reset_day = '$reset_day'			
                        AND u.coop_id = " . (int)$coop_id . " 
                        GROUP BY o.order_id 
                        ORDER BY u.user_first_name";
                
                if (!$orders = $this->adapter->fetchAll($sql))
		{
                    return false;
		}
		
                foreach ($orders as $key => $value)
                {
                    $current_order = $this->getCurrentOrder($value['user_id']);
                    if ($current_order)
                    {
                        $total = 0;
                        $items = $this->getItems($current_order['order_id']);
						
						if (!empty($items)) {
	                        foreach ($items as $item)
    	                    {
        	                    $total += ($item['item_amount'] * $item['product_price']);
            	            }
						}
                        $orders[$key]['total'] = $total;
                    }
                }
                    
		return $orders;
	}
        
        public function getOrdersGroupedByStatus($coop_id)
        {
            $orders = $this->getAllThisWeekOrders($coop_id);
            
            $groups = array("unpayed" => array(), "payed" => array());
            if ($orders)
            {
                foreach ($orders as $key => $value)
                {
                    $groups[$value['order_status']][] = $value;
                }
            }
            
            return $groups;
        }


        public function getFarmerReportForThisWeek($coop_id)
	{
		$coop_coops = new Coop_Coops();
		$coop = $coop_coops->getCoop($coop_id);
		$reset_day = $coop['coop_last_reset_day'];
		
		$sql = "SELECT p.product_name, SUM(oi.item_amount) AS amount, p.product_measure, pc.* 
		FROM orders o, order_items oi, products p, product_categories AS pc 
		WHERE p.category_id = pc.category_id 
		AND oi.product_id = p.product_id 
		AND oi.order_id = o.order_id 
		AND o.order_deleted = 0 
		AND o.order_reset_day = '$reset_day'
		AND p.coop_id = " . (int)$coop_id . " 
		GROUP BY p.product_id 
		ORDER BY pc.category_list_position, p.product_name";	
		if (!$data = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $data;
	}
	
	public function getItems($order_id)
	{
                $sql = "SELECT *
                        FROM order_items AS oi, products AS p, product_categories AS pc
                        WHERE oi.product_id = p.product_id 
                        AND p.category_id = pc.category_id 
                        AND oi.item_deleted = 0 
                        AND oi.order_id = " . (int)$order_id . "
                        ORDER BY p.product_manufacturer";
		if (!$items = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		$returned = array();
		foreach ($items as $num => $row)
		{
			$returned[$row['product_id']] = $row;
		}
		return $returned;
	}
	
	public function getItemsByCategory($order_id)
	{
                $sql = "SELECT *
                        FROM order_items AS oi, products AS p, product_categories AS pc
                        WHERE oi.product_id = p.product_id 
                        AND p.category_id = pc.category_id 
                        AND oi.item_deleted = 0 
                        AND oi.order_id = " . (int)$order_id . "
                        ORDER BY p.category_id, p.product_name";
		if (!$items = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		$returned = array();
		foreach ($items as $num => $row)
		{
			$returned[$row['product_id']] = $row;
		}
		return $returned;
	}
	
	public function getItemsForPrint($coop_id)
	{
		$orders = $this->getAllThisWeekOrders($coop_id);
		if ($orders != null)
		{
	    	foreach ($orders as $key => $order)
    		{
	    		$orders[$key]['items'] = $items = $this->getItemsByCategory($order['order_id']);
		    	$total = 0;
	    		foreach ($items as $item_key => $item)
	    		{
	    			$total += $orders[$key]['items'][$item_key]['cost'] = (float)($item['item_amount'] * $item['product_price']);
	    			
	    		}
	    		$orders[$key]['total'] = $total;
	    	}			
		}
		return $orders;
	}
	
	public function getWeekSummary($reset_day, $coop_id)
	{	
		$sql = "SELECT p.*, SUM(oi.item_amount) AS weekly_order, c.* 
		FROM products p, orders o, order_items oi, product_categories c 
		WHERE o.order_id = oi.order_id 
		AND oi.product_id = p.product_id 
		AND p.category_id = c.category_id 
		AND o.order_reset_day = '$reset_day'
		AND o.order_deleted = 0
		AND p.coop_id = " . (int)$coop_id . " 
		GROUP BY p.product_id 
		ORDER BY c.category_id, p.product_name";
				
		if (!$report = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $report;
	}
	
	public function getOrdersOfProduct($reset_day, $product_id)
	{
		$sql = "SELECT u.*, 
		o.*, 
		SUM( p.product_price  * oi.item_amount) AS order_amount, 
		oi.* FROM users u, orders o, order_items oi, products p 
		WHERE oi.order_id = o.order_id 
		AND o.user_id = u.user_id 
		AND oi.product_id = p.product_id 
		AND order_deleted = 0 
		AND o.order_reset_day = '$reset_day'
		AND p.product_id = $product_id 
		GROUP BY o.order_id";		
		
		if (!$report = $this->adapter->fetchAll($sql))
		{
			return false;
		}
		return $report;
	}	
        
        public function getPayments($coop_id, $reset_day)
        {
           $sql = "SELECT pc.category_id, p.`product_price` * SUM(oi.item_amount) AS payments
            FROM orders o, order_items oi, products p, product_categories AS pc 
            WHERE p.category_id = pc.category_id 
            AND oi.product_id = p.product_id 
            AND oi.order_id = o.order_id 
            AND o.order_reset_day = '$reset_day'
            AND p.coop_id = " . (int)$coop_id . " 
            GROUP BY pc.category_id;";
            
            if (!$data = $this->adapter->fetchAll($sql))
            {
                    return false;
            }
            
            $result = array();
            foreach ($data as $row)
            {
                $result[$row['category_id']] = $row['payments'];
            }
            
            return $result;
        }
	
	public function getOrder($id)
	{
		$sql = "SELECT * FROM users u, orders o WHERE o.user_id = u.user_id AND order_deleted = 0 AND order_id = " . (int)$id;
		if (!$order = $this->adapter->fetchRow($sql))
		{
			return false;
		}
		return $order;
	}
	
	public function addOrder($data)
	{
		return $this->add($data);
	}
	
	public function editOrder($id, $data)
	{
		return $this->edit($id, $data);
	}
	
	public function deleteOrder($id)
	{
		return $this->delete($id);
	}
}