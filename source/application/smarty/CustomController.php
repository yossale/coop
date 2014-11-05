<?php
require_once 'Smarty/Smarty.class.php';

class CustomController extends Zend_Controller_Action
{
	protected $_smarty;
	
	public function init()
	{
		$this->_smarty = new Smarty();
		$this->_smarty->setTemplateDir(APPLICATION_PATH . '/views/scripts');
		$this->_smarty->setCompileDir(APPLICATION_PATH . '/smarty/compiler');
		$this->_smarty->setCacheDir(APPLICATION_PATH . '/smarty/cache');
		$this->_smarty->setConfigDir(APPLICATION_PATH . '/smarty/configs');
		
		$renderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$renderer->setNeverRender(true);
		$menu = array("user" => "משתמש", "duty" => "תורן", "manager" => "מנהל");
		$this->_smarty->assign('menu', $menu);

		$this->_smarty->assign('public_path', PUBLIC_PATH);	
		$this->_smarty->assign('img_path', PUBLIC_PATH."/images");	
		$this->_smarty->assign('css_path', PUBLIC_PATH."/css");	
		$this->_smarty->assign('js_path', PUBLIC_PATH."/js");	
		
		$request = $this->getRequest();		
		$this->_smarty->assign('zf_action', $request->getActionName());	
		
		$this->getResponse()->setHeader("Content-Type", "text/html; charset=utf-8");	
	}
	
	public function checkAuth()
	{
		$users = new Coop_Users();
		if (!$users->isLogged())
		{
			$this->_redirect("/");
			return;
		}
		$user = $users->getUser($users->getLoggedUserID());
		$this->_smarty->assign('loggedUserName', $user['user_first_name'] . " " . $user['user_last_name']);
		$this->_smarty->assign('userAccess', $user['user_access']);
	}
	
	public function saveOrderAction()
	{
		
		$coop_order_items = new Coop_OrderItems();
		$coop_orders = new Coop_Orders();
		$coop_users = new Coop_Users();
		
		$post = $this->getRequest()->getPost();
		
		$order_id = (int)$post['order_id'];
		if (empty($order_id) && $this->getRequest()->getControllerName() == "user")
		{
			$order = $coop_orders->getCurrentOrder($coop_users->getLoggedUserID());
			if ($order != false)
			{
				$order_id = $order['order_id'];
			}
			else 
			{
				$order_id = $coop_orders->createCurrentOrder($coop_users->getLoggedUserID());			
			}
		}
	
		$coop_order_items->deleteAllItems($order_id);
		
		if (!empty($post['items']))
		{
			foreach ($post['items'] as $product_id => $items_amount)
			{
				if (!empty($items_amount))
				{				
					$item = array();
					$item['item_deleted'] = 0;
					$item['order_id'] = $order_id;
					$item['product_id'] = (int)$product_id;
					$item['item_amount'] = (float)$items_amount;
					$coop_order_items->addItem($item);
				}
			}
		}
		
		$coop_orders->editOrder($order_id, array('order_last_edit' => date('Y-m-d H:i:s')));
		
		$this->_redirect($post['backto']);
		
		return;
	}
	
	public function previousReportsAction()
	{
		$coop_reoprts = new Coop_DutyReports();
		$reports = $coop_reoprts->getAllReports();
    	$this->_smarty->assign('reports', $reports);
    	
    	$this->_smarty->assign('action', 'previous-reports');
    	$this->_smarty->assign('tpl_file', 'common/duty_reports.tpl');
    	$this->_smarty->display('common/layout.tpl');
	}
} 