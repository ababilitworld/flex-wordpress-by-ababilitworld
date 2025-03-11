<?php 
namespace Ababilitworld\FlexWordpressByAbabilitworld\Package\Posttype\Contract;

interface Posttype 
{
    public function init(array $data): void;
}