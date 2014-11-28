<?php
require_once 'Smarty/Smarty.class.php';

class CustomController extends Zend_Controller_Action
{
	protected $_smarty;
	protected $_isSuperGuest = false;
	
	public function init()
	{
		$this->_smarty = new Smarty();
		$this->_smarty->setTemplateDir(APPLICATION_PATH . '/views/scripts');
		$this->_smarty->setCompileDir(APPLICATION_PATH . '/smarty/compiler');
		$this->_smarty->setCacheDir(APPLICATION_PATH . '/smarty/cache');
		$this->_smarty->setConfigDir(APPLICATION_PATH . '/smarty/configs');
		$this->_smarty->addPluginsDir(APPLICATION_PATH . '/smarty/plugins');
		
		$renderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$renderer->setNeverRender(true);
		$menu = array("user" => "משתמש", "duty" => "תורן", "manager" => "מנהל");
		
		$users = new Coop_Users();
		if ($users->isLogged())
		{
			$user = $users->getUser($users->getLoggedUserID());
			
			$contoller_name = $this->getRequest()->getControllerName();

			if ($user['user_access'] == 'SUPER')
			{
				$menu["coops"] = "מנהל-על";
				if ($user['coop_id'] != $this->getCoopId())
				{
					unset($menu["duty"]);
					unset($menu["user"]);
					if ($contoller_name == "user" || $contoller_name == "duty")
					{
						$this->_redirect("/coops");
					}
					$this->_isSuperGuest = true;
				}
			}
			
			if ($user['user_access'] == 'FARMER')
			{
				$menu = array("farmer" => "חלקאי"); 
				if ($contoller_name == "user")
				{
					$this->_redirect("/farmer");
				}
			} 
                        
                        //cooperativeta@gmail.com patch
                        if ($user['user_email'] == "cooperativeta@gmail.com")
                        {
                            $menu = array("duty" => "תורן");
                            if ($contoller_name == "user")
                            {
                                $this->_redirect("/duty");
                            }
                        }
		}
		
		$this->_smarty->assign('menu', $menu);

		$this->_smarty->assign('public_path', PUBLIC_PATH);	
		$this->_smarty->assign('img_path', PUBLIC_PATH."/images");	
		$this->_smarty->assign('css_path', PUBLIC_PATH."/css");	
		$this->_smarty->assign('js_path', PUBLIC_PATH."/js");	
		
		$request = $this->getRequest();		
		$this->_smarty->assign('zf_action', $request->getActionName());	
		
		$this->getResponse()->setHeader("Content-Type", "text/html; charset=utf-8");			
	}

	public function reloginAction()
	{
		$host = "http://" . $_SERVER['HTTP_HOST'];
		$this->_redirect($host);
	}
	
	public function checkAuth()
	{
		$users = new Coop_Users();
		if (!$users->isLogged())
		{
			$this->_redirect("/index/relogin");
			return;
		}
		$user = $users->getUser($users->getLoggedUserID());
		$this->_smarty->assign('loggedUserName', $user['user_first_name'] . " " . $user['user_last_name']);
		$this->_smarty->assign('userAccess', $user['user_access']);
		
		$users = new Coop_Users();
		$coop = $users->getLoggedUserCoop();
		$this->_smarty->assign('coop', $coop);
	}

	public function getCoopId()
	{
		$users = new Coop_Users();
		$coop = $users->getLoggedUserCoop();
		if (!isset($coop) || empty($coop))
		{
			throw new Exception("could not find coop");
		}
		return $coop['coop_id']; 
	}
	
