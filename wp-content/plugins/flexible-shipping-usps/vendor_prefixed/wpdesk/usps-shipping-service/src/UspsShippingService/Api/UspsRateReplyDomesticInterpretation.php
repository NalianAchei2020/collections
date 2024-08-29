<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\Money;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API
 */
class UspsRateReplyDomesticInterpretation extends \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInterpretation
{
    /**
     * Is tax enabled.
     *
     * @var bool
     */
    private $commercial_rates;
    private bool $add_insurance;
    /**
     * UspsRateReplyInterpretation constructor.
     *
     * @param Rate $request Rate request.
     * @param bool $commercial_rates Commercial rates.
     * @param bool $is_tax_enabled Is tax enabled.
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate $request, bool $commercial_rates, bool $add_insurance, bool $is_tax_enabled)
    {
        parent::__construct($request, $is_tax_enabled);
        $this->commercial_rates = $commercial_rates;
        $this->add_insurance = $add_insurance;
    }
    /**
     * Get response from USPS.
     *
     * @return SingleRate[]
     */
    public function get_rates() : array
    {
        $rates = [];
        $postages = $this->prepare_postages($this->get_packages($this->request->getArrayResponse()));
        foreach ($postages as $class_id_postages) {
            $rates[] = $this->prepare_rate_from_postages($class_id_postages);
        }
        return $rates;
    }
    /**
     * @param DomesticResponsePostage[] $postages
     *
     * @return SingleRate
     */
    private function prepare_rate_from_postages(array $postages) : \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate
    {
        $rate = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate();
        foreach ($postages as $postage) {
            $rate->service_type = $postage->get_class_id();
            $rate->service_name = $postage->get_mail_service();
            if (!isset($rate->total_charge)) {
                $rate->total_charge = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\Money();
                $rate->total_charge->amount = 0;
                $rate->total_charge->currency = 'USD';
            }
            $rate->total_charge->amount += $postage->get_preferred_rate($this->commercial_rates) + ($this->add_insurance ? $postage->get_insurance_price() : 0.0);
        }
        return $rate;
    }
    /**
     * @param array $packages
     *
     * @return DomesticResponsePostage[]
     */
    private function prepare_postages(array $packages) : array
    {
        $postages = [];
        foreach ($packages as $package) {
            $postages = $this->prepare_postages_from_package($postages, $package);
        }
        return $this->remove_uncomplete_postages($postages, \count($packages));
    }
    /**
     * @param DomesticResponsePostage[] $postages
     * @param int $packages_count
     *
     * @return DomesticResponsePostage[]
     */
    private function remove_uncomplete_postages(array $postages, int $packages_count) : array
    {
        foreach ($postages as $class_id => $class_id_postages) {
            if (\count($class_id_postages) !== $packages_count) {
                unset($postages[$class_id]);
            }
        }
        return $postages;
    }
    /**
     * @param DomesticResponsePostage[] $postages
     *
     * @return DomesticResponsePostage[]
     */
    private function prepare_postages_from_package(array $postages, array $package) : array
    {
        $package_postages = $package['Postage'];
        if (!isset($package_postages[0])) {
            $package_postages = [$package_postages];
        }
        foreach ($package_postages as $single_package_postage) {
            $single_postage = \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\DomesticResponsePostage::create_from_response($single_package_postage);
            if (!isset($postages[$single_postage->get_class_id()])) {
                $postages[$single_postage->get_class_id()] = [];
            }
            if (0.0 !== $single_postage->get_rate() || $this->commercial_rates && 0.0 !== $single_postage->get_commercial_rate()) {
                $postages[$single_postage->get_class_id()][] = $single_postage;
            }
        }
        return $postages;
    }
    /**
     * @param array $array_response
     *
     * @return array
     */
    private function get_packages(array $array_response) : array
    {
        if (isset($array_response['RateV4Response']) && isset($array_response['RateV4Response']['Package']) && \is_array($array_response['RateV4Response']['Package'])) {
            if (isset($array_response['RateV4Response']['Package'][0])) {
                return $array_response['RateV4Response']['Package'];
            }
            return [$array_response['RateV4Response']['Package']];
        }
        return [];
    }
}
