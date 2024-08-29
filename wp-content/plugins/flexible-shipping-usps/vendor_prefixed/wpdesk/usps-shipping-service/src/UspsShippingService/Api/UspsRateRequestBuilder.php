<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Item;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Build request for USPS rate
 */
abstract class UspsRateRequestBuilder
{
    const WEIGHT_ROUNDING_PRECISION = 4;
    /**
     * WooCommerce shipment.
     *
     * @var Shipment
     */
    protected $shipment;
    /**
     * Settings values.
     *
     * @var SettingsValues
     */
    protected $settings;
    /**
     * Request
     *
     * @var Rate
     */
    protected $request;
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    protected $shop_settings;
    /**
     * UspsRateRequestBuilder constructor.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment $shipment Shipment.
     * @param ShopSettings $helper Helper.
     * @param array $services Services.
     * @param LoggerInterface $logger Logger.
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings $helper, \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->shipment = $shipment;
        $this->shop_settings = $helper;
        $this->request = $this->prepare_rate_request($logger);
    }
    /**
     * Prepare rate request.
     *
     * @return Rate
     */
    protected abstract function prepare_rate_request(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate;
    /**
     * Calculate package weight.
     *
     * @param Package $shipment_package .
     * @param string $weight_unit .
     *
     * @return float
     * @throws UnitConversionException Weight exception.
     */
    protected function calculate_package_weight(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package $shipment_package, $weight_unit) : float
    {
        $package_weight = 0.0;
        foreach ($shipment_package->items as $item) {
            $item_weight = (new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight($item->weight->weight, $item->weight->weight_unit, self::WEIGHT_ROUNDING_PRECISION))->as_unit_rounded($weight_unit, self::WEIGHT_ROUNDING_PRECISION);
            $package_weight += $item_weight;
        }
        return $package_weight;
    }
    /**
     * @param RatePackage $package
     * @param PAckage $shipment_package
     *
     * @return void
     * @throws UnitConversionException
     */
    protected function set_package_weight(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage $package, $shipment_package)
    {
        $pounds = $this->calculate_package_weight($shipment_package, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB);
        $truncated_pounds = \floor($pounds);
        $ounces = (new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalWeight($pounds - $truncated_pounds, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_LB))->as_unit_rounded(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight::WEIGHT_UNIT_OZ);
        $pounds = $truncated_pounds;
        $package->setPounds($pounds);
        $package->setOunces($ounces);
    }
    /**
     * Calculate package value.
     *
     * @param Package $shipment_package .
     *
     * @return float
     */
    protected function calculate_package_value(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package $shipment_package) : float
    {
        $total_value = 0.0;
        /** @var Item $item */
        // phpcs:ignore
        foreach ($shipment_package->items as $item) {
            $total_value += $item->declared_value->amount;
        }
        return \round($total_value, 2);
    }
    /**
     * Set package dimensions if present.
     *
     * @param RatePackage $usps_package .
     * @param Package $shipment_package .
     *
     * @return RatePackage
     * @throws UnitConversionException .
     */
    protected function set_dimensions_if_present(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage $usps_package, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package $shipment_package) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage
    {
        if (isset($shipment_package->dimensions)) {
            $target_dimension_unit = \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Dimensions::DIMENSION_UNIT_IN;
            $width = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension($shipment_package->dimensions->width, $shipment_package->dimensions->dimensions_unit);
            $height = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension($shipment_package->dimensions->height, $shipment_package->dimensions->dimensions_unit);
            $length = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\UnitConversion\UniversalDimension($shipment_package->dimensions->length, $shipment_package->dimensions->dimensions_unit);
            $usps_package->setWidth($width->as_unit_rounded($target_dimension_unit));
            $usps_package->setLength($length->as_unit_rounded($target_dimension_unit));
            $usps_package->setHeight($height->as_unit_rounded($target_dimension_unit));
        }
        return $usps_package;
    }
    /**
     * Add package.
     *
     * @param Package $shipment_package .
     *
     * @throws UnitConversionException .
     */
    protected abstract function add_package(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package $shipment_package) : void;
    /**
     * Set package;
     *
     * @throws UnitConversionException Weight exception.
     */
    protected function set_packages()
    {
        $package_index = 0;
        foreach ($this->shipment->packages as $package) {
            $this->add_package($package, ++$package_index);
        }
    }
    /**
     * Build request.
     *
     * @throws UnitConversionException Weight exception.
     */
    public function build_request()
    {
        $this->set_packages();
    }
    /**
     * Get request.
     *
     * @return Rate
     */
    public function get_build_request() : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate
    {
        return $this->request;
    }
}
