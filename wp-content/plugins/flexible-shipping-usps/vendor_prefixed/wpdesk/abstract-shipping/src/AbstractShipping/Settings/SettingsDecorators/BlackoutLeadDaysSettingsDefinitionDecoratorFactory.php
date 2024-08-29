<?php

/**
 * Class BlackoutLeadDaysSettingsDefinitionDecoratorFactory
 *
 * @package WPDesk\AbstractShipping\Settings\SettingsDecorators
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDecorators;

/**
 * Can create Blackout Lead Days settings decorator.
 */
class BlackoutLeadDaysSettingsDefinitionDecoratorFactory extends \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDecorators\AbstractDecoratorFactory
{
    const OPTION_ID = 'blackout_lead_days';
    /**
     * @return string
     */
    public function get_field_id()
    {
        return self::OPTION_ID;
    }
    /**
     * @return array
     */
    protected function get_field_settings()
    {
        return array('title' => \__('Blackout Lead Days', 'flexible-shipping-usps'), 'type' => 'multiselect', 'description' => \__('Blackout Lead Days are used to define days of the week when shop is not processing orders.', 'flexible-shipping-usps'), 'options' => array('1' => \__('Monday', 'flexible-shipping-usps'), '2' => \__('Tuesday', 'flexible-shipping-usps'), '3' => \__('Wednesday', 'flexible-shipping-usps'), '4' => \__('Thursday', 'flexible-shipping-usps'), '5' => \__('Friday', 'flexible-shipping-usps'), '6' => \__('Saturday', 'flexible-shipping-usps'), '7' => \__('Sunday', 'flexible-shipping-usps')), 'custom_attributes' => array('size' => 7), 'class' => 'wc-enhanced-select', 'desc_tip' => \true, 'default' => '');
    }
}
