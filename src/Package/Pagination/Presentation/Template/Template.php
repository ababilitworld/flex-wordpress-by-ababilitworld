<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Pagination\Presentation\Template;

(defined('ABSPATH') && defined('WPINC')) || die();

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
};

if (!class_exists(__NAMESPACE__.'\Template')) 
{
    class Template 
    {
        use Standard;
        private $template_url;
        private $asset_url;

        public function __construct() 
        {
            $this->asset_url = $this->get_url('Asset/');
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts' ) );
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );
        }

        public function enqueue_scripts()
        {
            if (!wp_style_is('flex-pagination-by-ababilitworld-template-style', 'enqueued')) 
            {
                wp_enqueue_style(
                    'flex-pagination-by-ababilitworld-template-style', 
                    $this->asset_url.'css/style.css',
                    array(), 
                    time()
                );
            }

            if (!wp_script_is('flex-pagination-by-ababilitworld-template-script', 'enqueued')) 
            {
                wp_enqueue_script(
                    'flex-pagination-by-ababilitworld-template-script', 
                    $this->asset_url.'js/script.js',
                    array(), 
                    time(), 
                    true
                );
            }
        }

        public static function render_pagination(array $paginationData) 
        {
            ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php            
                        for ($i = 1; $i <= $paginationData['total_pages']; $i++) 
                        {
                    ?>
                    <li class="page-item <?php echo ($i == $paginationData['current_page'] ? 'active' : '') ?> " >
                    <a class="page-link" href="?page=<?php echo esc_attr($i) ?>"><?php echo esc_html($i); ?></a>
                    </li>
                    <?php
                        }
                    ?>
                
                </ul>
            </nav>
            <?php
        }

        public static function default_pagination_template(array $data) 
        {
            if (array_key_exists('pagination_links',$data) && is_array($data['pagination_links']))
            {
                //echo "<Pre>";print_r($data);echo "</pre>";exit;
                $pagination_links = join("\n", $data['pagination_links']);
            ?>
            
                <div class="pagination" data-current-page="<?php echo esc_attr($data['paged']); ?>"><?php echo $pagination_links; ?></div>
                
            <?php
            }
        }
    }
}