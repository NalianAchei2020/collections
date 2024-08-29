<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices;
/**
 * Postage.
 */
class DomesticResponsePostage
{
    /**
     * @var string
     */
    private $class_id;
    /**
     * @var string
     */
    private $mail_service;
    /**
     * @var float
     */
    private $rate;
    /**
     * @var float
     */
    private $commercial_rate;
    private float $insurance_price;
    /**
     * @param string $class_id
     * @param string $mail_service
     * @param float $rate
     * @param float $commercial_rate
     */
    public function __construct(string $class_id, string $mail_service, float $rate, float $commercial_rate, float $insurance_price)
    {
        $this->class_id = $class_id;
        $this->mail_service = $mail_service;
        $this->rate = $rate;
        $this->commercial_rate = $commercial_rate;
        $this->insurance_price = $insurance_price;
    }
    /**
     * @return string
     */
    public function get_class_id() : string
    {
        return $this->class_id;
    }
    /**
     * @return string
     */
    public function get_mail_service() : string
    {
        return $this->mail_service;
    }
    /**
     * @return float
     */
    public function get_rate() : float
    {
        return $this->rate;
    }
    /**
     * @return float
     */
    public function get_commercial_rate() : float
    {
        return $this->commercial_rate;
    }
    /**
     * @param bool $commercial_rate
     *
     * @return float
     */
    public function get_preferred_rate(bool $commercial_rate)
    {
        if ($commercial_rate && null !== $this->commercial_rate && 0.0 !== $this->commercial_rate) {
            return $this->commercial_rate;
        }
        return $this->rate;
    }
    public function get_insurance_price() : float
    {
        return $this->insurance_price;
    }
    /**
     * @param array $postage
     *
     * @return DomesticResponsePostage
     */
    public static function create_from_response(array $postage) : self
    {
        return new self(self::prepare_class_id($postage['@attributes']['CLASSID'], $postage['MailService']), $postage['MailService'], $postage['Rate'], (float) ($postage['CommercialRate'] ?? 0.0), self::get_insurance_from_special_services($postage['SpecialServices'] ?? []));
    }
    private static function get_insurance_from_special_services(array $special_services) : float
    {
        foreach ($special_services['SpecialService'] ?? [] as $special_service) {
            if (($special_service['ServiceName'] ?? '') === 'Insurance') {
                return (float) $special_service['Price'];
            }
        }
        return 0.0;
    }
    /**
     * @param string $classid
     * @param string $mail_service
     *
     * @return string
     */
    private static function prepare_class_id(string $classid, string $mail_service)
    {
        if ($classid === '0') {
            if ($mail_service === \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices::FIRST_CLASS_PACKAGE_SERVICE_RETAIL_NAME) {
                return \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices::FIRST_CLASS_PACKAGE_SERVICE_RETAIL;
            }
            if ($mail_service === \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices::FIRST_CLASS_MAIL_LARGE_ENVELOPE_NAME) {
                return \FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices::FIRST_CLASS_LARGE_ENVELOPE;
            }
        }
        return $classid;
    }
}
