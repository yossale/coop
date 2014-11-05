<?php
class ManagerController extends CustomController
{
	public function init()
	{
		parent::init();
		parent::checkAuth();
    	$this->_smarty->assign('controller', 'manager');
    	$submenu = array("index" => "ראשי", "farmer-order" => "הזמנה לחקלאי", "previous-reports" => "דוחות תורנים", "products" => "מוצרים", "categories" => "קטגוריות", "weekly-report" => "טבלת סיכום שבועית", "email" => "שליחת מייל", "users" => "ניהול משתמשים", "updates" => "עדכונים", "debts" => "חובות");
		
		$this->_smarty->assign('hide_left_panel', '1');
    	
		$days = array("Sunday" => "ראשון", "Monday" => "שני", "Tuesday" => "שלישי", "Wednesday" => "רביעי", "Thursday" => "חמישי", "Friday" => "שישי", "Saturday" => "שבת");
    	$this->_smarty->assign('days', $days);
    	
		if ($this->_isSuperGuest)
		{
			$submenu = array("users" => "ניהול משתמשים");	
			$action_name = $this->getRequest()->getActionName();
			if ($action_name != "users" && $action_name != "add-user" && $action_name != "edit-user" && $action_name != "delete-user")
			{
				$this->_redirect("/manager/users");
			}
		}
		$this->_smarty->assign('submenu', $submenu);
	}

	public function testAction() {
		$json_reports_model = new Coop_JsonReports;
		$json_reports_model->addWeeklyReportToAllCoops();
	}
	
	// index

	public function indexAction()
	{
		// change opennig days
		$request = $this->getRequest();
    	if ($request->isPost())
    	{
			$post = $request->getPost();
			
			$id = $this->getCoopId();
			
			$coop_coops = new Coop_Coops();
			$coop_coops->editCoop($id, $post);
			
			$this->_redirect($this->public_path . "/manager/index/saved/1");	
		}
		
		$params = $request->getParams();
		if ($params['saved'] == "1")
		{
			$this->_smarty->assign("saved", 1);
		}
		
		$users = new Coop_Users;
		$coop = $users->getLoggedUserCoop();
		
    	$is_open = (bool)$coop['coop_is_open_now'];		
    	$this->_smarty->assign('open_for_orders', $is_open);
		
		// days
		$days = array("ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת");
		$this->_smarty->assign('days', $days);
		
    	$this->_smarty->assign('action', 'index');
    	$this->_smarty->assign('tpl_file', 'manager/rounds.tpl');
    	$this->_smarty->display('common/layout.tpl');		
	}
	
	// list all users (for newsletter plugin)
	public function listForNewsletterAction()
	{
		$coop_id = $this->getCoopId();
		
		$coop_users = new Coop_Users();
		$users = $coop_users->getAllUsers($coop_id);
		echo "<pre>";
		foreach ($users as $user)
		{
			echo $user['user_email'] . ";" . stripslashes($user['user_first_name']). ";" . stripslashes($user['user_last_name']) . "\n";
			for ($i = 1; $i < 5; $i++)
			{
				if (!empty($user['user_email' . $i]))
				{
					echo $user['user_email' . $i] . ";" . stripslashes($user['user_first_name']). ";" . stripslashes($user['user_last_name']) . "\n";				
				}
			}
		}
	}
	
	// AJAX - Open for orders?
	public function isOpenForOrdersAction()
	{
		$params = $this->getRequest()->getParams();
		
		$change = (int)$params['open'];
		
		$coop_id = $this->getCoopId();
		$coops_model = new Coop_Coops;
		$coops_model->editCoop($coop_id, array("coop_is_open_now" => $change));

		if ($change == 0) {
			$coop = $coops_model->getCoop($coop_id);
			$coops_model->closeCoopWithoutCheck($coop);
		}

		echo Zend_Json::encode(array('success' => 1));

	}
	
	// farmer order
	
	public function farmerOrderAction()
	{
		$coop_id = $this->getCoopId();
		$coop_orders = new Coop_Orders();
    	$report = $coop_orders->getFarmerReportForThisWeek($coop_id);
    	$this->_smarty->assign('report', $report);
    	
    	$this->_smarty->assign('action', 'farmer-order');
    	$this->_smarty->assign('tpl_file', 'manager/farmer_report.tpl');
    	$this->_smarty->display('common/layout.tpl');    	
	}
	
