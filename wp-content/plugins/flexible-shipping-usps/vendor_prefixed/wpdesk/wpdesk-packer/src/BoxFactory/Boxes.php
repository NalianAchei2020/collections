<?php

/**
 * Boxes interface.
 *
 * @package WPDesk\Packer\BoxFactory
 */
namespace FlexibleShippingUspsVendor\WPDesk\Packer\BoxFactory;

use FlexibleShippingUspsVendor\WPDesk\Packer\Box;
/**
 * Boxes as array.
 */
interface Boxes
{
    /**
     * Get boxes array.
     *
     * @return Box[]
     */
    public function get_boxes();
}
