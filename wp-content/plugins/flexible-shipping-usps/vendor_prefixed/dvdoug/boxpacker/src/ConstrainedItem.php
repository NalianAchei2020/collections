<?php

/**
 * Box packing (3D bin packing, knapsack problem).
 *
 * @author Doug Wright
 */
declare (strict_types=1);
namespace FlexibleShippingUspsVendor\DVDoug\BoxPacker;

/**
 * An item to be packed where additional constraints need to be considered. Only implement this interface if you actually
 * need this additional functionality as it will slow down the packing algorithm.
 *
 * @deprecated use ConstrainedPlacementItem instead which has additional flexibility
 */
interface ConstrainedItem extends \FlexibleShippingUspsVendor\DVDoug\BoxPacker\Item
{
    /**
     * Hook for user implementation of item-specific constraints, e.g. max <x> batteries per box.
     */
    public function canBePackedInBox(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedItemList $alreadyPackedItems, \FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box $box) : bool;
}
