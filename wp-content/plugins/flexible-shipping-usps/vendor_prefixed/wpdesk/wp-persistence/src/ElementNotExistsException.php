<?php

namespace FlexibleShippingUspsVendor\WPDesk\Persistence;

use FlexibleShippingUspsVendor\Psr\Container\NotFoundExceptionInterface;
/**
 * @package WPDesk\Persistence
 */
class ElementNotExistsException extends \RuntimeException implements \FlexibleShippingUspsVendor\Psr\Container\NotFoundExceptionInterface
{
}
