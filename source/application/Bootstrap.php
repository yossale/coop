<?php
require_once APPLICATION_PATH.'/CustomController.php';

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initRequest()
	{
		// autoload awsome lib		 
		set_include_path("../../../Awsome/" . PATH_SEPARATOR . get_include_path());				
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Awsome_');				
		
		// autoload coop models
		set_include_path(APPLICATION_PATH . '/models' . PATH_SEPARATOR . get_include_path());
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Coop_');
		
		// bootstrap
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		
		// configuration
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		Zend_Registry::set('config', $config);
		
		// db adapter
		$db = Zend_Db::factory($config->database);
		$db->query("SET NAMES utf8");
		Zend_Db_Table::setDefaultAdapter($db);

        // set logger
        $logger = new Zend_Log();

        $syslog_writer = new Zend_Log_Writer_Syslog(array('application' => 'Coop'));
        $logger->addWriter($syslog_writer);

        if (APPLICATION_ENV == 'development')
        {
            $php_output = new Zend_Log_Writer_Stream('php://output');
            $logger->addWriter($php_output);
        }
        Zend_Registry::set("logger", $logger);
	}
}

