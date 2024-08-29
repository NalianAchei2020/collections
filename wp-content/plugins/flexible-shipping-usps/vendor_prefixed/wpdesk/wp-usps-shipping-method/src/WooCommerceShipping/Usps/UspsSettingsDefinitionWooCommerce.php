<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
/**
 * Can handle global and instance settings for WooCommerce shipping method.
 */
class UspsSettingsDefinitionWooCommerce
{
    private $global_method_fields = [\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::SHIPPING_METHOD_TITLE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::API_SETTINGS_TITLE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::USER_ID, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::PASSWORD, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::TESTING, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ORIGIN_SETTINGS_TITLE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::CUSTOM_ORIGIN, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ORIGIN_ADDRESS, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ORIGIN_CITY, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ORIGIN_POSTCODE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ORIGIN_COUNTRY, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::ADVANCED_OPTIONS_TITLE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::DEBUG_MODE, \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::API_STATUS];
    /**
     * Form fields.
     *
     * @var array
     */
    private $form_fields;
    /**
     * UspsSettingsDefinitionWooCommerce constructor.
     *
     * @param array $form_fields Form fields.
     */
    public function __construct(array $form_fields)
    {
        $this->form_fields = $form_fields;
    }
    /**
     * Get form fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \false);
    }
    /**
     * Get instance form fields.
     *
     * @return array
     */
    public function get_instance_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \true);
    }
    /**
     * Get global method fields.
     *
     * @return array
     */
    protected function get_global_method_fields()
    {
        return $this->global_method_fields;
    }
    /**
     * Filter instance form fields.
     *
     * @param array $all_fields .
     * @param bool  $instance_fields .
     *
     * @return array
     */
    private function filter_instance_fields(array $all_fields, $instance_fields)
    {
        $fields = array();
        foreach ($all_fields as $key => $field) {
            $is_instance_field = !\in_array($key, $this->get_global_method_fields(), \true);
            if ($instance_fields && $is_instance_field || !$instance_fields && !$is_instance_field) {
                $fields[$key] = $field;
            }
        }
        return $fields;
    }
}
