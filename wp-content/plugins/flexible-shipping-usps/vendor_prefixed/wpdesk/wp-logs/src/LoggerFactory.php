<?php

namespace FlexibleShippingUspsVendor\WPDesk\Logger;

use FlexibleShippingUspsVendor\Monolog\Logger;
/*
 * @package WPDesk\Logger
 */
interface LoggerFactory
{
    /**
     * Returns created Logger
     *
     * @param string $name
     *
     * @return Logger
     */
    public function getLogger($name);
}
