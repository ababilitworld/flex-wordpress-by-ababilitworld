<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Posttype\Concrete;

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexTraitByAbabilitworld\Security\Sanitization\Sanitization,
    FlexWordpressByAbabilitworld\Package\Interface\Posttype as WordpressInterface,
    FlexWordpressByAbabilitworld\Package\Posttype\Base\Posttype as BasePosttype,
};
/**
 * Concrete Class Posttype
 * Registers a concrete post type.
 */
if (!class_exists(__NAMESPACE__ . '\Posttype')) 
{
    class Posttype extends BasePosttype
    {
         /**
         * Set posttype configuration.
         */
        protected function setConfig(array $config): array
        {
            return [
                'post_type'  => 'blog_post',
                'singular'   => 'Blog Post',
                'plural'     => 'Blog Posts',
                'textdomain' => 'blog-textdomain',
                'labels'     => $this->setDefaultLabels([]),
                'args'       => [
                    'public'             => true,
                    'has_archive'        => true,
                    'show_ui'            => true,
                    'show_in_menu'       => true,
                    'menu_icon'          => 'dashicons-admin-post',
                    'menu_position'      => 5,
                    'show_in_rest'       => true,
                    'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'comments'],
                    'hierarchical'       => false,
                    'rewrite'            => ['slug' => 'blog'],
                ],
            ];
        }

        /**
         * Set posttype labels.
         */
        protected function setLabels(array $labels): array
        {
            return [
                'name'               => __($this->plural, $this->textdomain),
                'singular_name'      => __($this->singular, $this->textdomain),
                'add_new'            => __('Add New', $this->textdomain),
                'add_new_item'       => sprintf(__('Add New %s', $this->textdomain), $this->singular),
                'edit_item'          => sprintf(__('Edit %s', $this->textdomain), $this->singular),
                'new_item'           => sprintf(__('New %s', $this->textdomain), $this->singular),
                'view_item'          => sprintf(__('View %s', $this->textdomain), $this->singular),
                'all_items'          => sprintf(__('All %s', $this->textdomain), $this->plural),
                'search_items'       => sprintf(__('Search %s', $this->textdomain), $this->plural),
                'not_found'          => sprintf(__('No %s found', $this->textdomain), strtolower($this->plural)),
                'not_found_in_trash' => sprintf(__('No %s found in Trash', $this->textdomain), strtolower($this->plural)),
            ];

        }

        /**
         * Set posttype args.
         */
        protected function setArgs(array $args): array
        {
            return [
                'public'            => true,
                'has_archive'       => true,
                'rewrite'           => ['slug' => $this->slug],
                'show_ui'           => true,
                'show_in_menu'      => true,
                'menu_icon'         => 'dashicons-portfolio',
                'show_in_rest'      => true,
                'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
            ];
        }

        protected function registerHooks(): void
        {
            $this->disableGutenberg([$this->posttype]);

            $this->addPostTypeSupport($this->posttype, ['thumbnail']);

            $this->setTitlePlaceholder($this->posttype, __('Enter Portfolio Title', $this->textdomain));

            $this->customizePostUpdatedMessages($this->posttype, [
                1 => __('Portfolio updated.', $this->textdomain),
                6 => __('Portfolio published.', $this->textdomain),
            ]);

            $this->registerTaxonomies();
            $this->registerMetaFields();
        }

        /**
         * register custom taxonomies
         */
        public function registerTaxonomies(): void
        {
            add_action('init', function () {
                register_taxonomy('blog_category', [$this->posttype], [
                    'label'        => __('Categories', $this->textdomain),
                    'rewrite'      => ['slug' => 'blog-category'],
                    'hierarchical' => true,
                    'show_in_rest' => true,
                ]);

                register_taxonomy('blog_tag', [$this->posttype], [
                    'label'        => __('Tags', $this->textdomain),
                    'rewrite'      => ['slug' => 'blog-tag'],
                    'hierarchical' => false,
                    'show_in_rest' => true,
                ]);
            });
        }

        /**
         * Register Custom meta fields
         */
        public function registerMetaFields(): void
        {
            add_action('add_meta_boxes', function () {
                add_meta_box(
                    'blog_post_meta',
                    __('Blog Post Details', $this->textdomain),
                    [$this, 'renderMetaBox'],
                    $this->posttype,
                    'side'
                );
            });

            add_action('save_post', [$this, 'saveMetaFields']);
        }

        /**
         * Render meta box
         */
        public function renderMetaBox($post): void
        {
            $subtitle = get_post_meta($post->ID, '_blog_post_subtitle', true);
            ?>
            <label for="blog_post_subtitle"><?php _e('Subtitle', $this->textdomain); ?></label>
            <input type="text" id="blog_post_subtitle" name="blog_post_subtitle" value="<?php echo esc_attr($subtitle); ?>" class="widefat">
            <?php
        }

        /**
         * Save meta fields
         */
        public function saveMetaFields($post_id): void
        {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
            if (!isset($_POST['blog_post_subtitle'])) return;

            update_post_meta($post_id, '_blog_post_subtitle', sanitize_text_field($_POST['blog_post_subtitle']));
        }
    }

}