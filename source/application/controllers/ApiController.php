<?
/* maybe when I will move coop to rails
class ApiController extends Zend_Controller_Action
{
    public function init()
    {
        $renderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $renderer->setNeverRender(true);

        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $this->logger = Zend_Registry::get('logger');
    }

    private function sendResponse($data)
    {
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function getParam($name)
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params[$name]) || empty($params[$name]))
        {
            $this->getResponse()->setHttpResponseCode(503);
            $error = array("error" => "missing param '$name'");
            $this->sendResponse($error);
            exit;
        }
        return $params[$name];
    }

    public function coopsAction()
    {
        $coops_model = new Coop_Coops();
        $coops = $coops_model->getAllCoops();
        $this->sendResponse($coops);
    }

    public function productsAction()
    {
        $products_model = new Coop_Products();
        $coop_id = $this->getParam('coop');
        $products = $products_model->getAllProducts($coop_id);
        $this->sendResponse($products);
    }

    public function usersAction()
    {
        $users_model = new Coop_Users();
        $coop_id = $this->getParam('coop');
        $users = $users_model->getAllUsers($coop_id);
        $this->sendResponse($users);
    }

    public function ordersAction()
    {
        $orders_model = new Coop_Orders();
        $user_id = $this->getParam('user');
        $orders = $orders_model->getUserOrders($user_id);
        $this->sendResponse($orders);
    }
}