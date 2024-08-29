<?php

/**
 * Box packing (3D bin packing, knapsack problem).
 *
 * @author Doug Wright
 */
declare (strict_types=1);
namespace FlexibleShippingUspsVendor\DVDoug\BoxPacker;

use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\LogLevel;
use FlexibleShippingUspsVendor\Psr\Log\NullLogger;
use SplObjectStorage;
use function count;
use function usort;
use const PHP_INT_MAX;
/**
 * Actual packer.
 */
class Packer implements \FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface
{
    private \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger;
    protected int $maxBoxesToBalanceWeight = 12;
    protected \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList $items;
    protected \FlexibleShippingUspsVendor\DVDoug\BoxPacker\BoxList $boxes;
    /**
     * @var SplObjectStorage<Box, int>
     */
    protected \SplObjectStorage $boxQuantitiesAvailable;
    protected \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedBoxSorter $packedBoxSorter;
    private bool $beStrictAboutItemOrdering = \false;
    public function __construct()
    {
        $this->items = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList();
        $this->boxes = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\BoxList();
        $this->boxQuantitiesAvailable = new \SplObjectStorage();
        $this->packedBoxSorter = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\DefaultPackedBoxSorter();
        $this->logger = new \FlexibleShippingUspsVendor\Psr\Log\NullLogger();
    }
    public function setLogger(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }
    /**
     * Add item to be packed.
     */
    public function addItem(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Item $item, int $qty = 1) : void
    {
        $this->items->insert($item, $qty);
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, "added {$qty} x {$item->getDescription()}", ['item' => $item]);
    }
    /**
     * Set a list of items all at once.
     * @param iterable<Item> $items
     */
    public function setItems(iterable $items) : void
    {
        if ($items instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList) {
            $this->items = clone $items;
        } else {
            $this->items = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\ItemList();
            foreach ($items as $item) {
                $this->items->insert($item);
            }
        }
    }
    /**
     * Add box size.
     */
    public function addBox(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box $box) : void
    {
        $this->boxes->insert($box);
        $this->setBoxQuantity($box, $box instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\LimitedSupplyBox ? $box->getQuantityAvailable() : \PHP_INT_MAX);
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, "added box {$box->getReference()}", ['box' => $box]);
    }
    /**
     * Add a pre-prepared set of boxes all at once.
     */
    public function setBoxes(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\BoxList $boxList) : void
    {
        $this->boxes = $boxList;
        foreach ($this->boxes as $box) {
            $this->setBoxQuantity($box, $box instanceof \FlexibleShippingUspsVendor\DVDoug\BoxPacker\LimitedSupplyBox ? $box->getQuantityAvailable() : \PHP_INT_MAX);
        }
    }
    /**
     * Set the quantity of this box type available.
     */
    public function setBoxQuantity(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\Box $box, int $qty) : void
    {
        $this->boxQuantitiesAvailable[$box] = $qty;
    }
    /**
     * Number of boxes at which balancing weight is deemed not worth the extra computation time.
     */
    public function getMaxBoxesToBalanceWeight() : int
    {
        return $this->maxBoxesToBalanceWeight;
    }
    /**
     * Number of boxes at which balancing weight is deemed not worth the extra computation time.
     */
    public function setMaxBoxesToBalanceWeight(int $maxBoxesToBalanceWeight) : void
    {
        $this->maxBoxesToBalanceWeight = $maxBoxesToBalanceWeight;
    }
    public function setPackedBoxSorter(\FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedBoxSorter $packedBoxSorter) : void
    {
        $this->packedBoxSorter = $packedBoxSorter;
    }
    public function beStrictAboutItemOrdering(bool $beStrict) : void
    {
        $this->beStrictAboutItemOrdering = $beStrict;
    }
    /**
     * Pack items into boxes using built-in heuristics for the best solution.
     */
    public function pack() : \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedBoxList
    {
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, '[PACKING STARTED]');
        $packedBoxes = $this->doBasicPacking();
        // If we have multiple boxes, try and optimise/even-out weight distribution
        if (!$this->beStrictAboutItemOrdering && $packedBoxes->count() > 1 && $packedBoxes->count() <= $this->maxBoxesToBalanceWeight) {
            $redistributor = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\WeightRedistributor($this->boxes, $this->packedBoxSorter, $this->boxQuantitiesAvailable);
            $redistributor->setLogger($this->logger);
            $packedBoxes = $redistributor->redistributeWeight($packedBoxes);
        }
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, "[PACKING COMPLETED], {$packedBoxes->count()} boxes");
        return $packedBoxes;
    }
    /**
     * @internal
     */
    public function doBasicPacking(bool $enforceSingleBox = \false) : \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedBoxList
    {
        $packedBoxes = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\PackedBoxList($this->packedBoxSorter);
        // Keep going until everything packed
        while ($this->items->count()) {
            $packedBoxesIteration = [];
            // Loop through boxes starting with smallest, see what happens
            foreach ($this->getBoxList($enforceSingleBox) as $box) {
                $volumePacker = new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\VolumePacker($box, $this->items);
                $volumePacker->setLogger($this->logger);
                $volumePacker->beStrictAboutItemOrdering($this->beStrictAboutItemOrdering);
                $packedBox = $volumePacker->pack();
                if ($packedBox->getItems()->count()) {
                    $packedBoxesIteration[] = $packedBox;
                    // Have we found a single box that contains everything?
                    if ($packedBox->getItems()->count() === $this->items->count()) {
                        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::DEBUG, "Single box found for remaining {$this->items->count()} items");
                        break;
                    }
                }
            }
            if (\count($packedBoxesIteration) > 0) {
                // Find best box of iteration, and remove packed items from unpacked list
                \usort($packedBoxesIteration, [$this->packedBoxSorter, 'compare']);
                $bestBox = $packedBoxesIteration[0];
                $this->items->removePackedItems($bestBox->getItems());
                $packedBoxes->insert($bestBox);
                $this->boxQuantitiesAvailable[$bestBox->getBox()] = $this->boxQuantitiesAvailable[$bestBox->getBox()] - 1;
            } elseif (!$enforceSingleBox) {
                throw new \FlexibleShippingUspsVendor\DVDoug\BoxPacker\NoBoxesAvailableException("No boxes could be found for item '{$this->items->top()->getDescription()}'", $this->items->top());
            } else {
                $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, "{$this->items->count()} unpackable items found");
                break;
            }
        }
        return $packedBoxes;
    }
    /**
     * Get a "smart" ordering of the boxes to try packing items into. The initial BoxList is already sorted in order
     * so that the smallest boxes are evaluated first, but this means that time is spent on boxes that cannot possibly
     * hold the entire set of items due to volume limitations. These should be evaluated first.
     *
     * @return iterable<Box>
     */
    protected function getBoxList(bool $enforceSingleBox = \false) : iterable
    {
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, 'Determining box search pattern', ['enforceSingleBox' => $enforceSingleBox]);
        $itemVolume = 0;
        foreach ($this->items as $item) {
            $itemVolume += $item->getWidth() * $item->getLength() * $item->getDepth();
        }
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::DEBUG, 'Item volume', ['itemVolume' => $itemVolume]);
        $preferredBoxes = [];
        $otherBoxes = [];
        foreach ($this->boxes as $box) {
            if ($this->boxQuantitiesAvailable[$box] > 0) {
                if ($box->getInnerWidth() * $box->getInnerLength() * $box->getInnerDepth() >= $itemVolume) {
                    $preferredBoxes[] = $box;
                } elseif (!$enforceSingleBox) {
                    $otherBoxes[] = $box;
                }
            }
        }
        $this->logger->log(\FlexibleShippingUspsVendor\Psr\Log\LogLevel::INFO, 'Box search pattern complete', ['preferredBoxCount' => \count($preferredBoxes), 'otherBoxCount' => \count($otherBoxes)]);
        return [...$preferredBoxes, ...$otherBoxes];
    }
}
