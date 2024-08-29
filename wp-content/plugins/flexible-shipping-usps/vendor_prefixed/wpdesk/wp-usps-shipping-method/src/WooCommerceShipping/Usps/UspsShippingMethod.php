<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\AddressProvider;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\CustomOriginAddressSender;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceAddressSender;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * USPS Shipping Method.
 */
class UspsShippingMethod extends \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod implements \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasFreeShipping, \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\HasRateCache
{
    /**
     * Supports.
     *
     * @var array
     */
    public $supports = array('settings', 'shipping-zones', 'instance-settings');
    /**
     * @var FieldApiStatusAjax
     */
    protected static $api_status_ajax_handler;
    /**
     * Set api status field AJAX handler.
     *
     * @param FieldApiStatusAjax $api_status_ajax_handler .
     */
    public static function set_api_status_ajax_handler(\FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax $api_status_ajax_handler)
    {
        static::$api_status_ajax_handler = $api_status_ajax_handler;
    }
    /**
     * Prepare description.
     * Description depends on current page.
     *
     * @return string
     */
    private function prepare_description()
    {
        $docs_link = 'https://octol.io/usps-method-docs';
        return \sprintf(
            // Translators: docs URL.
            \__('Dynamically calculated USPS live rates based on the established USPS API connection. %1$sLearn more →%2$s', 'flexible-shipping-usps'),
            '<a target="_blank" href="' . $docs_link . '">',
            '</a>'
        );
    }
    /**
     * Init method.
     */
    public function init()
    {
        parent::init();
        $this->method_description = $this->prepare_description();
    }
    /**
     * Init form fields.
     */
    public function build_form_fields()
    {
        $usps_settings_definition = new \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\UspsSettingsDefinitionWooCommerce($this->form_fields);
        $this->form_fields = $usps_settings_definition->get_form_fields();
        $this->instance_form_fields = $usps_settings_definition->get_instance_form_fields();
    }
    /**
     * Create meta data builder.
     *
     * @return UspsMetaDataBuilder
     */
    protected function create_metadata_builder()
    {
        return new \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\UspsMetaDataBuilder($this);
    }
    /**
     * Render shipping method settings.
     */
    public function admin_options()
    {
        if ($this->instance_id) {
            $shipping_zone = $this->get_zone_for_shipping_method($this->instance_id);
            if (!$this->is_zone_for_domestic_services($shipping_zone)) {
                unset($this->instance_form_fields[\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::SERVICES_DOMESTIC]);
            }
            if (!$this->is_zone_for_international_services($shipping_zone)) {
                unset($this->instance_form_fields[\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::SERVICES_INTERNATIONAL]);
            }
        }
        parent::admin_options();
        include __DIR__ . '/view/shipping-method-script.php';
    }
    /**
     * @param int $instance_id
     *
     * @return \WC_Shipping_Zone
     */
    private function get_zone_for_shipping_method($instance_id)
    {
        $woocommerce_shipping_zones = \WC_Shipping_Zones::get_zones();
        $zone = new \WC_Shipping_Zone();
        foreach ($woocommerce_shipping_zones as $woocommerce_shipping_zone) {
            foreach ($woocommerce_shipping_zone['shipping_methods'] as $woocommerce_shipping_method) {
                if ($woocommerce_shipping_method->instance_id === $instance_id) {
                    $zone = \WC_Shipping_Zones::get_zone($woocommerce_shipping_zone['id']);
                }
            }
        }
        return $zone;
    }
    /**
     * @param \WC_Shipping_Zone $zone
     *
     * @return bool
     */
    private function is_zone_for_domestic_services(\WC_Shipping_Zone $zone)
    {
        $zone_locations = $zone->get_zone_locations();
        $is_domestic = \count($zone_locations) ? \false : \true;
        foreach ($zone_locations as $zone_location) {
            if ('country' === $zone_location->type || 'state' === $zone_location->type) {
                $code_exploded = \explode(':', $zone_location->code);
                $country_code = $code_exploded[0];
                $is_domestic = $is_domestic || 'US' === $country_code;
            }
        }
        return $is_domestic;
    }
    /**
     * @param \WC_Shipping_Zone $zone
     *
     * @return bool
     */
    private function is_zone_for_international_services(\WC_Shipping_Zone $zone)
    {
        $zone_locations = $zone->get_zone_locations();
        $is_international = \count($zone_locations) ? \false : \true;
        foreach ($zone_locations as $zone_location) {
            if ('country' === $zone_location->type || 'state' === $zone_location->type) {
                $code_exploded = \explode(':', $zone_location->code);
                $country_code = $code_exploded[0];
                $is_international = $is_international || 'US' !== $country_code;
            } elseif ('continent' === $zone_location->type) {
                $is_international = \true;
            }
        }
        return $is_international;
    }
}
