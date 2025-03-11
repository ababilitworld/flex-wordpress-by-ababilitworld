<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Test\PostType;

(defined('ABSPATH') && defined('WPINC')) || exit();

abstract class PostType
{
    protected string $slug;
    protected string $singular;
    protected string $plural;
    protected array $labels = [];
    protected array $args = [];

    public function __construct()
    {
        add_action('init', [$this, 'register']);
    }

    abstract protected function get_args(): array;

    public function register(): void
    {
        register_post_type($this->slug, $this->get_args());
    }
}
