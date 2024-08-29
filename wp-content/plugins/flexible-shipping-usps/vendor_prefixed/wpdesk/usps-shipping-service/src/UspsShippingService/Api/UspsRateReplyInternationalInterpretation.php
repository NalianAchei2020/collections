<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\Money;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
/**
 * Get response from API
 */
class UspsRateReplyInternationalInterpretation extends \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInterpretation
{
    /**
     * Get response from USPS.
     *
     * @return SingleRate[]
     */
    public function get_rates() : array
    {
        $rates = [];
        $services = $this->prepare_services($this->get_packages($this->request->getArrayResponse()));
        foreach ($services as $service) {
            $rates[] = $this->prepare_rate_from_services($service);
        }
        return $rates;
    }
    /**
     * @param InternationalResponseService[] $services
     *
     * @return SingleRate
     */
    private function prepare_rate_from_services(array $services) : \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate
    {
        $rate = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate();
        foreach ($services as $postage) {
            $rate->service_type = $postage->get_id();
            $rate->service_name = $postage->get_svc_description();
            if (!isset($rate->total_charge)) {
                $rate->total_charge = new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\Money();
                $rate->total_charge->amount = 0;
                $rate->total_charge->currency = 'USD';
            }
            $rate->total_charge->amount += $postage->get_postage();
        }
        return $rate;
    }
    /**
     * @param array $packages
     *
     * @return DomesticResponsePostage[]
     */
    private function prepare_services(array $packages) : array
    {
        $services = [];
        foreach ($packages as $package) {
            $services = $this->prepare_services_from_package($services, $package);
        }
        return $this->remove_uncomplete_services($services, \count($packages));
    }
    /**
     * @param DomesticResponsePostage[] $services
     * @param int $packages_count
     *
     * @return DomesticResponsePostage[]
     */
    private function remove_uncomplete_services(array $services, int $packages_count) : array
    {
        foreach ($services as $id => $id_service) {
            if (\count($id_service) !== $packages_count) {
                unset($services[$id]);
            }
        }
        return $services;
    }
    /**
     * @param DomesticResponsePostage[] $services
     *
     * @return DomesticResponsePostage[]
     */
    private function prepare_services_from_package(array $services, array $package) : array
    {
        $package_services = $package['Service'];
        if (!isset($package_services[0])) {
            $package_services = [$package_services];
        }
        foreach ($package_services as $single_package_service) {
            $single_service = \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\InternationalResponseService::create_from_response($single_package_service);
            if (!isset($services[$single_service->get_id()])) {
                $services[$single_service->get_id()] = [];
            }
            $services[$single_service->get_id()][] = $single_service;
        }
        return $services;
    }
    /**
     * @param array $array_response
     *
     * @return array
     */
    private function get_packages(array $array_response) : array
    {
        if (isset($array_response['IntlRateV2Response']) && isset($array_response['IntlRateV2Response']['Package']) && \is_array($array_response['IntlRateV2Response']['Package'])) {
            if (isset($array_response['IntlRateV2Response']['Package'][0])) {
                return $array_response['IntlRateV2Response']['Package'];
            } else {
                return [$array_response['IntlRateV2Response']['Package']];
            }
        }
        return [];
    }
}
