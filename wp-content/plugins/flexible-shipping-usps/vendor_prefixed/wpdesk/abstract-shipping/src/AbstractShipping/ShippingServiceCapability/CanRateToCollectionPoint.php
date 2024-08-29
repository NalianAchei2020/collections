<?php

/**
 * Capability: CanRateToCollectionPoint class
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
/**
 * Interface for rate shipment to collection point
 */
interface CanRateToCollectionPoint
{
    /**
     * Rate shipment to collection point.
     *
     * @param SettingsValues  $settings Settings.
     * @param Shipment        $shipment Shipment.
     * @param CollectionPoint $collection_point Collection point.
     *
     * @return ShipmentRating
     */
    public function rate_shipment_to_collection_point(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint $collection_point);
    /**
     * Is rate to collection point enabled?
     *
     * @param SettingsValues $settings
     *
     * @return mixed
     */
    public function is_rate_to_collection_point_enabled(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings);
}
