<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlexibleShippingUspsVendor\Monolog\Handler;

use FlexibleShippingUspsVendor\Monolog\Logger;
use FlexibleShippingUspsVendor\Monolog\Formatter\NormalizerFormatter;
use FlexibleShippingUspsVendor\Monolog\Formatter\FormatterInterface;
use FlexibleShippingUspsVendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \FlexibleShippingUspsVendor\Monolog\Handler\AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(\FlexibleShippingUspsVendor\Doctrine\CouchDB\CouchDBClient $client, $level = \FlexibleShippingUspsVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : \FlexibleShippingUspsVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FlexibleShippingUspsVendor\Monolog\Formatter\NormalizerFormatter();
    }
}
