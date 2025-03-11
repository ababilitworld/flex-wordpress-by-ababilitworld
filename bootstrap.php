<?php
namespace Ababilitworld\FlexWordpressByAbabilitworld;

class Bootstrap
{
    public function __construct() 
    {
        //Bootstrap construct
    }
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) 
{
    require __DIR__ . '/vendor/autoload.php';
}

new Bootstrap();

