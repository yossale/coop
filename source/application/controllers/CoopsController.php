<?php
class CoopsController extends CustomController
{
	public function init()
	{
		parent::init();
		parent::checkAuth();
    	$this->_smarty->assign('controller', 'coops');
    	
    	$submenu = array("index" => "ניהול קואופרטיבים");
		$this->_smarty->assign('submenu', $submenu);
		
		$users = new Coop_Users();
		if ($users->isLogged())
		{
			$user = $users->getUser($users->getLoggedUserID());
			if ($user['user_access'] != 'SUPER')
			{
				$this->_redirect("/");
				return;
			}
		}
	}
	
	public function indexAction()
	{
		$coops = new Coop_Coops;
		$coop_users = new Coop_Users;
		$logged_id = $coop_users->getLoggedUserID();
		
		$list = $coops->getAllCoops();
		$this->_smarty->assign('list', $list);    	
		
		$user = $coop_users->getUser($logged_id);
		$this->_smarty->assign('user', $user);
		
    	$this->_smarty->assign('action', 'index');
    	$this->_smarty->assign('tpl_file', 'supermanager/coops_list.tpl');
    	$this->_smarty->display('common/layout.tpl');		
	}
	
	public function addAction()
	{
		$request = $this->getRequest();
		
		if (!$request->isPost())
		{
	    	$this->_smarty->assign('action', 'index');
	    	$this->_smarty->assign('tpl_file', 'supermanager/coops_form.tpl');
	    	$this->_smarty->display('common/layout.tpl');					
		}
		else
		{
			$post = $request->getPost();

			$user = $post['user'];
			$user['user_access'] = "MANAGER";
			
			$users = new Coop_Users();
			$post['coop_manager_user_id'] = $users->addUser($user);

			$coops = new Coop_Coops;
			$coop_id = $coops->addCoop($post);
			$this->_redirect("/coops");		
		}
	}
	
	public function editAction()
	{
		$request = $this->getRequest();
		
		$coops = new Coop_Coops;
		$coop_id = $request->getParam("id");
		$coop = $coops->getCoop($coop_id);		
			
		if (!$request->isPost())
		{
			$this->_smarty->assign('data', $coop);
			
	    	$this->_smarty->assign('action', 'index');
	    	$this->_smarty->assign('tpl_file', 'supermanager/coops_form.tpl');
	    	$this->_smarty->display('common/layout.tpl');					
		}
		else
		{
			$post = $request->getPost();
			$coops->editCoop($coop_id, $post);
			$this->_redirect("/coops");
		}
	}
	
	public function deleteAction()
	{
		$request = $this->getRequest();
		
		$coops = new Coop_Coops;
		$coop_id = $request->getParam("id");
		$coop = $coops->getCoop($coop_id);		
		$coops->deleteCoop($coop_id);
		
		echo Zend_Json::encode(array("success" => 1));
	}
}
		