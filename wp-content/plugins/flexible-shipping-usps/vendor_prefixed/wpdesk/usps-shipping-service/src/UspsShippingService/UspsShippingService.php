<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\RateException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\UnitConversionException;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRatingImplementation;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingService;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\HasSettings;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\ConnectionChecker;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyDomesticInterpretation;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInternationalInterpretation;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInterpretation;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestBuilder;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestDomesticBuilder;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestInternationalBuilder;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Exception\CurrencySwitcherException;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Usps main shipping class injected into WooCommerce shipping method.
 */
class UspsShippingService extends \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingService implements \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\HasSettings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanRate, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability\CanTestSettings
{
    /** Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /** Shipping method helper.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * Origin country.
     *
     * @var string
     */
    private $origin_country;
    const UNIQUE_ID = 'flexible_shipping_usps';
    /**
     * UspsShippingService constructor.
     *
     * @param LoggerInterface $logger Logger.
     * @param ShopSettings    $shop_settings Helper.
     * @param string          $origin_country Origin country.
     */
    public function __construct(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger, \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings $shop_settings, string $origin_country)
    {
        $this->logger = $logger;
        $this->shop_settings = $shop_settings;
        $this->origin_country = $origin_country;
    }
    /**
     * Set logger.
     *
     * @param LoggerInterface $logger Logger.
     */
    public function setLogger(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * .
     *
     * @return LoggerInterface
     */
    public function get_logger() : \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface
    {
        return $this->logger;
    }
    /**
     * .
     *
     * @return ShopSettings
     */
    public function get_shop_settings() : \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings
    {
        return $this->shop_settings;
    }
    /**
     * Is standard rate enabled?
     *
     * @param SettingsValues $settings .
     *
     * @return bool
     */
    public function is_rate_enabled(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings) : bool
    {
        return \true;
    }
    /**
     * Rate shipment.
     *
     * @param SettingsValues $settings Settings.
     * @param Shipment       $shipment Shipment.
     *
     * @return ShipmentRating
     * @throws InvalidSettingsException InvalidSettingsException.
     * @throws RateException RateException.
     * @throws UnitConversionException Weight exception.
     */
    public function rate_shipment(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment) : \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating
    {
        if (!$this->is_domestic_shipment($shipment) || !$this->shipment_has_special_services($shipment, $settings)) {
            return $this->rate_shipment_without_special_services($settings, $shipment);
        }
        $rates = [];
        foreach (\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices::API_SERVICES as $service) {
            try {
                $rates = \array_merge($rates, $this->rate_shipment_for_service($settings, $shipment, $service));
            } catch (\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\RateException $e) {
                $this->get_logger()->warning($e->getMessage());
            }
        }
        return new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRatingImplementation($rates);
    }
    private function rate_shipment_without_special_services(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment) : \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRating
    {
        if (!$this->get_settings_definition()->validate_settings($settings)) {
            throw new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\InvalidSettingsException();
        }
        $this->verify_currency($this->shop_settings->get_default_currency(), $this->shop_settings->get_currency());
        $is_domestic = $this->is_domestic_shipment($shipment);
        $request_builder = $this->create_rate_request_builder($settings, $shipment, $this->shop_settings, $is_domestic);
        $request_builder->build_request();
        $request = $request_builder->get_build_request();
        $request->getRate();
        if ($request->getResponse()) {
            $this->logger->debug('USPS API Response', ['content' => $this->pretty_print_xml($request->getResponse())]);
        } else {
            $this->logger->debug('USPS API Response', ['content' => \__('Empty response!', 'flexible-shipping-usps')]);
        }
        $reply = $this->create_reply_interpretation($request, $this->shop_settings, $settings, $is_domestic);
        if ($reply->has_reply_warning()) {
            $this->logger->info($reply->get_reply_message());
        }
        if (!$reply->has_reply_error()) {
            $rates = $this->filter_service_rates($settings, $is_domestic, $reply->get_rates());
        } else {
            throw new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\RateException($request->getErrorMessage());
        }
        return new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\ShipmentRatingImplementation($rates);
    }
    private function rate_shipment_for_service(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, string $service) : array
    {
        $logger = $this->get_logger();
        $request_builder = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestDomesticBuilder($settings, $shipment, $this->shop_settings, $logger, $service);
        $request_builder->build_request();
        $request = $request_builder->get_build_request();
        $request->getRate();
        if ($request->getResponse()) {
            $logger->debug('USPS API Response', ['content' => $this->pretty_print_xml($request->getResponse())]);
        } else {
            $logger->debug('USPS API Response', ['content' => \__('Empty response!', 'flexible-shipping-usps')]);
        }
        $reply = $this->create_reply_interpretation($request, $this->shop_settings, $settings, \true);
        if ($reply->has_reply_warning()) {
            $logger->info($reply->get_reply_message());
        }
        if (!$reply->has_reply_error()) {
            $rates = $this->filter_service_rates($settings, \true, $reply->get_rates());
        } else {
            throw new \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Exception\RateException($request->getErrorMessage());
        }
        return $rates;
    }
    private function shipment_has_special_services(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings) : bool
    {
        return $this->has_insurance_enabled($settings);
    }
    private function has_insurance_enabled(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings) : bool
    {
        return 'yes' === $settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::INSURANCE, 'no');
    }
    /**
     * @param string $xml_string
     *
     * @return string
     */
    protected function pretty_print_xml($xml_string) : string
    {
        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = \false;
        $xml->formatOutput = \true;
        $xml->loadXML($xml_string);
        return $xml->saveXML();
    }
    /**
     * Create reply interpretation.
     *
     * @param Rate           $request .
     * @param ShopSettings   $shop_settings .
     * @param SettingsValues $settings .
     * @param bool           $domestic .
     *
     * @return UspsRateReplyInterpretation
     */
    protected function create_reply_interpretation(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\Rate $request, $shop_settings, $settings, $domestic) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInterpretation
    {
        if ($domestic) {
            return new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyDomesticInterpretation($request, 'yes' === $settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::COMMERCIAL_RATES, 'no'), $this->has_insurance_enabled($settings), $shop_settings->is_tax_enabled());
        }
        return new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateReplyInternationalInterpretation($request, $shop_settings->is_tax_enabled());
    }
    /**
     * Create rate request builder.
     *
     * @param SettingsValues $settings .
     * @param Shipment       $shipment .
     * @param ShopSettings   $shop_settings .
     * @param bool           $domestic .
     * @param array          $services .
     *
     * @return UspsRateRequestBuilder
     */
    protected function create_rate_request_builder(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment, \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings $shop_settings, bool $domestic) : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestBuilder
    {
        if ($domestic) {
            return new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestDomesticBuilder($settings, $shipment, $shop_settings, $this->logger);
        }
        return new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\UspsRateRequestInternationalBuilder($settings, $shipment, $shop_settings, $this->logger);
    }
    /**
     * @param Shipment $shipment .
     *
     * @return bool
     */
    protected function is_domestic_shipment(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Shipment\Shipment $shipment) : bool
    {
        return \in_array($shipment->ship_to->address->country_code, ['US', 'AS', 'GU', 'MP', 'PR', 'VI', 'MH', 'FM', 'PW'], \true);
    }
    /**
     * Verify currency.
     *
     * @param string $default_shop_currency .
     * @param string $currency .
     *
     * @throws CurrencySwitcherException .
     */
    protected function verify_currency(string $default_shop_currency, string $currency)
    {
        if ('USD' !== $currency || 'USD' !== $default_shop_currency) {
            throw new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Exception\CurrencySwitcherException();
        }
    }
    /**
     * Get settings
     *
     * @return UspsSettingsDefinition
     */
    public function get_settings_definition() : \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition
    {
        return new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition($this->shop_settings);
    }
    /**
     * Get unique ID.
     *
     * @return string
     */
    public function get_unique_id() : string
    {
        return self::UNIQUE_ID;
    }
    /**
     * Get name.
     *
     * @return string
     */
    public function get_name() : string
    {
        return \__('USPS Live Rates', 'flexible-shipping-usps');
    }
    /**
     * Get description.
     *
     * @return string
     */
    public function get_description() : string
    {
        return \__('USPS integration', 'flexible-shipping-usps');
    }
    /**
     * Pings API.
     * Returns empty string on success or error message on failure.
     *
     * @param SettingsValues  $settings .
     * @param LoggerInterface $logger .
     *
     * @return string
     */
    public function check_connection(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger) : string
    {
        try {
            $connection_checker = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\ConnectionChecker($settings, $logger);
            $connection_checker->check_connection();
            return '';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * Returns field ID after which API Status field should be added.
     *
     * @return string
     */
    public function get_field_before_api_status_field() : string
    {
        return \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::DEBUG_MODE;
    }
    /**
     * Filter&change rates according to settings.
     *
     * @param SettingsValues $settings Settings.
     * @param bool           $is_domestic Domestic rates.
     * @param SingleRate[]   $usps_rates Response.
     *
     * @return SingleRate[]
     */
    protected function filter_service_rates(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, bool $is_domestic, array $usps_rates) : array
    {
        $rates = [];
        if (!empty($usps_rates)) {
            $all_services = $this->get_services($is_domestic);
            $all_services_keys = \array_keys($all_services);
            $services_settings = $this->get_services_settings($settings, $is_domestic);
            if ($this->is_custom_services_enable($settings)) {
                foreach ($usps_rates as $usps_single_rate) {
                    if (isset($usps_single_rate->service_type) && \in_array($usps_single_rate->service_type, $all_services_keys) && !empty($services_settings[$usps_single_rate->service_type]['enabled'])) {
                        $usps_single_rate->service_name = $services_settings[$usps_single_rate->service_type]['name'];
                        $rates[$usps_single_rate->service_type] = $usps_single_rate;
                    }
                }
                $rates = $this->sort_services($rates, $services_settings);
            } else {
                foreach ($usps_rates as $usps_single_rate) {
                    if (isset($usps_single_rate->service_type) && \in_array($usps_single_rate->service_type, $all_services_keys)) {
                        $usps_single_rate->service_name = $all_services[$usps_single_rate->service_type];
                        $rates[$usps_single_rate->service_type] = $usps_single_rate;
                    }
                }
            }
        }
        return $rates;
    }
    /**
     * @param bool $is_domestic .
     *
     * @return array
     */
    private function get_services(bool $is_domestic) : array
    {
        $usps_services = new \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices();
        if ($is_domestic) {
            return $usps_services->get_services_domestic();
        }
        return $usps_services->get_services_international();
    }
    /**
     * @param SettingsValues $settings Settings.
     * @param bool           $is_domestic Domestic rates.
     *
     * @return array
     */
    private function get_services_settings(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings, bool $is_domestic) : array
    {
        if ($is_domestic) {
            $services_settings = $settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::SERVICES_DOMESTIC, []);
        } else {
            $services_settings = $settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::SERVICES_INTERNATIONAL, []);
        }
        return \is_array($services_settings) ? $services_settings : [];
    }
    /**
     * Sort rates according to order set in admin settings.
     *
     * @param SingleRate[] $rates           Rates.
     * @param array        $option_services Saved services to settings.
     *
     * @return SingleRate[]
     */
    private function sort_services(array $rates, array $option_services) : array
    {
        if (!empty($option_services)) {
            $services = [];
            foreach ($option_services as $service_code => $service_name) {
                if (isset($rates[$service_code])) {
                    $services[] = $rates[$service_code];
                }
            }
            return $services;
        }
        return $rates;
    }
    /**
     * Are customs service settings enabled.
     *
     * @param SettingsValues $settings Values.
     *
     * @return bool
     */
    private function is_custom_services_enable(\FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues $settings) : bool
    {
        return $settings->has_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::CUSTOM_SERVICES) && 'yes' === $settings->get_value(\FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition::CUSTOM_SERVICES);
    }
}
