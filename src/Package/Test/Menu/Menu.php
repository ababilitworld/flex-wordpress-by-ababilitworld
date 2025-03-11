<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Test\Menu;

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexMenuManByAbabilitworld\Package\Wordpress\Base\Menu as BaseMenu,
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

if (!class_exists(__NAMESPACE__.'\Menu')) 
{
    /**
     * Concrete Class ThemeSettingsMenu
     * Implements the WordPress admin menu for theme settings
     */
    class Menu extends BaseMenu
    {
        /**
         * Constructor to define menu properties and submenus
         */
        public function __construct()
        {
            $this->page_title    = 'Flex Themes';
            $this->menu_title    = 'Flex Themes';
            $this->capability    = 'manage_options';
            $this->menu_slug     = 'theme-settings';
            $this->callback      = [$this, 'render_page'];
            $this->menu_icon     = 'dashicons-admin-customizer';
            $this->menu_position = 9;

            parent::__construct();

            // Add submenus dynamically
            $this->add_submenu([
                'page_title' => 'Color Schemes',
                'menu_title' => 'Color Schemes',
                'capability' => 'manage_options',
                'slug'       => 'color-schemes',
                'callback'   => [$this, 'render_submenu']
            ]);

            $this->add_submenu([
                'page_title' => 'Typography',
                'menu_title' => 'Typography',
                'capability' => 'manage_options',
                'slug'       => 'typography',
                'callback'   => [$this, 'render_submenu']
            ]);

        }

        /**
         * Renders the main settings page
         */
        public function render_page(): void
        {
            echo '<div class="wrap"><h1>Theme Settings</h1></div>';
        }

        /**
         * Renders the submenu pages
         */
        public function render_submenu(): void
        {
            echo '<div class="wrap"><h1>Under Construction !!!</h1></div>';
        }

        /**
         * Get the page title
         */
        protected function get_page_title(): string
        {
            return $this->page_title;
        }

        /**
         * Get the menu title
         */
        protected function get_menu_title(): string
        {
            return $this->menu_title;
        }

        /**
         * Get the menu capability
         */
        protected function get_menu_capability(): string
        {
            return $this->capability;
        }

        /**
         * Get the menu slug
         */
        protected function get_menu_slug(): string
        {
            return $this->menu_slug;
        }

        /**
         * Get the callback function
         */
        protected function get_callback(): callable 
        {
            return is_callable($this->callback) ? $this->callback : '__return_false';
        }
    }
}
