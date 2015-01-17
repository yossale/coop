<?php
use Masterminds\HTML5;

class stock_tpl_Test extends PHPUnit_Framework_TestCase
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

    public function test_strictHtmlStructure() {
        // ***************************
        // Build

        // ***************************
        // Build

        $reset_day = "2014-12-09";
        $reset_days = array("2014-12-09", "2014-12-01");
        $stock = array();
        $comments = array();
        $weekly_report = array();

        $this->_smarty->assign('stock', $stock);
        $this->_smarty->assign('comments', $comments);
        $this->_smarty->assign('products', $weekly_report);
        $this->_smarty->assign('reset_days', $reset_days);
        $this->_smarty->assign('current_date', $reset_day);

        // ***************************
        // Test

        $smarty_output = $this->_smarty->fetch(
                PROJECT_PATH .
                '/source/application/views/scripts/duty/stock.tpl');

        $output = '<html>' . '<head>' . '</head>' . '<body>' . $smarty_output .
        '</body>' . '</html>';

        // Test strict html parsing.
        $dom = new DOMdocument();
        $dom->loadHTML($output);
    }

}
