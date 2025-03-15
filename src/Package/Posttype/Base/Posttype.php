<?php

namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Posttype\Base;

(defined('ABSPATH') && defined('WPINC')) || die();

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexTraitByAbabilitworld\Wordpress\Security\Sanitization\Sanitization,
    FlexWordpressByAbabilitworld\Package\Posttype\Contract\Posttype as WpPosttypeInterface,
    FlexWordpressByAbabilitworld\Package\Posttype\Mixin\Posttype as WpPosttypeMixin
};

/**
 * Abstract class Posttype
 * Handles custom post type registration with configurable labels, args, and config.
 */
if (!class_exists(__NAMESPACE__ . '\Posttype')) {
    abstract class Posttype implements WpPosttypeInterface
    {
        use Standard, WpPosttypeMixin;

        private static array $instances = [];

        protected string $posttype = '';
        protected string $slug = '';
        protected string $singular = '';
        protected string $plural = '';
        protected string $textdomain = '';

        protected array $defaultLabels = [];
        protected array $labels = [];

        protected array $defaultArgs = [];
        protected array $args = [];

        protected array $defaultConfig = [];
        protected array $config = [];

        /**
         * Private constructor for Singleton pattern.
         */
        private function __construct(array $config = [])
        {
            $this->initialize($config);
        }

        /**
         * Get Singleton instance with optional external config.
         */
        public static function getInstance(array $config = []): static
        {
            $postType = $config['post_type'] ?? static::class;

            if (!isset(self::$instances[$postType])) 
            {
                self::$instances[$postType] = new static($config);
            } 
            elseif (!empty($config)) 
            {
                // Re-initialize if external config provided and differs
                self::$instances[$postType]->initialize($config);
            }

            return self::$instances[$postType];
        }

        public function  init(array $data): void
        {

        }

        /**
         * Initialize or reinitialize the post type.
         */
        private function initialize(array $config): void
        {
            $this->defaultConfig = $this->setDefaultConfig();
            $this->config = $this->mergeIfDifferent($this->defaultConfig, $this->sanitizeConfig($config));

            $this->posttype = $this->config['post_type'];
            $this->slug = $this->posttype;
            $this->singular = $this->config['singular'];
            $this->plural = $this->config['plural'];
            $this->textdomain = $this->config['textdomain'];

            $this->defaultLabels = $this->setDefaultLabels();
            $this->labels = $this->mergeIfDifferent($this->defaultLabels, $this->config['labels'] ?? []);

            $this->defaultArgs = $this->setDefaultArgs();
            $this->args = $this->mergeIfDifferent($this->defaultArgs, $this->config['args'] ?? []);
        }

        /**
         * Merge arrays based on internal, external, default, and option rules.
         *
         * - If internal is not empty and external is empty, return internal.
         * - If both internal and external are empty, return default.
         * - If internal is empty but external is not, merge default with external.
         * - If both internal and external are not empty:
         *      - If option is 'replace', return external.
         *      - If option is 'merge', return merged internal and external.
         *
         * @param array $default  Default fallback values.
         * @param array $internal Internal set values.
         * @param array $external External provided values.
         * @param string $option  'replace' to replace internal, 'merge' to merge with internal.
         * 
         * @return array
         */
        private function mergeIfDifferent(array $default, array $internal, array $external, string $option = 'merge'): array
        {
            // Case 1: Both internal and external empty — return default
            if (empty($internal) && empty($external)) {
                return $default;
            }

            // Case 2: Internal not empty, external empty — return internal
            if (!empty($internal) && empty($external)) {
                return $internal;
            }

            // Case 3: Internal empty, external provided — merge default and external
            if (empty($internal) && !empty($external)) {
                return array_merge($default, $external);
            }

            // Case 4: Both internal and external provided
            if (!empty($internal) && !empty($external)) {
                if ($option === 'replace') {
                    return $external;
                } elseif ($option === 'merge') {
                    return array_merge($internal, $external);
                }
            }

            // Fallback to internal if no other condition matches
            return $internal;
        }

        /**
         * Sanitize configuration data.
         */
        protected function sanitizeConfig(array $config): array
        {
            return $this->sanitizeArray($config);
        }

        /**
         * Set default configuration.
         */
        abstract protected function setDefaultConfig(): array;

        /**
         * Set default labels.
         */
        abstract protected function setDefaultLabels(): array;

        /**
         * Set default args.
         */
        abstract protected function setDefaultArgs(): array;

        /**
         * Get prepared post type.
         */
        protected function getPostType(): string
        {
            return $this->posttype;
        }

        /**
         * Get prepared Config.
         */
        protected function getConfig(): array
        {
            return $this->config;
        }

        /**
         * Get prepared labels.
         */
        protected function getLabels(): array
        {
            return $this->labels;
        }

        /**
         * Get prepared arguments.
         */
        protected function getArgs(): array
        {
            $args = $this->args;
            $args['labels'] = $this->getLabels(); // Inject dynamic labels
            return $args;
        }

        /**
         * Dynamically set posttype Config.
         */
        abstract protected function setConfig(): void;

        /**
         * Dynamically set external labels.
         */
        abstract protected function setLabels(): void;

        /**
         * Dynamically set external args.
         */
        abstract protected function setArgs(): void;

        /**
         * Dynamically set external Config.
         */
        public function setConfig(array $config): void
        {
            $this->config = $this->mergeIfDifferent($this->defaultConfig, $this->config, $config);
        }

        /**
         * Dynamically set external labels.
         */
        public function setLabels(array $labels): void
        {
            $this->labels = $this->mergeIfDifferent($this->defaultLabels, $this->labels, $labels);
        }

        /**
         * Dynamically set external arguments.
         */
        public function setArgs(array $args): void
        {
            $this->args = $this->mergeIfDifferent($this->defaultArgs, $this->args, $args);
        }

        /**
         * Default fallback config method if not overridden.
         */
        protected function getDefaultConfig(): array
        {
            $this->posttype = 'custom_post';
            $this->singular = 'Custom Post';
            $this->plural = 'Custom Posts';
            $this->textdomain = 'plugin-textdomain';

            return [
                'post_type'  => $this->posttype,
                'singular'   => $this->singular,
                'plural'     => $this->plural,
                'textdomain' => $this->textdomain,
                'labels'     => $this->getDefaultLabels(),
                'args'       => $this->getDefaultArgs(),
            ];
        }

        /**
         * Default fallback labels if not overridden.
         */
        protected function getDefaultLabels(): array
        {
            $singular   = $this->singular ;
            $plural     = $this->plural ;
            $textdomain = $this->textdomain ;

            return [
                'name'                     => __($plural, $textdomain),
                'singular_name'            => __($singular, $textdomain),
                'menu_name'                => __($plural, $textdomain),
                'name_admin_bar'           => __($plural, $textdomain),
                'archives'                 => sprintf(__('%s List', $textdomain), $singular),
                'attributes'               => sprintf(__('%s List', $textdomain), $singular),
                'parent_item_colon'        => sprintf(__('%s Item : ', $textdomain), $singular),
                'all_items'                => sprintf(__('All %s', $textdomain), $plural),
                'add_new_item'             => sprintf(__('Add New %s', $textdomain), $singular),
                'add_new'                  => sprintf(__('Add New %s', $textdomain), $singular),
                'new_item'                 => sprintf(__('New %s', $textdomain), $singular),
                'edit_item'                => sprintf(__('Edit %s', $textdomain), $singular),
                'update_item'              => sprintf(__('Update %s', $textdomain), $singular),
                'view_item'                => sprintf(__('View %s', $textdomain), $singular),
                'view_items'               => sprintf(__('View %s', $textdomain), $plural),
                'search_items'             => sprintf(__('Search %s', $textdomain), $plural),
                'not_found'                => sprintf(__('%s Not found', $textdomain), $singular),
                'not_found_in_trash'       => sprintf(__('%s Not found in Trash', $textdomain), $singular),
                'featured_image'           => sprintf(__('%s Feature Image', $textdomain), $singular),
                'set_featured_image'       => sprintf(__('Set %s Feature Image', $textdomain), $singular),
                'remove_featured_image'    => __('Remove Feature Image', $textdomain),
                'use_featured_image'       => sprintf(__('Use as %s featured image', $textdomain), $singular),
                'insert_into_item'         => sprintf(__('Insert into %s', $textdomain), $singular),
                'uploaded_to_this_item'    => sprintf(__('Uploaded to this %s', $textdomain), $singular),
                'items_list'               => sprintf(__('%s list', $textdomain), $singular),
                'items_list_navigation'    => sprintf(__('%s list navigation', $textdomain), $singular),
                'filter_items_list'        => sprintf(__('Filter %s List', $textdomain), $singular),
            ];

        }

        /**
         * Default fallback args if not overridden.
         */
        protected function getDefaultArgs(): array
        {
            return [
                'public'            => true,
                'has_archive'       => true,
                'show_ui'           => true,
                'show_in_menu'      => true,
                'menu_icon'         => 'dashicons-admin-post',
                'menu_position'     => 20,
                'show_in_rest'      => true,
                'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
                'rewrite'           => ['slug' => $this->slug],
                'capability_type'   => 'post',
            ];
        }

        /**
         * Register post type.
         */
        public function register(): void
        {
            add_action('init', function () {
                if (!post_type_exists($this->getPostType())) {
                    register_post_type($this->getPostType(), $this->getArgs());
                }
            });

            $this->registerHooks();
        }

        /**
         * Register the required hooks to posttype.
         */
        abstract protected function registerHooks(): void;
    }
}
