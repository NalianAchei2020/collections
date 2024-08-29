<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms;

use FlexibleShippingUspsVendor\Psr\Container\ContainerInterface;
use FlexibleShippingUspsVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Persistent container support for forms.
 *
 * @package WPDesk\Forms
 */
interface ContainerForm
{
    /**
     * @param ContainerInterface $data
     *
     * @return void
     */
    public function set_data(\FlexibleShippingUspsVendor\Psr\Container\ContainerInterface $data);
    /**
     * Put data from form into a container.
     *
     * @return void
     */
    public function put_data(\FlexibleShippingUspsVendor\WPDesk\Persistence\PersistentContainer $container);
}
