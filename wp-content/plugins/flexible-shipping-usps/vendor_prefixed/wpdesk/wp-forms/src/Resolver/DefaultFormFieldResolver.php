<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Resolver;

use FlexibleShippingUspsVendor\WPDesk\View\Renderer\Renderer;
use FlexibleShippingUspsVendor\WPDesk\View\Resolver\DirResolver;
use FlexibleShippingUspsVendor\WPDesk\View\Resolver\Resolver;
/**
 * Use with View to resolver form fields to default templates.
 *
 * @package WPDesk\Forms\Resolver
 */
class DefaultFormFieldResolver implements \FlexibleShippingUspsVendor\WPDesk\View\Resolver\Resolver
{
    /** @var Resolver */
    private $dir_resolver;
    public function __construct()
    {
        $this->dir_resolver = new \FlexibleShippingUspsVendor\WPDesk\View\Resolver\DirResolver(__DIR__ . '/../../templates');
    }
    public function resolve($name, \FlexibleShippingUspsVendor\WPDesk\View\Renderer\Renderer $renderer = null) : string
    {
        return $this->dir_resolver->resolve($name, $renderer);
    }
}
