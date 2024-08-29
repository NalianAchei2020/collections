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

use FlexibleShippingUspsVendor\Aws\Sdk;
use FlexibleShippingUspsVendor\Aws\DynamoDb\DynamoDbClient;
use FlexibleShippingUspsVendor\Monolog\Formatter\FormatterInterface;
use FlexibleShippingUspsVendor\Aws\DynamoDb\Marshaler;
use FlexibleShippingUspsVendor\Monolog\Formatter\ScalarFormatter;
use FlexibleShippingUspsVendor\Monolog\Logger;
/**
 * Amazon DynamoDB handler (http://aws.amazon.com/dynamodb/)
 *
 * @link https://github.com/aws/aws-sdk-php/
 * @author Andrew Lawson <adlawson@gmail.com>
 */
class DynamoDbHandler extends \FlexibleShippingUspsVendor\Monolog\Handler\AbstractProcessingHandler
{
    public const DATE_FORMAT = 'Y-m-d\\TH:i:s.uO';
    /**
     * @var DynamoDbClient
     */
    protected $client;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var int
     */
    protected $version;
    /**
     * @var Marshaler
     */
    protected $marshaler;
    public function __construct(\FlexibleShippingUspsVendor\Aws\DynamoDb\DynamoDbClient $client, string $table, $level = \FlexibleShippingUspsVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        /** @phpstan-ignore-next-line */
        if (\defined('Aws\\Sdk::VERSION') && \version_compare(\FlexibleShippingUspsVendor\Aws\Sdk::VERSION, '3.0', '>=')) {
            $this->version = 3;
            $this->marshaler = new \FlexibleShippingUspsVendor\Aws\DynamoDb\Marshaler();
        } else {
            $this->version = 2;
        }
        $this->client = $client;
        $this->table = $table;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $filtered = $this->filterEmptyFields($record['formatted']);
        if ($this->version === 3) {
            $formatted = $this->marshaler->marshalItem($filtered);
        } else {
            /** @phpstan-ignore-next-line */
            $formatted = $this->client->formatAttributes($filtered);
        }
        $this->client->putItem(['TableName' => $this->table, 'Item' => $formatted]);
    }
    /**
     * @param  mixed[] $record
     * @return mixed[]
     */
    protected function filterEmptyFields(array $record) : array
    {
        return \array_filter($record, function ($value) {
            return !empty($value) || \false === $value || 0 === $value;
        });
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter() : \FlexibleShippingUspsVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FlexibleShippingUspsVendor\Monolog\Formatter\ScalarFormatter(self::DATE_FORMAT);
    }
}