	public function printFarmerOrderAction()
	{
		$coop_orders = new Coop_Orders();
    	$report = $coop_orders->getFarmerReportForThisWeek($coop_id = $this->getCoopId());
    	$this->_smarty->assign('report', $report);
    	$this->_smarty->assign('date', date("d/m/y"));
    	$this->_smarty->display('manager/print_farmer_report.tpl');    			
	}
		
	protected function convertToSQLFormat($date, $time)
	{
		$explode = explode("/", $date);
		return $explode[2] . "-" . $explode[1] . "-" . $explode[0] . " " . $time . ":00";
	}
	
	// products
	
    public function productsAction()
    {
    	$coop_products = new Coop_Products();
		$coop_orders = new Coop_Orders();
    	$coop_id = $this->getCoopId();
    	$categories = $coop_products->getAllProductsInsideCategories($coop_id, true);
		
    	$this->_smarty->assign('categories', $categories);			
    	$this->_smarty->assign('action', 'products');
    	$this->_smarty->assign('tpl_file', 'manager/products.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }
	
	public function supplyAction()
	{
		$coop_products = new Coop_Products();
		$coop_supply = new Coop_Supplies();
	
    	$request = $this->getRequest();
		
		$product_id = (int)$request->getParam("product");
    	$product = $coop_products->getProduct($product_id);
    	
    	if (!$request->isPost())
    	{
    		$supply = $coop_supply->getAllSupplies($product_id);

    		$this->_smarty->assign('supplies', $supply);
			$this->_smarty->assign('date', date("d/m/Y"));
			$this->_smarty->assign('product', $product); 
    		$this->_smarty->assign('tpl_file', 'manager/supplies.tpl');
    		$this->_smarty->display('common/layout.tpl');    			
		}
		else
		{
			$post = $request->getPost();
			if (isset($post['oper']) && $post['oper'] == "add")
			{
				$data = array();
				$data['supply_date'] = date("Y-m-d");
				$data['supply_amount'] = (int)$post['supply_amount'];
				$data['product_id'] = (int)$post['product_id'];
				$coop_supply->addSupply($data);
				
				$url  = "/manager/supply/product/" . $post['product_id'];
				$this->_redirect($url);
			}	
		}		
	}
	
    public function addProductAction()
    {
    	$request = $this->getRequest();
		$coop_id = $this->getCoopId();
		    	
    	if (!$request->isPost())
    	{
    		$coop_categories = new Coop_Categories();
    		$this->_smarty->assign('category_options', $coop_categories->getAllCategoriesForHTMLSelectBox($coop_id));

    		$this->_smarty->assign('tpl_file', 'manager/product_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{
    		$coop_products = new Coop_Products();
    		$product_id = $coop_products->addProduct($coop_id, $request->getPost());
			
			if ($this->getProductImagesModel()->save($product_id))
			{
				$coop_products->editProduct($product_id, array('product_image' => 1));
			}			
    		
    		$this->_redirect($this->public_path . "/manager/products");
    	}
    }
    
    public function editProductAction()
    {
    	$coop_products = new Coop_Products();
    	$request = $this->getRequest();
    	$product_id = $request->getParam("id");
   		$coop_id = $this->getCoopId();
		
    	if (!$request->isPost())
    	{
    		$coop_categories = new Coop_Categories();
    		$this->_smarty->assign('category_options', $coop_categories->getAllCategoriesForHTMLSelectBox($coop_id));

    		$product = $coop_products->getProduct($product_id);
                
                $coop_supply = new Coop_Supply;
                $supplies = $coop_supply->getSupplyByProduct($product['product_id']);
                
    		$this->_smarty->assign('product', $product);                                 
    		$this->_smarty->assign('supplies', $supplies); 
	        $this->_smarty->assign('tpl_file', 'manager/product_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{     		
    		$post = $request->getPost();
			$post['product_in_shortage'] = "1" ? ($post['product_in_shortage'] == "1") : "0";
			
			if ($this->getProductImagesModel()->save($product_id))
			{
				$post['product_image'] = "1";
			}
			
    		$coop_products->editProduct($product_id, $post);
    		$this->_redirect("/manager/products");
    	}
    }
    
    public function addSupplyAction()
    {
        $request = $this->getRequest();
        if ($request->isPost())
    	{
            $post = $request->getPost();
            $post['supply_date'] = date("Y-m-d");
            $coop_supply = new Coop_Supply;
            $coop_supply->addSupply($post);
            $this->_redirect("/manager/edit-product/id/" . $post['product_id'] . "#supply");
        }
    }
    
    public function deleteProductAction()
    {
    	$coop_products = new Coop_Products();
		$id = (int)$this->getRequest()->getParam("id");
		$this->getProductImagesModel()->delete($id);
    	$coop_products->deleteProduct($id);
    	echo Zend_Json::encode(array("success" => 1));
    }	
	
	// Product Images
	
	private function getProductImagesModel()
	{
		$config = Zend_Registry::get('config');
		$model = new Application_Model_UploadImages($config->uploads . "/temp/", $config->uploads . "/products/");
		return $model;		
	}
	
	public function productImageUploadAction()
	{
		$this->getProductImagesModel()->upload();	      
	}
	
	public function productImagePreviewAction()
	{
		$this->getProductImagesModel()->preview();		
	}
	
	public function productImagePreviewContainerAction()
	{
		$this->_smarty->assign("img_path", "/manager/product-image-preview/rand/" . rand());
		$this->_smarty->display("myImageUploader/img.tpl");
	}
	
	public function productImageViewAction()
	{
		$id = (int)$this->getRequest()->getParam('id');
		$this->getProductImagesModel()->view($id);
	}
	
	public function productImageViewContainerAction()
	{
		$id = (int)$this->getRequest()->getParam('id');
		
		$this->_smarty->assign("img_path", "/manager/product-image-view/id/$id");
		$this->_smarty->display("myImageUploader/img.tpl");
	}
	
	public function productImageDelete()
	{
		$id = $this->getRequest()->getParam('id');
		$this->getProductImagesModel()->delete($id);
	}
    
	// Categories
	
    public function categoriesAction()
    {
		$coop_id = $this->getCoopId();
    	
    	$coop_categories = new Coop_Categories();
    	$categories = $coop_categories->getAllCategories($coop_id);
    	$this->_smarty->assign('categories', $categories);
    	
    	$this->_smarty->assign('action', 'categories');
    	$this->_smarty->assign('tpl_file', 'manager/categories.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }
	
    public function addCategoryAction()
    {
    	$this->_smarty->assign('action', 'categories');
   		$request = $this->getRequest();
    	$coop_id = $this->getCoopId();
		
    	if (!$request->isPost())
    	{		
	        $this->_smarty->assign('tpl_file', 'manager/category_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{
    		$coop_categories = new Coop_Categories();
    		$coop_categories->addCategory($coop_id, $request->getPost());
    		$this->_redirect("/manager/categories");
    	}
    }
    
    public function editCategoryAction()
    {
    	$coop_categories = new Coop_Categories();    		
    	$category_id = $this->getRequest()->getParam("id");
    	
		if (!$this->getRequest()->isPost())
    	{
    		$category = $coop_categories->getCategory($category_id);
    		$this->_smarty->assign('action', 'categories');
    		$this->_smarty->assign('category', $category); 
	        $this->_smarty->assign('tpl_file', 'manager/category_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{ 
    		$post = $this->getRequest()->getPost();
    		$coop_categories->editCategory($category_id, $post);    		
    		$this->_redirect("/manager/categories");
    	}
    }
    
    public function deleteCategoryAction()
    {
    	$category_id = $this->getRequest()->getParam("id");

    	$coop_categories = new Coop_Categories();    		
		$category = $coop_categories->deleteCategory($category_id);
		
    	echo Zend_Json::encode(array("success" => 1));
    }
    
    // Weekly report
    public function weeklyReportAction()
    {
    	
		parent::baseWeeklyReport();
    	
		$this->_smarty->assign('isFarmer', false);
    	$this->_smarty->assign('action', 'weekly-report');
    	$this->_smarty->assign('tpl_file', 'manager/weekly_report.tpl');
    	$this->_smarty->display('common/layout.tpl');    	
    }
	
    // Send Email
    
    public function emailAction()
    {
    	$request = $this->getRequest();
		$coop_users = new Coop_Users();
			
    	$coop_id = $this->getCoopId();
		
		$users = $coop_users->getAllUsers($coop_id);
		$this->_smarty->assign('users', $users);

		if (!$request->isPost())
    	{
			// get config settings
			$coop = $coop_users->getLoggedUserCoop();
			
	    	$this->_smarty->assign('from_name', $coop['coop_name']);
	    	$this->_smarty->assign('from_email', $coop['coop_email']);
			
			$this->_smarty->assign('action', 'email');		   
	    	$this->_smarty->assign('tpl_file', 'manager/send_email.tpl');
	    	$this->_smarty->display('common/layout.tpl');
    	}
    	else 
    	{ 
    		$post = $request->getPost();
			$coop_email_msgs = new Coop_EmailMessages();
			$coop_email_deliveries = new Coop_EmailDeliveries();
			$email_msg_id = $coop_email_msgs->addEmailMessage($coop_id, $post);
			// get users
			switch ($post['send_to'])
			{
				case "everyone":
					$users_to_send = $users;
					break;
				
				case "specific_users":
					$users_to_send = array();
					foreach ($post['users'] as $user_id => $to_send)
					{
						if ($to_send == "1")
						{
							foreach ($users as $user)
							{
								if ($user['user_id'] == $user_id)
								{
									$users_to_send[] = $user;
								}
							}
						}
					}
					break;
			}
			
			// add deliveries to queue
			foreach ($users_to_send as $user)
			{
				$delivery = array("email_msg_id" => $email_msg_id,
					"email_delivery_address" => $user['user_email'],
					"email_delivery_cc1" => $user['user_email2'],
					"email_delivery_cc2" => $user['user_email3'],
					"email_delivery_cc3" => $user['user_email4'],
					"email_delivery_name" => stripslashes($user['user_first_name'] . " " . $user['user_last_name']),
					"email_delivery_added_datetime" => date("Y-m-d H:i:s"),
					"user_id" => $user['user_id']);
				$coop_email_deliveries->addDelivery($delivery);
			}	
			
			$this->_redirect("manager/index");
    	}
    }
    
    // Users

    public function usersAction()
    {
    	$coop_id = $this->getCoopId();
		$coop_users = new Coop_Users();
		$users = $coop_users->getAllUsers($coop_id);
		$this->_smarty->assign('users', $users);
		
    	$this->_smarty->assign('action', 'users');
    	$this->_smarty->assign('hide_left_panel', '1');
    	$this->_smarty->assign('tpl_file', 'manager/users.tpl');
    	$this->_smarty->display('common/layout.tpl');
    
	}

    public function addUserAction()
    {
   		$request = $this->getRequest();
    	
    	if (!$request->isPost())
    	{		
			// get default password
			$config = Zend_Registry::get('config');
			$default_password = $config->users->default_password;			
	        $this->_smarty->assign('default_password', $default_password);

	        $this->_smarty->assign('tpl_file', 'manager/users_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{
    		$coop_id = $this->getCoopId();
			$coop_users = new Coop_Users();
    		$post = $request->getPost();
			$coop_users->addUser($coop_id, $post);
    		$this->_redirect("/manager/users");
    	}
    }
    
    public function editUserAction()
    {
    	$request = $this->getRequest();
    	$user_id = $request->getParam("id");
		$coop_users = new Coop_Users();
		
		$logged_user_id = $coop_users->getLoggedUserID();
		$logged_user = $coop_users->getUser($logged_user_id);
		$this->_smarty->assign("allow_super", ($logged_user['user_access'] == "SUPER"));
		
    	if (!$request->isPost())
    	{
			$user = $coop_users->getUser($user_id);
    		$this->_smarty->assign('user', $user);
	        $this->_smarty->assign('tpl_file', 'manager/users_form.tpl');
    		$this->_smarty->display('common/layout.tpl');    
    	}
    	else 
    	{ 
    		$post = $request->getPost();
    		$coop_users->editUser($user_id, $post);
			$this->_redirect("/manager/users");
    	}
    }
    
    public function deleteUserAction()
    {
		$user_id = $this->getRequest()->getParam("id");

    	$coop_users = new Coop_Users();    		
		$coop_users->deleteUser($user_id);
    	echo Zend_Json::encode(array("success" => 1));    }
        
    public function updatesAction()
    {
        $coop_id = $this->getCoopId();
        $coop_coops = new Coop_Coops;
        
        $request = $this->getRequest();
    	if ($request->isPost())
    	{
            $post = $request->getPost();
            $coop_coops->editCoop($coop_id, array("coop_notes" => $post['coop_notes']));
        }

        $coop = $coop_coops->getCoop($coop_id);        
        $this->_smarty->assign('coop', $coop);
        $this->_smarty->assign('action', 'index');
    	$this->_smarty->assign('tpl_file', 'manager/updates.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }
    public function debtsAction()
    {
    	$coop_id = $this->getCoopId();
		$coop_users = new Coop_Users();
		$users = $coop_users->getAllUsers($coop_id);
		
		$this->_smarty->assign('users', $users);
		
    	$this->_smarty->assign('action', 'debts');
    	$this->_smarty->assign('hide_left_panel', '1');
    	$this->_smarty->assign('tpl_file', 'manager/debts.tpl');
    	$this->_smarty->display('common/layout.tpl');
    
	}
}

