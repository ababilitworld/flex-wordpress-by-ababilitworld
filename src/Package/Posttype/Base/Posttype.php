<?php

namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Base;

(defined('ABSPATH') && defined('WPINC')) || die();

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexTraitByAbabilitworld\Security\Sanitization\Sanitization,
    FlexWordpressByAbabilitworld\Package\Posttype\Contract\Posttype as WpPosttypeInterface
};

/**
 * Abstract class Posttype
 * Handles custom post type registration with configurable labels, args, and config.
 */
if (!class_exists(__NAMESPACE__ . '\Posttype')) {
    abstract class Posttype implements WpPosttypeInterface
    {
        use Standard, Sanitization;

        private static array $instances = [];

        protected string $posttype;
        protected string $slug;
        protected string $singular;
        protected string $plural;
        protected string $textdomain;

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

            if (!isset(self::$instances[$postType])) {
                self::$instances[$postType] = new static($config);
            } elseif (!empty($config)) {
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
         * Merge arrays only if they differ.
         */
        private function mergeIfDifferent(array $default, array $external): array
        {
            return !empty($external) && $external !== $default ? array_merge($default, $external) : $default;
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
         * Get prepared arguments.
         */
        protected function getArgs(): array
        {
            $args = $this->args;
            $args['labels'] = $this->labels; // Inject dynamic labels
            return $args;
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
        }

        /**
         * Dynamically set external labels.
         */
        public function setLabels(array $labels): void
        {
            $this->labels = $this->mergeIfDifferent($this->defaultLabels, $labels);
        }

        /**
         * Dynamically set external arguments.
         */
        public function setArgs(array $args): void
        {
            $this->args = $this->mergeIfDifferent($this->defaultArgs, $args);
        }

        /**
         * Default fallback config method if not overridden.
         */
        protected function getDefaultConfig(): array
        {
            return [
                'post_type'  => 'custom_post',
                'singular'   => 'Custom Post',
                'plural'     => 'Custom Posts',
                'textdomain' => 'default-textdomain',
                'labels'     => $this->getDefaultLabels(),
                'args'       => $this->getDefaultArgs(),
            ];
        }

        /**
         * Default fallback labels if not overridden.
         */
        protected function getDefaultLabels(): array
        {
            $singular = $this->singular ?: 'Item';
            $plural = $this->plural ?: 'Items';
            $textdomain = $this->textdomain ?: 'default-textdomain';

            return [
                'name'                     => __($plural, $textdomain),
                'singular_name'            => __($singular, $textdomain),
                'add_new'                  => __('Add New', $textdomain),
                'add_new_item'             => sprintf(__('Add New %s', $textdomain), $singular),
                'edit_item'                => sprintf(__('Edit %s', $textdomain), $singular),
                'new_item'                 => sprintf(__('New %s', $textdomain), $singular),
                'view_item'                => sprintf(__('View %s', $textdomain), $singular),
                'search_items'             => sprintf(__('Search %s', $textdomain), $plural),
                'not_found'                => sprintf(__('No %s found', $textdomain), strtolower($plural)),
                'not_found_in_trash'       => sprintf(__('No %s found in Trash', $textdomain), strtolower($plural)),
                'all_items'                => sprintf(__('All %s', $textdomain), $plural),
                'archives'                 => sprintf(__('%s Archives', $textdomain), $singular),
                'attributes'               => sprintf(__('%s Attributes', $textdomain), $singular),
                'insert_into_item'         => sprintf(__('Insert into %s', $textdomain), strtolower($singular)),
                'uploaded_to_this_item'    => sprintf(__('Uploaded to this %s', $textdomain), strtolower($singular)),
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
    }
}
