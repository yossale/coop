<?php
use Mailgun\Mailgun;

require_once APPLICATION_PATH.'/CustomController.php';

class DutyController extends CustomController
{
	public function init()
	{
		parent::init();
		parent::checkAuth();
		
		$this->_smarty->assign('controller', 'duty');

		$submenu = array("index" => "הזמנות", "stock" => "מלאי", "duty-reports" => "דוח תורן", "previous-reports" => "דוחות קודמים", "weekly-report" => "טבלת סיכום שבועית");
		$this->_smarty->assign('submenu', $submenu);
		
		$this->_smarty->assign('hide_left_panel', '1');
    	
	}
	
    public function indexAction()
    {
    	$coop_id = $this->getCoopId();

        $coop_users = new Coop_Users();
        $users = $coop_users->getAllUsers($coop_id);
    	$this->_smarty->assign('users', $users);
        
        $coop_orders = new Coop_Orders(); 
    	$orders = $coop_orders->getOrdersGroupedByStatus($coop_id);
    	$this->_smarty->assign('orders', $orders);        
        
    	$this->_smarty->assign('action', 'index');
    	$this->_smarty->assign('tpl_file', 'orders/orders.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }

    public function debtAction()
    {
       $coop_debts = new Coop_Debt();
    	$coop_id = $this->getCoopId();		
    	$debts = $coop_debts->getDebts($coop_id);
        
    	$this->_smarty->assign('debts', $debts);
        $this->_smarty->assign('action', 'debt');
    	$this->_smarty->assign('tpl_file', 'orders/debts.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }
    
    public function stockAction()
	{
		$coop_coops = new Coop_Coops;
		$coop_id = $this->getCoopId(); 						
		$coop = $coop_coops->getCoop($coop_id);
        $params = $this->getRequest()->getParams();
		
		$request = $this->getRequest();
        $post = $request->getPost();

        if (!empty($params['reset_day']))
		{
			$reset_day = $params['reset_day'];
		}
		else 
		{			
			$coop = $coop_coops->getCoop($coop_id);
			$reset_day = $coop['coop_last_reset_day'];			
		}
                
       	// manager?
		$coop_users = new Coop_Users;
		$logged_id = $coop_users->getLoggedUserID();
        $logged_user = $coop_users->getUser($logged_id);
        $is_manager = $logged_user['user_access'] == 'SUPER';
        $this->_smarty->assign("is_manager", $is_manager);
                
		$coop_stock = new Coop_Stock;
		
		if (empty($post['stock']))
		{
			// get weekly report
			$coop_users = new Coop_Users;
			$logged_id = $coop_users->getLoggedUserID();
			
			$coop_products = new Coop_Products;
			$coop_orders = new Coop_Orders;
			$coop_reports = new Coop_JsonReports;
			$json = $coop_reports->getReportContentAsArray($coop_id, 'weekly-reports', $reset_day);
			
			
			$weekly_report = $json['report'];
			
			$this->_smarty->assign('stock', $json['stock']);
			$this->_smarty->assign('comments', $json['comments']);
			$this->_smarty->assign('products', $weekly_report);
		
            $reset_days = $coop_reports->getAllPossibleDates($coop_id, 'weekly-reports');
            $this->_smarty->assign('reset_days', $reset_days);
			
            $this->_smarty->assign('current_date', $reset_day);
            
            $this->_smarty->assign('action', 'stock');
            $this->_smarty->assign('tpl_file', 'duty/stock.tpl');
            $this->_smarty->display('common/layout.tpl');	
        }
        else
    	{
            $coop_stock->setStock($reset_day, $post['stock'], $post['comments'], $coop_id);

            $this->_smarty->assign('text', 'השינויים נשמרו בהצלחה');
            $this->_smarty->assign('url', PUBLIC_PATH . '/duty/stock/reset_day/' . $reset_day);
            $this->_smarty->display('common/thanks.tpl');	
        }
    }

	public function weeklyReportAction()
	{
		parent::baseWeeklyReport();
		
		$this->_smarty->assign('isFarmer', false);
		$this->_smarty->assign('action', 'weekly-report');
    	$this->_smarty->assign('tpl_file', 'duty/duty_weekly_report.tpl');
    	$this->_smarty->display('common/layout.tpl');
	}
    
    public function viewOrderAction()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$order_id = (int)$params['id'];
    	$coop_id = $this->getCoopId();
		
        $coop_users = new Coop_Users;
        $coop = $coop_users->getLoggedUserCoop();

        // for duties were always open...
        $this->_smarty->assign('is_open', true);    	    		
		
    	$coop_orders = new Coop_Orders();
    	$order = $coop_orders->getOrder($order_id);
    	$this->_smarty->assign('order', $order);

        $items = $coop_orders->getItems($order_id);
    	$this->_smarty->assign('items', $items);
    	
        $coop_products = new Coop_Products();
    	$categories = $coop_products->getAllProductsInsideCategories($coop_id, false);
    	$this->_smarty->assign('cats', $categories);
        
        $products = $coop_products->getAllProducts($coop_id);
        $this->_smarty->assign('products', $products);
        
    	$this->_smarty->assign('backto', '/duty');
    	$this->_smarty->assign('order_view_type', Coop_OrderViewType::get());

        $this->_smarty->assign('edit', '1');
        $this->_smarty->assign('duty_editing', true);

    	$this->_smarty->assign('tpl_file', 'duty/duty_view_order.tpl');
    	$this->_smarty->display('common/layout.tpl');    	
    }
    
    
    public function newOrderAction()
    {		
    	$coop_id = $this->getCoopId();
    	
        $params = $this->getRequest()->getParams();
        $user_id = (int)$params['id'];
        
        $coop_orders = new Coop_Orders();
        
        $existing_order = $coop_orders->getCurrentOrder($user_id);        
        if ($existing_order != false)
        {
            $order_id = $existing_order['order_id'];
        }
        else
        {
            $coop_users = new Coop_Users();
            
            $user = $coop_users->getUser($user_id);

            $coop_coops = new Coop_Coops();
            $coop = $coop_coops->getCoop($user['coop_id']);
            $reset_day = $coop['coop_last_reset_day'];

            $data = array("user_id" => $user_id, "order_status" => "unpayed", "order_date" => date("Y-m-d"), "order_reset_day" => $reset_day);
            $order_id = $coop_orders->addOrder($data);            
        }
        
        $this->_redirect("duty/view-order/id/" . $order_id);
        return;
        
        /*
    	$coop_users = new Coop_Users();
    	$user_id = (int)$this->getRequest()->getParam('id');
    	$user = $coop_users->getUser($user_id);
	
        $coop = $coop_users->getLoggedUserCoop();

        // for duties were always opened
        $this->_smarty->assign('is_open', true);    	    

	$coop_products = new Coop_Products();
    	$categories = $coop_products->getAllProductsInsideCategories($coop_id, false);
		
        foreach ($categories as $catKey => $category)
        {
                foreach ($category['products'] as $prodKey => $product)
                {
                        $categories[$catKey]['products'][$prodKey]['orders_count'] = $orders_count;
                }
        }

        $this->_smarty->assign('user', $user);

    	$this->_smarty->assign('cats', $categories);
    		
    	$this->_smarty->assign('user_id', $user_id);
	$this->_smarty->assign('backto', '/duty/view-order/id/%id%');
	$this->_smarty->assign('order_view_type', Coop_OrderViewType::get());
		
	$this->_smarty->assign('action', 'current');
    	$this->_smarty->assign('tpl_file', 'duty/new_order.tpl');
    	$this->_smarty->display('common/layout.tpl');    
         */	
    }
    
	
    public function changeStatusAction()
    {
    	$params = $this->getRequest()->getParams();
    	$status = $params['status'];
    	if ($status != "payed" && $status != "unpayed")
    	{
    		throw new Exception('invalid status');
    	}
    	$order_id = (int)$params['id'];
    	
    	$coop_orders = new Coop_Orders();
    	$coop_orders->editOrder($order_id, array('order_status' => $status));
    	echo 1;	
    }
    
    public function printAction()
    {    	
    	$coop_orders = new Coop_Orders();
		$coop_id = $this->getCoopId();
		
    	$orders = $coop_orders->getItemsForPrint($coop_id);
    	
    	$this->_smarty->assign('orders', $orders);
    	$this->_smarty->assign('date', date("d/m/y"));
    	
    	$this->_smarty->display('duty/duty_print.tpl');    	
    }

    public function dutyReportsAction()
    {
    	$coop_repots = new Coop_DutyReports();
    	$coop_id = $this->getCoopId();
		
    	$request = $this->getRequest();
    	if (!$request->isPost())
    	{
	    	$this->_smarty->assign('action', 'duty-reports');
	    	$this->_smarty->assign('date', date("d/m/y"));
	    	
	    	if (!$report = $coop_repots->getThisWeekReport($coop_id))
	    	{
		    	$template = $this->_smarty->fetch('duty/report_template.tpl');
		    	$this->_smarty->assign('content', $template);		
	    	}
	    	else 
	    	{
		    	$this->_smarty->assign('content', $report['report_content']);			    		
	    	}
	    	
			$this->_smarty->assign('coop_id', $coop_id);
	    	$this->_smarty->assign('tpl_file', 'duty/duty_reports.tpl');
	    	$this->_smarty->display('common/layout.tpl');
    		
    	}
    	else 
    	{
    		$post = $request->getPost();
    		
    		if (!$report = $coop_repots->getThisWeekReport($coop_id))
    		{
    			$coop_coops = new Coop_Coops();
				$coop = $coop_coops->getCoop($coop_id);
				$reset_day = $coop['coop_last_reset_day'];
		
	    		$data = array();
	    		$data['report_content'] = $post['report_content'];
	    		$data['report_week_number'] = date("W");
	    		$data['report_year'] = date("Y");   
				$data['coop_id'] = $coop_id;
				$data['report_reset_day'] = $reset_day;
	    		$coop_repots->newReport($data);
    		}
    		else 
    		{
    			
    			$data = array();
	    		$data['report_content'] = $post['report_content'];
	    		$coop_repots->editReport($report['report_id'], $data);
    		}
    		
    		if ($post['email'] == "1")
			{
                $config = Zend_Registry::get('config');
                $mailgun = $config->mailgun;

                $subject = "דוח תורן - " . date("d/m/y");
                $to = 'Coop <' . $config->email_sender->from_email . '>';

                $mg = new Mailgun($mailgun->key);
                $mg->sendMessage($mailgun->domain, array(
                    'from' => $mailgun->from,
                    'to'      => $to,
                    'subject' => $subject,
                    'html'    => $post['report_content']));
			}
			
			if (isset($post['ajax']) && $post['ajax'] == "1")
			{
				echo "1";
			}
			else 
			{
				$this->_redirect("duty/duty-reports");			
			} 
    	}
    }
    
}
