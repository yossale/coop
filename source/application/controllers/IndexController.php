<?php
require_once APPLICATION_PATH.'/CustomController.php';

class IndexController extends CustomController
{
	public function init()
	{
		parent::init();
	}
	
    public function indexAction()
    {
    	$request = $this->getRequest();
    	
        $redirect_error = null;
        
    	$coop_id = (int)$request->getParam('coop');
        if (empty($coop_id))
        {
            $redirect_error = "NO_COOP_ID";
        }
        $coops = new Coop_Coops;
        $coop = $coops->getCoopSafely($coop_id);
        if ($coop == null)
        {
            $redirect_error = "INVALID_COOP";
        }
        
        
        $this->_smarty->assign("coop", $coop);
        
        if ($redirect_error != null)
        {
            $allcoops = $coops->getAllCoops();
            $this->_smarty->assign("redirect_error", $redirect_error);
            
            $this->_smarty->assign("allcoops", $allcoops);
        }
               
    	if (!$request->isPost())
    	{	
    		
    		$error = $request->getParam('error');

			if (!empty($error) && $error == "nofields")
			{
    			$this->_smarty->assign('error', $error);					
			}
			
 			$this->_smarty->display('login/login.tpl');	
	  	}
    	else 
    	{
    		$post = $request->getPost();
    		
    		$email = $post['email'];
    		$password = $post['password'];
    		
    		if (empty($email) || empty($password))
    		{
	    		$this->_redirect("index/index/error/nofields/coop/" . $coop_id);    			
    		}

    		$coop_users = new Coop_Users();
    		if ($coop_users->login($email, $password, $coop))
    		{
 	    		$this->_redirect("user");			
    		}
    		else
    		{
    			$this->_smarty->assign('error', "invalid");	    			
     			$this->_smarty->display('login/login.tpl');	
    		}
    	}
    }
	
    public function logoutAction()
	{
                $coop_id =  (int) $coop['coop_id'];
                if (empty($coop_id))
                {
                    $this->_redirect("/index/index/coop/1");
                }
		$users = new Coop_Users();
		$coop = $users->getLoggedUserCoop();
		$users = new Coop_Users();
		$users->logout();
		$this->_redirect("/index/index/coop/" . (int) $coop['coop_id']);
    }

}
