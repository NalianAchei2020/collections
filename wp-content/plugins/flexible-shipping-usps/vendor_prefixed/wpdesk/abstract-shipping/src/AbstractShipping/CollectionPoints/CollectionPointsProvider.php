<?php

/**
 * Capability: CollectionPointsProvider class
 *
 * @package WPDesk\AbstractShipping\CollectionPointCapability
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\CollectionPointCapability;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\CollectionPoints\CollectionPoint;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\CollectionPointNotFoundException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Address;
/**
 * Interface for classes that provides collections points.
 */
interface CollectionPointsProvider
{
    /**
     * Get nearest collection points to given address.
     *
     * @param Address $address .
     *
     * @return CollectionPoint[]
     * @throws CollectionPointNotFoundException
     */
    public function get_nearest_collection_points(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Address $address);
    /**
     * Get single nearest collection point to given address.
     *
     * @param Address $address .
     *
     * @return CollectionPoint
     * @throws CollectionPointNotFoundException
     */
    public function get_single_nearest_collection_point(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Address $address);
    /**
     * Get get collection point by given id.
     *
     * @param string $collection_point_id .
     * @param string $country_code .
     *
     * @return CollectionPoint
     * @throws CollectionPointNotFoundException .
     */
    public function get_point_by_id($collection_point_id, $country_code);
}
