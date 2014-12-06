<?php
use Masterminds\HTML5;

class orders_tpl_Test extends PHPUnit_Framework_TestCase
{
    protected $_smarty;

    public function setup ()
    {
        // setup smarty options, caching, etc
        $this->_smarty = new Smarty();
        
        // $this->_smarty->setTemplateDir(APPLICATION_PATH . '/views/scripts');
        // $this->_smarty->setCompileDir(APPLICATION_PATH . '/smarty/compiler');
        // $this->_smarty->setCacheDir(APPLICATION_PATH . '/smarty/cache');
        // $this->_smarty->setConfigDir(APPLICATION_PATH . '/smarty/configs');
        // $this->_smarty->addPluginsDir(APPLICATION_PATH . '/smarty/plugins');
        
        // $this->_smarty->assign('menu', $menu);
        
        $this->_smarty->assign('public_path', PUBLIC_PATH);
        // $this->_smarty->assign('img_path', PUBLIC_PATH . "/images");
        $this->_smarty->assign('css_path', PUBLIC_PATH . "/css");
        $this->_smarty->assign('js_path', PUBLIC_PATH . "/js");
        
        $this->_smarty->caching = 0;
    }

    public function test_OrdersGroupsSummaries ()
    {
        // ***************************
        // Build
        
        $orders = array(
                "payed" => array(
                        array(
                                "order_id" => 1,
                                "user_first_name" => "user_1_first_name",
                                "user_last_name" => "user_1_last_name",
                                "user_phone" => "012-1234567",
                                "order_last_edit" => "2014-01-01",
                                "total" => "45"
                        ),
                        array(
                                "order_id" => 2,
                                "user_first_name" => "user_3_first_name",
                                "user_last_name" => "user_3_last_name",
                                "user_phone" => "012-1234567",
                                "order_last_edit" => "2014-01-01",
                                "total" => "13"
                        )
                ),
                "unpayed" => array(
                        array(
                                "order_id" => 4,
                                "user_first_name" => "user_4_first_name",
                                "user_last_name" => "user_4_last_name",
                                "user_phone" => "012-1234567",
                                "order_last_edit" => "2014-01-01",
                                "total" => "45"
                        ),
                        array(
                                "order_id" => 5,
                                "user_first_name" => "user_5_first_name",
                                "user_last_name" => "user_5_last_name",
                                "user_phone" => "012-1234567",
                                "order_last_edit" => "2014-01-01",
                                "total" => "445"
                        ),
                        array(
                                "order_id" => 6,
                                "user_first_name" => "user_6_first_name",
                                "user_last_name" => "user_6_last_name",
                                "user_phone" => "012-1234567",
                                "order_last_edit" => "2014-01-01",
                                "total" => "245"
                        )
                )
        );
        
        $users = array();
        
        $this->_smarty->assign('orders', $orders);
        $this->_smarty->assign('users', $users);
        // $this->_smarty->assign('action', 'index');
        // $this->_smarty->assign('tpl_file', 'orders/orders.tpl');
        
        // ***************************
        // Test
        
        $smarty_output = $this->_smarty->fetch(
                PROJECT_PATH .
                         '/source/application/views/scripts/orders/orders.tpl');
        
        $output = '<html>' . '<head>' . '</head>' . '<body>' . $smarty_output .
                 '</body>' . '</html>';
        
        // Test strict html parsing. 
        $dom = new DOMdocument();
        $dom->loadHTML($output);
        
        $html5 = new HTML5();
        $dom = $html5->loadHTML($output);
        
        // ***************************
        // Assert
        
        $orders_group_summary_items = qp($dom, '.orders_group_summary');
        
        $this->assertEquals(2, count($orders_group_summary_items), 
                'group summary count');
        
        // Payed Group.
        
        $payed_group_summary = qp($dom, '#orders_group_payed .orders_group_summary');
        $this->assertEquals(1, $payed_group_summary->length,
                'payed summary existance');
        
        $payed_sum_label = $payed_group_summary->find(".orders_group_summary__label");
        $this->assertEquals(1, $payed_sum_label->length,
                'payed sum label existance');
        
        $payed_sum_data = $payed_group_summary->find(".orders_group_summary__data");
        $this->assertEquals(1, $payed_sum_data->length,
                'payed sum data existance');
        $this->assertEquals("2", $payed_sum_data->text(),
                'payed sum data value');
        
        // Unpayed Group.
        
        $unpayed_group_summary = qp($dom, '#orders_group_unpayed .orders_group_summary');
        $this->assertEquals(1, $unpayed_group_summary->length,
                'unpayed summary existance');
        
        $unpayed_sum_label = $unpayed_group_summary->find(".orders_group_summary__label");
        $this->assertEquals(1, $unpayed_sum_label->length,
                'unpayed sum label existance');
        
        $unpayed_sum_data = $unpayed_group_summary->find(".orders_group_summary__data");
        $this->assertEquals(1, $unpayed_sum_data->length,
                'unpayed sum data existance');
        $this->assertEquals("3", $unpayed_sum_data->text(),
                'unpayed sum data value');
    }
}