<?php

/**
 * Shipping builder: ShippingBuilder.
 *
 * @package WPDesk\ShippingBuilder
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use FlexibleShippingUspsVendor\WPDesk\Packer\Box;
use FlexibleShippingUspsVendor\WPDesk\Packer\Item\ItemImplementation;
use FlexibleShippingUspsVendor\WPDesk\Packer\Packer;
use FlexibleShippingUspsVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings;
/**
 * Build raw shipping data from WooCommerce - pro version.
 *
 * @package WPDesk\WooCommerceShippingPro
 */
class WooCommerceShippingBuilder extends \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingBuilder
{
    /** @var Packer */
    private $packer;
    /** @var bool */
    private $should_use_packer;
    /** @var bool */
    private $is_unit_metric;
    /**
     * WooCommerceShippingBuilder constructor.
     *
     * @param Packer $packer
     * @param string $packaging_method One of packaging method names
     * @param bool   $is_unit_metric
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk\Packer\Packer $packer, $packaging_method, $is_unit_metric)
    {
        $this->packer = $packer;
        $this->should_use_packer = $packaging_method !== \FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\Packer\PackerSettings::PACKING_METHOD_WEIGHT;
        $this->is_unit_metric = $is_unit_metric;
    }
    /**
     * @param \WC_Product $item
     * @param array $package_item
     *
     * @throws UnitConversionException
     */
    private function add_converted_item_to_packer(\WC_Product $item, array $package_item)
    {
        if ($this->is_unit_metric) {
            $packer_dimension_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_CM;
            $packer_weight_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG;
        } else {
            $packer_dimension_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_IN;
            $packer_weight_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LBS;
        }
        $item_value = ($package_item['line_total'] + $package_item['line_tax']) / $package_item['quantity'];
        $this->packer->add_item(new \FlexibleShippingUspsVendor\WPDesk\Packer\Item\ItemImplementation(\wc_get_dimension($item->get_length(), $packer_dimension_unit), \wc_get_dimension($item->get_width(), $packer_dimension_unit), \wc_get_dimension($item->get_height(), $packer_dimension_unit), \wc_get_weight($item->get_weight(), $packer_weight_unit), $item_value, $package_item));
    }
    /**
     * Put WooCommerce packages to packer and pack them.
     *
     * @throws CannotPackItemsException
     * @throws UnitConversionException
     */
    private function pack()
    {
        foreach ($this->package['contents'] as $package_item) {
            /** @var \WC_Product $item */
            // phpcs:ignore
            $item = $package_item['data'];
            for ($i = 1; $i <= \intval($package_item['quantity']); $i++) {
                // phpcs:ignore
                $this->verify_item($item);
                $this->add_converted_item_to_packer($item, $package_item);
            }
        }
        $this->packer->pack();
        $items_cannot_pack = $this->packer->get_items_cannot_pack();
        if (!empty($items_cannot_pack) && \count($items_cannot_pack)) {
            throw new \FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder\CannotPackItemsException($items_cannot_pack);
        }
    }
    /**
     * Verify if item can be added to package.
     *
     * @param \WC_Product $item .
     *
     * @throws CannotPackItemException .
     */
    private function verify_item($item)
    {
        $reason = '';
        if (empty($item->get_weight())) {
            $reason .= \__('weight', 'flexible-shipping-usps') . ', ';
        }
        if (empty($item->get_width())) {
            $reason .= \__('width', 'flexible-shipping-usps') . ', ';
        }
        if (empty($item->get_length())) {
            $reason .= \__('length', 'flexible-shipping-usps') . ', ';
        }
        if (empty($item->get_height())) {
            $reason .= \__('height', 'flexible-shipping-usps') . ', ';
        }
        if (!empty($reason)) {
            \wc_clear_notices();
            $reason = \trim(\trim($reason), ',');
            // Translators: reasons.
            $reason = \sprintf(\__('Item %1$s not set!', 'flexible-shipping-usps'), $reason);
            throw new \FlexibleShippingUspsVendor\WPDesk\WooCommerceShippingPro\ShippingBuilder\CannotPackItemException($item, $reason);
        }
    }
    /**
     * Convert packed packages to packages that can be shipped.
     *
     * @return Package[]
     */
    private function convert_packed_to_shipping_package()
    {
        $shipping_packages = [];
        foreach ($this->packer->get_packages() as $package) {
            $new_package = $this->create_package_from_box($package->get_box());
            $new_package->weight->weight += $package->get_packed_weight();
            foreach ($package->get_packed_items() as $packed_item) {
                $new_package->items[] = $this->add_package_item($packed_item->get_internal_data());
            }
            $shipping_packages[] = $new_package;
        }
        return $shipping_packages;
    }
    /**
     * @param Box $box
     *
     * @return Package warning: Returned package does not have weight value!
     */
    private function create_package_from_box(\FlexibleShippingUspsVendor\WPDesk\Packer\Box $box)
    {
        $settings_box = null;
        if (isset($box->get_internal_data()['box']) && $box->get_internal_data()['box'] instanceof \FlexibleShippingUspsVendor\WpDesk\WooCommerce\ShippingMethod\SettingsBox) {
            $settings_box = $box->get_internal_data()['box'];
        }
        $package = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package();
        $package->package_type = $box->get_unique_id();
        $package->description = $box->get_name();
        $dimension = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions();
        $dimension->length = $box->get_length();
        $dimension->width = $box->get_width();
        $dimension->height = $box->get_height();
        if ($settings_box) {
            $dimension->length += $settings_box->get_padding();
            $dimension->width += $settings_box->get_padding();
            $dimension->height += $settings_box->get_padding();
        }
        if ($this->is_unit_metric) {
            $dimension->dimensions_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_CM;
        } else {
            $dimension->dimensions_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_IN;
        }
        $package->dimensions = $dimension;
        $weight = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight();
        $weight->weight = 0.0;
        if ($this->is_unit_metric) {
            $weight->weight_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_KG;
        } else {
            $weight->weight_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LBS;
        }
        $package->weight = $weight;
        return $package;
    }
    /**
     * Get package.
     *
     * @return Package[]
     *
     * @throws CannotPackItemsException
     * @throws UnitConversionException
     */
    protected function get_packages()
    {
        if ($this->should_use_packer) {
            $this->pack();
            return $this->convert_packed_to_shipping_package();
        }
        return parent::get_packages();
    }
    /**
     * Return shipping.
     *
     * @return \WPDesk\AbstractShipping\Shipment\Shipment;
     */
    public function build_shipment()
    {
        $shipment = parent::build_shipment();
        $shipment->packed = $this->should_use_packer;
        return $shipment;
    }
}
