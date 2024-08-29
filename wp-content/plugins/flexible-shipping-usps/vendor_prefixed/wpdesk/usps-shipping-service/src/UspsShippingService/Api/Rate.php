<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api;

use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\NullLogger;
use FlexibleShippingUspsVendor\USPS\XMLParser;
/**
 * USPS Rate request.
 */
class Rate extends \FlexibleShippingUspsVendor\USPS\Rate implements \FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $password;
    /**
     * @param string $username
     * @param LoggerInterface $logger
     */
    public function __construct(string $username = '', $password = '', \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger = null)
    {
        parent::__construct($username);
        $this->password = $password;
        if ($logger !== null) {
            $this->setLogger($logger);
        } else {
            $this->setLogger(new \FlexibleShippingUspsVendor\Psr\Log\NullLogger());
        }
    }
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return null
     */
    public function setLogger(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * @return string
     */
    public function getXmlRequest() : string
    {
        return $this->getXMLString();
    }
    /**
     * Return the xml string built that we are about to send over to the api.
     *
     * @return string
     */
    protected function getXMLString() : string
    {
        $attributes = ['USERID' => $this->username];
        if ($this->password) {
            $attributes['PASSWORD'] = $this->password;
        }
        $postFields = ['@attributes' => $attributes, 'Revision' => 2];
        $postFields = \array_merge($postFields, $this->getPostFields());
        $xml = \FlexibleShippingUspsVendor\USPS\XMLParser::createXML($this->apiCodes[$this->apiVersion], $postFields);
        return $xml->saveXML();
    }
    /**
     * Perform the API call.
     *
     * @return string
     */
    public function getRate() : string
    {
        $this->logger->debug('Request to USPS API', ['content' => $this->getPostData()['XML'], 'endpointurl' => $this->getEndpoint()]);
        return parent::getRate();
    }
}
