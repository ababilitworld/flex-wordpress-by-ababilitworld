<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Test;

use Ababilitworld\FlexWordpressByAbabilitworld\Package\Test\Template\Template;

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexWordpressByAbabilitworld\Package\Test\Menu\Menu as TestMenu,
    FlexWordpressByAbabilitworld\Package\Test\Setting\Setting as Setting,
    FlexWordpressByAbabilitworld\Package\Test\Service\Service as TestService,
    FlexWordpressByAbabilitworld\Package\Test\Presentation\Template\Template as TestTemplate
};

use const Ababilitworld\{
    FlexWordpressByAbabilitworld\PLUGIN_NAME,
    FlexWordpressByAbabilitworld\PLUGIN_DIR,
    FlexWordpressByAbabilitworld\PLUGIN_URL,
    FlexWordpressByAbabilitworld\PLUGIN_FILE,
    FlexWordpressByAbabilitworld\PLUGIN_PRE_UNDS,
    FlexWordpressByAbabilitworld\PLUGIN_PRE_HYPH,
    FlexWordpressByAbabilitworld\PLUGIN_VERSION
};

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

if (!class_exists(__NAMESPACE__.'\Test')) 
{
    class Test 
    {
        use Standard;
        private $menu;

        public function __construct($data = []) 
        {
            $this->init($data); 
            
        }

        public function init($data) 
        {
            $this->menu = TestMenu::instance();       
        }
    }
}