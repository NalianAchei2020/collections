<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerTrait;
use FlexibleShippingUspsVendor\Psr\Log\LogLevel;
/**
 * Logger that can return last error message that occured.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod
 */
class ErrorLogCatcher implements \FlexibleShippingUspsVendor\Psr\Log\LoggerInterface
{
    use LoggerTrait;
    /** @var LoggerInterface Decorated logger */
    private $logger;
    /** @var string[] */
    private $error_messages = [];
    /**
     * @param LoggerInterface $logger Decorated logger.
     */
    public function __construct(\FlexibleShippingUspsVendor\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Logs with an arbitrary level.
     *
     * @see https://github.com/php-fig/log
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        if ($level === \FlexibleShippingUspsVendor\Psr\Log\LogLevel::ERROR) {
            $this->error_messages[] = $message;
        }
        $this->logger->log($level, $message, $context);
    }
    /**
     * Returns last error message that was logged.
     *
     * @return string|false False if empty
     */
    public function get_last_error_message()
    {
        return \reset($this->error_messages);
    }
    /**
     * Returns true when error occured and was logged.
     *
     * @return bool
     */
    public function was_error()
    {
        return $this->get_last_error_message() !== \false;
    }
}
