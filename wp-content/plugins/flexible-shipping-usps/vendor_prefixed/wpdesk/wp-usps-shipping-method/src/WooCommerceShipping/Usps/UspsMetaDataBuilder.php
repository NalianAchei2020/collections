<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
/**
 * Can build USPS meta data.
 */
class UspsMetaDataBuilder extends \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder
{
    const META_USPS_SERVICE_CODE = 'usps_service_id';
    /**
     * Build meta data for rate.
     *
     * @param SingleRate $rate .
     * @param Shipment $shipment .
     *
     * @return array
     */
    public function build_meta_data_for_rate(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate $rate, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment)
    {
        $meta_data = parent::build_meta_data_for_rate($rate, $shipment);
        $meta_data = $this->add_usps_service_code_to_metadata($meta_data, $rate);
        return $meta_data;
    }
    /**
     * Add USPS service to metadata.
     *
     * @param array $meta_data
     * @param SingleRate $rate
     *
     * @return array
     */
    private function add_usps_service_code_to_metadata(array $meta_data, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate $rate)
    {
        $meta_data[self::META_USPS_SERVICE_CODE] = $rate->service_type;
        return $meta_data;
    }
}
