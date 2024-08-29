<?php

namespace FlexibleShippingUspsVendor\WPDesk\Persistence;

use FlexibleShippingUspsVendor\Psr\Container\ContainerInterface;
/**
 * Container that allows to get all data stored by container.
 *
 * @package WPDesk\Persistence
 */
interface AllDataAccessContainer extends \FlexibleShippingUspsVendor\Psr\Container\ContainerInterface
{
    /**
     * Get all values.
     *
     * @return array
     */
    public function get_all() : array;
}
