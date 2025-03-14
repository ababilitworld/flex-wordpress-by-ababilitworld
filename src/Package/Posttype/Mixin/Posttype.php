<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Posttype\Mixin;

if ( ! trait_exists(__NAMESPACE__.'\Posttype' ) ) :

    trait Posttype
    {
        /**
         * Disable Gutenberg (block editor) for specific post types.
         *
         * @param array $post_types List of post types to disable Gutenberg for.
         * 
         * @return void
         */
        public function disableGutenberg(array $post_types): void
        {
            add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) use ($post_types) {
                if (in_array($post_type, $post_types, true)) {
                    return false;
                }
                return $use_block_editor;
            }, 10, 2);
        }

        /**
         * Register support features for post types (e.g., thumbnails, excerpts).
         *
         * @param string $post_type Post type key.
         * @param array $features List of supported features.
         * 
         * @return void
         */
        public function addPostTypeSupport(string $post_type, array $features): void
        {
            add_action('init', function () use ($post_type, $features) {
                add_post_type_support($post_type, $features);
            });
        }

        /**
         * Set custom placeholder title for post type.
         *
         * @param string $post_type Post type key.
         * @param string $placeholder Placeholder text.
         * 
         * @return void
         */
        public function setTitlePlaceholder(string $post_type, string $placeholder): void
        {
            add_filter('enter_title_here', function ($title, $current_post) use ($post_type, $placeholder) {
                if ($current_post->post_type === $post_type) {
                    return $placeholder;
                }
                return $title;
            }, 10, 2);
        }

        /**
         * Change default messages after post update.
         *
         * @param string $post_type Post type key.
         * @param array $messages Custom messages array.
         * 
         * @return void
         */
        public function customizePostUpdatedMessages(string $post_type, array $messages): void
        {
            add_filter('post_updated_messages', function ($default_messages) use ($post_type, $messages) {
                $default_messages[$post_type] = wp_parse_args($messages, $default_messages[$post_type] ?? []);
                return $default_messages;
            });
        }
    }

endif;
