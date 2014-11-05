<?php
require_once APPLICATION_PATH.'/CustomController.php';

class DebtController extends CustomController
{
    public function init()
    {
        parent::init();
        parent::checkAuth();

        $this->_smarty->assign('controller', 'duty');

        $submenu = array("index" => "הזמנות", "debt" => "חובות", "stock" => "מלאי", "duty-reports" => "דוח תורן", "previous-reports" => "דוחות קודמים", "weekly-report" => "טבלת סיכום שבועית");
        $this->_smarty->assign('submenu', $submenu);

        $this->_smarty->assign('hide_left_panel', '1');
    }
    
    public function indexAction()
    {
        $coop_debts = new Coop_Debt();
    	$coop_id = $this->getCoopId();		
    	$debts = $coop_debts->getDebts($coop_id);
        
    	$this->_smarty->assign('debts', $debts);
        $this->_smarty->assign('action', 'debt');
    	$this->_smarty->assign('tpl_file', 'orders/debts.tpl');
    	$this->_smarty->display('common/layout.tpl');
    }

    public function addDebtAction()
    {
    	$post = $this->getRequest()->getPost();
    	
        $user_id = (int)$post['user_id'];
    	$amount = (float)$post['amount'];
	$date = $post['date'];
	$comments = $post['comments'];

    	$coop_debts = new Coop_Debt();
    	$coop_debts->AddDebt($user_id, $amount, $date, $comments);
        
    	$this->_redirect("duty");
    	
    }
    
    public function removeDebtAction()
    {
    	$params = $this->getRequest()->getParams();
    	$user_id = (int)$params['user'];
        
    	$coop_debts = new Coop_Debt();
    	$coop_debts->deleteDebt($user_id);
        
    	$this->_redirect("duty");
    }
}