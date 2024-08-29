<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

/**
 * USPS API Rate Package.
 */
class RatePackage extends \FlexibleShippingUspsVendor\USPS\RatePackage
{
    /**
     * Set the width property.
     *
     * @param float $value
     *
     * @return RatePackage
     */
    public function setWidth(float $value) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage
    {
        return $this->setField('Width', $value);
    }
    /**
     * Set the length property.
     *
     * @param float $value
     *
     * @return object RatePackage
     */
    public function setLength(float $value) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage
    {
        return $this->setField('Length', $value);
    }
    /**
     * Set the height property.
     *
     * @param float $value
     *
     * @return object RatePackage
     */
    public function setHeight(float $value) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage
    {
        return $this->setField('Height', $value);
    }
    /**
     * Set the value of contents property.
     *
     * @param float $value
     *
     * @return object RatePackage
     */
    public function setValueOfContents(float $value) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RatePackage
    {
        return $this->setField('ValueOfContents', $value);
    }
    /**
     * @param int $service .
     */
    public function addSpecialService(int $service)
    {
        if (!isset($this->packageInfo['SpecialServices'])) {
            $this->packageInfo['SpecialServices'] = [];
        }
        if (empty($this->packageInfo['SpecialServices'])) {
            $this->packageInfo['SpecialServices']['SpecialService'] = [];
        }
        $this->packageInfo['SpecialServices']['SpecialService'][] = $service;
    }
}
