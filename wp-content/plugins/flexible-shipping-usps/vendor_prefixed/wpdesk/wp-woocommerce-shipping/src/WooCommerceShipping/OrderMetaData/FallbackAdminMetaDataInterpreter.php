<?php

/**
 * Meta data interpreter.
 *
 * @package WPDesk\WooCommerceShipping\Ups
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters;

use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminMetaDataUnchangedTrait;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
/**
 * Can interpret Fallback meta data from WooCommerce order shipping on admin.
 */
class FallbackAdminMetaDataInterpreter implements \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreter
{
    use AdminMetaDataUnchangedTrait;
    /**
     * Get meta key on admin order edit page.
     *
     * @param string         $display_key .
     * @param \WC_Meta_Data  $meta .
     * @param \WC_Order_Item $order_item .
     *
     * @return string
     */
    public function get_display_key($display_key, $meta, $order_item)
    {
        if ($this->is_supported_key_on_admin($display_key)) {
            return \__('Fallback reason', 'flexible-shipping-usps');
        }
        return $display_key;
    }
    /**
     * Is supported key on admin?
     *
     * @param string $display_key .
     *
     * @return bool
     */
    public function is_supported_key_on_admin($display_key)
    {
        return $display_key === \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod::META_DATA_KEY;
    }
}
