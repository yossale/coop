<?php
use Masterminds\HTML5;

class report_template_tpl_Test extends PHPUnit_Framework_TestCase
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
        // Test
    
        $smarty_output = $this->_smarty->fetch(
                PROJECT_PATH .
                '/source/application/views/scripts/duty/report_template.tpl');
    
        $output = '<html>' . '<head>' . '</head>' . '<body>' . $smarty_output .
        '</body>' . '</html>';
    
    
        // Test strict html parsing.
        $dom = new DOMdocument();
        $dom->loadHTML($output);
    }
}