	public function saveOrderAction()
	{		
		$coop_order_items = new Coop_OrderItems();
		$coop_orders = new Coop_Orders();
		$coop_users = new Coop_Users();		
		$post = $this->getRequest()->getPost();

                if (isset($post['order_id']) && !empty($post['order_id']))
		{
			echo "by order\n";
			$order_id = (int)$post['order_id'];
			echo "order id: $order_id \n";
			$order = $coop_orders->getOrder($order_id);
		}
		else
		{
			echo "by user \n";
			$user_id = (isset($post['user_id']) && !empty($post['user_id'])) ? (int)$post['user_id'] : $coop_users->getLoggedUserID(); 

			$order = $coop_orders->getCurrentOrder($user_id);
			if ($order == false)
			{
			 	$order = $coop_orders->getOrder($coop_orders->createCurrentOrder($user_id));
				echo "create new order\n";	
			}			
			else
			{
				echo "use exsiting order\n";
			}
		}
		$coop_order_items->deleteAllItems($order['order_id']);
		if (!empty($post['items']))
		{
			foreach ($post['items'] as $product_id => $items_amount)
			{
				if (!empty($items_amount))
				{				
					$item = array();
					$item['item_deleted'] = 0;
					$item['order_id'] = $order['order_id'];
					$item['product_id'] = (int)$product_id;
					$item['item_amount'] = (float)$items_amount;
					$coop_order_items->addItem($item);
				}
			}
		}
		$coop_orders->editOrder($order['order_id'], array('order_last_edit' => date('Y-m-d H:i:s')));
		
                
                
                if (isset($post['user_comments']))
                {
                    $order = $coop_orders->getOrder($order['order_id']);
                    
                    $coop_users->editUser($order['user_id'], array("user_comments" => $post['user_comments']));
                }
                
                
                $backto = str_replace("%id%", $order['order_id'], $post['backto']);		

		$this->_redirect($backto);		
	}
	
	public function previousReportsAction()
	{
		$coop_reoprts = new Coop_DutyReports();
		
		$coop_id = $this->getCoopId();
		
		$reports = $coop_reoprts->getAllReportsInReverseOrder($coop_id);
    	$this->_smarty->assign('reports', $reports);
    	
    	$this->_smarty->assign('action', 'previous-reports');
    	$this->_smarty->assign('tpl_file', 'common/duty_reports.tpl');
    	$this->_smarty->display('common/layout.tpl');
	}
	
	protected function baseWeeklyReport()
	{
		$coop_id = $this->getCoopId(); 
		$coop_coops = new Coop_Coops;
		$coop = $coop_coops->getCoop($coop_id);

		$post = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() && isset($post['reset_day']) && !empty($post['reset_day']))
		{
			$reset_day = $post['reset_day'];
		}
		else 
		{						
			$reset_day = $coop['coop_last_reset_day'];			
		}
		$this->_smarty->assign('current', $reset_day);

		$json_reports_model = new Coop_JsonReports;
		$weekly_report = $json_reports_model->getReportContentAsArray($coop_id, 'weekly-reports', $reset_day);		
		
		foreach ($weekly_report as $name => $object)
		{
			$this->_smarty->assign($name, $object);	
		}
		
		// reset days
		$list = $json_reports_model->getAllPossibleDates($coop_id, 'weekly-reports');
		
