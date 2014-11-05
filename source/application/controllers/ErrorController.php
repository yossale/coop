<?php
require_once APPLICATION_PATH.'/CustomController.php';
use Mailgun\Mailgun;

class ErrorController extends CustomController
{
	public function init()
	{
		parent::init();
	}
	
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
       
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $request->getParams());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;

        $exception = $errors->exception;

        $display = "<title>Debug Error</title>";
        $display .= "<h1>" . $exception->getMessage() . "</h1>";
        $display .= $this->displayArray("Stack Trace:", nl2br($exception->getTraceAsString()));
        $display .= $this->displayArray("ZF Request:", $this->getRequest()->getParams());
        $display .= $this->displayArray("POST:", $_POST);
        $display .= $this->displayArray("GET:", $_GET);
        $display .= $this->displayArray("Session:", $_SESSION);
        $display .= $this->displayArray("Server:", $_SERVER);

        $config = Zend_Registry::get('config');
        $mailgun = $config->mailgun;

        //$mg = new Mailgun($mailgun->key);

        # Now, compose and send your message.
        /*$mg->sendMessage($mailgun->domain, array('from' => $mailgun->from,
            'to'      => $config->errors_email,
            'subject' => $exception->getMessage(),
            'html'    => $display));*/

        if (APPLICATION_ENV == 'development') {
            echo $display;
        }
        else
        {
            $this->_smarty->display('common/error.tpl');
        }

    }

	private function displayArray($name, $array)
    {
    	if (count($array) > 0)
    	{
    		return "<p><b>$name</b><br /><pre>" . $this->grab_dump($array) . "</pre></p>";    	
    	}
    }

    private function grab_dump($var)
	{
	    ob_start();
	    var_dump($var);
	    return ob_get_clean();
	}

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

