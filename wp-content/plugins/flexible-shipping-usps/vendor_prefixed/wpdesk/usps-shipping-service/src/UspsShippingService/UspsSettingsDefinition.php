<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException;
/**
 * A class that defines the basic settings for the shipping method.
 */
class UspsSettingsDefinition extends \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition
{
    const CUSTOM_SERVICES_CHECKBOX_CLASS = 'wpdesk_wc_shipping_custom_service_checkbox';
    const SHIPPING_METHOD_TITLE = 'shipping_method_title';
    const API_SETTINGS_TITLE = 'api_settings_title';
    const USER_ID = 'user_id';
    const PASSWORD = 'password';
    const TESTING = 'testing';
    const ORIGIN_SETTINGS_TITLE = 'origin_settings_title';
    const CUSTOM_ORIGIN = 'custom_origin';
    const ORIGIN_ADDRESS = 'origin_address';
    const ORIGIN_CITY = 'origin_city';
    const ORIGIN_POSTCODE = 'origin_postcode';
    const ORIGIN_COUNTRY = 'origin_country';
    const ADVANCED_OPTIONS_TITLE = 'advanced_options_title';
    const DEBUG_MODE = 'debug_mode';
    const API_STATUS = 'api_status';
    const METHOD_SETTINGS_TITLE = 'method_settings_title';
    const TITLE = 'title';
    const FALLBACK = 'fallback';
    const CUSTOM_SERVICES = 'custom_services';
    const SERVICES_DOMESTIC = 'services_domestic';
    const SERVICES_INTERNATIONAL = 'services_international';
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    const INSURANCE = 'insurance';
    const FREE_SHIPPING = 'free_shipping';
    const COMMERCIAL_RATES = 'commercial_rates';
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * UspsSettingsDefinition constructor.
     *
     * @param ShopSettings $shop_settings Shop settings.
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings $shop_settings)
    {
        $this->shop_settings = $shop_settings;
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings) : bool
    {
        return \true;
    }
    /**
     * Prepare country state options.
     *
     * @return array
     */
    private function prepare_country_state_options() : array
    {
        try {
            $countries = $this->shop_settings->get_countries();
        } catch (\FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException $e) {
            $countries = [];
        }
        $country_state_options = $countries;
        foreach ($country_state_options as $country_code => $country) {
            $states = $this->shop_settings->get_states($country_code);
            if ($states) {
                unset($country_state_options[$country_code]);
                foreach ($states as $state_code => $state_name) {
                    $country_state_options[$country_code . ':' . $state_code] = $country . ' &mdash; ' . $state_name;
                }
            }
        }
        return $country_state_options;
    }
    /**
     * Initialise Settings Form Fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        $services = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices();
        $docs_link = 'https://octol.io/usps-method-docs';
        $connection_fields = [self::SHIPPING_METHOD_TITLE => ['title' => \__('USPS', 'flexible-shipping-usps'), 'type' => 'title', 'description' => \sprintf(
            // Translators: docs link.
            \__('These are the USPS Live Rates plugin general settings. In order to learn more about its configuration please refer to its %1$sdedicated documentation →%2$s', 'flexible-shipping-usps'),
            '<a href="' . $docs_link . '" target="_blank">',
            '</a>'
        )], self::API_SETTINGS_TITLE => [
            'title' => \__('API Settings', 'flexible-shipping-usps'),
            'type' => 'title',
            // Translators: link.
            'description' => \sprintf(\__('Enter your USPS Web Tools API USERID and PASSWORD to establish the USPS API connection for the live rates to be calculated. Please mind that it usually consists of random numbers and characters and it is different than your standard USPS user account login you use to sign in at usps.com. If you do not have a USERID yet, %1$sregister for USPS Web Tools now%2$s or follow the instructions from our guide on %3$show to obtain the USPS Web Tools API access &rarr;%4$s', 'flexible-shipping-usps'), '<a href="https://octol.io/usps-web-tools-api-site" target="_blank">', '</a>', '<a href="https://octol.io/usps-web-tools-api-access-docs" target="_blank">', '</a>'),
        ], self::USER_ID => ['title' => \__('USPS Web Tools API USERID*', 'flexible-shipping-usps'), 'type' => 'text', 'custom_attributes' => ['required' => 'required'], 'description' => \__('Enter your USPS USERID you acquired during the USPS Web Tools API registration process.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => ''], self::PASSWORD => ['title' => \__('USPS Web Tools API PASSWORD*', 'flexible-shipping-usps'), 'type' => 'password', 'custom_attributes' => ['required' => 'required'], 'description' => \__('Enter your USPS PASSWORD you acquired during the USPS Web Tools API registration process.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => '']];
        if ($this->shop_settings->is_testing()) {
            $connection_fields[self::TESTING] = ['title' => \__('Test Credentials', 'fedex-shipping-service'), 'type' => 'checkbox', 'label' => \__('Enable to use test credentials', 'fedex-shipping-service'), 'desc_tip' => \true, 'default' => 'no'];
        }
        $fields = [self::ADVANCED_OPTIONS_TITLE => ['title' => \__('Advanced Options', 'flexible-shipping-usps'), 'type' => 'title'], self::DEBUG_MODE => ['title' => \__('Debug Mode', 'flexible-shipping-usps'), 'label' => \__('Enable debug mode', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => \__('Enable debug mode to display the additional tech information, incl. the data sent to USPS API, visible only for Admins and Shop Managers in the cart and checkout.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => 'no']];
        $instance_fields = [self::METHOD_SETTINGS_TITLE => ['title' => \__('Method Settings', 'flexible-shipping-usps'), 'description' => \__('Manage the way how the USPS services are displayed in the cart and checkout.', 'flexible-shipping-usps'), 'type' => 'title'], self::TITLE => ['title' => \__('Method Title', 'flexible-shipping-usps'), 'type' => 'text', 'description' => \__('Define the USPS shipping method title which should be used in the cart/checkout when the Fallback option was triggered.', 'flexible-shipping-usps'), 'default' => \__('USPS Live rates', 'flexible-shipping-usps'), 'desc_tip' => \true], self::FALLBACK => ['type' => \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod::FIELD_TYPE_FALLBACK, 'description' => \__('Enable to offer flat rate cost for shipping so that the user can still checkout, if API for some reason returns no matching rates.', 'flexible-shipping-usps'), 'default' => ''], self::FREE_SHIPPING => ['title' => \__('Free Shipping', 'flexible-shipping-usps'), 'type' => \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields::FIELD_TYPE_FREE_SHIPPING, 'default' => ''], self::CUSTOM_SERVICES => ['title' => \__('Services', 'flexible-shipping-usps'), 'label' => \__('Enable the services\' custom settings', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => \__('Decide which services should be displayed and which not, change their names and order. Please mind that enabling a service does not guarantee it will be visible in the cart/checkout. It has to be available for the provided package weight, origin and destination in order to be displayed.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'class' => self::CUSTOM_SERVICES_CHECKBOX_CLASS, 'default' => 'no'], self::SERVICES_DOMESTIC => ['title' => \__('Domestic Services Table', 'flexible-shipping-usps'), 'type' => 'services', 'default' => '', 'options' => $services->get_services_domestic()], self::SERVICES_INTERNATIONAL => ['title' => \__('International Services Table', 'flexible-shipping-usps'), 'type' => 'services', 'default' => '', 'options' => $services->get_services_international()], self::RATE_ADJUSTMENTS_TITLE => ['title' => \__('Rates Adjustments', 'flexible-shipping-usps'), 'description' => \sprintf(\__('Adjust these settings to get more accurate rates. Read %swhat affects the USPS rates in USPS WooCommerce plugin →%s', 'flexible-shipping-usps'), \sprintf('<a href="%s" target="_blank">', \__('https://octol.io/usps-free-rates', 'flexible-shipping-usps')), '</a>'), 'type' => 'title'], self::COMMERCIAL_RATES => ['title' => \__('Commercial Rates', 'flexible-shipping-usps'), 'label' => \__('Use the USPS Commercial Pricing if available', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => \__('Tick this checkbox if you want to use and display the Commercial Pricing shipping rates to your customers instead of the standard ones.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => 'no'], self::INSURANCE => ['title' => \__('Insurance', 'flexible-shipping-usps'), 'label' => \__('Request insurance to be included in the USPS rates', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => \__('Enabling this option will increase time needed to return rates and may limit the number of available services.', 'flexible-shipping-usps'), 'desc_tip' => \__('Enable if you want to include insurance in the USPS rates if possible.', 'flexible-shipping-usps'), 'default' => 'no']];
        return $connection_fields + $fields + $instance_fields;
    }
}
