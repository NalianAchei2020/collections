<?php

namespace FlexibleShippingUspsVendor\WPDesk\View\Resolver;

use FlexibleShippingUspsVendor\WPDesk\View\Renderer\Renderer;
use FlexibleShippingUspsVendor\WPDesk\View\Resolver\Exception\CanNotResolve;
/**
 * This resolver never finds the file
 *
 * @package WPDesk\View\Resolver
 */
class NullResolver implements \FlexibleShippingUspsVendor\WPDesk\View\Resolver\Resolver
{
    public function resolve($name, \FlexibleShippingUspsVendor\WPDesk\View\Renderer\Renderer $renderer = null)
    {
        throw new \FlexibleShippingUspsVendor\WPDesk\View\Resolver\Exception\CanNotResolve("Null Cannot resolve");
    }
}
