<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Weight;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
/**
 * Build request for USPS international rate.
 */
class UspsRateRequestInternationalBuilder extends \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestBuilder
{
    /**
     * Prepare rate request.
     *
     * @return Rate
     */
    protected function prepare_rate_request(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate
    {
        $rate = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate($this->settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::USER_ID), $this->settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::PASSWORD, ''), $logger);
        $rate->setInternationalCall(\true);
        return $rate;
    }
    /**
     * Add package.
     *
     * @param Package $shipment_package .
     *
     * @throws UnitConversionException .
     */
    protected function add_package(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Package $shipment_package) : void
    {
        $package = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage();
        $this->set_package_weight($package, $shipment_package);
        $package->setField('MailType', \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage::MAIL_TYPE_PACKAGE);
        $package->setField('ValueOfContents', $this->calculate_package_value($shipment_package));
        $package->setField('Country', (new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Countries())->get_country($this->shipment->ship_to->address->country_code));
        $package = $this->set_dimensions_if_present($package, $shipment_package);
        $package->setField('OriginZip', $this->shipment->ship_from->address->postal_code);
        $this->request->addPackage($package);
    }
}
