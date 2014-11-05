<?php
require_once APPLICATION_PATH.'/CustomController.php';

class DutyOrderController extends CustomController
{
	public function init()
	{
		parent::init();
		parent::checkAuth();
	}
	
    public function indexAction()
	{
		$this->handleOrder();
		$this->_smarty->display('duty/lightbox_order.tpl');
	}	
	
	private function handleOrder()
	{
		$params = $this->getRequest()->getParams();
    	
    	$order_id = (int)$params['id'];
    	$coop_id = $this->getCoopId();
		
		$coop_users = new Coop_Users;
		$coop = $coop_users->getLoggedUserCoop();

    	$coop_orders = new Coop_Orders();
    	$order = $coop_orders->getOrder($order_id);
		
	    if (!empty($order))
	    {
			$items = $coop_orders->getItems($order['order_id']);
            $this->_smarty->assign('order', $order);
			$this->_smarty->assign('items', $items); 
    	}
		
		$coop_products = new Coop_Products();
    	$categories = $coop_products->getAllProductsInsideCategories($coop_id, false);

		$this->_smarty->assign('cats', $categories);
	}
}
