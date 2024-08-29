<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

/**
 * Service.
 */
class InternationalResponseService
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $svc_description;
    /**
     * @var float
     */
    private $postage;
    /**
     * @param int $id
     * @param string $svc_description
     * @param float $postage
     */
    public function __construct(int $id, string $svc_description, float $postage)
    {
        $this->id = $id;
        $this->svc_description = $svc_description;
        $this->postage = $postage;
    }
    /**
     * @return int
     */
    public function get_id() : int
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function get_svc_description() : string
    {
        return $this->svc_description;
    }
    /**
     * @return float
     */
    public function get_postage() : float
    {
        return $this->postage;
    }
    /**
     * @param array $postage
     *
     * @return InternationalResponseService
     */
    public static function create_from_response(array $service)
    {
        return new self($service['@attributes']['ID'], $service['SvcDescription'], $service['Postage']);
    }
}
