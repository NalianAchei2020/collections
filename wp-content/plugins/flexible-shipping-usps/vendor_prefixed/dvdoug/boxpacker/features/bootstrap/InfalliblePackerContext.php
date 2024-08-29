<?php

/**
 * Box packing (3D bin packing, knapsack problem).
 *
 * @author Doug Wright
 */
declare (strict_types=1);
namespace FlexibleShippingUspsVendor;

use FlexibleShippingUspsVendor\DVDoug\BoxPacker\InfalliblePacker;
use FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList;
use FlexibleShippingUspsVendor\PHPUnit\Framework\Assert;
/**
 * Defines application features from the specific context.
 */
class InfalliblePackerContext extends \FlexibleShippingUspsVendor\PackerContext
{
    protected string $packerClass = \FlexibleShippingUspsVendor\DVDoug\BoxPacker\InfalliblePacker::class;
    protected \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList $unpackedItemList;
    /**
     * @When I do an infallible packing
     */
    public function iDoAnInfalliblePacking() : void
    {
        $packer = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\InfalliblePacker();
        $packer->setBoxes($this->boxList);
        $packer->setItems($this->itemList);
        $this->packedBoxList = $packer->pack();
        $this->unpackedItemList = $packer->getUnpackedItems();
    }
    /**
     * @Then /^the unpacked item list should have (\d+) items of type "([^"]+)"$/
     */
    public function theUnpackedItemListShouldHaveItems($qty, $itemType) : void
    {
        $foundItems = 0;
        foreach ($this->unpackedItemList as $unpackedItem) {
            if ($unpackedItem->getDescription() === $itemType) {
                ++$foundItems;
            }
        }
        \FlexibleShippingUspsVendor\PHPUnit\Framework\Assert::assertEquals($qty, $foundItems);
    }
}
