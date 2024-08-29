<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Exception;

/**
 * Exception thrown when switcher is not accepted.
 *
 * @package WPDesk\AbstractShipping\Exception
 */
class CurrencySwitcherException extends \RuntimeException
{
    /**
     * CurrencySwitcherException constructor.
     */
    public function __construct()
    {
        $link = 'https://octol.io/usps-pro-currency';
        $message = \sprintf(
            // Translators: link.
            \__('Multi-currency is supported only in the USPS Live Rates PRO version. %1$sLearn more →%2$s', 'flexible-shipping-usps'),
            '<a href="' . $link . '" target="_blank">',
            '</a>'
        );
        parent::__construct($message);
    }
}
