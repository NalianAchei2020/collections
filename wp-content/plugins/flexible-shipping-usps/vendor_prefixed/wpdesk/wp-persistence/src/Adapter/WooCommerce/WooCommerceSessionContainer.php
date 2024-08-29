<?php

namespace FlexibleShippingUspsVendor\WPDesk\Persistence\Adapter\WooCommerce;

use FlexibleShippingUspsVendor\WPDesk\Persistence\ElementNotExistsException;
use FlexibleShippingUspsVendor\WPDesk\Persistence\FallbackFromGetTrait;
use FlexibleShippingUspsVendor\WPDesk\Persistence\PersistentContainer;
/**
 * @package WPDesk\Persistence\WooCommerce
 */
final class WooCommerceSessionContainer implements \FlexibleShippingUspsVendor\WPDesk\Persistence\PersistentContainer
{
    use FallbackFromGetTrait;
    /** @var \WC_Settings_API */
    private $session;
    public function __construct(\WC_Session $session)
    {
        $this->session = $session;
    }
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new \FlexibleShippingUspsVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $id));
        }
        return $this->session->get($id);
    }
    public function has($id) : bool
    {
        $value = $this->session->get($id);
        return $value !== null;
    }
    public function set(string $id, $value)
    {
        $this->session->set($id, $value);
    }
    public function delete(string $id)
    {
        $this->session->set($id, null);
    }
}
