<?php

/**
 * Ajax status handler.
 *
 * @package WPDesk\WooCommerceShipping\Usps
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\ConnectionChecker;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
/**
 * Can handle api status ajax request.
 */
class UspsFieldApiStatusAjax extends \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax
{
    /**
     * Check connection error.
     *
     * @return string|false
     */
    protected function check_connection_error()
    {
        try {
            $this->ping();
            return \false;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Ping api.
     *
     * @throws \Exception
     */
    private function ping()
    {
        $connection_checker = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\ConnectionChecker($this->get_settings(), $this->get_logger());
        $connection_checker->check_connection();
    }
}
