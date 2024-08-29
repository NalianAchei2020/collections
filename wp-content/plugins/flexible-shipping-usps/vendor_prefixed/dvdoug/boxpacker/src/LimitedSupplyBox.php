<?php

/**
 * Box packing (3D bin packing, knapsack problem).
 *
 * @author Doug Wright
 */
declare (strict_types=1);
namespace FlexibleShippingUspsVendor\DVDoug\BoxPacker;

/**
 * A "box" (or envelope?) to pack items into with limited supply.
 */
interface LimitedSupplyBox extends \FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box
{
    /**
     * Quantity of boxes available.
     */
    public function getQuantityAvailable() : int;
}
