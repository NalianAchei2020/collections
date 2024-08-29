<?php

/**
 * Capability: CanRate class
 *
 * @package WPDesk\AbstractShipping\Shipment
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
/**
 * Interface for rate shipment
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
interface CanRate
{
    /**
     * Rate shipment.
     *
     * @param SettingsValues  $settings Settings.
     * @param Shipment        $shipment Shipment.
     *
     * @return ShipmentRating
     */
    public function rate_shipment(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment);
    /**
     * Is rate enabled?
     *
     * @param SettingsValues $settings .
     *
     * @return bool
     */
    public function is_rate_enabled(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings);
}