    	$this->_smarty->assign('reset_days', $list);
	}
	
	
	protected function baseWeeklyReport_backup()
	{
		$coop_coops = new Coop_Coops;
		$coop_id = $this->getCoopId();
		$post = $this->getRequest()->getPost();
		if ($this->getRequest()->isPost() && isset($post['reset_day']) && !empty($post['reset_day']))
		{
			$reset_day = $post['reset_day'];
		}
		else 
		{			
			$coop = $coop_coops->getCoop($coop_id);
			$reset_day = $coop['coop_last_reset_day'];			
		}
		$this->_smarty->assign('current', $reset_day);
		
		$wr = new Coop_WeeklyReports;
		$coop_id = $this->getCoopId(); 
		$weekly_report = $wr->getWeeklyReport($coop_id, $reset_day);
		$this->_smarty->assign($weekly_report);
		
		// reset days
		$coop_orders = new Coop_Orders();		
		$reset_days = $coop_orders->getAllPossibleResetDays($coop_id);
		$list = array();
		foreach ($reset_days as $day)
		{
			if ($day['order_reset_day'] != '0000-00-00')
			{
				$list[] = $day['order_reset_day'];			
			}
		}
    	$this->_smarty->assign('reset_days', $list);
	}
	
	
	protected function baseWeeklyReport3()
	{
		$post = $this->getRequest()->getPost();
		$coop_coops = new Coop_Coops();
		$coop_id = $this->getCoopId(); 
		
		if ($this->getRequest()->isPost() && isset($post['reset_day']) && !empty($post['reset_day']))
		{
			$reset_day = $post['reset_day'];
		}
		else 
		{			
			$coop = $coop_coops->getCoop($coop_id);
			$reset_day = $coop['coop_last_reset_day'];			
		}
		$this->_smarty->assign('current', $reset_day);
		
		$this->setSmaryForWeeklyReport($reset_day, $coop_id);
		
		// reset days
		$coop_orders = new Coop_Orders();		
		$reset_days = $coop_orders->getAllPossibleResetDays($coop_id);
		$list = array();
		foreach ($reset_days as $day)
		{
			if ($day['order_reset_day'] != '0000-00-00')
			{
				$list[] = $day['order_reset_day'];			
			}
		}
    	$this->_smarty->assign('reset_days', $list);
		
		
	}

	public function setSmaryForWeeklyReport($reset_day, $coop_id) 
	{
		$coop_orders = new Coop_Orders();
		
		$weekly_report = $coop_orders->getWeekSummary($reset_day, $coop_id);
		
		if ($weekly_report != null)
		{
			// get orders for each product
			foreach ($weekly_report as $key => $product)
			{
				$orders = $coop_orders->getOrdersOfProduct($reset_day, $product['product_id']);
				if ($orders)
				{
					$weekly_report[$key]['orders'] = $orders;
				}
	
				$weekly_report[$key]['orders_count'] = (int)$coop_orders->countWeeklyOrdersForProduct($coop_id, $product['product_id'], $reset_day);
			}
			
		}
		// get stock
		
		$coop_stock = new Coop_Stock;
		 
		$stockResults = $coop_stock->getStock($reset_day);
		$stock = array();
		$comments = array();
		if ($stockResults)
		{
			foreach ($stockResults as $row)
			{
				$stock[$row['product_id']] = $row['stock_amount'];
				$comments[$row['product_id']] = $row['stock_comments'];
			}
		}
		$this->_smarty->assign('stock', $stock);
		$this->_smarty->assign('comments', $comments);

		// sum
		if (!empty($weekly_report))
		{
			$currentCategory = 0;
			$supplierPayment = array();
			$supplierPaymentSum = array();
		
			$userPayment = array();
			$userPaymentSum = array();
		
			foreach ($weekly_report as $product)
			{
				$supplierPayment[$product['category_id']][] = $product['product_coop_cost'] * $stock[$product['product_id']];
				
				$userPayment[$product['category_id']][] = $product['product_price'] * $stock[$product['product_id']];
			}
			
			foreach ($supplierPayment as $category_id => $amounts)
			{
				$supplierPaymentSum[$category_id] = array_sum($amounts);
			}
			$this->_smarty->assign('sum', $supplierPaymentSum);

			foreach ($userPayment as $category_id => $amounts)
			{
				$userPaymentSum[$category_id] = array_sum($amounts);
			}
			$this->_smarty->assign('user_payments', $userPaymentSum);
		}

        $payments = $coop_orders->getPayments($coop_id, $reset_day);				
		
        $this->_smarty->assign('payments', $payments);
       	$this->_smarty->assign('report', $weekly_report);
		
		// current prices
		$coop_prices = new Coop_Prices;
		$prices = $coop_prices->getPrices($coop_id, $reset_day);
		$this->_smarty->assign('prices', $prices);
	}
    
} 