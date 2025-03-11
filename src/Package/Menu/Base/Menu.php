<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Menu\Base;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
};

use const Ababilitworld\FlexWordpressByAbabilitworld\{
    PLUGIN_NAME,
    PLUGIN_DIR,
    PLUGIN_URL,
    PLUGIN_FILE,
    PLUGIN_VERSION
};

if ( ! class_exists( __NAMESPACE__.'\Menu' ) ) 
{
    /**
     * Abstract Class BaseMenu
     * Defines the structure for WordPress menu classes
     */
    abstract class Menu
    {
        use Standard;

        /**
         * Holds the page title
         * @var string
         */
        protected string $page_title;

        /**
         * Holds the menu title
         * @var string
         */
        protected string $menu_title;

        /**
         * Holds the menu capability
         * @var string
         */
        protected string $capability;

        /**
         * Holds the menu slug
         * @var string
         */
        protected string $menu_slug;

        /**
         * Holds the menu callback
         * @var callable
         */
        protected $callback;

        /**
         * Holds the menu icon
         * @var string
         */
        protected string $menu_icon;

        /**
         * Holds the menu position
         * @var string
         */
        protected string $menu_position; 

        /**
         * Holds the submenu items
         * @var array
         */
        protected array $submenus = [];

        /**
         * BaseMenu constructor
         */
        public function __construct()
        {
            add_action('admin_menu', [$this, 'register_menus']);
        }

        /**
         * Register the main menu in WordPress
         */
        public function register_menus(): void
        {
            add_menu_page(
                $this->get_page_title(),
                $this->get_menu_title(),
                $this->get_menu_capability(),
                $this->get_menu_slug(),
                $this->get_callback(),
                $this->get_menu_icon(),
                $this->get_menu_position()
            );

            $this->register_submenus();
        }

        /**
         * Registers submenus dynamically
         */
        protected function register_submenus(): void
        {
            foreach ($this->submenus as $submenu) 
            {
                add_submenu_page(
                    $this->get_menu_slug(),
                    $submenu['page_title'],
                    $submenu['menu_title'],
                    $submenu['capability'],
                    $submenu['slug'],
                    $submenu['callback']
                );
            }
        }

        /**
         * Adds a submenu
         */
        public function add_submenu(array $data): void
        {
            if (!isset($data['page_title'], $data['menu_title'], $data['capability'], $data['slug'], $data['callback'])) 
            {
                throw new \InvalidArgumentException("Missing submenu parameters.");
            }

            if (!is_callable($data['callback']) && !is_string($data['callback'])) 
            {
                throw new \InvalidArgumentException("Callback must be a string or a callable function.");
            }

            $this->submenus[] = [
                'page_title' => $data['page_title'],
                'menu_title' => $data['menu_title'],
                'capability' => $data['capability'],
                'slug'       => $data['slug'],
                'callback'   => $data['callback'],
            ];
        }

        /**
         * Get the page title
         */
        abstract protected function get_page_title(): string;

        /**
         * Get the menu title
         */
        abstract protected function get_menu_title(): string;

        /**
         * Get the page capability
         */
        abstract protected function get_menu_capability(): string;        

        /**
         * Get the menu slug
         */
        abstract protected function get_menu_slug(): string;

        /**
         * Get the menu callback method
         */
        //abstract protected function get_callbacka(): callable;

        abstract protected function get_callback(): callable;

        /**
         * Get the menu icon
         */
        protected function get_menu_icon(): string
        {
            return !empty($this->menu_icon) ? $this->menu_icon : 'dashicons-admin-generic';
        }

        /**
         * Get the menu position
         */
        protected function get_menu_position(): int
        {
            return $this->menu_position ?? 60;
        }
    }

}
	