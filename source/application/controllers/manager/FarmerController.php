<?php
class FarmerController extends CustomController
{
	public function init()
	{
		parent::init();
		parent::checkAuth();
    	$this->_smarty->assign('controller', 'farmer');
    	
    	$submenu = array("index" => "מחירים", "report" => "טבלת סיכום");
		$this->_smarty->assign('submenu', $submenu);
		
	}
	 
	public function indexAction()
	{ 
		$coop_coops = new Coop_Coops;
		$coop_id = $this->getCoopId();
		if ($coop_id != 1)
		{
			$this->_smarty->assign('tpl_file', 'farmer/not_tlv.tpl');
	    	$this->_smarty->display('common/layout.tpl');
			return;
		}
		
		$coop_users = new Coop_Users;
		$logged_id = $coop_users->getLoggedUserID();
		
		$coop_products = new Coop_Products;
		
		$request = $this->getRequest();
		if (!$request->isPost())
		{
                    $coop_categories = new Coop_Categories();
                    $cats = $coop_categories->getAllCategoriesForHTMLSelectBox($coop_id);
                    
                    print_r($cats);
                    
                    // todo: make this generic for other coops
                    $allowed = array(1,2,3,4,7);
                    
                    foreach ($cats as $key => $data)
                    {
                        if (!in_array($key, $allowed))
                        {
                            unset($cats[$key]);
                        }
                    }
                    
                    $this->_smarty->assign('category_options', $cats);


			$products = $coop_products->getAllFruitsAndVegtebles();		
			$this->_smarty->assign('products', $products);
	    	$this->_smarty->assign('action', 'index');
	    	$this->_smarty->assign('tpl_file', 'farmer/prices.tpl');
	    	$this->_smarty->display('common/layout.tpl');	
		}
		else
		{
			$post = $request->getPost();
			$coop_products->updateFruitsAndVegtebles($post['shortage'], $post['prices']);
			
			$this->_smarty->assign('text', 'השינויים נשמרו בהצלחה');
			$this->_smarty->assign('url', PUBLIC_PATH . '/farmer');
			$this->_smarty->display('common/thanks.tpl');	
		}
	}
	 
	public function reportAction()
	{
		parent::baseWeeklyReport();
		
		$this->_smarty->assign('isFarmer', true);
		$this->_smarty->assign('action', 'report');
    	$this->_smarty->assign('tpl_file', 'duty/duty_weekly_report.tpl');
    	$this->_smarty->display('common/layout.tpl');
	}
        

        public function printAction()
    {
    	parent::baseWeeklyReport();
		
        $this->_smarty->display('farmer/farmer_print.tpl');    	
    }

    public function addProductAction()
    {
        $request = $this->getRequest();
        $coop_id = $this->getCoopId();
        
        $post = $this->getRequest()->getPost();
        $post['product_price'] = $post['product_coop_cost'] + ($post['product_coop_cost'] * 0.15); 
        
        $coop_products = new Coop_Products();
        $product_id = $coop_products->addProduct($coop_id, $post);
        
        $this->_smarty->assign('text', 'המוצר נוסף בהצלחה');
        $this->_smarty->assign('url', PUBLIC_PATH . '/farmer');
        $this->_smarty->display('common/thanks.tpl');	
    }
}	