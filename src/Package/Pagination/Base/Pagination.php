<?php

namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Pagination\Base;

(defined('ABSPATH') && defined('WPINC')) || die();

use Ababilitworld\{
    FlexTraitByAbabilitworld\Standard\Standard,
    FlexTraitByAbabilitworld\Security\Sanitization\Sanitization,
    FlexWordpressByAbabilitworld\Package\Pagination\Contract\Pagination as PaginationContract
};

if (!class_exists(__NAMESPACE__.'\Pagination')) 
{
    abstract class Pagination implements PaginationContract
    {
        use Standard;

        protected $query;
        protected $attribute;
        protected $totalPages;
        protected $currentPage;
        protected $paginationLinks;
        protected $paginationTemplate;
        
        public function __construct($data)
        {
            $this->query = $data['query'];
            $this->attribute = $data['attribute'];
        }

        abstract public function init($data);

        abstract public function paginate();

        abstract public function pagination_links();

        abstract public function render();
    }
}