<?php
require_once APPLICATION_PATH . '/CustomController.php';

class UserController extends CustomController
{
    public function init()
    {
        parent::init();
        parent::checkAuth();
        $this->_smarty->assign('controller', 'user');

        $submenu = array(
            "index" => "רשימת הזמנות",
            "current" => "ההזמנה השבועית",
            "edit-details" => "הפרטים שלי",
            "users" => "חברי הקואופ"
        );
        $this->_smarty->assign('submenu', $submenu);
    }

    public function indexAction()
    {
        $coop_id = $this->getCoopId();

        $coop_orders = new Coop_Orders();
        $coop_users = new Coop_Users();

        $previous_orders = $coop_orders->getUserPreviousOrders($coop_users->getLoggedUserID());
        $this->_smarty->assign('previous', $previous_orders);

        $coop = $coop_users->getLoggedUserCoop();
        $is_open = (bool)$coop['coop_is_open_now'];
        $this->_smarty->assign('is_open', $is_open);

        $days = array("ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת");

        $this->_smarty->assign('closing_day', $days[$coop['coop_close_day']]);
        $this->_smarty->assign('openning_day', $days[$coop['coop_open_day']]);

        $this->_smarty->assign('closing_time', substr($coop['coop_close_time'], 0, 5));
        $this->_smarty->assign('openning_time', substr($coop['coop_open_time'], 0, 5));

        // updates
        $updates = nl2br(stripslashes($coop['coop_notes']));
        /* $updates = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $updates);*/
        $this->_smarty->assign('updates', $updates);

        $this->_smarty->assign('action', 'index');
        $this->_smarty->assign('tpl_file', 'user/user_index.tpl');
        $this->_smarty->display('common/layout.tpl');
    }

    public function changeViewTypeAction()
    {
        $post = $this->getRequest()->getPost();
        Coop_OrderViewType::set($post['type']);
        echo "1";
    }

    public function currentAction()
    {
        $coop_id = $this->getCoopId();
        $coop_users = new Coop_Users();

        $coop = $coop_users->getLoggedUserCoop();
        $is_open = (bool)$coop['coop_is_open_now'];
        $this->_smarty->assign('is_open', $is_open);

        $params = $this->getRequest()->getParams();
        $edit_mode = ($params['edit'] == '1');
        $allow_edit = ($edit_mode);
        $this->_smarty->assign('allow_edit', $allow_edit);

        $coop_users = new Coop_Users();
        $user_id = $coop_users->getLoggedUserID();


        $coop_orders = new Coop_Orders();
        $order = $coop_orders->getCurrentOrder($user_id);


        if (!empty($order)) {
            $items = $coop_orders->getItems($order['order_id']);
            $this->_smarty->assign('order', $order);
            $this->_smarty->assign('items', $items);
        }

        $coop_products = new Coop_Products();
        $categories = $coop_products->getAllProductsInsideCategories($coop_id, false);

        if (!empty($categories)) {
            foreach ($categories as $catKey => $category) {
                foreach ($category['products'] as $prodKey => $product) {
                    $orders_count = (int)$coop_orders->countWeeklyOrdersForProduct($coop_id, $product['product_id']);
                    $categories[$catKey]['products'][$prodKey]['orders_count'] = $orders_count;
                }
            }

            $this->_smarty->assign('cats', $categories);
        }

        $this->_smarty->assign('backto', 'user/current');
        $this->_smarty->assign('order_view_type', Coop_OrderViewType::get());

        $this->_smarty->assign('action', 'current');
        $this->_smarty->assign('tpl_file', 'user/user_current_order.tpl');
        $this->_smarty->display('common/layout.tpl');
    }

    public function prevOrderAction()
    {
        $params = $this->getRequest()->getParams();
        $coop_id = $this->getCoopId();

        $coop_orders = new Coop_Orders();
        $order = $coop_orders->getOrder((int)$params['id']);

        $items = $coop_orders->getItems($order['order_id']);
        $this->_smarty->assign('items', $items);

        $coop_products = new Coop_Products();
        $cats = $coop_products->getAllProductsInsideCategories($coop_id, false);
        $this->_smarty->assign('cats', $cats);

        $this->_smarty->assign('order_view_type', Coop_OrderViewType::get());

        $this->_smarty->assign('tpl_file', 'user/user_prev_order.tpl');
        $this->_smarty->display('common/layout.tpl');
    }

    public function viewProductImageAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->getProductImagesModel()->view($id);
    }

    private function getProductImagesModel()
    {
        $config = Zend_Registry::get('config');
        $model = new Application_Model_UploadImages($config->uploads . "/temp/", $config->uploads . "/products/");
        return $model;
    }

    public function editDetailsAction()
    {
        $request = $this->getRequest();
        $coop_users = new Coop_Users();

        $logged_user_id = $coop_users->getLoggedUserID();
        $logged_user = $coop_users->getUser($logged_user_id);

        if (!$request->isPost()) {
            $user = $coop_users->getUser($logged_user_id);
            $this->_smarty->assign('user', $user);
            $this->_smarty->assign('tpl_file', 'user/user_edit_details.tpl');
            $this->_smarty->assign('action', 'edit-details');
            $this->_smarty->display('common/layout.tpl');
        } else {
            $post = $request->getPost();
            $coop_users->editUser($logged_user_id, $post);
            $this->_smarty->assign('text', "השינויים נשמרו בהצלחה");
            $this->_smarty->assign('url', PUBLIC_PATH . "/user");
            $this->_smarty->display('common/thanks.tpl');
        }
    }

    // Users

    public function usersAction()
    {
        $coop_id = $this->getCoopId();
        $coop_users = new Coop_Users();
        $users = $coop_users->getAllUsersForUserView($coop_id);
        $this->_smarty->assign('users', $users);

        $this->_smarty->assign('action', 'users');
        $this->_smarty->assign('tpl_file', 'user/users.tpl');
        $this->_smarty->display('common/layout.tpl');
    }

}


