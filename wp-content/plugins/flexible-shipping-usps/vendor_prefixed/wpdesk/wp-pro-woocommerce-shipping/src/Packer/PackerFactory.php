<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\Packer;

use FlexibleShippingUspsVendor\WPDesk\Packer\Box;
use FlexibleShippingUspsVendor\WPDesk\Packer\Packer;
use FlexibleShippingUspsVendor\WPDesk\Packer\Packer3D;
use FlexibleShippingUspsVendor\WPDesk\Packer\PackerSeparately;
/**
 * Can create a ready to use  packer.
 *
 * @package WPDesk\WooCommerceShippingPro\Packer
 */
class PackerFactory
{
    /** @var string */
    private $packaging_method;
    /**
     * PackerFactory constructor.
     *
     * @param string $packaging_method One of packaging method names
     */
    public function __construct($packaging_method)
    {
        $this->packaging_method = $packaging_method;
    }
    /**
     * Create packer that can pack to given boxes.
     *
     * @param Box[] $boxes Boxes to pack.
     *
     * @return Packer
     */
    public function create_packer(array $boxes)
    {
        if ($this->packaging_method === \FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings::PACKING_METHOD_SEPARATELY) {
            $packer = new \FlexibleShippingUspsVendor\WPDesk\Packer\PackerSeparately();
        } else {
            if ($this->packaging_method === \FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings::PACKING_METHOD_BOX_3D) {
                $packer = new \FlexibleShippingUspsVendor\WPDesk\Packer\Packer3D();
            } else {
                $packer = new \FlexibleShippingUspsVendor\WPDesk\Packer\Packer();
            }
            foreach ($boxes as $box) {
                $packer->add_box($box);
            }
        }
        return $packer;
    }
}
