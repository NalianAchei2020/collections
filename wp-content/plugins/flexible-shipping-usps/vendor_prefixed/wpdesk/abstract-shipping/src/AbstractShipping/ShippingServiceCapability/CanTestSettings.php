<?php

/**
 * Capability: CanTestSettings class
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
/**
 * Interface for checking connection to API.
 */
interface CanTestSettings
{
    /**
     * Pings API.
     * Returns empty string on success or error message on failure.
     *
     * @param SettingsValues  $settings .
     * @param LoggerInterface $logger .
     * @return string
     */
    public function check_connection(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger);
    /**
     * Returns field ID after which API Status field should be added.
     *
     * @return string
     */
    public function get_field_before_api_status_field();
}
