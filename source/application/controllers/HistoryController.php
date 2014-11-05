<?php
require_once APPLICATION_PATH.'/CustomController.php';

class HistoryController extends CustomController
{
	public function init()
	{
		parent::init();
		
	}
	
	public function saveAction() 
	{
		$coop_coops = new Coop_Coops();
		$coop = $coop_coops->getCoop(1);
		$reset_day = $coop['coop_last_reset_day'];	
		parent::setSmaryForWeeklyReport($reset_day, 1);
		
		$template_vars = $this->_smarty->getTemplateVars();
		$reset_date = $template_vars['current'];

		$cache = array();
		$cache['stock'] = $template_vars['stock'];
		$cache['comments'	] = $template_vars['comments'];
		$cache['sum'] = $template_vars['sum'];
		$cache['user_payments'] = $template_vars['user_payments'];
		$cache['payments'] = $template_vars['payments'];
		$cache['report'] = $template_vars['report'];
		
		$cache_text = serialize($cache);
		
//		echo $cache_text;
	}
}			
        